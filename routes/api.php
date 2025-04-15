<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
Use App\http\Controllers\AuthController;
use App\Http\Controllers\CategorieController;
Use App\http\Controllers\OpportunitesController;
use App\Http\Controllers\postuleController;
use App\Http\Controllers\ProfileController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout',[AuthController::class,'logout']);
});

Route::middleware(['auth:sanctum', 'role:association'])->group(function () {
    Route::post('/dashboard/opportunite',[OpportunitesController::class,'addOpportunite']);
    Route::get('/dashboard/profile', [ProfileController::class, 'getProfile']);
    Route::put('/dashboard/profile/association/userInfo', [ProfileController::class, 'updateUserInfo']);
    Route::put('/dashboard/profile/association/details', [ProfileController::class, 'updateAssociationDetails']);
    Route::put('/dashboard/profile/association/password', [ProfileController::class, 'updatePassword']);
});

Route::middleware(['auth:sanctum', 'role:association','CheckOpportuniteOwner'])->group(function () {
    Route::put('/dashboard/opportunite/{id}',[OpportunitesController::class,'updateOpportunite']);
    Route::delete('/dashboard/opportunite/{id}',[OpportunitesController::class,'deleteOpportunite']);
    Route::get('/dashboard/opportunite/{id}/postulations',[postuleController::class,'postulationByOpportunite']);
    Route::put('/dashboard/opportunite/{id}/postulations/{benevole_id}', [PostuleController::class, 'changeStatusBnenvole']);
});

Route::middleware(['auth:sanctum', 'role:benevole'])->group(function () {
    Route::post('/opportunite/{id}', [postuleController::class, 'addPostulation']);
    Route::delete('/opportunite/{id}', [postuleController::class, 'cancelPostulation']);
    Route::get('/mespostulations', [postuleController::class, 'benevolePostulation']);
    Route::get('/profile', [ProfileController::class, 'getProfile']);
    Route::put('/profile/benevole/userInfo', [ProfileController::class, 'updateUserInfo']);
    Route::put('/profile/benevole/details', [ProfileController::class, 'updateBenevoleDetails']);
    Route::put('/profile/benevole/password', [ProfileController::class, 'updatePassword']);
    Route::Post('/profile/benevole/reset-password', [ProfileController::class, 'reset']);

});

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::post('/categorie', [CategorieController::class, 'addCategorie']);
    Route::put('/categorie/{id}', [CategorieController::class, 'updateCategorie']);
    Route::delete('/categorie/{id}', [CategorieController::class, 'deleteCategorie']);
    Route::get('/categorie', [CategorieController::class, 'getCategorie']);
    Route::put('/dashboard/profile/admin/userInfo', [ProfileController::class, 'updateUserInfo']);
    Route::put('/dashboard/profile/admin/password', [ProfileController::class, 'updatePassword']);
    
});


Route::get('/opportunites', [OpportunitesController::class, 'getAllOpportunite']);
Route::get('/opportunites/Top', [OpportunitesController::class, 'getTop3Opportunite']);
Route::get('/opportunites/search', [OpportunitesController::class, 'searchOpportunites']);
Route::get('/opportunites/type', [OpportunitesController::class, 'filterByTypes']);
Route::get('/opportunites/populare', [OpportunitesController::class, 'getMostPopularOpportunites']);
Route::get('/opportunites/recent', [OpportunitesController::class, 'getRecentOpportunites']);






Route::post('login',[AuthController::class,'login']);
Route::post('/benevole',[AuthController::class,'registerBenevole']);
Route::post('/association',[AuthController::class,'registerAssociation']);




Route::get('/opportunites/{id}', [OpportunitesController::class, 'getOpportuniteById']);
