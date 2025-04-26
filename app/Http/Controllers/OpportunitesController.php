<?php

namespace App\Http\Controllers;

use App\Models\Association;
use Illuminate\Http\Request;
use App\Models\Opportunite;
use Illuminate\Support\Facades\Validator;
use App\Services\PostulationService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;





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
                'association_id' => $association->id,
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
        $validator = Validator::make($request->all(), [
            'titre' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'date' => 'sometimes|date',
            'derniere_date_postule' => 'sometimes|date',
            'ville' => 'sometimes|string',
            'adress' => 'sometimes|string',
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
        if ($validator->fails()) {
            return response()->json(["message" => "Erreur de validation", "errors" => $validator->errors()], 422);
        }
        try {
            $opportunite = Opportunite::findOrFail($id);
            $user = Auth::user()->id;
            $association = Association::where('user_id', $user)->first();
                    
            $opportunite->update($request->only([
                'titre', 'description', 'date','duree','engagement_requis',
                'missions_principales','competences','pays','type',
                'derniere_date_postule', 'ville', 'categorie_id', 'nb_benevole'
            ]));
        
            if ($request->hasFile('image')) {
                if ($opportunite->image) {
                    $oldPath = str_replace(asset('storage/'), '', $opportunite->image);
                    $oldPath = ltrim($oldPath, '/');
                    
                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }
        
                $folderName = Str::slug($association->nom_association . '_' . $association->numero_rna_association, '_');
                $imagePath = $request->file('image')->store("associations/{$folderName}/opportunites", 'public');
                $imageUrl = asset('storage/' . $imagePath);
        
                $opportunite->update(['image' => $imageUrl]);

                }
    
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

            if ($request->has('types') && is_array($request->types) && count($request->types) > 0) {
                $query->whereIn('type', $request->types);
            }

            if ($request->has('sort')) {
                switch ($request->sort) {
                    case 'recent':
                        $query->orderBy('created_at', 'desc');
                        break;
                    case 'popular':
                        $query->orderBy('postules_count', 'desc');
                        break;
                }
            }

            $opportunites = $query->paginate($perPage);

            return response()->json($opportunites, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la recherche des opportunités',
                'error' => $e->getMessage()
            ], 500);
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

    public function getOpportunitesByAssociation(Request $request)
    {
        try {
            $association = Association::where('user_id', Auth::id())->first();
            $perPage = $request->input('per_page', 9);
    
            $opportunites = Opportunite::with(['categorie:id,nom']) 
                ->withCount('postules')
                ->where('association_id', $association->id)
                ->orderByDesc('created_at')
                ->paginate($perPage);
    
            return response()->json($opportunites, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la récupération des opportunités de l\'association',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    




  
}

