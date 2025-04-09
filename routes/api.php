<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
Use App\http\Controllers\AuthController;
use App\Http\Controllers\CategorieController;
Use App\http\Controllers\EventController;
use App\Http\Controllers\postuleController;
use App\Http\Controllers\ProfileController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout',[AuthController::class,'logout']);
});

Route::middleware(['auth:sanctum', 'role:association'])->group(function () {
    Route::post('/dashboard/evenement',[EventController::class,'addEvent']);
    Route::get('/dashboard/profile', [ProfileController::class, 'getProfile']);
    Route::put('/dashboard/profile/association/userInfo', [ProfileController::class, 'updateUserInfo']);
    Route::put('/dashboard/profile/association/details', [ProfileController::class, 'updateAssociationDetails']);
    Route::put('/dashboard/profile/association/password', [ProfileController::class, 'updatePassword']);
});

Route::middleware(['auth:sanctum', 'role:association','CheckEventOwner'])->group(function () {
    Route::put('/dashboard/evenement/{id}',[EventController::class,'updateEvent']);
    Route::delete('/dashboard/evenement/{id}',[EventController::class,'deleteEvent']);
    Route::get('/dashboard/evenement/{id}/postulations',[postuleController::class,'postulationByEvent']);
    Route::put('/dashboard/evenement/{id}/postulations/{benevole_id}', [PostuleController::class, 'changeStatusBnenvole']);
});

Route::middleware(['auth:sanctum', 'role:benevole'])->group(function () {
    Route::post('/evenement/{id}', [postuleController::class, 'addPostulation']);
    Route::delete('/evenement/{id}', [postuleController::class, 'cancelPostulation']);
    Route::get('/evenement/{id}', [EventController::class, 'getEventById']);
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


Route::get('/evenemens', [EventController::class, 'getAllEvent']);






Route::post('login',[AuthController::class,'login']);
Route::post('/benevole',[AuthController::class,'registerBenevole']);
Route::post('/association',[AuthController::class,'registerAssociation']);
