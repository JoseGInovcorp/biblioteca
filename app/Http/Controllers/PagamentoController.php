<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use App\Models\CartItem;
use App\Models\Encomenda;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PagamentoController extends Controller
{
    public function checkout(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        // Guardar morada na sessão (se existir)
        if ($request->filled(['nome', 'telefone', 'morada', 'codigo_postal', 'localidade', 'pais'])) {
            session([
                'morada_checkout' => [
                    'nome'          => $request->input('nome'),
                    'telefone'      => $request->input('telefone'),
                    'morada'        => $request->input('morada'),
                    'codigo_postal' => $request->input('codigo_postal'),
                    'localidade'    => $request->input('localidade'),
                    'pais'          => $request->input('pais'),
                ]
            ]);
        }

        $itens = CartItem::with('livro')
            ->where('user_id', auth()->id())
            ->get();

        if ($itens->isEmpty()) {
            return redirect()->route('carrinho.index')->with('error', 'O carrinho está vazio.');
        }

        // Verificar stock antes de criar sessão Stripe
        foreach ($itens as $item) {
            if ($item->tipo_encomenda === 'compra') {
                if ($item->livro->stock_venda < $item->quantity) {
                    return redirect()->route('carrinho.index')->with(
                        'error',
                        "O livro '{$item->livro->nome}' tem apenas {$item->livro->stock_venda} unidades disponíveis. Reduza a quantidade antes de continuar."
                    );
                }
            }
        }

        $lineItems = [];
        foreach ($itens as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $item->livro->nome,
                    ],
                    'unit_amount' => intval($item->preco_unitario * 100),
                ],
                'quantity' => $item->quantity,
            ];
        }

        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('checkout.sucesso'),
            'cancel_url' => route('checkout.cancelado'),
        ]);

        return redirect($session->url);
    }

    public function sucesso()
    {
        $user = auth()->user();
        $itens = CartItem::with('livro')->where('user_id', $user->id)->get();

        if ($itens->isEmpty()) {
            return redirect()->route('carrinho.index')->with('error', 'Carrinho vazio.');
        }

        // 1️⃣ Primeiro tenta buscar da sessão
        $morada = session('morada_checkout');

        // 2️⃣ Se não existir, tenta buscar da BD
        if (!$morada) {
            $moradaModel = \App\Models\EnderecoEntrega::where('user_id', $user->id)
                ->latest()
                ->first();
            if ($moradaModel) {
                $morada = $moradaModel->toArray();
            }
        }

        // 3️⃣ Se mesmo assim não existir, redireciona
        if (!$morada) {
            return redirect()->route('checkout.endereco')
                ->with('error', 'Por favor defina a morada de entrega antes de concluir o pagamento.');
        }

        $subtotal = $itens->sum(fn($i) => $i->quantity * $i->preco_unitario);

        DB::transaction(function () use ($user, $morada, $subtotal, $itens) {
            $encomenda = Encomenda::create([
                'user_id' => $user->id,
                'morada'  => $morada,
                'total'   => $subtotal,
                'estado'  => 'paga',
            ]);

            foreach ($itens as $item) {
                $encomenda->livros()->attach($item->livro_id, [
                    'quantidade'     => $item->quantity,
                    'preco_unitario' => $item->preco_unitario,
                ]);

                // Atualizar stock com proteção
                $item->livro->stock_venda = max(0, $item->livro->stock_venda - $item->quantity);
                $item->livro->save();

                // Log se esgotar
                if ($item->livro->stock_venda === 0) {
                    Log::warning("📉 Livro esgotado: {$item->livro->nome} (ID {$item->livro->id})");
                }
            }
        });

        // Limpar carrinho e sessão
        CartItem::where('user_id', $user->id)->delete();
        session()->forget('morada_checkout');

        return view('pages.checkout.sucesso');
    }

    public function cancelado()
    {
        return view('pages.checkout.cancelado');
    }
}
