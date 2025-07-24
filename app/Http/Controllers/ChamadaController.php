<?php

namespace App\Http\Controllers;

use App\Models\Chamada;
use App\Models\Turma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChamadaController extends Controller
{
    public function store(Request $request, Turma $turma)
    {
        
        if ($turma->user_id !== Auth::id()) {
            return response()->json(['message' => 'Acesso não autorizado a esta turma.'], 403);
        }

        
        $validatedData = $request->validate([
            'data' => 'required|date_format:Y-m-d',
        ]);

        
        $chamadaExistente = Chamada::where('turma_id', $turma->id)
                                   ->where('data', $validatedData['data'])
                                   ->first();

        if ($chamadaExistente) {
            return response()->json([
                'message' => 'Já existe uma chamada para esta turma na data especificada.',
                'chamada' => $chamadaExistente
            ], 409); 
        }

        $chamada = Chamada::create([
            'user_id' => Auth::id(),
            'turma_id' => $turma->id,
            'data' => $validatedData['data'],
        ]);

        return response()->json($chamada, 201);
    }
}
