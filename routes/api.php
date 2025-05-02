<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
Use App\http\Controllers\AuthController;
use App\Http\Controllers\CategorieController;
Use App\http\Controllers\OpportunitesController;
use App\Http\Controllers\postuleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CertificatController;
use App\Http\Controllers\StatistiqueController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout',[AuthController::class,'logout']);
    Route::get('/auth-status', [AuthController::class, 'authStatus']);
});

Route::middleware(['auth:sanctum', 'role:association'])->group(function () {
    Route::post('/dashboard/opportunite',[OpportunitesController::class,'addOpportunite']);
    Route::get('/dashboard/profile', [ProfileController::class, 'getProfile']);
    Route::post('/dashboard/profile/association/userInfo', [ProfileController::class, 'updateUserInfo']);
    Route::post('/dashboard/profile/association/details', [ProfileController::class, 'updateAssociationDetails']);
    Route::put('/dashboard/profile/association/password', [ProfileController::class, 'updatePassword']);
    Route::get('/dashboard/Myopportunites', [OpportunitesController::class, 'getOpportunitesByAssociation']);
    Route::get('/dashboard/lastThreeOpportunites', [OpportunitesController::class, 'getLastThreeOpportunitesActives']);
    Route::get('/dashboard/opportunite/{id}/postulations',[postuleController::class,'postulationByOpportunite']);
    Route::get('/dashboard/opportunite/postulations',[postuleController::class,'getAllPostulationsByAssociation']);
    Route::get('/dashboard/opportunite/benevole/{id}',[ProfileController::class,'getBenevoleData']);
    Route::post('/dashboard/opportunite/{id}/certification/{benevole_id}',[CertificatController::class,'uploadCertificat']);
    Route::get('/dashboard/opportunite/postulations/accepted',[CertificatController::class,'getAllPostulationsByAssociationAccpted']);
    Route::get('/opportunitess/Statistics', [StatistiqueController::class, 'getAssociationStatistics']);

});

Route::middleware(['auth:sanctum', 'role:association','CheckOpportuniteOwner'])->group(function () {
    Route::post('/dashboard/opportunite/{id}',[OpportunitesController::class,'updateOpportunite']);
    Route::delete('/dashboard/opportunite/{id}',[OpportunitesController::class,'deleteOpportunite']);
    Route::put('/dashboard/opportunite/{id}/postulations/{benevole_id}', [PostuleController::class, 'changeStatusBenevole']);
});

Route::middleware(['auth:sanctum', 'role:benevole'])->group(function () {
    Route::post('/benevole/postulation/add/{id}', [postuleController::class, 'addPostulation']);
    Route::delete('/opportunite/{id}', [postuleController::class, 'cancelPostulation']);
    Route::get('/mespostulations', [postuleController::class, 'benevolePostulation']);
    Route::get('/profile', [ProfileController::class, 'getProfile']);
    Route::post('/profile/benevole/userInfo', [ProfileController::class, 'updateUserInfo']);
    Route::post('/profile/benevole/details', [ProfileController::class, 'updateBenevoleDetails']);
    Route::put('/profile/benevole/password', [ProfileController::class, 'updatePassword']);
    Route::Post('/profile/benevole/reset-password', [ProfileController::class, 'reset']);
    Route::get('/profile/benevole/top3Opportunites', [postuleController::class, 'top3Opportunites']);
    Route::get('/benevole/postulation/check/{id', [postuleController::class, 'hasAlreadyPostulated']);
    Route::get('/benevole/Certififctaion', [CertificatController::class, 'getAllCertificationsForUser']);
    Route::get('/benevole/Statistics', [StatistiqueController::class, 'getBenevoleStatistics']);

    

});

Route::middleware(['auth:sanctum', 'role:association'])->group(function () {
    Route::post('/categorie', [CategorieController::class, 'addCategorie']);
    Route::put('/categorie/{id}', [CategorieController::class, 'updateCategorie']);
    Route::delete('/categorie/{id}', [CategorieController::class, 'deleteCategorie']);
    Route::put('/dashboard/profile/admin/userInfo', [ProfileController::class, 'updateUserInfo']);
    Route::put('/dashboard/profile/admin/password', [ProfileController::class, 'updatePassword']);

    
});

Route::get('/opportunites', [OpportunitesController::class, 'getAllOpportunite']);
Route::get('/opportunites/Top', [OpportunitesController::class, 'getTop3Opportunite']);
Route::get('/opportunites/search', [OpportunitesController::class, 'searchOpportunites']);
Route::get('/opportunites/type', [OpportunitesController::class, 'filterByTypes']);
Route::get('/opportunites/populare', [OpportunitesController::class, 'getMostPopularOpportunites']);
Route::get('/opportunites/recent', [OpportunitesController::class, 'getRecentOpportunites']);
Route::get('/opportunites/{id}/similar', [OpportunitesController::class, 'getSimilarOpportunites']);
Route::get('/categorie', [CategorieController::class, 'getCategorie']);






Route::post('login',[AuthController::class,'login']);
Route::post('/benevole',[AuthController::class,'registerBenevole']);
Route::post('/association',[AuthController::class,'registerAssociation']);




Route::get('/opportunites/{id}', [OpportunitesController::class, 'getOpportuniteById']);
