<?php

namespace App\Http\Controllers;

use App\Models\Aluno;
use App\Models\Turma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlunoController extends Controller
{
    /**
     * Cria um novo aluno no sistema, sem o associar a nenhuma turma.
     * O 'turma_id' será automaticamente definido como null.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $request->turma_id = null;

        // Cria o aluno apenas com o nome. O turma_id será null por defeito,
        // conforme definido na migração da base de dados.
        $aluno = Aluno::create($validatedData);

        return response()->json($aluno, 201);
    }

    /**
     * Associa um aluno existente a uma turma específica.
     */
    public function assignToTurma(Request $request, Turma $turma)
    {
        // Garante que o professor só pode adicionar alunos às suas próprias turmas.
        if ($turma->user_id !== Auth::id()) {
            return response()->json(['message' => 'Acesso não autorizado para adicionar alunos a esta turma.'], 403);
        }

        $validatedData = $request->validate([
            'aluno_id' => 'required|exists:alunos,id',
        ]);

        $aluno = Aluno::find($validatedData['aluno_id']);

        // Opcional: Verifica se o aluno já não está noutra turma para evitar conflitos.
        if ($aluno->turma_id) {
            return response()->json(['message' => 'Este aluno já está associado a outra turma.'], 409); // 409 Conflict
        }

        $aluno->turma_id = $turma->id;
        $aluno->save();

        return response()->json($aluno);
    }

    /**
     * Mostra uma lista de todos os alunos de uma turma específica.
     */
    public function indexByTurma(Turma $turma)
    {
        if ($turma->user_id !== Auth::id()) {
            return response()->json(['message' => 'Acesso não autorizado a esta turma.'], 403);
        }
        return response()->json($turma->alunos);
    }

    public function index(Request $request)
    {
        $alunos = Aluno::all();
        return response()->json($alunos);
    }
}


    