<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TurmaController; // 1. Importe o novo controller

/*
|--------------------------------------------------------------------------
| Rotas de Autenticação (Públicas)
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


/*
|--------------------------------------------------------------------------
| Rotas Protegidas (Exigem Autenticação)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    // Rota para obter o utilizador logado
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Rotas para gerir as turmas
    Route::apiResource('turmas', TurmaController::class);
});
