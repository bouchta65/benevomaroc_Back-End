<?php

namespace App\Http\Controllers;

use App\Models\Benevole;
use Illuminate\Http\Request;
use App\Models\Certification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;



class CertificatController extends Controller
{
    public function uploadCertificat(Request $request, $benevole_id, $opportunite_id)
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
    
                $certificat = Certification::create([
                    'benevole_id' => $benevole_id,
                    'opportunite_id' => $opportunite_id,
                    'image_path' => $imageUrl,
                ]);
    
                return response()->json(['message' => 'Certificat ajouté avec succès.','certificat' => $certificat,
                ], 201);
            }
    
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de l\'upload du certificat.','error' => $e->getMessage()
            ], 500);
        }
    }
    

}
