@extends('layouts.app')

@section('content')
<a href="{{ route('home') }}" class="btn btn-outline btn-secondary mb-4">⬅️ Voltar ao menu</a>
<a href="{{ route('livros.index') }}" class="btn btn-outline btn-accent mb-4">📚 Voltar aos livros</a>

<h2 class="text-2xl font-bold mb-4">🛒 O meu carrinho</h2>

@if($itens->isEmpty())
    <p class="text-gray-500">O seu carrinho está vazio.</p>
@else
    <div class="overflow-x-auto">
        <table class="table w-full mb-4">
            <thead>
                <tr>
                    <th>Livro</th>
                    <th>Tipo</th>
                    <th>Quantidade</th>
                    <th>Preço Unitário</th>
                    <th>Total</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($itens as $item)
                    <tr>
                        <td>{{ $item->livro->nome }}</td>
                        <td>{{ ucfirst($item->tipo_encomenda) }}</td>
                        <td>
                            <form method="POST" action="{{ route('carrinho.update', $item->livro) }}" class="flex items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <input type="number"
                                    name="quantity"
                                    value="{{ $item->quantity }}"
                                    min="1"
                                    max="{{ $item->livro->stock_venda }}"
                                    class="input input-bordered w-16" />

                                @if($item->livro->stock_venda <= $item->quantity)
                                    <p class="text-xs text-warning mt-1">
                                        ⚠️ Apenas {{ $item->livro->stock_venda }} unidade{{ $item->livro->stock_venda > 1 ? 's' : '' }} disponível.
                                    </p>
                                @endif
                                <button type="submit" class="btn btn-sm btn-primary">🔄 Atualizar</button>
                            </form>
                        </td>
                        <td>€{{ number_format($item->preco_unitario, 2, ',', '.') }}</td>
                        <td>€{{ number_format($item->quantity * $item->preco_unitario, 2, ',', '.') }}</td>
                        <td>
                            <form method="POST" action="{{ route('carrinho.remove', $item->livro) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-error">🗑 Remover</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <p class="text-lg font-semibold mb-6">
        Subtotal: €{{ number_format($subtotal, 2, ',', '.') }}
    </p>

    {{-- Morada de entrega, se existir --}}
    @if($morada)
        <div class="bg-base-200 p-4 rounded mb-6">
            <h3 class="font-bold mb-2">📍 Morada de Entrega</h3>
            <p>{{ $morada->nome }} — {{ $morada->telefone }}</p>
            <p>{{ $morada->morada }}, {{ $morada->codigo_postal }} {{ $morada->localidade }}</p>
            <p>{{ $morada->pais }}</p>
            <a href="{{ route('checkout.endereco.edit', $morada) }}" class="btn btn-sm btn-outline mt-2">
                ✏️ Editar Morada
            </a>
        </div>

        {{-- Botão para pagamento --}}
        <a href="{{ route('checkout.pagamento') }}" class="btn btn-primary">
            💳 Continuar para Pagamento
        </a>
    @else
        {{-- Se não houver morada, pedir para inserir --}}
        <a href="{{ route('checkout.endereco') }}" class="btn btn-success">
            📦 Inserir Morada de Entrega
        </a>
    @endif
@endif
@endsection
