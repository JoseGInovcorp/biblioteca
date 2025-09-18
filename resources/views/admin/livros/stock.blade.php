@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto mt-10">
    <h2 class="text-2xl font-bold mb-6 text-warning">📉 Livros com Stock Baixo</h2>

    @if($livros->isEmpty())
        <p class="text-gray-500">Todos os livros têm stock suficiente.</p>
    @else
        <table class="table w-full">
            <thead>
                <tr class="bg-base-200">
                    <th>Livro</th>
                    <th>Stock</th>
                    <th>Autor</th>
                    <th>Editora</th>
                </tr>
            </thead>
            <tbody>
                @foreach($livros as $livro)
                    <tr>
                        <td>{{ $livro->nome }}</td>
                        <td class="{{ $livro->stock_venda === 0 ? 'text-error font-bold' : 'text-warning' }}">
                            {{ $livro->stock_venda }}
                        </td>
                        <td>{{ $livro->autor->nome ?? '—' }}</td>
                        <td>{{ $livro->editora->nome ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
