<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Association;
use App\Models\Benevole;
use App\Models\Admin;

class AuthController extends Controller
{
  
    public function registerBenevole(Request $request)
    {
    
        $validator = Validator::make($request->all(), [
            'civilite' => 'required|string',
            'prenom' => 'required|string',
            'nom' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => [
                'required', 'string', 'confirmed',
                Password::min(8)->mixedCase()->letters()->numbers()->symbols()
            ],
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'cin' => 'required|string|unique:users',
            'adresse' => 'required|string',
            'date_naissance' => 'required|date',
            'ville' => 'required|string',
            'telephone_1' => 'required|string',
            'telephone_2' => 'nullable|string',
            'domaines_action' => 'nullable|array',
            'types_mission' => 'nullable|string',
            'disponibilites' => 'nullable|string',
            'missions_preferrees' => 'nullable|array',
            'talents' => 'nullable|string',
            'niveau_etudes' => 'nullable|string',
            'metier' => 'nullable|string',
            'cv' => 'sometimes|nullable|file|mimes:pdf|max:2048',
        ],[
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'cin.unique' => 'Ce numéro de CIN est déjà utilisé.',
        ]);;
    
        if ($validator->fails()) {
            return response()->json(["message" => "Erreur de validation","errors" => $validator->errors()], 422);
        }
    
        try {
            $folderName = Str::slug($request->cin, '_');
    
            $imagePath = $request->file('image')->store("benevoles/{$folderName}", 'public');
            $imageUrl = asset('storage/' . $imagePath);
    
            $cvPath = $request->hasFile('cv') 
                ? $request->file('cv')->store("benevoles/{$folderName}", 'public') 
                : null;
            $cvUrl = $cvPath ? asset('storage/' . $cvPath) : null;
    
            $user = User::create([
                'civilite' => $request->civilite,
                'prenom' => $request->prenom,
                'nom' => $request->nom,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'image' => $imageUrl,
                'cin' => $request->cin,
                'adresse' => $request->adresse,
                'date_naissance' => $request->date_naissance,
                'ville' => $request->ville,
                'telephone_1' => $request->telephone_1,
                'telephone_2' => $request->telephone_2,
                'role' => 'benevole',
            ]);
    
            Benevole::create([
                'user_id' => $user->id,
                'domaines_action' => json_encode($request->domaines_action),
                'types_mission' => $request->types_mission,
                'disponibilites' => $request->disponibilites,
                'missions_preferrees' => json_encode($request->missions_preferrees),
                'talents' => $request->talents,
                'niveau_etudes' => $request->niveau_etudes,
                'metier' => $request->metier,
                'cv' => $cvUrl,
            ]);
    
            return response()->json(["message" => "Bénévole inscrit avec succès", "user" => $user], 201);
    
        } catch (\Exception $e) {
            if (isset($user)) {
                $user->delete();
            }
            return response()->json(["message" => "Erreur d'inscription", "error" => $e->getMessage()], 500);
        }
    }
    
    

    public function registerAssociation(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'civilite' => 'required|string',
            'prenom' => 'required|string',
            'nom' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => [
                'required', 'string', 'confirmed',
                Password::min(8)->mixedCase()->letters()->numbers()->symbols()
            ],
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'cin' => 'required|string|unique:users',
            'adresse' => 'required|string',
            'date_naissance' => 'required|date',
            'ville' => 'required|string',
            'telephone_1' => 'required|string',
            'telephone_2' => 'nullable|string',
            'nom_association' => 'required|string',
            'date_creation' => 'required|date',
            'numero_rna_association' => 'required|string|unique:associations',
            'objet_social' => 'required|string',
            'site_web' => 'nullable|string',
            'facebook' => 'nullable|string',
            'instagram' => 'nullable|string',
            'logo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'carte_nationale' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'status_association' => 'required|file|mimes:pdf|max:2048',
        ],[
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'cin.unique' => 'Ce numéro de CIN est déjà utilisé.',
            'numero_rna_association.unique' => 'Ce numéro RNA est déjà enregistré.',
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => "Erreur de validation","errors" => $validator->errors()], 422);
        }
        
        try {

            $folderName = Str::slug($request->nom_association . '_' . $request->numero_rna_association, '_');

            $imagePath = $request->file('image')->store("associations/{$folderName}", 'public');
            $imageUrl = asset('storage/' . $imagePath);

            $statusPath = $request->file('status_association')->store("associations/{$folderName}", 'public');
            $statusUrl = asset('storage/' . $statusPath);

            $cartePath = $request->file('carte_nationale')->store("associations/{$folderName}", 'public');
            $carteUrl = asset('storage/' . $cartePath);

            $logoPath = $request->file('logo')->store("associations/{$folderName}", 'public');
            $logoUrl = asset('storage/' . $logoPath);

            $user = User::create([
                'civilite' => $request->civilite,
                'prenom' => $request->prenom,
                'nom' => $request->nom,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'image' => $imageUrl,
                'cin' => $request->cin,
                'adresse' => $request->adresse,
                'date_naissance' => $request->date_naissance,
                'ville' => $request->ville,
                'telephone_1' => $request->telephone_1,
                'telephone_2' => $request->telephone_2,
                'role' => 'association',
            ]);
    
            Association::create([
                'user_id' => $user->id,
                'nom_association' => $request->nom_association,
                'date_creation' => $request->date_creation,
                'numero_rna_association' => $request->numero_rna_association,
                'objet_social' => $request->objet_social,
                'site_web' => $request->site_web,
                'instagram' => $request->instagram,
                'facebook' => $request->facebook,
                'logo' => $logoUrl,
                'status_association' => $statusUrl,
                'carte_nationale' => $carteUrl,
            ]);
        
            return response()->json(["message" => "Association inscrit avec succès", "user" => $user], 201);
    
        } catch (\Exception $e) {
            if (isset($user)) {
                $user->delete();
            }
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
                return response()->json(["message" => "Email ou mot de passe incorrect"], 401);
            }
    
            if ($user->role === 'association') {
                $association = Association::where('user_id',$user->id)->first(); 
                if (!$association || $association->statut_dossier !== 'approuvé') {
                    return response()->json(["message" => "Votre compte n’a pas encore été validé."], 403);
                }
            }
    
            $user->tokens()->delete();
            $token = $user->createToken("AuthSanctum", ["*"], now()->addHours(4))->plainTextToken;
    
            return response()->json([
                "message" => "Connexion réussie",
                "token" => $token,
                "user" => $user
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Erreur lors de la connexion",
                "error" => $e->getMessage()
            ], 500);
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

    public function authStatus(Request $request)
    {
        try {
         
            if (Auth::check()) {
                return response()->json([
                    'authenticated' => true,
                    'user' => $request->user() 
                ], 200);
            }

            return response()->json([
                'authenticated' => false,
                'message' => 'User is not authenticated.'
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while checking authentication status.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function changeStatusAssociation(Request $request, $associationId)
    {
        $validator = Validator::make($request->all(), [
            'statut' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $association = Association::findOrFail($associationId);
            $association->statut_dossier = $request->statut;
            $association->save();

            $user = User::findOrFail($association->user_id);
            
            $statusMessage = $request->statut == 'approuvé' 
                ? 'Félicitations ! Votre demande a été acceptée.' 
                : 'Désolé, votre demande a été refusée.';

            $url = url('Benevo-maroc/login');
            Mail::raw("Bonjour {$user->prenom},\n\n{$statusMessage}\n\nVous pouvez vous connecter à votre compte en cliquant sur le lien suivant : {$url}", function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Mise à jour de votre statut - Benevo Maroc');
            });

            return response()->json([
                'message' => "Statut de l'association mis à jour avec succès.",
                'association' => $association
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => "Erreur lors de la mise à jour du statut.",
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function getAllAssociations(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
    
            $associations = Association::orderByRaw("statut_dossier = 'en attente' DESC")
                ->orderBy('date_creation', 'desc')
                ->paginate($perPage);
    
            return response()->json([
                'message' => 'Liste des associations récupérée avec succès.',
                'associations' => $associations
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la récupération des associations.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getAssociationById($associationId)
    {
        try {
            $association = Association::with('user')->findOrFail($associationId);
            return response()->json([
                'message' => 'Association récupérée avec succès.',
                'association' => $association
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la récupération de l\'association.',
                'error' => $e->getMessage()
            ], 500);
        }
    }




    
    









}
