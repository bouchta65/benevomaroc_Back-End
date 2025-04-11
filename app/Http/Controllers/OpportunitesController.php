<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Opportunite;
use App\Services\PostulationService;


class OpportunitesController extends Controller
{
    protected $postulationService;

    public function __construct(PostulationService $postulationService)
    {
        $this->postulationService = $postulationService;
    }

    public function addOpportunite(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string',
            'description' => 'required|string',
            'date' => 'required|date',
            'derniere_date_postule' => 'required|date',
            'ville' => 'required|string',
            'adresse' => 'required|string',
            'association_id' => 'required|integer',
            'categorie_id' => 'required|integer',
            'image' => 'nullable|string',
            'nb_benevole' => 'required|integer',
            'duree' => 'nullable|string',
            'engagement_requis' => 'nullable|string',
        ]);
    
        try {
            $opportunite = Opportunite::create([
                'titre' => $validated['titre'],
                'description' => $validated['description'],
                'date' => $validated['date'],
                'derniere_date_postule' => $validated['derniere_date_postule'],
                'ville' => $validated['ville'],
                'adresse' => $validated['adresse'],
                'association_id' => $validated['association_id'],
                'categorie_id' => $validated['categorie_id'],
                'image' => $validated['image'],
                'nb_benevole' => $validated['nb_benevole'],
                'duree' => $validated['duree'],
                'engagement_requis' => $validated['engagement_requis'],
            ]);

            return response()->json(['message' => 'Opportunites créé avec succès. En attente d’activation par un administrateur.', 'opportunite' => $opportunite], 201);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la création de l\'événement', 'error' => $e->getMessage()], 500);
        }
    }


    public function updateOpportunite(Request $request, $id)
    {
        $validatedData = $request->validate([
            'titre' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'date' => 'sometimes|date',
            'derniere_date_postule' => 'sometimes|date',
            'ville' => 'sometimes|string',
            'adress' => 'sometimes|string',
            'association_id' => 'sometimes|exists:associations,id',
            'categorie_id' => 'sometimes|exists:categories,id',
            'image' => 'nullable|string',
            'status' => 'sometimes|string',
            'nb_benevole' => 'sometimes|integer',
            'duree' => 'sometimes|string',
            'engagement_requis' => 'sometimes|string',
        ]);

        try {
            $opportunite = Opportunite::findOrFail($id);

            $opportunite->update($validatedData);

            return response()->json(['message' => 'Opportunite mis à jour avec succès', 'opportunite' => $opportunite], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la mise à jour de l\'opportunite', 'error' => $e->getMessage()], 500);
        }
    }

    public function deleteOpportunite($id)
    {
        try {
            $opportunite = Opportunite::findOrFail($id);

            $opportunite->delete();

            return response()->json(['message' => 'Opportunite supprimé avec succès'], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la suppression de l\'opportunite', 'error' => $e->getMessage()], 500);
        }
    }

    public function getAllOpportunite()
    {
        try {
            $opportunite = Opportunite::All();

            return response()->json(['opportunites' => $opportunite], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la récupération de l\'opportunite','error' => $e->getMessage()], 500);
        }
    }

    public function getOpportuniteById($opportunite_id)
    {
        try {
            $opportunite = Opportunite::find($opportunite_id);

            if (!$opportunite) {
                return response()->json(['message' => 'Opportunite non trouvé.'], 404);
            }

            $status = $this->postulationService->getPostulationStatus($opportunite_id);

            return response()->json(['opportunite' => $opportunite , 'status'=>$status], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la récupération de l\'opportunite','error' => $e->getMessage()], 500);
        }
    }






}
