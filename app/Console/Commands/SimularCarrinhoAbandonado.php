<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CartItem;
use App\Jobs\VerificarCarrinhosAbandonados;

class SimularCarrinhoAbandonado extends Command
{
    protected $signature = 'carrinho:simular-abandono';
    protected $description = 'Simula carrinho abandonado e executa o job de notificação';

    public function handle()
    {
        $item = CartItem::latest()->first();

        if (!$item) {
            $this->warn('⚠️ Nenhum item encontrado no carrinho.');
            return;
        }

        $item->created_at = now()->subHours(2);
        $item->enviado_notificacao = null;
        $item->save();

        $this->info("🛒 Item #{$item->id} marcado como abandonado para o utilizador #{$item->user_id}");

        (new VerificarCarrinhosAbandonados)->handle();

        $this->info('📨 Job executado. Verifica o Mailhog para confirmar envio.');
    }
}
