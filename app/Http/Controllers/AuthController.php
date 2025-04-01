<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Association;
use App\Models\Benevole;
use App\Models\Admin;

class AuthController extends Controller
{
  
    public function registerBenevole(Request $request)
    {
        $request->validate([
            'civilite' => 'required|string',
            'prenom' => 'required|string',
            'nom' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8',
            'image' => 'nullable|string',
            'cin' => 'required|string|unique:users',
            'adresse' => 'nullable|string',
            'date_naissance' => 'nullable|date',
            'code_postal' => 'nullable|string',
            'ville' => 'nullable|string',
            'telephone_1' => 'nullable|string',
            'telephone_2' => 'nullable|string',
            'domaines_action' => 'required|string',
            'types_mission' => 'required|string',
            'disponibilites' => 'required|string',
            'missions_preferrees' => 'nullable|string',
            'talents' => 'nullable|string',
            'niveau_etudes' => 'nullable|string',
            'metier' => 'nullable|string',
            'cv' => 'nullable|string',
        ]);
    
        try {
    
            $user = User::create([
                'civilite' => $request->civilite,
                'prenom' => $request->prenom,
                'nom' => $request->nom,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'image' => $request->image,
                'cin' => $request->cin,
                'adresse' => $request->adresse,
                'date_naissance' => $request->date_naissance,
                'code_postal' => $request->code_postal,
                'ville' => $request->ville,
                'telephone_1' => $request->telephone_1,
                'telephone_2' => $request->telephone_2,
                'role' => 'benevole',
            ]);
    
            Benevole::create([
                'user_id' => $user->id,
                'domaines_action' => $request->domaines_action,
                'types_mission' => $request->types_mission,
                'disponibilites' => $request->disponibilites,
                'missions_preferrees' => $request->missions_preferrees,
                'talents' => $request->talents,
                'niveau_etudes' => $request->niveau_etudes,
                'metier' => $request->metier,
                'cv' => $request->cv,
            ]);
    
        
            return response()->json(["message" => "Bénévole inscrit avec succès", "user" => $user], 201);
    
        } catch (\Exception $e) {
            return response()->json(["message" => "Erreur d'inscription", "error" => $e->getMessage()], 500);
        }
    }
    

}
