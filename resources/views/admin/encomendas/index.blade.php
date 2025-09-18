@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto mt-10">
    <h2 class="text-2xl font-bold mb-6 text-primary">
        @if(request()->routeIs('admin.encomendas.pendentes'))
            ⏳ Encomendas Pendentes
        @elseif(request()->routeIs('admin.encomendas.pagas'))
            ✅ Encomendas Pagas
        @else
            📦 Todas as Encomendas
        @endif
    </h2>

    <div class="mb-6">
        <a href="{{ route('home') }}" class="btn btn-outline btn-sm">
            🔙 Voltar para Dashboard
        </a>
    </div>

    @if($encomendas->isEmpty())
        <p class="text-gray-500">Não existem encomendas nesta categoria.</p>
    @else
        <div class="overflow-x-auto">
            <table class="table w-full border">
                <thead>
                    <tr class="bg-base-200 text-base-content">
                        <th class="px-4 py-2">#</th>
                        <th class="px-4 py-2">Utilizador</th>
                        <th class="px-4 py-2">Estado</th>
                        <th class="px-4 py-2">Total</th>
                        <th class="px-4 py-2">Data</th>
                        <th class="px-4 py-2">Livros</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($encomendas as $encomenda)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $encomenda->id }}</td>
                            <td class="px-4 py-2">{{ $encomenda->user->name ?? '—' }}</td>
                            <td class="px-4 py-2">
                                <span class="font-semibold {{ $encomenda->estado === 'paga' ? 'text-green-600' : 'text-yellow-600' }}">
                                    {{ ucfirst($encomenda->estado) }}
                                </span>
                            </td>
                            <td class="px-4 py-2">€{{ number_format($encomenda->total, 2, ',', '.') }}</td>
                            <td class="px-4 py-2">{{ $encomenda->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-2">
                                @if($encomenda->livros->isEmpty())
                                    <span class="text-gray-500">—</span>
                                @else
                                    <ul class="list-disc pl-4">
                                        @foreach ($encomenda->livros as $livro)
                                            <li>
                                                {{ $livro->nome }} × {{ $livro->pivot->quantidade }}
                                                (€{{ number_format($livro->pivot->preco_unitario, 2, ',', '.') }})
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
