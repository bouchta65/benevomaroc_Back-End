<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
Use App\http\Controllers\AuthController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout',[AuthController::class,'logout']);
    Route::put('/benevole', [AuthController::class, 'updateBenevole']);
    Route::put('/association', [AuthController::class, 'updateAssociation']);
});


Route::post('login',[AuthController::class,'login']);
Route::post('/benevole',[AuthController::class,'registerBenevole']);
Route::get('benevolles',[AuthController::class,'getAllBenevoles']);
Route::post('/association',[AuthController::class,'registerAssociation']);
