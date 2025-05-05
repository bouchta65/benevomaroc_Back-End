<?php
namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'nom_complet' => 'required|string|max:255',
            'sujet' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);

        Contact::create($validated);

        return response()->json([
            'message' => 'Message envoyé avec succès !'
        ], 200);
    }

    public function getAllMessages(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);  
            $messages = Contact::orderBy('created_at', 'desc')->paginate($perPage);
    
            return response()->json([
                'message' => 'Liste des messages récupérée avec succès.',
                'messages' => $messages
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la récupération des messages.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteAllMessages()
    {
        try {
            Contact::query()->delete();

            return response()->json([
                'message' => 'Tous les messages ont été supprimés avec succès.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la suppression des messages.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    

}
