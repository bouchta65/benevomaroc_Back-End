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

    public function updateCategorie(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'sometimes|required|string',
            'description' => 'sometimes|required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => "Erreur de validation", "errors" => $validator->errors()], 422);
        }

        try {
            $categorie = Categorie::findOrFail($id);
            $categorie->update($request->only(['nom', 'description']));

            return response()->json(["message" => "Catégorie mise à jour avec succès", "categorie" => $categorie], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la mise à jour de la catégorie', 'error' => $e->getMessage()], 500);
        }
    }

    public function deleteCategorie($id)
    {
        try {
            $categorie = Categorie::findOrFail($id);
            $categorie->delete();

            return response()->json(["message" => "Catégorie supprimée avec succès"], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la suppression de la catégorie', 'error' => $e->getMessage()], 500);
        }
    }

    public function getCategorie()
    {
        try {
            $categorie = Categorie::Paginate(10);
    
            return response()->json(["categorie" => $categorie], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Catégorie non trouvée',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function getAllCategorie()
    {
        try {
            $categorie = Categorie::All();

            return response()->json(["categorie" => $categorie], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Catégorie non trouvée', 'error' => $e->getMessage()], 404);
        }
    }
    

}
