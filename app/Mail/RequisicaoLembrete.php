<?php

namespace App\Mail;

use App\Models\Requisicao;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RequisicaoLembrete extends Mailable
{
    use Queueable, SerializesModels;

    public $requisicao;

    public function __construct(Requisicao $requisicao)
    {
        $this->requisicao = $requisicao;
    }

    public function build()
    {
        return $this->subject('Lembrete: Devolução da Requisição #' . $this->requisicao->numero_sequencial)
            ->view('emails.requisicoes.lembrete')
            ->with([
                'requisicao' => $this->requisicao
            ]);
    }
}
