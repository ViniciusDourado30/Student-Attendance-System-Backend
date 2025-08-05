<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TurmaController;
use App\Http\Controllers\AlunoController;
use App\Http\Controllers\ChamadaController;
use App\Http\Controllers\PresencaController;
use App\Http\Controllers\PasswordResetController;

/*
|--------------------------------------------------------------------------
| Rotas Públicas
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [PasswordResetController::class, 'sendCode']);
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);


    
/*
|--------------------------------------------------------------------------
| Rotas Protegidas (Exigem Autenticação)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Nova rota para validar o token
    Route::get('/validate-token', [AuthController::class, 'validateToken']);

    // Rotas para gerir as turmas
    Route::apiResource('turmas', TurmaController::class);

    // Rotas para gerir os alunos DENTRO de uma turma
    Route::apiResource('turmas.alunos', AlunoController::class)->only(['index', 'store']);

    // Rota para criar uma chamada DENTRO de uma turma
    Route::post('/turmas/{turma}/chamadas', [ChamadaController::class, 'store']);

    // Rota para registar as presenças de uma chamada
    Route::post('/chamadas/{chamada}/presencas', [PresencaController::class, 'store']);
});
