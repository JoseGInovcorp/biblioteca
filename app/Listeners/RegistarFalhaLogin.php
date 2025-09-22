<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use App\Models\Log;

class RegistarFalhaLogin
{
    /**
     * Handle the event.
     */
    public function handle(Failed $event): void
    {
        Log::create([
            'user_id'   => null, // não há utilizador autenticado
            'modulo'    => 'Autenticação',
            'objeto_id' => null,
            'alteracao' => 'Tentativa de login falhada para o email: ' . ($event->credentials['email'] ?? 'desconhecido'),
            'ip'        => request()->ip(),
            'browser'   => request()->userAgent(),
        ]);
    }
}
