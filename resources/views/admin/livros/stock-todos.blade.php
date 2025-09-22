@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto mt-10">
    <h2 class="text-2xl font-bold mb-6 text-warning">📦 Gestão de Stock — Todos os Livros</h2>

    <a href="{{ route('home') }}" 
    class="btn btn-outline btn-primary mb-4">
        ⬅️ Voltar ao Menu
    </a>

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
                    <th class="w-40">Ações</th>
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
                        <td>
                            <form action="{{ route('admin.livros.stock.update', $livro) }}" method="POST" class="flex items-center gap-2">
                                @csrf
                                @method('PUT')
                                <input type="number" name="stock_venda" value="{{ $livro->stock_venda }}" min="0" class="input input-sm input-bordered w-20">
                                <button type="submit" class="btn btn-sm btn-primary">💾</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
