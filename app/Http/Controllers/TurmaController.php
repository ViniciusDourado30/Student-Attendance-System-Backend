<?php

namespace App\Http\Controllers;

use App\Models\Turma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TurmaController extends Controller
{
    /**
     * Mostra uma lista das turmas que pertencem ao utilizador autenticado.
     */
    public function index()
    {
        return Auth::user()->turmas;
    }

    /**
     * Cria uma nova turma e associa-a automaticamente ao utilizador autenticado.
     */
    public function store(Request $request)
    {
        // 1. Validar os dados que vêm do front-end (apenas nome e matéria)
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
        ]);

        // 2. A MÁGICA ACONTECE AQUI:
        // Nós adicionamos o ID do utilizador autenticado aos dados validados.
        // O front-end NÃO precisa de enviar o user_id. Nós obtemo-lo de forma segura no back-end.
        $validatedData['user_id'] = Auth::id();

        // 3. Criamos a turma com os dados completos (nome, matéria e o user_id seguro).
        $turma = Turma::create($validatedData);

        return response()->json($turma, 201);
    }

    /**
     * Mostra os detalhes de uma turma específica, se pertencer ao utilizador.
     */
    public function show(Turma $turma)
    {
        if ($turma->user_id !== Auth::id()) {
            return response()->json(['message' => 'Acesso não autorizado.'], 403);
        }
        return response()->json($turma);
    }

    /**
     * Atualiza uma turma existente, se pertencer ao utilizador.
     */
    public function update(Request $request, Turma $turma)
    {
        if ($turma->user_id !== Auth::id()) {
            return response()->json(['message' => 'Acesso não autorizado.'], 403);
        }

        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'subject' => 'sometimes|required|string|max:255',
        ]);

        $turma->update($validatedData);

        return response()->json($turma);
    }

    /**
     * Apaga uma turma, se pertencer ao utilizador.
     */
    public function destroy(Turma $turma)
    {
        if ($turma->user_id !== Auth::id()) {
            return response()->json(['message' => 'Acesso não autorizado.'], 403);
        }

        $turma->delete();

        return response()->json(['message' => 'Turma apagada com sucesso.']);
    }
}
