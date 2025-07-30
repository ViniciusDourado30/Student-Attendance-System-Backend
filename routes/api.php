<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TurmaController;
use App\Http\Controllers\AlunoController;
use App\Http\Controllers\ChamadaController;
use App\Http\Controllers\PresencaController; 

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

    // Rotas para gerir os alunos DENTRO de uma turma
    Route::apiResource('turmas.alunos', AlunoController::class)->only(['index', 'store']);

    // Rota para criar uma chamada DENTRO de uma turma
    Route::post('/turmas/{turma}/chamadas', [ChamadaController::class, 'store']);

    // 2. Adicione a rota para registar as presenças de uma chamada
    Route::post('/chamadas/{chamada}/presencas', [PresencaController::class, 'store']);
});
