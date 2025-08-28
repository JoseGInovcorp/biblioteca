<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Confirmação de Requisição</title>
</head>
<body style="font-family: Arial, sans-serif; background-color:#f4f4f4; padding:20px;">

    <table width="100%" cellpadding="0" cellspacing="0" style="max-width:600px; margin:auto; background:#ffffff; border-radius:8px; overflow:hidden;">
        <tr>
            <td style="background-color:#2b6cb0; color:#ffffff; padding:20px; text-align:center; font-size:20px; font-weight:bold;">
                📚 Confirmação de Requisição
            </td>
        </tr>
        <tr>
            <td style="padding:20px; color:#333333; font-size:15px; line-height:1.6;">
                <p>Estimado(a) <strong>{{ $requisicao->cidadao->name }}</strong>,</p>

                <p>
                    Serve o presente email para <strong>confirmar a requisição</strong> do seguinte livro:
                </p>

                <table cellpadding="0" cellspacing="0" width="100%" style="margin-top:10px; margin-bottom:20px;">
                    <tr>
                        <td style="width:120px; vertical-align:top; text-align:center;">
                            @if($capaUrl)
                                <a href="{{ $capaUrl }}" target="_blank">
                                    <img src="{{ $capaUrl }}" alt="Capa" style="width:100px; border-radius:4px; box-shadow:0 2px 4px rgba(0,0,0,0.2);">
                                </a>
                            @else
                                <span style="color:#999; font-style:italic;">Sem capa</span>
                            @endif
                        </td>
                        <td style="vertical-align:top; padding-left:15px;">
                            <p><strong>Título:</strong> {{ $requisicao->livro->nome }}</p>
                            <p><strong>Editora:</strong> {{ $requisicao->livro->editora->nome ?? '—' }}</p>
                            <p><strong>Início:</strong> {{ $requisicao->data_inicio }}</p>
                            <p><strong>Fim previsto:</strong> {{ $requisicao->data_fim_prevista }}</p>
                            <p><strong>Nº Requisição:</strong> {{ $requisicao->numero_sequencial }}</p>
                        </td>
                    </tr>
                </table>

                <p style="background-color:#fef3c7; padding:10px; border-radius:4px; border:1px solid #fcd34d;">
                    ⚠️ <strong>Por favor guarde este email</strong> como comprovativo da sua requisição.
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
