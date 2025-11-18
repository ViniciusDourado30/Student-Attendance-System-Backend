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
    // Rotas de Utilizador
    Route::get('/user', fn (Request $request) => $request->user());
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/validate-token', [AuthController::class, 'validateToken']);

    // Rotas de Turmas
    Route::apiResource('turmas', TurmaController::class);

    // Rota para CRIAR um novo aluno no sistema (sem turma)
    // POST /api/alunos
    Route::post('/alunos', [AlunoController::class, 'store']);

    // Rota para LISTAR TODOS os alunos do sistema
    // GET /api/alunos
    Route::get('/alunos', [AlunoController::class, 'index']);

    // Rota para LISTAR os alunos de UMA TURMA específica
    // GET /api/turmas/{turma}/alunos
    Route::get('/turmas/{turma}/alunos', [TurmaController::class, 'indexByTurma']);

    // Rota para ASSOCIAR um aluno existente a uma turma
    // POST /api/turmas/{turma}/alunos
    Route::post('/turmas/{turma}/alunos/{aluno}', [AlunoController::class, 'assignAlunoToTurma']);

    // Rota para EXCLUIR um aluno
    Route::delete('/alunos/{aluno}', [AlunoController::class, 'destroy']);

    // Rota para ATUALIZAR os dados de um aluno
    Route::put('/alunos/{aluno}', [AlunoController::class, 'update']);

    // --- FIM DA LÓGICA DE ALUNOS ---

    // Rota para criar uma chamada DENTRO de uma turma
    Route::post('/turmas/{turma}/chamadas', [ChamadaController::class, 'store']); 

    // Rota para registar as presenças de uma chamada
    Route::post('/chamadas/{chamada}/presencas', [PresencaController::class, 'store']);

    //rota para excluir uma chamada
    Route::delete('/chamadas/{chamada}', [ChamadaController::class, 'destroy']);

    //rota para update uma chamada
    Route::put('/chamadas/{chamada}', [ChamadaController::class, 'update']);
});
