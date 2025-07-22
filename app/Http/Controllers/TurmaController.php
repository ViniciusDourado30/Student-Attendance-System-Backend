<?php

namespace App\Http\Controllers;

use App\Models\Turma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TurmaController extends Controller
{
    public function index()
    {
        $turmas = Turma::where('user_id', Auth::id())->get();
        return response()->json($turmas);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
        ]);

        $validatedData['user_id'] = Auth::id();

        $turma = Turma::create($validatedData);

        return response()->json($turma, 201);
    }

    public function show(Turma $turma)
    {
        if ($turma->user_id !== Auth::id()) {
            return response()->json(['message' => 'Acesso não autorizado'], 403);
        }
        return response()->json($turma);
    }

    public function update(Request $request, Turma $turma)
    {
        if ($turma->user_id !== Auth::id()) {
            return response()->json(['message' => 'Acesso não autorizado'], 403);
        }

        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'subject' => 'sometimes|required|string|max:255',
        ]);

        $turma->update($validatedData);
        return response()->json($turma);
    }

    public function destroy(Turma $turma)
    {
        if ($turma->user_id !== Auth::id()) {
            return response()->json(['message' => 'Acesso não autorizado'], 403);
        }

        $turma->delete();
        return response()->json(null, 204);
    }
}
