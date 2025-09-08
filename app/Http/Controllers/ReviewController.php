<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Requisicao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReviewCriada;
use App\Mail\ReviewModerada;

class ReviewController extends Controller
{
    // Cidadão submete review
    public function store(Request $request, Requisicao $requisicao)
    {
        if (!auth()->user()->isCidadao() || $requisicao->cidadao_id !== auth()->id()) {
            abort(403);
        }

        if (!$requisicao->data_fim_real) {
            return redirect()->back()->with('error', 'Só pode deixar review após entrega do livro.');
        }

        $request->validate([
            'comentario' => 'required|string|min:10',
        ]);

        $review = Review::create([
            'livro_id'      => $requisicao->livro_id,
            'requisicao_id' => $requisicao->id,
            'user_id'       => auth()->id(),
            'comentario'    => $request->comentario,
            'estado'        => 'suspenso',
        ]);

        // Enviar email para todos os admins
        $admins = \App\Models\User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new ReviewCriada($review));
        }

        return redirect()->back()->with('success', 'Review enviada e aguarda moderação.');
    }

    // Admin vê lista de reviews suspensos
    public function index()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        return view('pages.reviews.index', [
            'pendentes' => Review::where('estado', 'suspenso')->with('user', 'livro')->latest()->get(),
            'ativas'    => Review::where('estado', 'ativo')->with('user', 'livro')->latest()->get(),
            'recusadas' => Review::where('estado', 'recusado')->with('user', 'livro')->latest()->get(),
        ]);
    }


    // Admin modera review
    public function update(Request $request, Review $review)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'estado' => 'required|in:ativo,recusado',
            'justificacao' => 'nullable|string|max:1000',
        ]);

        $review->estado = $request->estado;
        $review->justificacao = $request->estado === 'recusado' ? $request->justificacao : null;
        $review->save();

        // Notificar cidadão
        Mail::to($review->user->email)->send(new ReviewModerada($review));

        return redirect()->route('reviews.index')->with('success', 'Review moderada com sucesso.');
    }
}
