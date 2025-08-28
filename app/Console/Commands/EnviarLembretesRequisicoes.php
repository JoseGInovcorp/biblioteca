<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Requisicao;
use Illuminate\Support\Facades\Mail;
use App\Mail\RequisicaoLembrete;

class EnviarLembretesRequisicoes extends Command
{
    protected $signature = 'requisicoes:enviar-lembretes';
    protected $description = 'Envia lembretes de devolução para requisições que terminam amanhã';

    public function handle()
    {
        $amanha = now()->addDay()->toDateString();

        $requisicoes = Requisicao::with('livro', 'cidadao')
            ->whereDate('data_fim_prevista', $amanha)
            ->where('status', 'ativa')
            ->get();

        foreach ($requisicoes as $req) {
            // 📧 Apenas para o cidadão
            Mail::to($req->cidadao->email)
                ->send(new RequisicaoLembrete($req));
        }

        $this->info("Lembretes enviados: " . $requisicoes->count());
    }
}
