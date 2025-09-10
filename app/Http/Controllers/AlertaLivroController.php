<?php

namespace App\Http\Controllers;

use App\Models\Livro;
use Illuminate\Http\Request;

class AlertaLivroController extends Controller
{
    public function store(Livro $livro)
    {
        $user = auth()->user();

        if (!$user || !$user->isCidadao()) {
            abort(403);
        }

        // Verifica o alerta mais recente do utilizador para este livro
        $alertaExistente = $livro->alertas()
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        // Permite novo alerta se não existir ou se o anterior já foi notificado
        if (!$alertaExistente || $alertaExistente->notificado_em !== null) {
            $livro->alertas()->create([
                'user_id' => $user->id,
            ]);
        }

        return back()->with('success', 'Será notificado por email quando o livro estiver disponível.');
    }
}
