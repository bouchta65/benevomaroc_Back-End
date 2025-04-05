<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
Use App\http\Controllers\AuthController;
use App\Http\Controllers\CategorieController;
Use App\http\Controllers\EventController;
use App\Http\Controllers\postuleController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout',[AuthController::class,'logout']);
});

Route::middleware(['auth:sanctum', 'role:association'])->group(function () {
    Route::put('/association', [AuthController::class, 'updateAssociation']);
    Route::post('/evenement',[EventController::class,'addEvent']);
    Route::put('/evenement/{id}',[EventController::class,'updateEvent']);
    Route::delete('/evenement/{id}',[EventController::class,'deleteEvent']);
});

Route::middleware(['auth:sanctum', 'role:association','CheckEventOwner'])->group(function () {
    Route::put('/evenement/{id}',[EventController::class,'updateEvent']);
    Route::delete('/evenement/{id}',[EventController::class,'deleteEvent']);
    Route::get('/evenement/{id}/postulations',[postuleController::class,'postulationByEvent']);
    Route::put('/evenement/{id}/postulations/{benevole_id}', [PostuleController::class, 'changeStatusBnenvole']);
});

Route::middleware(['auth:sanctum', 'role:benevole'])->group(function () {
    Route::put('/benevole', [AuthController::class, 'updateBenevole']);
    Route::post('/evenement/{id}', [postuleController::class, 'addPostulation']);
    Route::delete('/evenement/{id}', [postuleController::class, 'cancelPostulation']);
    Route::get('/evenement/{id}', [EventController::class, 'getEventById']);

});

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::post('/categorie', [CategorieController::class, 'addCategorie']);
    Route::put('/categorie/{id}', [CategorieController::class, 'updateCategorie']);
    Route::delete('/categorie/{id}', [CategorieController::class, 'deleteCategorie']);
    Route::get('/categorie', [CategorieController::class, 'getCategorie']);
});


Route::get('/evenemens', [EventController::class, 'getAllEvent']);






Route::post('login',[AuthController::class,'login']);
Route::post('/benevole',[AuthController::class,'registerBenevole']);
Route::post('/association',[AuthController::class,'registerAssociation']);
