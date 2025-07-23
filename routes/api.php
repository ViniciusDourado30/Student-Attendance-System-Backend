<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TurmaController;
use App\Http\Controllers\AlunoController; // 1. Importe o AlunoController

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
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Rotas para gerir as turmas
    Route::apiResource('turmas', TurmaController::class);

    // 2. Adicione as rotas para gerir os alunos DENTRO de uma turma
    Route::apiResource('turmas.alunos', AlunoController::class)->only(['index', 'store']);
});
