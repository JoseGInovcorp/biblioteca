<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Traits\RegistaLog;

class RegistarLogin
{
    use RegistaLog;

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $this->registarLog(
            'Autenticação',
            $event->user->id,
            'Login bem-sucedido'
        );
    }
}
