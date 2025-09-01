<?php

namespace App\Http\Controllers;

use App\Models\Chamada;
use App\Models\Turma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChamadaController extends Controller
{
    /**
     * Cria uma nova chamada para uma turma específica.
     */
    public function store(Request $request, Turma $turma)
    {
        // Valida se o professor que está a fazer o pedido é o dono da turma.
        if ($turma->user_id !== Auth::id()) {
            return response()->json(['message' => 'Acesso não autorizado a esta turma.'], 403);
        }

        // Valida apenas os dados que o front-end precisa de enviar.
        $validatedData = $request->validate([
            'data' => 'required|date',
        ]);

        // Verifica se já não existe uma chamada para esta turma nesta data.
        $chamadaExistente = Chamada::where('turma_id', $turma->id)
            ->where('data', $validatedData['data'])
            ->first();

        if ($chamadaExistente) {
            return response()->json([
                'message' => 'Já existe uma chamada para esta turma na data especificada.',
                'chamada' => $chamadaExistente
            ], 409); 
        }

        // CRIA A CHAMADA USANDO O ID DO UTILIZADOR AUTENTICADO
        // A função Auth::id() obtém o ID do utilizador a partir do Bearer Token de forma segura.
        // O front-end NÃO envia o user_id, o back-end adiciona-o automaticamente.
        $chamada = Chamada::create([
            'user_id' => Auth::id(), // <-- O ID do responsável é obtido aqui
            'turma_id' => $turma->id,
            'data' => $validatedData['data'],
        ]);

        return response()->json($chamada, 201);
    }
}
