<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Categorie;
class CategorieController extends Controller
{
    public function addCategorie(Request $request){
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string',
            'description' => 'required|string'
        ]);

        if($validator->fails()){
            return response()->json(["message" => "Erreur de validation","errors" => $validator->errors()], 422);
        }

        try{
            $categorie = Categorie::create($validator->validated());
            return response()->json(["message" => "Categorie créé avec succès", "categorie" => $categorie], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la création de l\'événement', 'error' => $e->getMessage()], 500);
        }


}
}
