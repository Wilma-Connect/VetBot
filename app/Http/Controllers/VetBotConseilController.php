<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Support\Str;
use App\Models\Conversation;
use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Storage;

class VetBotConseilController extends Controller
{
    public function showInitForm()
    {
        return view('pages.conseil', ['step' => 'init']);
    }

    public function startConversation(Request $request)
    {
        $request->validate([
            'experience' => 'required|string',
            'type_elevage' => 'required|string',
            'quantite' => 'nullable|integer|min:1',
            'localisation' => 'nullable|string',
            'surface_m2' => 'nullable|integer|min:1',
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

        return response()->json([
            'status' => 'success',
            'message' => 'Conversation créée avec succès',
            'conversation' => $conversation,
        ]);
    }

    public function getConversation(Conversation $conversation)
    {
        // Récupère les messages du plus ancien au plus récent
        return response()->json([
            'status' => 'success',
            'conversation' => $conversation,
            'messages' => $conversation->messages()->oldest()->get(),
        ]);
    }

    public function sendMessage(Request $request, Conversation $conversation)
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

        // Appel API OpenAI
        $context = "Tu es un assistant vétérinaire spécialisé dans les élevages en Côte d'Ivoire.
            Voici les informations sur l’élevage :
            - Expérience : {$conversation->experience}
            - Type d’élevage : {$conversation->type_elevage}
            - Nombre d’animaux : {$conversation->quantite}
            - Localisation : {$conversation->localisation}
            - Surface de l’abri : {$conversation->surface_m2} m²

            Ta réponse doit toujours être structurée comme suit :
            1. Selon le niveau d'expérience d'expérience de l'éleveur, adapte ton langage et ta terminologie.
            2. Donne des conseils pratiques et concrets basés sur les informations fournies.
            3. Si une image est fournie, analyse-la et donne des conseils spécifiques basés sur ce que tu vois.
            4. Si tu n'as pas assez d'informations, demande des précisions.
            Sois clair, simple, et pertinent pour des éleveurs locaux qui n’ont pas forcément assez d'information sur l'élévage.
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
