<?php

namespace App\Http\Controllers;

use App\Models\Benevole;
use App\Models\Association;
use App\Models\Postule;
use Illuminate\Http\Request;
use App\Models\Certification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;




class CertificatController extends Controller
{
    public function uploadCertificat(Request $request, $opportunite_id ,$benevole_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'certificat' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);
    
            if ($validator->fails()) {
                return response()->json(["message" => "Erreur de validation","errors" => $validator->errors()], 422);
            }
    
            $user = Benevole::join('users', 'benevoles.user_id', '=', 'users.id')->where('benevoles.id', $benevole_id)->first();
            $folderName = Str::slug($user->cin, '_');
            
            $existingCertificat = Certification::where('benevole_id', $benevole_id)->where('opportunite_id', $opportunite_id)->first();
    
            if ($existingCertificat) {
                if ($existingCertificat->image_path) {
                    $oldImagePath = str_replace(asset('storage/'), '', $existingCertificat->image_path); 
                    Storage::disk('public')->delete($oldImagePath);  
                }
                $existingCertificat->update([
                    'image_path' => asset('storage/' . $request->file('certificat')->store("benevoles/{$folderName}", 'public'))
                ]);
                return response()->json(['message' => 'Certificat mis à jour avec succès.','certificat' => $existingCertificat,
                ], 200);
            } else {
                $imagePath = $request->file('certificat')->store("benevoles/{$folderName}", 'public');

                $imageUrl = asset('storage/' . $imagePath);
                Postule::where('benevole_id', $benevole_id)
                ->where('opportunite_id', $opportunite_id)
                ->update(['certif' => 'Attribués']);
                
                $certificat = Certification::create([
                    'benevole_id'    => $benevole_id,
                    'opportunite_id' => $opportunite_id,
                    'image_path'     => $imageUrl,
                ]);

                

    
                return response()->json(['message' => 'Certificat ajouté avec succès.','certificat' => $certificat,
                ], 201);
            }
    
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de l\'upload du certificat.','error' => $e->getMessage()
            ], 500);
        }
    }

    public function getAllPostulationsByAssociationAccpted()
    {
        try {
            $association = Association::where('user_id', Auth::user()->id)->first();
    
            if (!$association) {
                return response()->json(['message' => 'Association non trouvée.'], 404);
            }
    
            $postulations = Postule::join('opportunites', 'postules.opportunite_id', '=', 'opportunites.id')
                ->join('benevoles', 'postules.benevole_id', '=', 'benevoles.id') 
                ->join('users', 'benevoles.user_id', '=', 'users.id')
                ->leftJoin('certifications', function($join) {
                    $join->on('postules.benevole_id', '=', 'certifications.benevole_id')
                         ->on('postules.opportunite_id', '=', 'certifications.opportunite_id');
                }) 
                ->where('opportunites.association_id', $association->id)
                ->where('etat', 'accepté')
                ->orderBy('postules.created_at', 'desc')
                ->select([
                    'users.prenom',
                    'users.nom',
                    'users.email',
                    'users.image',
                    'benevoles.id',
                    'opportunites.titre',
                    'opportunites.id as opportunite_id',
                    'opportunites.ville',
                    'opportunites.date',
                    'opportunites.duree',
                    'postules.certif',
                    'certifications.image_path as certificat_image',
                ])
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

    public function getAllCertificationsForUser()
    {
        try {
            $userId = Auth::id();
            $benevole = Benevole::where('user_id', $userId)->firstOrFail();
    
            $lastCertifications = Certification::where('benevole_id', $benevole->id)
                ->with('opportunite:id,id,titre') 
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get();
    
            $paginatedCertifications = Certification::where('benevole_id', $benevole->id)
                ->with('opportunite:id,id,titre')
                ->orderBy('created_at', 'desc')
                ->paginate(10);
    
            return response()->json([
                'message' => 'Certificats récupérés avec succès.',
                'last_certifications' => $lastCertifications,
                'all_certifications' => $paginatedCertifications
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la récupération des certificats.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    

    

}
