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

        // AQUI ESTÁ A LIGAÇÃO SEGURA (O "ANEXO"):
        // Criamos ou atualizamos um registo na base de dados que liga
        // o e-mail do pedido a uma versão encriptada do código.
        // Isto garante que cada código está associado a um único e-mail.
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($code),
                'created_at' => Carbon::now()
            ]
        );

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
        $request->validate([
            'code' => 'required|string|digits:6',
            'new_password' => 'required|string|min:8',
        ]);

        // AQUI VERIFICAMOS A LIGAÇÃO:
        // Procuramos o token na base de dados USANDO O E-MAIL que veio do front-end.
        // Isto garante que estamos a olhar para o código da pessoa certa.
        $tokenData = DB::table('password_reset_tokens')
            ->where('email', $request->email)->first();
        
        // Verifica se o token existe para aquele e-mail e se não expirou.
        if (!$tokenData || Carbon::parse($tokenData->created_at)->addMinutes(5)->isPast()) {
            return response()->json(['message' => 'Código inválido ou expirado.'], 400);
        }

        // Compara o código que o utilizador enviou com o que está guardado para aquele e-mail.
        if (!Hash::check($request->code, $tokenData->token)) {
            return response()->json(['message' => 'Código inválido.'], 400);
        }
        
        // Se tudo estiver correto, encontramos o utilizador PELO E-MAIL e mudamos a senha.
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();
        
        // Apagamos o token da base de dados para que não possa ser reutilizado.
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Senha redefinida com sucesso.']);
    }
}
