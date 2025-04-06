<?php
namespace App\Services;

use App\Models\Benevole;
use App\Models\Postule;
use Illuminate\Support\Facades\Auth;

class PostulationService
{
    public function getPostulationStatus($event_id)
    {
        try {
            $benevole_id = Benevole::where('user_id', Auth::user()->id)->first();
    
            $postulation = Postule::where('benevole_id', $benevole_id->id)->where('evenement_id', $event_id)->first();
    
            if (!$postulation) {
                return response()->json(['message' => 'Aucune postulation trouvée pour ce bénévole et cet événement.'], 404);
            }
    
            return response()->json(['etat' => $postulation->etat], 200);
    
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la récupération de l\'état de la postulation', 'error' => $e->getMessage()], 500);
        }
    }
}
