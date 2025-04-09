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
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }

        
            $folderName = '';
            $imagePath = '';
            if ($user->hasRole('benevole')) {
                $folderName = Str::slug($user->cin, '_'); 
                $imagePath = $request->file('image')->store("benevoles/{$folderName}", 'public');

            } elseif ($user->hasRole('association')) {
                $association = Association::where('user_id', $user->id)->first(); 
                $folderName = Str::slug($association->nom_association . '_' . $association->numero_rna_association, '_');
                $imagePath = $request->file('image')->store("associations/{$folderName}", 'public');

            } elseif ($user->hasRole('admin')) {
                $folderName = 'admin_' . Str::slug($user->cin, '_'); 
                $imagePath = $request->file('image')->store("admins/{$folderName}", 'public');
            }

            $user->update(['image' => $imagePath]);
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
                Storage::disk('public')->delete($benevole->cv);
            }

            $folderName = Str::slug($user->cin, '_');
            $cvPath = $request->file('cv')->store("benevoles/{$folderName}", 'public');
            $benevole->update(['cv' => $cvPath]);
        }

        return response()->json(["message" => "Informations bénévoles mises à jour avec succès", "benevole" => $benevole], 200);
    }

    public function updateAssociationDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fonction_occupee' => 'sometimes|string',
            'site_web' => 'nullable|string',
            'nom_association' => 'sometimes|string',
            'sigle_association' => 'sometimes|string',
            'numero_rna_association' => 'sometimes|string',
            'objet_social' => 'sometimes|string',
            'presentation_association' => 'sometimes|string',
            'principales_reussites' => 'sometimes|string',
        ]);
    
        if ($validator->fails()) {
            return response()->json(["message" => "Erreur de validation", "errors" => $validator->errors()], 422);
        }
    
        try {
            $user = Auth::user();
            $association = Association::where('user_id', $user->id)->firstOrFail();
            $association->update($request->only([
                'fonction_occupee', 'nom_association', 'sigle_association',
                'numero_rna_association', 'objet_social', 'site_web',
                'presentation_association', 'principales_reussites'
            ]));
    
            return response()->json(["message" => "Détails de l'association mis à jour avec succès","association" => $association], 200);
        } catch (\Exception $e) {
            return response()->json(["message" => "Erreur de mise à jour", "error" => $e->getMessage()], 500);
        }
    }

    
    
    





}
