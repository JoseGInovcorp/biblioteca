<?php

namespace App\Mail;

use App\Models\Requisicao;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RequisicaoCriada extends Mailable
{
    use Queueable, SerializesModels;

    public $requisicao;
    public $capaUrl;

    public function __construct(Requisicao $requisicao)
    {
        $this->requisicao = $requisicao;
        $this->capaUrl = $requisicao->livro?->imagem_capa
            ? asset('storage/' . $requisicao->livro->imagem_capa)
            : null;
    }

    public function build()
    {
        return $this->subject('Confirmação de Requisição #' . $this->requisicao->numero_sequencial)
            ->view('emails.requisicoes.criada');
    }
}
