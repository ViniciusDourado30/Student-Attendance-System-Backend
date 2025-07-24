<?php

namespace App\Http\Controllers;

use App\Models\Chamada;
use App\Models\Presenca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PresencaController extends Controller
{
    /**
     * Store a newly created resource in storage.
     * Guarda os registos de presença para uma chamada específica.
     */
    public function store(Request $request, Chamada $chamada)
    {
        // 1. Verificação de Segurança: A chamada pertence ao professor logado?
        if ($chamada->user_id !== Auth::id()) {
            return response()->json(['message' => 'Acesso não autorizado a esta chamada.'], 403);
        }

        // 2. Valida os dados recebidos. Esperamos um array de presenças.
        $validatedData = $request->validate([
            'presencas' => 'required|array',
            'presencas.*.aluno_id' => 'required|integer|exists:alunos,id',
            'presencas.*.status' => ['required', Rule::in(['presente', 'ausente', 'justificado'])],
        ]);

        // 3. Itera sobre cada registo de presença enviado
        foreach ($validatedData['presencas'] as $presencaData) {
            // Usamos updateOrCreate para evitar duplicados.
            // Ele procura um registo com a mesma chamada_id e aluno_id.
            // Se encontrar, atualiza o status. Se não, cria um novo.
            Presenca::updateOrCreate(
                [
                    'chamada_id' => $chamada->id,
                    'aluno_id' => $presencaData['aluno_id'],
                ],
                [
                    'status' => $presencaData['status'],
                ]
            );
        }

        // 4. Retorna uma resposta de sucesso
        return response()->json(['message' => 'Presenças registadas com sucesso.'], 201);
    }
}
