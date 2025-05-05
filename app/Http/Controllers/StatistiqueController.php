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

    public function getAssociationStatistics()
    {
        try {
            $user = Auth::user()->id;
            $association = Association::where('user_id', $user)->first();

            if (!$association) {
                return response()->json([
                    'error' => 'Association not found for the current user'
                ], 404);
            }

            $associationId = $association->id;

            $totalCandidatures = Postule::whereHas('opportunite', function ($query) use ($associationId) {
                $query->where('association_id', $associationId);
            })->count();

            $acceptedCandidatures = Postule::whereHas('opportunite', function ($query) use ($associationId) {
                $query->where('association_id', $associationId);
            })->where('etat', 'accepté')->count();

            $pendingCandidatures = Postule::whereHas('opportunite', function ($query) use ($associationId) {
                $query->where('association_id', $associationId);
            })->where('etat', 'en attente')->count();

            $refusedCandidatures = Postule::whereHas('opportunite', function ($query) use ($associationId) {
                $query->where('association_id', $associationId);
            })->where('etat', 'refusé')->count();

            $activeOpportunites = Opportunite::where('association_id', $associationId)->where('status', 'actif')->count();
            $pendingOpportunites = Opportunite::where('association_id', $associationId)->where('status', 'en attente')->count();
            $closedOpportunites = Opportunite::where('association_id', $associationId)->where('status', 'fermé')->count();

            $totalCertificationsGiven = Certification::whereHas('opportunite', function ($query) use ($associationId) {
                $query->where('association_id', $associationId);
            })->count();

            $last4Benevoles = Postule::whereHas('opportunite', function ($query) use ($associationId) {
                $query->where('association_id', $associationId);
            })->orderBy('date', 'desc')
                ->take(3)
                ->with(['benevole.user', 'opportunite']) 
                ->get()
                ->map(function ($postule) {
                    return [
                        'prenom' => $postule->benevole->user->prenom,
                        'nom' => $postule->benevole->user->nom,
                        'image' => $postule->benevole->user->image,
                        'email' => $postule->benevole->user->email,
                        'telephone_1' => $postule->benevole->user->telephone_1,
                        'opportunite_nom' => $postule->opportunite->titre,
                        'etat_postule' => $postule->etat,
                        'date_postule' => $postule->date,
                    ];
                });

            $topBenevoles = Benevole::select(
                'benevoles.id',
                'users.prenom',
                'users.nom',
                'users.image',
                DB::raw('COUNT(postules.id) as postules_count')
            )
                ->join('users', 'benevoles.user_id', '=', 'users.id')
                ->join('postules', 'benevoles.id', '=', 'postules.benevole_id')
                ->join('opportunites', 'postules.opportunite_id', '=', 'opportunites.id')
                ->where('opportunites.association_id', $associationId)
                ->groupBy('benevoles.id', 'users.prenom', 'users.nom')
                ->orderByDesc('postules_count')
                ->take(5)
                ->get();

            $mostPopularOpportunites = Opportunite::select('opportunites.id', 'opportunites.titre', 'opportunites.image', 'opportunites.date' , 'opportunites.ville',  DB::raw('COUNT(postules.id) as postules_count'))
                ->join('postules', 'opportunites.id', '=', 'postules.opportunite_id')
                ->where('opportunites.association_id', $associationId)
                ->groupBy('opportunites.id', 'opportunites.titre')
                ->orderByDesc('postules_count')
                ->take(3)
                ->get();

            $monthlyStats = Postule::select(
                DB::raw('MONTH(date) as month'),
                DB::raw('COUNT(*) as total_postules'),
                DB::raw('SUM(CASE WHEN etat = "accepté" THEN 1 ELSE 0 END) as accepted_postules'),
                DB::raw('SUM(CASE WHEN etat = "refusé" THEN 1 ELSE 0 END) as refused_postules')
            )->whereHas('opportunite', function ($query) use ($associationId) {
                $query->where('association_id', $associationId);
            })->groupBy(DB::raw('MONTH(date)'))
                ->get();

            $successRate = $totalCandidatures > 0 ? ($acceptedCandidatures / $totalCandidatures) * 100 : 0;

            $pendingTasks = Opportunite::where('association_id', $associationId)
                ->where('status', 'en attente')
                ->count();

            $statistics = [
                'postuleStatistics' => [
                    'totalCandidatures' => $totalCandidatures,
                    'acceptedCandidatures' => $acceptedCandidatures,
                    'pendingCandidatures' => $pendingCandidatures,
                    'refusedCandidatures' => $refusedCandidatures,
                ],
                'opportuniteStatistics' => [
                    'activeOpportunites' => $activeOpportunites,
                    'pendingOpportunites' => $pendingOpportunites,
                    'closedOpportunites' => $closedOpportunites,
                ],
                'certificationStatistics' => [
                    'totalCertificationsGiven' => $totalCertificationsGiven,
                ],
                'last4Benevoles' => $last4Benevoles,
                'additionalStatistics' => [
                    'topBenevoles' => $topBenevoles,
                    'mostPopularOpportunites' => $mostPopularOpportunites,
                    'monthlyStats' => $monthlyStats,
                    'successRate' => $successRate,
                    'pendingTasks' => $pendingTasks,
                ],
            ];

            return response()->json($statistics);

        } catch (Exception $e) {
            return response()->json([
                'error' => 'An error occurred while retrieving statistics',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function getAdminStatistics()
    {
        try {
            $totalAssociations = Association::count();
            $acceptedAssociations = Association::where('statut_dossier', 'actif')->count();
            $pendingAssociations = Association::where('statut_dossier', 'en attente')->count();
            $refusedAssociations = Association::where('statut_dossier', 'refusé')->count();

            $activeOpportunites = Opportunite::where('status', 'actif')->count();
            $pendingOpportunites = Opportunite::where('status', 'en attente')->count();
            $closedOpportunites = Opportunite::where('status', 'fermé')->count();

            $totalBenevoles = Benevole::count();

            $last4Benevoles = Postule::orderBy('date', 'desc')
                ->take(4)
                ->with(['benevole.user', 'opportunite'])
                ->get()
                ->map(function ($postule) {
                    return [
                        'prenom' => $postule->benevole->user->prenom,
                        'nom' => $postule->benevole->user->nom,
                        'image' => $postule->benevole->user->image,
                        'email' => $postule->benevole->user->email,
                        'telephone_1' => $postule->benevole->user->telephone_1,
                        'opportunite_nom' => $postule->opportunite->titre,
                        'etat_postule' => $postule->etat,
                        'date_postule' => $postule->date,
                    ];
                });

            $topBenevoles = Benevole::select(
                'benevoles.id',
                'users.prenom',
                'users.nom',
                'users.image',
                DB::raw('COUNT(postules.id) as postules_count')
            )
                ->join('users', 'benevoles.user_id', '=', 'users.id')
                ->join('postules', 'benevoles.id', '=', 'postules.benevole_id')
                ->groupBy('benevoles.id', 'users.prenom', 'users.nom', 'users.image')
                ->orderByDesc('postules_count')
                ->take(5)
                ->get();

            $mostPopularOpportunites = Opportunite::select('opportunites.id', 'opportunites.titre', 'opportunites.image', 'opportunites.date', 'opportunites.ville', DB::raw('COUNT(postules.id) as postules_count'))
                ->join('postules', 'opportunites.id', '=', 'postules.opportunite_id')
                ->groupBy('opportunites.id', 'opportunites.titre', 'opportunites.image', 'opportunites.date', 'opportunites.ville')
                ->orderByDesc('postules_count')
                ->take(3)
                ->get();

            $successRate = $totalAssociations > 0 ? ($acceptedAssociations / $totalAssociations) * 100 : 0;

            $pendingTasks = Opportunite::where('status', 'en attente')->count();

            $totalPostulation = Postule::count();

            $statistics = [
                'associationStatistics' => [
                    'totalAssociations' => $totalAssociations,
                    'acceptedAssociations' => $acceptedAssociations,
                    'pendingAssociations' => $pendingAssociations,
                    'refusedAssociations' => $refusedAssociations,
                ],
                'opportuniteStatistics' => [
                    'activeOpportunites' => $activeOpportunites,
                    'pendingOpportunites' => $pendingOpportunites,
                    'closedOpportunites' => $closedOpportunites,
                ],
                'benevolesStatistics' => [
                    'totalBenevoles' => $totalBenevoles,
                    'totalPostules' => $totalPostulation,
                ],
                'last4Benevoles' => $last4Benevoles,
                'additionalStatistics' => [
                    'topBenevoles' => $topBenevoles,
                    'mostPopularOpportunites' => $mostPopularOpportunites,
                    'successRate' => $successRate,
                    'pendingTasks' => $pendingTasks,
                ],
            ];

            return response()->json($statistics);

        } catch (Exception $e) {
            return response()->json([
                'error' => 'An error occurred while retrieving statistics',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

}