<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Livro;
use App\Models\CartItem;
use App\Models\EnderecoEntrega;
use App\Traits\RegistaLog;

class CarrinhoController extends Controller
{
    use RegistaLog;

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

        // 📜 Log da adição ao carrinho
        $this->registarLog(
            'Carrinho',
            $item->id,
            "Adicionou o livro '{$livro->nome}' ao carrinho (tipo: {$data['tipo_encomenda']}, quantidade: {$item->quantity})"
        );

        return back()->with('success', 'Livro adicionado ao carrinho.');
    }

    public function index()
    {
        $itens = CartItem::with('livro')
            ->where('user_id', auth()->id())
            ->get();

        $subtotal = $itens->sum(fn($i) => $i->quantity * $i->preco_unitario);

        $morada = EnderecoEntrega::where('user_id', auth()->id())
            ->latest()
            ->first();

        return view('pages.carrinho.index', compact('itens', 'subtotal', 'morada'));
    }

    public function remove(Livro $livro)
    {
        $item = CartItem::where('user_id', auth()->id())
            ->where('livro_id', $livro->id)
            ->first();

        if ($item) {
            // 📜 Log da remoção antes de apagar
            $this->registarLog(
                'Carrinho',
                $item->id,
                "Removeu o livro '{$livro->nome}' do carrinho"
            );

            $item->delete();
        }

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

        // 📜 Log da atualização de quantidade
        $this->registarLog(
            'Carrinho',
            $item->id,
            "Atualizou a quantidade do livro '{$livro->nome}' no carrinho para {$data['quantity']}"
        );

        return back()->with('success', 'Quantidade atualizada.');
    }
}
