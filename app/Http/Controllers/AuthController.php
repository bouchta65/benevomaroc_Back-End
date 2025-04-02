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
    

    public function registerAssociation(Request $request)
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
            'fonction_occupee' => 'required|string',
            'nom_association' => 'required|string',
            'sigle_association' => 'required|string',
            'numero_rna_association' => 'required|string',
            'objet_social' => 'required|string',
            'site_web' => 'nullable|string',
            'logo' => 'nullable|string',
            'presentation_association' => 'nullable|string',
            'principales_reussites' => 'nullable|string',
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
                'role' => 'association',
            ]);
    
            Association::create([
                'user_id' => $user->id,
                'fonction_occupee' => $request->fonction_occupee,
                'nom_association' => $request->nom_association,
                'sigle_association' => $request->sigle_association,
                'numero_rna_association' => $request->numero_rna_association,
                'objet_social' => $request->objet_social,
                'site_web' => $request->site_web,
                'logo' => $request->logo,
                'presentation_association' => $request->presentation_association,
                'principales_reussites' => $request->principales_reussites,
            ]);
        
            return response()->json(["message" => "Association inscrit avec succès", "user" => $user], 201);
    
        } catch (\Exception $e) {
            return response()->json(["message" => "Erreur d'inscription", "error" => $e->getMessage()], 500);
        }

    
    }



    public function login(Request $request) {
        $validatedData = $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);

        try {
            $user = User::where('email', $validatedData['email'])->first();

            if (!$user || !Hash::check($validatedData['password'], $user->password)) {
                return response()->json(["message" => "Email or password is incorrect"], 401);
            }

            $token = $user->createToken("AuthSanctum", ["*"], now()->addMinutes(10000))->plainTextToken;

            return response()->json(["message" => "User successfully logged in", "token" => $token], 200);
        } catch (\Exception $e) {
            return response()->json(["message" => "Error", "error" => $e->getMessage()], 500);
        }
    }

    public function logout(request $request){
        try {

            $request->user()->currentAccessToken()->delete();
            
            return response()->json(["message "=>"user succesfully loged out "],200);

            
        } catch (\Exception $e) {
            return response()->json(["message"=>"error","error"=>$e->getMessage()],500);
        }
    }
}
