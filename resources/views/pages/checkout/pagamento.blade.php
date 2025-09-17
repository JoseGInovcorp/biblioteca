@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-6">💳 Pagamento</h2>

<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
    {{-- Coluna esquerda: Morada de entrega --}}
    <div>
        <div class="bg-base-200 p-4 rounded shadow mb-6">
            <h3 class="font-bold text-lg mb-4">📍 Morada de Entrega</h3>

            @php
                // Primeiro tenta buscar da sessão (checkout atual)
                $morada = session('morada_checkout');

                // Se não existir na sessão, tenta buscar da BD
                if (!$morada) {
                    $moradaModel = \App\Models\EnderecoEntrega::where('user_id', auth()->id())
                        ->latest()
                        ->first();
                    if ($moradaModel) {
                        $morada = $moradaModel->toArray();
                    }
                }
            @endphp

            @if($morada)
                <p>{{ $morada['nome'] }} — {{ $morada['telefone'] }}</p>
                <p>{{ $morada['morada'] }}, {{ $morada['codigo_postal'] }} {{ $morada['localidade'] }}</p>
                <p>{{ $morada['pais'] }}</p>
                <a href="{{ isset($moradaModel) ? route('checkout.endereco.edit', $moradaModel) : route('checkout.endereco') }}" 
                   class="btn btn-sm btn-outline mt-3">
                    ✏️ Alterar Morada
                </a>
            @else
                <p class="text-gray-500">Nenhuma morada definida.</p>
                <a href="{{ route('checkout.endereco') }}" class="btn btn-sm btn-success mt-3">
                    ➕ Adicionar Morada
                </a>
            @endif
        </div>
    </div>

    {{-- Coluna direita: Resumo da encomenda --}}
    <div>
        <div class="bg-base-200 p-4 rounded shadow">
            <h3 class="font-bold text-lg mb-4">🛒 Resumo da Encomenda</h3>

            @php
                $itens = \App\Models\CartItem::with('livro')
                    ->where('user_id', auth()->id())
                    ->get();
                $subtotal = $itens->sum(fn($i) => $i->quantity * $i->livro->preco);
            @endphp

            @if($itens->isEmpty())
                <p class="text-gray-500">O seu carrinho está vazio.</p>
            @else
                <ul class="divide-y divide-gray-300 mb-4">
                    @foreach($itens as $item)
                        <li class="py-2 flex justify-between">
                            <span>{{ $item->livro->nome }} × {{ $item->quantity }}</span>
                            <span>€{{ number_format($item->quantity * $item->livro->preco, 2, ',', '.') }}</span>
                        </li>
                    @endforeach
                </ul>

                <p class="font-semibold text-right mb-4">
                    Subtotal: €{{ number_format($subtotal, 2, ',', '.') }}
                </p>

                {{-- Se houver morada, mostra botão de pagamento; caso contrário, alerta --}}
                @if($morada)
                    <form method="POST" action="{{ route('checkout.stripe') }}">
                        @csrf
                        <input type="hidden" name="nome" value="{{ $morada['nome'] ?? '' }}">
                        <input type="hidden" name="telefone" value="{{ $morada['telefone'] ?? '' }}">
                        <input type="hidden" name="morada" value="{{ $morada['morada'] ?? '' }}">
                        <input type="hidden" name="codigo_postal" value="{{ $morada['codigo_postal'] ?? '' }}">
                        <input type="hidden" name="localidade" value="{{ $morada['localidade'] ?? '' }}">
                        <input type="hidden" name="pais" value="{{ $morada['pais'] ?? '' }}">

                        <button type="submit" class="btn btn-primary w-full">
                            💳 Pagar com Stripe
                        </button>
                    </form>
                @else
                    <p class="text-error text-center font-semibold mt-4">
                        ⚠️ É necessário definir uma morada de entrega antes de prosseguir para o pagamento.
                    </p>
                    <a href="{{ route('checkout.endereco') }}" class="btn btn-success w-full mt-3">
                        ➕ Adicionar Morada
                    </a>
                @endif


                {{-- Voltar ao carrinho --}}
                <a href="{{ route('carrinho.index') }}" class="btn btn-outline btn-secondary w-full mt-3">
                    ⬅️ Voltar ao Carrinho
                </a>
            @endif
        </div>
    </div>
</div>
@endsection
