<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Livro;
use App\Models\CartItem;
use App\Models\EnderecoEntrega;

class CarrinhoController extends Controller
{
    public function add(Livro $livro)
    {
        $item = CartItem::firstOrNew([
            'user_id' => auth()->id(),
            'livro_id' => $livro->id,
        ]);

        $item->quantity = ($item->exists ? $item->quantity : 0) + 1;
        $item->touched_at = now();
        $item->save();

        return back()->with('success', 'Livro adicionado ao carrinho.');
    }

    public function index()
    {
        $itens = CartItem::with('livro')
            ->where('user_id', auth()->id())
            ->get();

        $subtotal = $itens->sum(fn($i) => $i->quantity * $i->livro->preco);

        $morada = EnderecoEntrega::where('user_id', auth()->id())
            ->latest()
            ->first();

        return view('pages.carrinho.index', compact('itens', 'subtotal', 'morada'));
    }

    public function remove(Livro $livro)
    {
        CartItem::where('user_id', auth()->id())
            ->where('livro_id', $livro->id)
            ->delete();

        return back()->with('success', 'Livro removido do carrinho.');
    }

    public function update(Request $request, Livro $livro)
    {
        $data = $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        CartItem::where('user_id', auth()->id())
            ->where('livro_id', $livro->id)
            ->update([
                'quantity' => $data['quantity'],
                'touched_at' => now()
            ]);

        return back()->with('success', 'Quantidade atualizada.');
    }
}
