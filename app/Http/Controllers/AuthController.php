<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    /**
     * Handle user registration.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        // 1. Validação dos dados recebidos da requisição
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed', // 'confirmed' busca por um campo 'password_confirmation'
            'role' => [
                'required',
                'string',
                Rule::in(['Monitor(a)', 'Professor(a)']), // Garante que a função seja uma das duas opções
            ],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400); // Retorna os erros de validação
        }

        // 2. Criação do usuário no banco de dados
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Criptografa a senha antes de salvar
            'role' => $request->role,
        ]);

        // 3. Retorno de uma resposta de sucesso
        return response()->json([
            'message' => 'Usuário registrado com sucesso!',
            'user' => $user
        ], 201);
    }
}                                           