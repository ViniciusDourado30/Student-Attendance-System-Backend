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
    public function assignAlunoToTurma(Request $request, Turma $turma, Aluno $aluno)
    {
        // 1. (Opcional, mas recomendado) Verificar se o usuário autenticado tem permissão para modificar esta turma
        // 2. Independente de já ter outra turma, sobrescreve para a nova turma solicitada
        $aluno->turma_id = $turma->id;
        $aluno->save();

        // 3. Retornar o aluno atualizado (agora com o turma_id atualizado)
        return response()->json($aluno->load('turma')); // Carrega a relação para mostrar no frontend
    }

    /**
     * Mostra uma lista de todos os alunos de uma turma específica.
     */
    
    public function index(Request $request)
    {
        $alunos = Aluno::with('turma')->get();
        return response()->json($alunos);
    }

    public function update(Request $request, Aluno $aluno)
    {
        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'id' => 'sometimes|required|integer|exists:alunos,id',
        ]);

        $aluno->update($validatedData);
        return response()->json($aluno);
    }

    public function destroy(Aluno $aluno)
    {
        $aluno->delete();
        return response()->json(['message' => 'Aluno removido com sucesso.'], 204);
    }
}

