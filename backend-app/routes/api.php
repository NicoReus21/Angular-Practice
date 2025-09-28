<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/process', [ProcessController::class, 'process']);
Route::get('/process', [ProcessController::class, 'index']);
Route::get('/process/{id}', [ProcessController::class, 'show']);
Route::put('/process/{id}', [ProcessController::class, 'update']);
Route::delete('/process/{id}', [ProcessController::class, 'destroy']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
