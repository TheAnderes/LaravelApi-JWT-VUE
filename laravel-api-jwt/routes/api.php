<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;

use App\Http\Controllers\UserController;

Route::apiResource('users', UserController::class);

/*
|--------------------------------------------------------------------------
| Rutas Públicas
|--------------------------------------------------------------------------
*/

// Registro de usuarios
Route::post('/register', [AuthController::class, 'register']);

// Login de usuarios
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Rutas Protegidas (requieren token JWT)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:api')->group(function () {

    // Obtener usuario autenticado
    Route::get('/me', [AuthController::class, 'me']);

    // Logout (invalida el token)
    Route::post('/logout', [AuthController::class, 'logout']);

    // CRUD completo de publicaciones
    Route::apiResource('/posts', PostController::class);
});
