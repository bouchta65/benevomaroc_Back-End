<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Postule;
use App\Models\Benevole;


class postuleController extends Controller
{

    public function hasAlreadyPostulated($event_id){
        $benevole_id = Benevole::where('user_id',Auth::user()->id)->first();
        $postuled = Postule::where('benevole_id',$benevole_id->id)->where('evenement_id',$event_id)->first();
        return $postuled ? true : false;
    }

    public function addPostulation($event_id)
    {
        try {

            if($this->hasAlreadyPostulated($event_id)){
                return response()->json(["message" => "Vous avez déjà postulé pour cet événement"], 400);
            }
            $benevole_id = Benevole::where('user_id',Auth::user()->id)->first();

            $postulation = Postule::create([
                'benevole_id' => $benevole_id->id,
                'evenement_id' => $event_id,
                'etat' => 'en attent', 
                'date' => Carbon::now(), 
            ]);

            return response()->json(["message" => "Postulation ajoutée avec succès", "postulation" => $postulation], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de l\'ajout de la postulation', 'error' => $e->getMessage()], 500);
        }
    }

    public function cancelPostulation($event_id)
    {
        try {
            $benevole_id = Benevole::where('user_id', Auth::user()->id)->first();

            $postulation = Postule::where('benevole_id', $benevole_id->id)->where('evenement_id', $event_id)->first();

            if (!$postulation) {
                return response()->json([
                    'message' => 'Aucune postulation trouvée pour ce bénévole et cet événement.'], 404); 
            }

            $postulation->delete();

            return response()->json(['message' => 'Postulation supprimée avec succès.'], 200); 

        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la suppression de la postulation','error' => $e->getMessage()], 500); 
        }
    }



    
}
