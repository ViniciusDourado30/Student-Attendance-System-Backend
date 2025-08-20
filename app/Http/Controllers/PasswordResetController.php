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
        // Gera um código aleatório de 6 dígitos
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Armazena o código na base de dados de forma segura (hash)
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($code),
                'created_at' => Carbon::now()
            ]
        );

        // Envia o e-mail para o utilizador com o código
        Mail::to($user)->send(new SendPasswordResetCode($code));

        return response()->json([
            'message' => 'Código de redefinição enviado com sucesso.',
        ]);
    }

    /**
     * Redefine a senha do utilizador usando o código.
     */
    public function resetPassword(Request $request)
    {
        // 1. Valida os dados recebidos: e-mail, código e a nova senha confirmada.
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'code' => 'required|string|min:6|max:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // 2. Procura o token na base de dados pelo e-mail do utilizador.
        $tokenData = DB::table('password_reset_tokens')
            ->where('email', $request->email)->first();

        // 3. Verifica se o token existe e se não expirou (válido por 5 minutos).
        if (!$tokenData || Carbon::parse($tokenData->created_at)->addMinutes(5)->isPast()) {
            return response()->json(['message' => 'Código inválido ou expirado.'], 400);
        }

        // 4. Compara o código recebido do front-end com o token guardado na base de dados.
        if (!Hash::check($request->code, $tokenData->token)) {
            // Se não forem iguais, retorna uma mensagem de erro.
            return response()->json(['message' => 'Código inválido.'], 400);
        }

        // 5. Se os códigos forem iguais, muda a senha.
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // 6. Apaga o token para que não possa ser reutilizado.
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // 7. Retorna uma mensagem de sucesso.
        return response()->json(['message' => 'Senha redefinida com sucesso.']);
    }
}
