@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-4">
    📦 {{ isset($endereco) ? 'Editar Morada de Entrega' : 'Morada de Entrega' }}
</h2>

<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
    {{-- Coluna esquerda: Formulário --}}
    <div>
        <form method="POST" 
              action="{{ isset($endereco) ? route('checkout.endereco.update', $endereco) : route('checkout.endereco.store') }}" 
              class="space-y-4">
            @csrf
            @if(isset($endereco))
                @method('PUT')
            @endif

            <div>
                <label class="block font-semibold">Nome</label>
                <input type="text" name="nome" 
                       value="{{ old('nome', $endereco->nome ?? '') }}" 
                       class="input input-bordered w-full" required>
            </div>

            <div>
                <label class="block font-semibold">Telefone</label>
                <input type="text" name="telefone" 
                       value="{{ old('telefone', $endereco->telefone ?? '') }}" 
                       class="input input-bordered w-full" required>
            </div>

            <div>
                <label class="block font-semibold">Morada</label>
                <input type="text" name="morada" 
                       value="{{ old('morada', $endereco->morada ?? '') }}" 
                       class="input input-bordered w-full" required>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold">Código Postal</label>
                    <input type="text" name="codigo_postal" 
                           value="{{ old('codigo_postal', $endereco->codigo_postal ?? '') }}" 
                           class="input input-bordered w-full" required>
                </div>
                <div>
                    <label class="block font-semibold">Localidade</label>
                    <input type="text" name="localidade" 
                           value="{{ old('localidade', $endereco->localidade ?? '') }}" 
                           class="input input-bordered w-full" required>
                </div>
            </div>

            <div>
                <label class="block font-semibold">País</label>
                <input type="text" name="pais" 
                       value="{{ old('pais', $endereco->pais ?? 'Portugal') }}" 
                       class="input input-bordered w-full" required>
            </div>

            <div class="flex gap-4">
                <button type="submit" 
                        name="acao" 
                        value="{{ isset($endereco) ? '' : 'guardar' }}" 
                        class="btn btn-primary">
                    {{ isset($endereco) ? '💾 Atualizar Morada' : '💾 Guardar Morada e Continuar para Pagamento' }}
                </button>

                @if(!isset($endereco))
                    <button type="submit" name="acao" value="nao_guardar" class="btn btn-warning">
                        ➡️ Continuar para Pagamento sem Guardar
                    </button>
                @endif

                <a href="{{ route('carrinho.index') }}" class="btn btn-outline btn-secondary">
                    ⬅️ Voltar ao Carrinho
                </a>
            </div>
        </form>
    </div>

    {{-- Coluna direita: Resumo da encomenda --}}
    <div>
        <div class="bg-base-200 p-4 rounded shadow">
            <h3 class="font-bold text-lg mb-4">🛒 Resumo da Encomenda</h3>

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

                <p class="font-semibold text-right">
                    Subtotal: €{{ number_format($subtotal, 2, ',', '.') }}
                </p>
            @endif
        </div>
    </div>
</div>
@endsection
