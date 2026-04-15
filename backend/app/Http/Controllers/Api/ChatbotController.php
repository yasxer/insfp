<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Specialty;
use App\Models\TrainingSession;

class ChatbotController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $userMessage = $request->message;
        $apiKey = env('GEMINI_API_KEY');
        if (empty($apiKey)) {
            Log::error("API KEY IS MISSING IN CONTROLLER");
        }

        // Fetch Database Context
        $specialties = Specialty::where('is_active', true)->get(['name', 'description', 'study_mode']);
        $sessions = TrainingSession::with('specialties')->where('is_active', true)->get();

        $contextData = "CONTEXTE EXTRAIT DE LA BASE DE DONNÉES:\nSpécialités disponibles globalement à l'INFSP :\n";
        if ($specialties->isEmpty()) {
            $contextData .= "- Aucune spécialité disponible pour le moment.\n";
        } else {
            foreach ($specialties as $sp) {
                $desc = $sp->description ?? "Pas de description détaillée";
                $contextData .= "- {$sp->name} (Mode: {$sp->study_mode}): {$desc}\n";
            }
        }

        $contextData .= "\nSessions de formation disponibles (très important pour l'inscription) :\n";
        if ($sessions->isEmpty()) {
            $contextData .= "- Aucune session ouverte pour le moment.\n";
        } else {
            foreach ($sessions as $session) {
                $contextData .= "\n* {$session->name} (Statut: {$session->status}) :\n";
                if ($session->specialties->isEmpty()) {
                    $contextData .= "  - Aucune spécialité n'est encore affectée à cette session.\n";
                } else {
                    foreach ($session->specialties as $spSession) {
                        $studyType = $spSession->pivot->study_type ?? 'Non spécifié';
                        $contextData .= "  - Spécialité: {$spSession->name} | Type d'étude: {$studyType}\n";
                    }
                }
            }
        }
        $contextData .= "\nFIN DU CONTEXTE.\n";

        // Use Gemini AI if key is set in .env
        if ($apiKey) {
            try {
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=' . $apiKey, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => "Tu es l'assistant virtuel IA de l'INFSP (Institut National Spécialisé de la Formation Professionnelle 'Mohamed Tayeb Boussena'). Tu parles en français ou en dardja (dialecte algérien) gentiment pour aider les étudiants ou visiteurs.
Règles strictes et impératives :
1. TU DOIS RÉPONDRE UNIQUEMENT EN UTILISANT LE CONTEXTE FOURNI CI-DESSOUS.
2. Si on te pose une question dont la réponse ne se trouve pas dans le contexte, dis poliment : \"Je suis désolé, je n'ai pas cette information car je me base uniquement sur notre base de données. Veuillez contacter l'administration.\"
3. NE FOURNIS AUCUNE INFORMATION INVENTÉE OU PROVENANT D'INTERNET. TU TE BASES SEULEMENT SUR LE CONTEXTE.
4. Tu NE DOIS POUVOIR RÉPONDRE à aucune question concernant des informations personnelles (étudiants, professeurs, etc.).

{$contextData}

Voici la question de l'utilisateur : \"{$userMessage}\""]
                            ]
                        ]
                    ]
                ]);

                if ($response->successful()) {
                    $val = $response->json();
                    if(isset($val['candidates'][0]['content']['parts'][0]['text'])) {
                        $botReply = $val['candidates'][0]['content']['parts'][0]['text'];
                        return response()->json(['reply' => $botReply]);
                    }
                } else {
                    Log::error("Gemini API Error: " . $response->status() . " " . $response->body());
                    if ($response->status() == 429) {
                        return response()->json(['reply' => "Désolé, le quota d'utilisation de l'intelligence artificielle est atteint pour le moment. Veuillez réessayer plus tard."]);
                    }
                    return response()->json(['reply' => "Désolé, l'intelligence artificielle est actuellement surchargée. Veuillez réessayer dans quelques instants. (Erreur " . $response->status() . ")"]);
                }
            } catch (\Exception $e) {
                Log::error('Erreur API Gemini: ' . $e->getMessage());
                return response()->json(['reply' => "Désolé, une erreur inattendue s'est produite lors de la connexion à l'IA."]);
            }
        }

        // Mock answers if no API KEY configured
        $lowerMsg = strtolower($userMessage);

        if (str_contains($lowerMsg, 'bonjour') || str_contains($lowerMsg, 'salut') || str_contains($lowerMsg, 'salam')) {
            $reply = "Bonjour ! Bienvenue à l'INFSP. Comment puis-je vous aider aujourd'hui ?";
        } elseif (str_contains($lowerMsg, 'inscription') || str_contains($lowerMsg, 'inscrire') || str_contains($lowerMsg, 'tasjil')) {
            $reply = "Pour vous inscrire, vous pouvez cliquer sur le bouton 'S'INSCRIRE' en haut à droite. Vous aurez besoin de remplir vos informations personnelles et de choisir votre spécialité.";
        } elseif (str_contains($lowerMsg, 'formation') || str_contains($lowerMsg, 'programme') || str_contains($lowerMsg, 'specialit')) {
            $reply = "L'INFSP propose plusieurs spécialités comme le Développement Web, l'Administration des Systèmes et Réseaux (ASRI), et les Bases de Données ! Que voulez-vous savoir d'autre ?";
        } elseif (str_contains($lowerMsg, 'contact') || str_contains($lowerMsg, 'téléphone') || str_contains($lowerMsg, 'email')) {
            $reply = "Vous pouvez nous contacter par téléphone au +213 335 7720 ou par email à infsp@gmail.com.";
        } elseif (str_contains($lowerMsg, 'merci') || str_contains($lowerMsg, 'chokran') || str_contains($lowerMsg, 'sahit')) {
            $reply = "Je vous en prie ! N'hésitez pas si vous avez d'autres questions 😊.";
        } else {
             $reply = "Mmm... Je réfléchis encore à votre question. (Astuce: ajoutez votre GEMINI_API_KEY dans le fichier .env du backend pour que je puisse vraiment réfléchir avec l'IA !) 😊";
        }

        sleep(1); // Simulate realistic bot processing delay

        return response()->json([
            'reply' => $reply
        ]);
    }
}
