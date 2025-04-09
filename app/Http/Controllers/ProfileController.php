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


    





}
