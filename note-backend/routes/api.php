<?php

use App\Http\Controllers\API\NoteController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('simple.token.auth');
Route::post('/create-user', [AuthController::class, 'createUser']);

Route::middleware('simple.token.auth')->group(function () {
    Route::apiResource('notes', NoteController::class);
});
