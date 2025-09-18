<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Regista o agendamento de comandos (scheduler).
     */
    protected function schedule(Schedule $schedule): void
    {
        // Agendar o comando dos lembretes para correr todos os dias às 09:00
        $schedule->command('requisicoes:enviar-lembretes')->dailyAt('09:00');

        // 💡 Para testar rapidamente podes usar:
        // $schedule->command('requisicoes:enviar-lembretes')->everyMinute();

        $schedule->job(new \App\Jobs\VerificarCarrinhosAbandonados)->hourly();
    }

    /**
     * Regista os comandos Artisan da aplicação.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
