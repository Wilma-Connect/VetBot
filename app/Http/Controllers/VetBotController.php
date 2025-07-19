<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use App\Models\Message;
use App\Models\Prestataire;
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
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Utilisateur non authentifié',
            ], 401);
        }

        $conversation = new Conversation();
        $conversation->id = Str::uuid();
        $conversation->user_id = $user->id;
        $conversation->experience = $user->experience;
        $conversation->type_elevage = $user->type_elevage;
        $conversation->quantite = $user->quantite;
        $conversation->localisation = $user->localisation;
        $conversation->surface_m2 = $user->surface_m2;
        $conversation->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Conversation créée avec succès',
            'conversation' => $conversation,
        ]);
    }


    public function getConversation(Conversation $conversation)
    {
        //Récupère les dernières conversations
        return response()->json([
            'status' => 'success',
            'conversation' => $conversation,
            'messages' => $conversation->messages()->oldest()->get(),
        ]);
    }

    /* public function sendMessage(Request $request, Conversation $conversation)
    {
        $request->validate([
            'content' => 'nullable|string',
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

         $prestataires = Prestataire::where('adresse', 'LIKE', "%{$conversation->localisation}%")
            ->limit(5)
            ->get();

            $prestataireTexte = $prestataires->isEmpty()
                ? "Aucun prestataire local n'a été trouvé dans la base. Propose un service de livraison national fiable."
                : $prestataires->map(function ($p) {
                    $numero = preg_replace('/(\d{3})(\d{2})(\d{2})(\d{2})(\d{2})/', '+$1 $2 $3 $4 $5', $p->numero ?? '');
                    return "- **{$p->nomprestataire}**
                - 📍 Adresse : {$p->adresse}
                - ☎️ Téléphone : {$numero}
                - ℹ️ Infos : {$p->description}";
                })->implode("\n\n");


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
            - **Voici les prestataires vétérinaires ou pharmacies disponibles à proximité** :
                {$prestataireTexte}
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
            - Pose des questions si necessaires pour affiner ta réponse et la rendre plus précise à la fin seulement si la réponse ne fait pas partie des informations sur l'éleveur.

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
    } */


    public function sendMessage(Request $request, Conversation $conversation)
    {

        $request->validate([
            'content' => 'nullable|string',
            'image' => 'nullable|image|max:5120',
        ]);

        $imagePath = null;
        $imageBase64 = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('vetbot-images', 's3');
            $imageContent = Storage::disk('s3')->get($imagePath);
            $imageBase64 = base64_encode($imageContent);
        }

        $userMessage = new Message();
        $userMessage->id = Str::uuid();
        $userMessage->conversation_id = $conversation->id;
        $userMessage->role = 'eleveur';
        $userMessage->content = $request->content;
        $userMessage->image = $imagePath ?? null;
        $userMessage->save();

        $prestataires = Prestataire::where('adresse', 'LIKE', "%{$conversation->localisation}%")->limit(5)->get();
        $prestataireTexte = $prestataires->isEmpty()
            ? "Aucun prestataire local n'a été trouvé dans la base. Propose un service de livraison national fiable."
            : $prestataires->map(function ($p) {
                $numero = preg_replace('/(\d{3})(\d{2})(\d{2})(\d{2})(\d{2})/', '+$1 $2 $3 $4 $5', $p->numero ?? '');
                return "- **{$p->nomprestataire}**
                - 📍 Adresse : {$p->adresse}
                - ☎️ Téléphone : {$numero}
                - ℹ️ Infos : {$p->description}";
            })->implode("\n\n");

        $prompt = "
            Tu es un **expert vétérinaire aviaire en Côte d’Ivoire**, spécialisé dans l’accompagnement des éleveurs locaux. Ton rôle est de poser un **diagnostic probable**, de recommander un **traitement efficace** et de proposer des **solutions locales concrètes** en fonction des données suivantes :

            **Informations sur l’éleveur :**
            - Expérience : {$conversation->experience}
            - Type d’élevage : {$conversation->type_elevage}
            - Nombre d’animaux : {$conversation->quantite}
            - Localisation : {$conversation->localisation}
            - Surface de l’abri : {$conversation->surface_m2} m²

            ---

            ### 📋 Structure obligatoire de ta réponse :

            1. Diagnostic probable
            - Donne clairement le nom de la maladie ou du trouble le plus probable (ex : coccidiose, poux rouges, carence…).

            2. Causes possibles
            - Explique simplement les causes (hygiène, nutrition, parasites, humidité, promiscuité, etc.).

            3. Traitement concret
            - Nom du médicament utilisé en Côte d’Ivoire
            - Dosage et durée du traitement
            - Mode d’administration
            - Prix estimatif en XOF
            - **Voici les prestataires vétérinaires ou pharmacies disponibles à proximité** :
                {$prestataireTexte}
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
            - Pose des questions si necessaires pour affiner ta réponse et la rendre plus précise à la fin seulement si la réponse ne fait pas partie des informations sur l'éleveur.
        ";

        $apiKey = env('GEMINI_API_KEY');
        $client = new Client();

        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent";

        $contents = [];
        if ($imageBase64) {
            $contents[] = [
                "inline_data" => [
                    "mime_type" => "image/jpeg",
                    "data" => $imageBase64,
                ]
            ];
        }
        $contents[] = ["text" => $request->content ?? "Voici l'information de l'éleveur."];
        $contents[] = ["text" => $prompt];

        try {
            $response = $client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-goog-api-key' => $apiKey,
                ],
                'json' => [
                    "contents" => [
                        [
                            "role" => "user",
                            "parts" => $contents
                        ]
                    ]
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            $aiContent = $data['candidates'][0]['content']['parts'][0]['text'] ?? "Désolé, je n’ai pas pu répondre.";

        } catch (\Exception $e) {
            \Log::error('Error calling Gemini API: ' . $e->getMessage());
            $aiContent = "Désolé, une erreur est survenue lors de la génération de la réponse.";
        }

        $aiMessage = new Message();
        $aiMessage->id = Str::uuid();
        $aiMessage->conversation_id = $conversation->id;
        $aiMessage->role = 'assistant';
        $aiMessage->content = $aiContent;
        $aiMessage->save();

        return response()->json([
            'status' => 'success',
            'content' => (string) str_replace(["\r", "\n"], ' ', $aiContent), // pas de retour à la ligne
            'message' => $aiMessage->id,
        ]);
    }

}
