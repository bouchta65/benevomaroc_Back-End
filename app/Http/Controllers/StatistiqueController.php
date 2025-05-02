<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Postule;
use App\Models\Opportunite;
use App\Models\Association;
use Illuminate\Support\Facades\Auth;
use App\Models\Certification;
use App\Models\Benevole;
use Illuminate\Support\Facades\DB;
use Exception;

class StatistiqueController extends Controller
{


    public function getBenevoleStatistics()
    {
        try {
            $user = Auth::user();
            $benevole = Benevole::where('user_id',$user->id)->first();

            if (!$benevole) {
                return response()->json([
                    'error' => 'Benevole record not found for the current user'
                ], 404);
            }

            $benevoleId = $benevole->id;

            $participatedOpportunities = Postule::where('benevole_id', $benevoleId)
                ->where('etat', 'accepté')
                ->count();

            $certificationsCount = Certification::where('benevole_id', $benevoleId)->count();

            $refusedOpportunities = Postule::where('benevole_id', $benevoleId)
                ->where('etat', 'refusé')
                ->count();

            $pendingOpportunities = Postule::where('benevole_id', $benevoleId)
                ->where('etat', 'en attente')
                ->count();

            $statistics = [
                'participatedOpportunities' => $participatedOpportunities,
                'certificationsCount' => $certificationsCount,
                'refusedOpportunities' => $refusedOpportunities,
                'pendingOpportunities' => $pendingOpportunities,
            ];

            return response()->json($statistics);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while retrieving statistics',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
}