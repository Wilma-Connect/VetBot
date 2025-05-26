<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Support\Str;
use App\Models\Conversation;
use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Storage;

class VetBotController extends Controller
{
    public function showInitFormConseil()
    {
        return view('pages.diagnostic', ['step' => 'init']);
    }

    public function startConversation(Request $request)
    {
        $request->validate([
            'experience' => 'required|string',
            'type_elevage' => 'required|string',
            'quantite' => 'required|integer|min:1',
            'localisation' => 'required|string',
            'surface_m2' => 'required|integer|min:1',
        ]);

        $conversation = new Conversation();
        $conversation->id = Str::uuid();
        $conversation->user_id = auth()->check() ? auth()->id() : null;
        $conversation->experience = $request->experience;
        $conversation->type_elevage = $request->type_elevage;
        $conversation->quantite = $request->quantite;
        $conversation->localisation = $request->localisation;
        $conversation->surface_m2 = $request->surface_m2;
        $conversation->save();

        return redirect()->route('vetbot.chat', $conversation->id);
    }

    public function chatInterface(Conversation $conversation)
    {
        // Récupère les messages du plus ancien au plus récent
        $messages = $conversation->messages()->oldest()->get();

        return view('pages.diagnostic', [
            'step' => 'chat',
            'conversation' => $conversation,
            'messages' => $messages, // Maintenant ordonnés du plus ancien au plus récent
        ]);
    }

    public function sendMessage(Request $request, Conversation $conversation)
    {
        $request->validate([
            'content' => 'required|string',
            'image' => 'nullable|image|max:5120', // 5 Mo max
        ]);

        $imagePath = null;

        // Sauvegarde de l’image si envoyée
        if ($request->hasFile('image')) {
            // Stocker sur S3 (AWS) dans le dossier vetbot-images/
            $imagePath = $request->file('image')->store('vetbot-images', 's3');
            $imageUrl = env('AWS_URL') . $imagePath;
        }


        // Enregistrement du message utilisateur
        $userMessage = new Message();
        $userMessage->id = Str::uuid();
        $userMessage->conversation_id = $conversation->id;
        $userMessage->role = 'user';
        $userMessage->content = $request->content;
        $userMessage->image = $imagePath ?? null; // stocke juste le chemin S3
        $userMessage->save();

        // Appel API OpenAI
        $context = "
            Tu es un **expert vétérinaire aviaire en Côte d’Ivoire**, spécialisé dans l’accompagnement des éleveurs locaux. Ton rôle est de poser un **diagnostic probable**, de recommander un **traitement efficace** et de proposer des **solutions locales concrètes** en fonction des données suivantes :

            **Informations sur l’éleveur :**
            - Expérience : {$conversation->experience}
            - Type d’élevage : {$conversation->type_elevage}
            - Nombre d’animaux : {$conversation->quantite}
            - Localisation : {$conversation->localisation}
            - Surface de l’abri : {$conversation->surface_m2} m²

            ---

            ### 📋 Structure obligatoire de ta réponse :

            1. **Diagnostic probable**
            - Donne clairement le nom de la maladie ou du trouble le plus probable (ex : coccidiose, poux rouges, carence…).

            2. **Causes possibles**
            - Explique simplement les causes (hygiène, nutrition, parasites, humidité, promiscuité, etc.).

            3. **Traitement concret**
            - Nom du médicament utilisé en Côte d’Ivoire
            - Dosage et durée du traitement
            - Mode d’administration
            - Prix estimatif en XOF
            - **Propose un ou plusieurs lieux d’achat réels** en fonction de la localisation donnée :
                - Si possible, une **pharmacie vétérinaire** ou une **clinique vétérinaire** à proximité.
                - Si tu n’as pas d’info locale précise, suggère un service de livraison national fiable
            4. **Mesures d’hygiène et de prévention**
            - Conseils pratiques pour améliorer l’environnement de l’élevage.

            5. **Conclusion claire et rassurante**
            - Encourage à consulter un vétérinaire **uniquement si les symptômes persistent ou s’aggravent**.

            ---

            ### ⚠️ Règles strictes :

            - N’utilise **jamais** des formulations vagues comme “si vous pensez que”, “peut-être”, “il pourrait s’agir de…” → Tu es **l’expert**.
            - **Adapte tes recommandations à la ville ou région de l’éleveur** grâce à sa localisation.
            - Ne recommande que des produits **disponibles en Côte d’Ivoire**.
            - Utilise un **langage simple et pratique** pour des éleveurs de terrain sans formation vétérinaire.

            ";


            $messages = [
                ["role" => "system", "content" => $context],
            ];

            if ($imagePath) {
                $imageUrl = Storage::disk('s3')->url($imagePath); // URL publique S3

                $messages[] = [
                    "role" => "user",
                    "content" => [
                        ["type" => "text", "text" => $request->content ?? "Voici une image à analyser."],
                        ["type" => "image_url", "image_url" => ["url" => $imageUrl]],
                    ],
                ];
            } else {
                $messages[] = [
                    "role" => "user",
                    "content" => $request->content ?? "Je n'ai pas d'image à partager.",
                ];
            }

            $openaiResponse = OpenAI::chat()->create([
                'model' => 'gpt-4-turbo',
                'messages' => $messages,
            ]);


        $aiContent = $openaiResponse['choices'][0]['message']['content'] ?? 'Désolé, je n’ai pas pu comprendre. Pouvez-vous reformuler ?';

        // Enregistrement de la réponse AI
        $aiMessage = new Message();
        $aiMessage->id = Str::uuid();
        $aiMessage->conversation_id = $conversation->id;
        $aiMessage->role = 'assistant';
        $aiMessage->content = $aiContent;
        $aiMessage->save();

        return response()->json([
            'status' => 'success',
            'content' => $aiContent,
            'message' => $aiMessage->id // Optionnel: pour le suivi
        ]);
    }
}
