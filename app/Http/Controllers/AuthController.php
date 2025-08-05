<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
        'role' => [
            'required',
            'string',
            Rule::in(['Monitor(a)', 'Professor(a)']),
        ],
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 400);
    }

    try {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return response()->json([
            'message' => 'Usuário registrado com sucesso!',
        ], 201);
    } catch (\Illuminate\Database\QueryException $e) {
        if ($e->getCode() === '23000') {
            return response()->json([
                'error' => 'E-mail já está em uso.',
            ], 409); // 409 Conflict
        }

        return response()->json([
            'error' => 'Erro inesperado ao registrar usuário.',
        ], 500);
    }
}


    /**
     * Handle user login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // 1. Valida os dados de entrada (email e senha)
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // 2. Tenta autenticar o usuário
        if (Auth::attempt($credentials)) {
            // 3. Se a autenticação for bem-sucedida...
            $user = Auth::user(); // Pega o usuário autenticado
                    
            // Cria um novo token de acesso para o usuário
            $token = $user->createToken('auth-token')->plainTextToken;

            // Retorna os dados do usuário e o token
            return response()->json([
                'message' => 'Login bem-sucedido!',
                'user' => $user,
                'access_token' => $token,
            ], 200);
        }

        // 4. Se a autenticação falhar...
        return response()->json([
            'message' => 'Credenciais inválidas.'
        ], 401); // 401 Unauthorized é o código HTTP correto para falha de login
    }
}