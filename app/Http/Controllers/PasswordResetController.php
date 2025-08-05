<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendPasswordResetCode;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    /**
     * Envia um código de redefinição de senha para o e-mail do utilizador.
     */
    public function sendCode(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $user = User::where('email', $request->email)->first();
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Armazena o código na base de dados
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($code), // Armazena o hash do código por segurança
                'created_at' => Carbon::now()
            ]
        );

        // Envia o e-mail
        Mail::to($user)->send(new SendPasswordResetCode($code));

        // ALTERAÇÃO: Adicionado o código à resposta JSON para facilitar os testes.
        return response()->json([
            'message' => 'Código de redefinição enviado com sucesso.',
            'code' => $code
        ]);
    }

    /**
     * Redefine a senha do utilizador usando o código.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'code' => 'required|string|min:6|max:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $tokenData = DB::table('password_reset_tokens')
            ->where('email', $request->email)->first();

        // Verifica se o token existe e não expirou (ex: 10 minutos)
        if (!$tokenData || Carbon::parse($tokenData->created_at)->addMinutes(10)->isPast()) {
            return response()->json(['message' => 'Código inválido ou expirado.'], 400);
        }

        // Verifica se o código fornecido corresponde ao hash armazenado
        if (!Hash::check($request->code, $tokenData->token)) {
            return response()->json(['message' => 'Código inválido.'], 400);
        }

        // Atualiza a senha do utilizador
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Apaga o token para que não possa ser reutilizado
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Senha redefinida com sucesso.']);
    }
}
