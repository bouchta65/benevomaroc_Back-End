<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evenement;
use App\Services\PostulationService;


class EventController extends Controller
{
    protected $postulationService;

    public function __construct(PostulationService $postulationService)
    {
        $this->postulationService = $postulationService;
    }

    public function addEvent(Request $request)
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
            $evenement = Evenement::create([
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

            return response()->json(['message' => 'Événement créé avec succès. En attente d’activation par un administrateur.', 'event' => $evenement], 201);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la création de l\'événement', 'error' => $e->getMessage()], 500);
        }
    }


    public function updateEvent(Request $request, $id)
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
            $event = Evenement::findOrFail($id);

            $event->update($validatedData);

            return response()->json(['message' => 'Événement mis à jour avec succès', 'event' => $event], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la mise à jour de l\'événement', 'error' => $e->getMessage()], 500);
        }
    }

    public function deleteEvent($id)
    {
        try {
            $event = Evenement::findOrFail($id);

            $event->delete();

            return response()->json(['message' => 'Événement supprimé avec succès'], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la suppression de l\'événement', 'error' => $e->getMessage()], 500);
        }
    }

    public function getAllEvent()
    {
        try {
            $event = Evenement::All();

            return response()->json(['events' => $event], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la récupération de l\'événement','error' => $e->getMessage()], 500);
        }
    }

    public function getEventById($event_id)
    {
        try {
            $event = Evenement::find($event_id);

            if (!$event) {
                return response()->json(['message' => 'Événement non trouvé.'], 404);
            }

            $status = $this->postulationService->getPostulationStatus($event_id);

            return response()->json(['event' => $event , 'status'=>$status], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la récupération de l\'événement','error' => $e->getMessage()], 500);
        }
    }






}
