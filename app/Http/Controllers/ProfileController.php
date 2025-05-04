<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Benevole;
use App\Models\Association;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class ProfileController extends Controller
{
    public function getProfile()
    {
        try {
            $user = Auth::user();
            if ($user->role == 'benevole') {
                $benevole = Benevole::join('users', 'benevoles.user_id', '=', 'users.id')->where('users.id', $user->id)->select('benevoles.*', 'users.*') ->first();     
                    return response()->json(["benevole" => $benevole], 200);
            } elseif ($user->role == 'association') {
                $association = Association::join('users','associations.user_id','=','users.id')->where('users.id',$user->id)->select('associations.*','users.*')->first();
                return response()->json(["association" => $association], 200);
            } elseif ($user->role == 'admin') {
                $admin = User::where('id', $user->id)->first();
                return response()->json(["admin" => $admin], 200);
            }
            return response()->json(["message" => "Profil introuvable"], 404);
        } catch (\Exception $e) {
            return response()->json(["message" => "Erreur", "error" => $e->getMessage()], 500);
        }
    }

    public function updateUserInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'prenom' => 'sometimes|string',
            'nom' => 'sometimes|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'adresse' => 'nullable|string',
            'date_naissance' => 'nullable|date',
            'ville' => 'nullable|string',
            'telephone_1' => 'nullable|string',
            'telephone_2' => 'nullable|string',
        ]);
    
        if ($validator->fails()) {
            return response()->json(["message" => "Erreur de validation", "errors" => $validator->errors()], 422);
        }
    
        $id = Auth::user()->id;
        $user = User::findOrFail($id);
    
        $userData = $request->only([
            'prenom', 'nom', 'adresse', 'date_naissance', 'ville', 'telephone_1', 'telephone_2'
        ]);
    
        $user->update($userData);
    
        if ($request->hasFile('image')) {
            try {
                if ($user->image) {
                    $path = str_replace(asset('storage/'), '', $user->image);
                    $path = ltrim($path, '/');
                    
                    if (Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->delete($path);
                    }
                }
                
                $folderName = '';
                $imagePath = '';
                if ($user->role === 'benevole') {
                    $folderName = Str::slug($user->cin, '_');
                    $folderPath = "benevoles/{$folderName}";
                    if (!Storage::disk('public')->exists($folderPath)) {
                        Storage::disk('public')->makeDirectory($folderPath);
                    }
                    
                    $imagePath = $request->file('image')->store($folderPath, 'public');
                    
                } elseif (Association::where('user_id', $user->id)->exists()) {
                    $association = Association::where('user_id', $user->id)->first();
                    if (!$association || empty($association->nom_association) || empty($association->numero_rna_association)) {
                        return response()->json([
                            "message" => "Erreur: Informations d'association manquantes", 
                            "errors" => ["association" => ["Informations d'association requises."]]
                        ], 422);
                    }
                    
                    $folderName = Str::slug($association->nom_association . '_' . $association->numero_rna_association, '_');
                    $folderPath = "associations/{$folderName}";
                    if (!Storage::disk('public')->exists($folderPath)) {
                        Storage::disk('public')->makeDirectory($folderPath);
                    }
                    
                    $imagePath = $request->file('image')->store($folderPath, 'public');
                    
                } elseif ($user->role === 'admin') {
                    if (empty($user->cin)) {
                        return response()->json([
                            "message" => "Erreur: CIN manquant", 
                            "errors" => ["cin" => ["CIN requis pour télécharger une image."]]
                        ], 422);
                    }
                    
                    $folderPath = "admins/bouchta_mohamed";
                    if (!Storage::disk('public')->exists($folderPath)) {
                        Storage::disk('public')->makeDirectory($folderPath);
                    }
                    
                    $imagePath = $request->file('image')->store($folderPath, 'public');
                } else {
                    $folderName = Str::slug($user->id . '_' . ($user->cin ?? ''), '_');
                    $folderPath = "users/{$folderName}";
                    if (!Storage::disk('public')->exists($folderPath)) {
                        Storage::disk('public')->makeDirectory($folderPath);
                    }
                    
                    $imagePath = $request->file('image')->store($folderPath, 'public');
                }
                
                if ($imagePath) {
                    $imageUrl = asset('storage/' . $imagePath);
                    $user->update(['image' => $imageUrl]);
                } 
                return response()->json(["message" => "Profil mis à jour avec succès", "user" => $user], 200);
    
            } catch (\Exception $e) {
                return response()->json([
                    "message" => "Erreur lors du téléchargement de l'image", 
                    "error" => $e->getMessage()
                ], 500);
            }
        }
        return response()->json(["message" => "Profil mis à jour avec succès", "user" => $user], 200);
    }


    public function updateBenevoleDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'domaines_action' => 'sometimes|string',
            'types_mission' => 'sometimes|string',
            'disponibilites' => 'sometimes|string',
            'missions_preferrees' => 'nullable|string',
            'talents' => 'nullable|string',
            'niveau_etudes' => 'nullable|string',
            'metier' => 'nullable|string',
            'cv' => 'nullable|file|mimes:pdf|max:2048',
        ]);
    
        if ($validator->fails()) {
            return response()->json(["message" => "Erreur de validation", "errors" => $validator->errors()], 422);
        }
    
        $user = Auth::user();
        $benevole = Benevole::where('user_id', $user->id)->firstOrFail();
    
        $benevole->update($request->only([
            'domaines_action', 'types_mission', 'disponibilites',
            'missions_preferrees', 'talents', 'niveau_etudes', 'metier'
        ]));
    
        if ($request->hasFile('cv')) {
            if ($benevole->cv) {
                $oldPath = str_replace(asset('storage/'), '', $benevole->cv);
                $oldPath = ltrim($oldPath, '/');
                
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
    
            $folderName = Str::slug($user->cin, '_');
            $cvPath = $request->file('cv')->store("benevoles/{$folderName}", 'public');
            $cvUrl = asset('storage/' . $cvPath);
    
            $benevole->update(['cv' => $cvUrl]);
        }
    
        return response()->json([
            "message" => "Informations bénévoles mises à jour avec succès", 
            "benevole" => $benevole
        ], 200);
    }

    public function updateAssociationDetails(Request $request)
    {
        try {
            $data = $request->validate([
                'objet_social' => 'required|string',
                'site_web' => 'nullable|string',
                'facebook' => 'nullable|string',
                'instagram' => 'nullable|string',
            ]);
    
            $user = Auth::user();
            $association = Association::where('user_id', $user->id)->firstOrFail();
            $association->update($data);
    
            return response()->json(['message' => 'Mise à jour réussie']);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la mise à jour', 'details' => $e->getMessage()], 500);
        }
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => ['required','string','confirmed',
            Password::min(8)->mixedCase()->letters()->numbers()->symbols()
            ],
        ]);
    
        if ($validator->fails()) {
            return response()->json(["message" => "Erreur de validation", "errors" => $validator->errors()], 422);
        }
    
        $id = Auth::user()->id;
        $user = User::findOrFail($id);
    
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(["message" => "L'ancien mot de passe est incorrect"], 403);
        }
        if (Hash::check($request->password, $user->password)) {
            return response()->json(["message" => "Le nouveau mot de passe ne peut pas être identique à l'ancien"], 422);
        }
    
        $user->update([
            'password' => Hash::make($request->password),
        ]);
    
        return response()->json(["message" => "Mot de passe mis à jour avec succès"], 200);
    }
  
    public function reset()
    {
        try {
            $id = Auth::user()->id;
            $user = User::findOrFail($id);
    
            $newPassword = Str::random(12);
    
            $user->password = Hash::make($newPassword);
            $user->save();
    
            Mail::raw("Bonjour {$user->prenom},\n\nVoici votre nouveau mot de passe : {$newPassword}\n\nPensez à le changer après connexion.", function ($message) use ($user) {
                $message->to($user->email) 
                        ->subject('Nouveau mot de passe - Votre site');
            });
    
            return response()->json(['message' => 'Un nouveau mot de passe a été envoyé à votre email.'], 200);
    
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur de réinitialisation du mot de passe', 'error' => $e->getMessage()], 500);
        }
    }

    public function getBenevoleData($user_id)
    {
        try {
        
            $benevole = Benevole::join('users', 'benevoles.user_id', '=', 'users.id')->where('users.id', $user_id)
                ->select('users.prenom','users.nom','users.email','users.image','users.ville','users.telephone_1','benevoles.domaines_action','benevoles.missions_preferrees','benevoles.talents','benevoles.niveau_etudes','benevoles.metier','benevoles.cv')->first();

            if (!$benevole) {
                return response()->json(["message" => "Bénévole introuvable"], 404);
            }

            return response()->json(["benevole" => $benevole], 200);
        } catch (\Exception $e) {
            return response()->json(["message" => "Erreur", "error" => $e->getMessage()], 500);
        }
    }


    
    





}
