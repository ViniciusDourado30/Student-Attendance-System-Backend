<?php

namespace App\Http\Controllers;

use App\Models\Aluno;
use App\Models\Turma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlunoController extends Controller
{
    /**
     * Mostra uma lista de todos os alunos de uma turma específica.
     */
    public function index(Turma $turma)
    {
        if ($turma->user_id !== Auth::id()) {
            return response()->json(['message' => 'Acesso não autorizado a esta turma.'], 403);
        }
        return response()->json($turma->alunos);
    }

    /**
     * Adiciona um novo aluno a uma turma específica.
     */
    public function store(Request $request, Turma $turma)
    {
        if ($turma->user_id !== Auth::id()) {
            return response()->json(['message' => 'Acesso não autorizado para adicionar alunos a esta turma.'], 403);
        }

        // Valida os dados recebidos, esperando por 'name'
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $validatedData['turma_id'] = $turma->id;

        $aluno = Aluno::create($validatedData);

        return response()->json($aluno, 201);
    }
}
