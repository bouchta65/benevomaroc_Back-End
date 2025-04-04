<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
Use App\http\Controllers\AuthController;
use App\Http\Controllers\CategorieController;
Use App\http\Controllers\EventController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout',[AuthController::class,'logout']);
});

Route::middleware(['auth:sanctum', 'role:association'])->group(function () {
    Route::put('/association', [AuthController::class, 'updateAssociation']);
    Route::post('/evenement',[EventController::class,'addEvent']);
    Route::put('/evenement/{id}',[EventController::class,'updateEvent']);
    Route::delete('/evenement/{id}',[EventController::class,'deleteEvent']);
});

Route::middleware(['auth:sanctum', 'role:benevole'])->group(function () {
    Route::put('/benevole', [AuthController::class, 'updateBenevole']);
});

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::post('/categorie', [CategorieController::class, 'addCategorie']);
    Route::put('/categorie/{id}', [CategorieController::class, 'updateCategorie']);
    Route::delete('/categorie/{id}', [CategorieController::class, 'deleteCategorie']);
});






Route::post('login',[AuthController::class,'login']);
Route::post('/benevole',[AuthController::class,'registerBenevole']);
Route::post('/association',[AuthController::class,'registerAssociation']);
