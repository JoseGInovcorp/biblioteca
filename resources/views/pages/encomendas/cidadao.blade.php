@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto mt-10">
    <h2 class="text-2xl font-bold mb-6">📦 As Minhas Encomendas</h2>
    <a href="{{ route('home') }}" class="btn btn-outline btn-secondary mb-6">
        ← Voltar à Página Inicial
    </a>


    @if($encomendas->isEmpty())
        <p class="text-gray-500">Ainda não efetuou nenhuma encomenda.</p>
    @else
        <div class="space-y-6">
            @foreach($encomendas as $encomenda)
                <div class="border p-4 rounded bg-base-200">
                    <p><strong>Data:</strong> {{ $encomenda->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Estado:</strong> 
                        <span class="{{ $encomenda->estado === 'paga' ? 'text-green-600' : 'text-yellow-600' }}">
                            {{ ucfirst($encomenda->estado) }}
                        </span>
                    </p>
                    <p><strong>Total:</strong> €{{ number_format($encomenda->total, 2, ',', '.') }}</p>
                    <p><strong>Livros:</strong></p>
                    <ul class="list-disc pl-5">
                        @foreach($encomenda->livros as $livro)
                            <li>
                                {{ $livro->nome }} × {{ $livro->pivot->quantidade }}
                                (€{{ number_format($livro->pivot->preco_unitario, 2, ',', '.') }})
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
