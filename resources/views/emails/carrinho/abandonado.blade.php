<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Carrinho Abandonado</title>
</head>
<body style="font-family: Arial, sans-serif; background-color:#f4f4f4; padding:20px;">

    <table width="100%" cellpadding="0" cellspacing="0" style="max-width:600px; margin:auto; background:#ffffff; border-radius:8px; overflow:hidden;">
        <tr>
            <td style="background-color:#3182ce; color:#ffffff; padding:20px; text-align:center; font-size:20px; font-weight:bold;">
                🛒 Livros no seu Carrinho
            </td>
        </tr>
        <tr>
            <td style="padding:20px; color:#333333; font-size:15px; line-height:1.6;">
                <p>Estimado(a) <strong>{{ $user->name ?? 'Utilizador' }}</strong>,</p>

                <p>Notamos que deixou <strong>{{ $quantidade }}</strong> livro(s) à sua espera há mais de uma hora e ainda não concluiu a encomenda.</p>

                <p style="background-color:#e6fffa; padding:10px; border-radius:4px; border:1px solid #81e6d9;">
                    ✅ <strong>Precisa de ajuda para finalizar a compra?</strong> Pode retomar o processo a qualquer momento.
                </p>

                <p style="text-align:center; margin:30px 0;">
                    <a href="{{ url('/carrinho') }}" target="_blank" style="background-color:#3182ce; color:#ffffff; padding:12px 20px; text-decoration:none; border-radius:4px; font-weight:bold;">
                        Ver Carrinho
                    </a>
                </p>

                <p style="margin-top:20px;">
                    Com os melhores cumprimentos,<br>
                    <em>Biblioteca Municipal</em>
                </p>
            </td>
        </tr>
        <tr>
            <td style="background-color:#f4f4f4; text-align:center; font-size:12px; color:#666; padding:10px;">
                Este é um email automático, por favor não responda diretamente a esta mensagem.
            </td>
        </tr>
    </table>

</body>
</html>