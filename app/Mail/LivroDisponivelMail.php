<?php

namespace App\Mail;

use App\Models\Livro;
use App\Models\AlertaLivro;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LivroDisponivelMail extends Mailable
{
    use Queueable, SerializesModels;

    public Livro $livro;
    public AlertaLivro $alerta;

    public function __construct(Livro $livro, AlertaLivro $alerta)
    {
        $this->livro = $livro;
        $this->alerta = $alerta;
    }

    public function build()
    {
        return $this->subject('📚 Livro disponível para requisição')
            ->view('emails.livros.disponivel')
            ->with([
                'livro' => $this->livro,
                'alerta' => $this->alerta,
            ]);
    }
}
