<?php

namespace App\Http\Controllers;

use App\Models\Association;
use Illuminate\Http\Request;
use App\Models\Opportunite;
use Illuminate\Support\Facades\Validator;
use App\Services\PostulationService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;




class OpportunitesController extends Controller
{
    protected $postulationService;

    public function __construct(PostulationService $postulationService)
    {
        $this->postulationService = $postulationService;
    }

    public function addOpportunite(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titre' => 'required|string',
            'description' => 'required|string',
            'date' => 'required|date',
            'derniere_date_postule' => 'required|date',
            'ville' => 'required|string',
            'adresse' => 'required|string',
            'association_id' => 'required|integer',
            'categorie_id' => 'required|integer',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'nb_benevole' => 'required|integer',
            'duree' => 'required|string',
            'engagement_requis' => 'required|string',
            'missions_principales' => 'required|string',
            'competences' => 'required|string',
            'pays' => 'required|string',
            'type' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => "Erreur de validation","errors" => $validator->errors()], 422);
        }
    
        try {
            $user = Auth::user()->id;
            $association =  Association::where('user_id',$user)->first();

            $folderName = Str::slug($association->nom_association . '_' . $association->numero_rna_association, '_');
            $imagePath = $request->file('image')->store("associations/{$folderName}/opportunites", 'public');
            $imageUrl = asset('storage/' . $imagePath);

            $opportunite = Opportunite::create([
                'titre' => $request->titre, 
                'description' => $request->description,
                'date' => $request->date,
                'derniere_date_postule' => $request->derniere_date_postule,
                'ville' => $request->ville,
                'adresse' => $request->adresse,
                'association_id' => $request->association_id,
                'categorie_id' => $request->categorie_id,
                'image' => $imageUrl,
                'nb_benevole' => $request->nb_benevole,
                'duree' => $request->duree,
                'engagement_requis' => $request->engagement_requis,
                'missions_principales' => $request->missions_principales,
                'competences' => $request->competences,
                'pays' => $request->pays,
                'type' => $request->type,
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'nb_benevole' => 'sometimes|integer',
            'duree' => 'sometimes|string',
            'engagement_requis' => 'sometimes|string',
            'missions_principales' => 'nullable|string',
            'competences' => 'nullable|string',
            'pays' => 'nullable|string',
            'type' => 'nullable|string',
        ]);

        try {
            $opportunite = Opportunite::findOrFail($id);

            if ($request->hasFile('image')) {
                $association = Auth::user();
                $folderName = Str::slug($association->nom_association . '_' . $association->numero_rna_association, '_');
                $imagePath = $request->file('image')->store("associations/{$folderName}/opportunites", 'public');
                
                $opportunite->image = $imagePath;
                $opportunite->save(); 
            }
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

    public function getAllOpportunite(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 9);
            $opportunites = Opportunite::withCount('postules')->orderByDesc('created_at')->paginate($perPage);
    
            return response()->json($opportunites, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la récupération de l\'opportunite','error' => $e->getMessage()], 500);
        }
    }

    public function getOpportuniteById($opportunite_id)
    {
        try {
            $opportunite = Opportunite::with('association')->withCount('postules')->find($opportunite_id);


            if (!$opportunite) {
                return response()->json(['message' => 'Opportunite non trouvé.'], 404);
            }

            $status = $this->postulationService->getPostulationStatus($opportunite_id);

            return response()->json(['opportunite' => $opportunite , 'status'=>$status], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la récupération de l\'opportunite','error' => $e->getMessage()], 500);
        }
    }

    public function getTop3Opportunite()
    {
        try {
            $topOpportunites = Opportunite::withCount('postules')->orderByDesc('postules_count')->take(3)->get();
            
            return response()->json(['top_opportunites' => $topOpportunites], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la récupération des top opportunités','error' => $e->getMessage() ], 500);
        }
    }

    public function searchOpportunites(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 9);
            $query = Opportunite::withCount('postules');

            if ($request->has('ville') && $request->ville !== null) {
                $query->where('ville', 'like', '%' . $request->ville . '%');
            }

            if ($request->has('titre') && $request->titre !== null) {
                $query->where('titre', 'like', '%' . $request->titre . '%');
            }

            $opportunites = $query->paginate($perPage);

            return response()->json($opportunites, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la recherche des opportunités','error' => $e->getMessage()], 500);
        }
    }

    public function filterByTypes(Request $request)
    {
        try {
            $types = $request->input('types');
            $perPage = $request->input('per_page', 9);

            $opportunites = Opportunite::withCount('postules')
                ->when($types, function ($query, $types) {
                    return $query->whereIn('type', $types);
                })
                ->paginate($perPage);

            return response()->json($opportunites, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors du filtrage par types.','error' => $e->getMessage()], 500);
        }
    }

    public function getMostPopularOpportunites()
    {
        try {
            $popularOpportunites = Opportunite::withCount('postules')->orderByDesc('postules_count')->paginate(10); 

            return response()->json($popularOpportunites, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la récupération des opportunités populaires.', 'error' => $e->getMessage()], 500);
        }
    }

    public function getRecentOpportunites()
    {
        try {
            $recentOpportunites = Opportunite::withCount('postules')->orderByDesc('created_at')
                ->paginate(10); 

            return response()->json($recentOpportunites, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la récupération des opportunités récentes.', 'error' => $e->getMessage()], 500);
        }
    }

    public function getSimilarOpportunites($id)
    {
        try {
            $opportunity = Opportunite::find($id);

            if (!$opportunity) {
                return response()->json(['message' => 'Opportunité non trouvée.'], 404);
            }   

            $similarOpportunites = Opportunite::where('categorie_id', $opportunity->categorie_id)->where('id', '!=', $id) ->orderByDesc('created_at')->limit(2)->get();

            return response()->json($similarOpportunites, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la récupération des opportunités similaires.', 'error' => $e->getMessage()], 500);
        }
    }



  
}

