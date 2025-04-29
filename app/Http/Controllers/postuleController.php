<?php

namespace App\Http\Controllers;

use App\Models\Association;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Postule;
use App\Models\Benevole;
use App\Models\Opportunite;


class postuleController extends Controller
{

    public function hasAlreadyPostulated($opportunite_id){
        $benevole_id = Benevole::where('user_id',Auth::user()->id)->first();
        $postuled = Postule::where('benevole_id',$benevole_id->id)->where('opportunite_id',$opportunite_id)->first();
        return $postuled ? true : false;
    }

    public function addPostulation($opportunite_id)
    {
        try {

        $opportunite = Opportunite::where('id', $opportunite_id)->where('status', 'actif')->first();

            if (!$opportunite) {
                return response()->json(["message" => "Opportunite introuvable ou non actif"], 404);
            }

            if($this->hasAlreadyPostulated($opportunite_id)){
                return response()->json(["message" => "Vous avez déjà postulé pour cet opportunite"], 400);
            }
            $benevole_id = Benevole::where('user_id',Auth::user()->id)->first();

            $postulation =  Postule::create([
                'benevole_id' => $benevole_id->id,
                'opportunite_id' => $opportunite_id,
                'etat' => 'en attente', 
                'date' => Carbon::now(), 
            ]);

            return response()->json(["message" => "Postulation ajoutée avec succès", "postulation" => $postulation], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de l\'ajout de la postulation', 'error' => $e->getMessage()], 500);
        }
    }

    public function cancelPostulation($opportunite_id)
    {
        try {
            $benevole_id = Benevole::where('user_id', Auth::user()->id)->first();

            $postulation = Postule::where('benevole_id', $benevole_id->id)->where('opportunite_id', $opportunite_id)->first();

            if (!$postulation) {
                return response()->json([
                    'message' => 'Aucune postulation trouvée pour ce bénévole et cet opportunite.'], 404); 
            }

            $postulation->delete();

            return response()->json(['message' => 'Postulation supprimée avec succès.'], 200); 

        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la suppression de la postulation','error' => $e->getMessage()], 500); 
        }
    }
    public function getAllPostulationsByAssociation() {
        try {
            $association = Association::where('user_id', Auth::user()->id)->first();
    
            $postulations = Postule::join('opportunites', 'postules.opportunite_id', '=', 'opportunites.id')
                ->join('benevoles', 'postules.benevole_id', '=', 'benevoles.id') 
                ->join('users', 'benevoles.user_id', '=', 'users.id') 
                ->where('opportunites.association_id', $association->id)
                ->orderBy('postules.created_at', 'desc')
                ->select('postules.*', 'users.*', 'benevoles.*', 'opportunites.*') 
                ->paginate(10);
    
            return response()->json([
                "message" => "Postulations récupérées avec succès.",
                "postulations" => $postulations
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la récupération des postulations',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    



    public function postulationByOpportunite($opportunite_id){
        try{
            $postulation = Postule::where('opportunite_id',$opportunite_id)->get();
            return response()->json(["message" => "Postulations récupérées avec succès. ", "postulation" => $postulation], 201);

        }catch(\Exception $e){
            return response()->json(['message' => 'Erreur lors de l\'ajout de la postulation', 'error' => $e->getMessage()], 500);
        }
    }

    public function changeStatusBnenvole(Request $request, $opportunite_id,$benevole_id)
    {
        try {
            $request->validate([
                'etat' => 'required|string',
            ]);
    
            $postulation = Postule::where('benevole_id', $benevole_id)->where('opportunite_id',$opportunite_id)->first();

            if (!$postulation) {
                return response()->json(['message' => 'Aucune postulation trouvée pour ce bénévole et cet opportunite.'], 404);  
            }
    
            $postulation->etat = $request->etat;
            $postulation->save();
    
            return response()->json(['message' => 'etat de la postulation mis à jour avec succès.','postulation' => $postulation], 200);
    
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la mise à jour du etat de la postulation.','error' => $e->getMessage()], 500);
        }
    }

    public function benevolePostulation()
    {
        try {
            $benevole = Benevole::where('user_id', Auth::user()->id)->first();
    
            $postulations = Postule::where('benevole_id', $benevole->id)->get();
    
            if ($postulations->isEmpty()) {
                return response()->json(['message' => 'Aucune postulation trouvée'], 404);
            }
    
            return response()->json(['message' => 'Postulations récupérées avec succès', 'postulations' => $postulations], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la récupération des postulations', 'error' => $e->getMessage()], 500);
        }
    }

    public function top3Opportunites()
    {
        try {
            $benevole = Benevole::where('user_id', Auth::id())->first();

            if (!$benevole) {
                return response()->json(['message' => 'Bénévole non trouvé.'], 404);
            }

            $opportunites = Postule::with('opportunite') 
                ->where('benevole_id', $benevole->id)
                ->where('etat', 'accepté')
                ->orderBy('date', 'desc')
                ->take(3)
                ->get()
                ->map(function ($postule) {
                    return $postule->opportunite; 
                });

            return response()->json(['opportunites' => $opportunites], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la récupération des opportunités', 'error' => $e->getMessage()], 500);
        }
    }
  
    
}
