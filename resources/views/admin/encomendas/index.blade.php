@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto mt-10">
    <h2 class="text-2xl font-bold mb-6 text-primary">📦 Encomendas</h2>

    <table class="table-auto w-full border">
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
                    <td class="px-4 py-2">{{ $encomenda->user->name }}</td>
                    <td class="px-4 py-2">
                        @if ($encomenda->estado === 'paga')
                            <span class="text-green-600 font-semibold">Paga</span>
                        @else
                            <span class="text-yellow-600 font-semibold">Pendente</span>
                        @endif
                    </td>
                    <td class="px-4 py-2">€{{ number_format($encomenda->total, 2, ',', '.') }}</td>
                    <td class="px-4 py-2">{{ $encomenda->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-2">
                        <ul class="list-disc pl-4">
                            @foreach ($encomenda->livros as $livro)
                                <li>
                                    {{ $livro->nome }} × {{ $livro->pivot->quantidade }}
                                    (€{{ number_format($livro->pivot->preco_unitario, 2, ',', '.') }})
                                </li>
                            @endforeach
                        </ul>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection