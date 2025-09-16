<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EnderecoEntrega;
use App\Models\CartItem;

class EnderecoEntregaController extends Controller
{
    public function create()
    {
        $itens = CartItem::with('livro')
            ->where('user_id', auth()->id())
            ->get();

        $subtotal = $itens->sum(fn($i) => $i->quantity * $i->livro->preco);

        return view('pages.checkout.endereco', compact('itens', 'subtotal'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'telefone' => 'required|string|max:20',
            'morada' => 'required|string|max:255',
            'codigo_postal' => 'required|string|max:20',
            'localidade' => 'required|string|max:100',
            'pais' => 'required|string|max:100',
        ]);

        if ($request->acao === 'guardar') {
            $data['user_id'] = auth()->id();
            EnderecoEntrega::create($data);
        }

        session(['morada_checkout' => $data]);

        return redirect()->route('checkout.pagamento');
    }

    public function edit(EnderecoEntrega $endereco)
    {
        abort_if($endereco->user_id !== auth()->id(), 403);

        $itens = CartItem::with('livro')
            ->where('user_id', auth()->id())
            ->get();

        $subtotal = $itens->sum(fn($i) => $i->quantity * $i->livro->preco);

        return view('pages.checkout.endereco', compact('endereco', 'itens', 'subtotal'));
    }

    public function update(Request $request, EnderecoEntrega $endereco)
    {
        abort_if($endereco->user_id !== auth()->id(), 403);

        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'telefone' => 'required|string|max:20',
            'morada' => 'required|string|max:255',
            'codigo_postal' => 'required|string|max:20',
            'localidade' => 'required|string|max:100',
            'pais' => 'required|string|max:100',
        ]);

        $endereco->update($data);

        return redirect()->route('carrinho.index')->with('success', 'Morada atualizada com sucesso.');
    }
}
