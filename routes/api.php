<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
Use App\http\Controllers\AuthController;
use App\Http\Controllers\CategorieController;
Use App\http\Controllers\OpportunitesController;
Use App\http\Controllers\ContactController;
use App\Http\Controllers\postuleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CertificatController;
use App\Http\Controllers\StatistiqueController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout',[AuthController::class,'logout']);
    Route::get('/auth-status', [AuthController::class, 'authStatus']);
    Route::put('/profile/password', [ProfileController::class, 'updatePassword']);
    Route::Post('/profile/reset-password', [ProfileController::class, 'reset']);
});

Route::middleware(['auth:sanctum', 'role:association'])->group(function () {
    Route::post('/dashboard/opportunite',[OpportunitesController::class,'addOpportunite']);
    Route::get('/dashboard/profile', [ProfileController::class, 'getProfile']);
    Route::post('/dashboard/profile/association/userInfo', [ProfileController::class, 'updateUserInfo']);
    Route::post('/dashboard/profile/association/details', [ProfileController::class, 'updateAssociationDetails']);
    Route::get('/dashboard/Myopportunites', [OpportunitesController::class, 'getOpportunitesByAssociation']);
    Route::get('/dashboard/lastThreeOpportunites', [OpportunitesController::class, 'getLastThreeOpportunitesActives']);
    Route::get('/dashboard/opportunite/{id}/postulations',[postuleController::class,'postulationByOpportunite']);
    Route::get('/dashboard/opportunite/postulations',[postuleController::class,'getAllPostulationsByAssociation']);
    Route::get('/dashboard/opportunite/benevole/{id}',[ProfileController::class,'getBenevoleData']);
    Route::post('/dashboard/opportunite/{id}/certification/{benevole_id}',[CertificatController::class,'uploadCertificat']);
    Route::get('/dashboard/opportunite/postulations/accepted',[CertificatController::class,'getAllPostulationsByAssociationAccpted']);
    Route::get('/dashboard/opportunitess/Statistics', [StatistiqueController::class, 'getAssociationStatistics']);

});

Route::middleware(['auth:sanctum', 'role:association','CheckOpportuniteOwner'])->group(function () {
    Route::post('/dashboard/opportunite/{id}',[OpportunitesController::class,'updateOpportunite']);
    Route::delete('/dashboard/opportunite/{id}',[OpportunitesController::class,'deleteOpportunite']);
    Route::put('/dashboard/opportunite/{id}/postulations/{benevole_id}', [PostuleController::class, 'changeStatusBenevole']);
    Route::get('/dashboard/association/opportunites/{id}', [OpportunitesController::class, 'getOpportuniteAssocById']);

});

Route::middleware(['auth:sanctum', 'role:benevole'])->group(function () {
    Route::post('/benevole/postulation/add/{id}', [postuleController::class, 'addPostulation']);
    Route::delete('/opportunite/{id}', [postuleController::class, 'cancelPostulation']);
    Route::get('/mespostulations', [postuleController::class, 'benevolePostulation']);
    Route::get('/profile', [ProfileController::class, 'getProfile']);
    Route::post('/profile/benevole/userInfo', [ProfileController::class, 'updateUserInfo']);
    Route::post('/profile/benevole/details', [ProfileController::class, 'updateBenevoleDetails']);
    Route::get('/profile/benevole/top3Opportunites', [postuleController::class, 'top3Opportunites']);
    Route::get('/benevole/postulation/check/{id', [postuleController::class, 'hasAlreadyPostulated']);
    Route::get('/benevole/Certififctaion', [CertificatController::class, 'getAllCertificationsForUser']);
    Route::get('/benevole/Statistics', [StatistiqueController::class, 'getBenevoleStatistics']);

    

});

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::post('dashboard/categories', [CategorieController::class, 'addCategorie']);
    Route::put('dashboard/categories/{id}', [CategorieController::class, 'updateCategorie']);
    Route::delete('dashboard/categories/{id}', [CategorieController::class, 'deleteCategorie']);
    Route::get('dashboard/categories', [CategorieController::class, 'getCategorie']);
    Route::post('/dashboard/profile/admin/userInfo', [ProfileController::class, 'updateUserInfo']);
    Route::get('/dashboard/admin/Statistics', [StatistiqueController::class, 'getAdminStatistics']);
    Route::put('/dashboard/admin/associations/{id}/status', [AuthController::class, 'changeStatusAssociation']);
    Route::get('/dashboard/admin/associations', [AuthController::class, 'getAllAssociations']);
    Route::get('/dashboard/admin/associations/details/{id}', [AuthController::class, 'getAssociationById']);
    Route::put('/dashboard/admin/opportunites/{id}/status', [OpportunitesController::class, 'changeOpportunityStatus']);
    Route::get('/dashboard/admin/opportunites/', [OpportunitesController::class, 'getAllOpportunities']);
    Route::get('/dashboard/admin/profile', [ProfileController::class, 'getProfile']);
    Route::get('/dashboard/admin/opportunites/{id}', [OpportunitesController::class, 'getOpportuniteAssocById']);
    Route::get('/dashboard/admin/contacts/all', [ContactController::class, 'getAllMessages']);
    Route::delete('/dashboard/admin/contacts/delete', [ContactController::class, 'deleteAllMessages']);
    
});

Route::get('/opportunites', [OpportunitesController::class, 'getAllOpportunite']);
Route::get('/opportunites/Top', [OpportunitesController::class, 'getTop3Opportunite']);
Route::get('/opportunites/search', [OpportunitesController::class, 'searchOpportunites']);
Route::get('/opportunites/type', [OpportunitesController::class, 'filterByTypes']);
Route::get('/opportunites/populare', [OpportunitesController::class, 'getMostPopularOpportunites']);
Route::get('/opportunites/recent', [OpportunitesController::class, 'getRecentOpportunites']);
Route::get('/opportunites/{id}/similar', [OpportunitesController::class, 'getSimilarOpportunites']);






Route::post('login',[AuthController::class,'login']);
Route::post('/benevole',[AuthController::class,'registerBenevole']);
Route::post('/association',[AuthController::class,'registerAssociation']);



Route::get('/categorie', [CategorieController::class, 'getAllCategorie']);
Route::post('/contact', [ContactController::class, 'sendMessage']);
Route::get('/opportunites/{id}', [OpportunitesController::class, 'getOpportuniteById']);
