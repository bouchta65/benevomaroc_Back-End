<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
Use App\http\Controllers\AuthController;
Use App\http\Controllers\EventController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout',[AuthController::class,'logout']);
});

Route::middleware(['auth:sanctum', 'role:association'])->group(function () {
    Route::put('/association', [AuthController::class, 'updateAssociation']);
    Route::post('/Evenement',[EventController::class,'addEvent']);
    Route::put('/Evenement/{id}',[EventController::class,'updateEvent']);
    Route::delete('/Evenement/{id}',[EventController::class,'deleteEvent']);
});

Route::middleware(['auth:sanctum', 'role:benevole'])->group(function () {
    Route::put('/benevole', [AuthController::class, 'updateBenevole']);
});





Route::post('login',[AuthController::class,'login']);
Route::post('/benevole',[AuthController::class,'registerBenevole']);
Route::post('/association',[AuthController::class,'registerAssociation']);
