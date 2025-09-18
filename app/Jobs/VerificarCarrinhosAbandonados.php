<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\CartItem;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\CarrinhoAbandonado;

class VerificarCarrinhosAbandonados implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $limite = now()->subHour();

        $usersComCarrinho = CartItem::where('created_at', '<=', $limite)
            ->whereNull('enviado_notificacao')
            ->pluck('user_id')
            ->unique();

        foreach ($usersComCarrinho as $userId) {
            $user = User::find($userId);

            if ($user && $user->email) {
                Mail::to($user->email)->send(new CarrinhoAbandonado($user));

                // Marcar os itens como notificados
                CartItem::where('user_id', $userId)
                    ->update(['enviado_notificacao' => now()]);
            }
        }
    }
}
