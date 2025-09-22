<?php

namespace App\Traits;

use App\Models\Log;

trait RegistaLog
{
    /**
     * Regista uma ação no sistema de logs.
     *
     * @param string $modulo     Nome do módulo (ex: 'Livros', 'Requisições')
     * @param int|null $objetoId ID do objeto afetado
     * @param string $alteracao  Descrição da alteração
     */
    public function registarLog(string $modulo, ?int $objetoId, string $alteracao): void
    {
        Log::create([
            'user_id'   => auth()->id(),
            'modulo'    => $modulo,
            'objeto_id' => $objetoId,
            'alteracao' => $alteracao,
            'ip'        => request()->ip(),
            'browser'   => request()->userAgent(),
        ]);
    }
}
