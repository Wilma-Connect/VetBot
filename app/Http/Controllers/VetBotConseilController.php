<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
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
        $conversation->localisation = $user->ville;
        $conversation->surface_m2 = $user->surface_m2;
        $conversation->service = 'conseil';
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
        $imageBase64 = null;

        // Sauvegarde de l’image si envoyée
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('vetbot-images', 's3');
            $imageContent = Storage::disk('s3')->get($imagePath);
            $imageBase64 = base64_encode($imageContent);
        }


        //Enregistrement du message utilisateur
        $userMessage = new Message();
        $userMessage->id = Str::uuid();
        $userMessage->conversation_id = $conversation->id;
        $userMessage->role = 'eleveur';
        $userMessage->content = $request->content;
        $userMessage->image = $imagePath ?? null; // stocke juste le chemin S3
        $userMessage->save();

        $prompt = "Tu es un assistant vétérinaire spécialisé dans les élevages en Côte d'Ivoire.
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

    public function getUserConversations(Request $request)
    {
        $user = $request->user();

        $conversations = Conversation::where('user_id', $user->id)
            ->where('service', 'conseil')
            ->with(['messages' => function ($q) {
                $q->latest()->take(1);
            }])
            ->orderByDesc('updated_at')
            ->get();

        return response()->json([
            'status' => 'success',
            'conversations' => $conversations
        ]);
    }
}
