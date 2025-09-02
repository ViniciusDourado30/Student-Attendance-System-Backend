<?php

namespace App\Http\Controllers;

use App\Models\Aluno;
use App\Models\Turma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlunoController extends Controller
{
    /**
     * NOVO: Lista todos os alunos registados no sistema, independentemente da turma.
     */
    public function index()
    {
        // Apenas para fins de exemplo, estamos a permitir que qualquer professor autenticado veja todos os alunos.
        // Numa aplicação real, poderia haver regras mais específicas.
        return Aluno::all();
    }

    /**
     * NOVO: Cria um novo aluno no sistema, sem o associar a uma turma.
     * Esta é a função principal para registar um aluno.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $aluno = Aluno::create($validatedData);

        return response()->json($aluno, 201);
    }

    /**
     * NOVO: Associa um aluno existente a uma turma específica.
     */
    public function assignToTurma(Request $request, Turma $turma)
    {
        // Verifica se a turma pertence ao professor autenticado.
        if ($turma->user_id !== Auth::id()) {
            return response()->json(['message' => 'Acesso não autorizado a esta turma.'], 403);
        }

        $validatedData = $request->validate([
            'aluno_id' => 'required|exists:alunos,id',
        ]);

        $aluno = Aluno::find($validatedData['aluno_id']);
        $aluno->turma_id = $turma->id;
        $aluno->save();

        return response()->json($aluno);
    }

    /**
     * NOVO: Lista todos os alunos de uma turma específica.
     */
    public function indexByTurma(Turma $turma)
    {
        if ($turma->user_id !== Auth::id()) {
            return response()->json(['message' => 'Acesso não autorizado a esta turma.'], 403);
        }
        return response()->json($turma->alunos);
    }
}
