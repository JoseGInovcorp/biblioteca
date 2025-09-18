<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Livro;
use App\Models\CartItem;
use App\Models\EnderecoEntrega;

class CarrinhoController extends Controller
{
    public function add(Request $request, Livro $livro)
    {
        $data = $request->validate([
            'tipo_encomenda' => 'required|in:compra,requisicao',
        ]);

        // Validação de disponibilidade e definição de preço
        if ($data['tipo_encomenda'] === 'compra') {
            if (! $livro->isDisponivelParaCompra()) {
                return back()->with('error', 'Este livro está esgotado para venda.');
            }
            $preco = $livro->preco_venda;
        } else { // requisição
            if (! $livro->isDisponivelParaRequisicao()) {
                return back()->with('error', 'Este livro não está disponível para requisição.');
            }
            // Mantemos o preço original da requisição
            $preco = $livro->preco;
        }

        // Adicionar ou atualizar item no carrinho
        $item = CartItem::firstOrNew([
            'user_id'        => auth()->id(),
            'livro_id'       => $livro->id,
            'tipo_encomenda' => $data['tipo_encomenda'],
        ]);

        $item->quantity = ($item->exists ? $item->quantity : 0) + 1;
        $item->preco_unitario = $preco;
        $item->touched_at = now();
        $item->save();

        return back()->with('success', 'Livro adicionado ao carrinho.');
    }


    public function index()
    {
        $itens = CartItem::with('livro')
            ->where('user_id', auth()->id())
            ->get();

        // Usa o preço guardado no carrinho (compra ou requisição)
        $subtotal = $itens->sum(fn($i) => $i->quantity * $i->preco_unitario);

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

        $item = CartItem::where('user_id', auth()->id())
            ->where('livro_id', $livro->id)
            ->firstOrFail();

        if ($item->tipo_encomenda === 'compra' && $data['quantity'] > $livro->stock_venda) {
            return back()->with('error', 'Quantidade excede o stock disponível para venda.');
        }

        $item->update([
            'quantity' => $data['quantity'],
            'touched_at' => now()
        ]);

        return back()->with('success', 'Quantidade atualizada.');
    }
}
