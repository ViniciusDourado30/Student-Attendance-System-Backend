<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seu Código de Redefinição de Senha</title>
    <style>
        
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
        body { height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important; font-family: 'Inter', Arial, sans-serif; }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f3f4f6;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" style="padding: 1rem;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
                    <tr>
                        <td align="center" bgcolor="#ffffff" style="padding: 32px 24px 16px 24px; border-bottom: 1px solid #e5e7eb;">
                            <img 
                                src="https://i.imgur.com/RhYJHbg.png" 
                                alt="Logo Léo Pizzato" 
                                width="180"
                                style="display: block; width: 180px; max-width: 180px; height: auto; border: 0;"
                            >
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor="#ffffff" style="padding: 32px; font-family: 'Inter', Arial, sans-serif;">
                            <h2 style="font-size: 24px; font-weight: 600; color: #1f2937; margin: 0;">Olá!</h2>
                            <p style="margin: 16px 0 0; color: #4b5563; font-size: 16px; line-height: 1.5;">
                                Recebemos uma solicitação para redefinir a senha da sua conta. Utilize o código abaixo para concluir o processo.
                            </p>
                            <div style="text-align: center; margin: 32px 0;">
                                <p style="color: #6b7280; font-size: 14px; margin: 0;">Seu código de uso único é:</p>
                                <div style="display: inline-block; background-color: #e5e7eb; color: #1f2937; font-size: 36px; font-weight: 700; letter-spacing: 4px; border-radius: 8px; padding: 12px 24px; margin-top: 8px;">
                                    {{ $code }}
                                </div>
                            </div>
                            <p style="color: #4b5563; font-size: 16px; line-height: 1.5;">
                                Este código expirará em 5 minutos. Se você não solicitou uma redefinição de senha, por favor, ignore este e-mail.
                            </p>
                            <p style="color: #4b5563; font-size: 16px; line-height: 1.5; margin: 24px 0 0;">
                                Obrigado,<br>
                                Equipe Sistema de Faltas Leo Pizzato
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor="#f9fafb" align="center" style="padding: 24px; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0; color: #6b7280; font-size: 14px;">
                                &copy; {{ date('Y') }} Sistema de Faltas Leo Pizzato. Todos os direitos reservados.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
