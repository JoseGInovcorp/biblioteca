<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Failed;
use App\Listeners\RegistarLogin;
use App\Listeners\RegistarFalhaLogin;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Os eventos que a aplicação escuta.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Login::class => [
            RegistarLogin::class,
        ],
        Failed::class => [
            RegistarFalhaLogin::class,
        ],
    ];

    /**
     * Regista quaisquer serviços.
     */
    public function register(): void
    {
        //
    }

    /**
     * Inicializa quaisquer serviços.
     */
    public function boot(): void
    {
        //
    }
}
