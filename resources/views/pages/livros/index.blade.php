@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-4">📘 Lista de Livros</h2>

<a href="{{ route('home') }}" class="btn btn-outline btn-secondary mb-4">⬅️ Voltar</a>
<a href="{{ route('livros.create') }}" class="btn btn-success mb-4">➕ Criar Livro</a>

<form method="GET" class="flex gap-2 mb-4">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Pesquisar..." class="input input-bordered" />
    <select name="editora_id" class="select select-bordered">
        <option value="">Todas as Editoras</option>
        @foreach($editoras as $editora)
            <option value="{{ $editora->id }}" @selected(request('editora_id') == $editora->id)>{{ $editora->nome }}</option>
        @endforeach
    </select>
    <select name="autor_id" class="select select-bordered">
        <option value="">Todos os Autores</option>
        @foreach($autores as $autor)
            <option value="{{ $autor->id }}" @selected(request('autor_id') == $autor->id)>{{ $autor->nome }}</option>
        @endforeach
    </select>
    <button class="btn btn-primary">Filtrar</button>
    <a href="{{ route('livros.exportar') }}" class="btn btn-accent">Exportar Excel</a>
</form>

<table class="table table-zebra w-full">
    <thead>
        <tr>
            <th>Capa</th>
            <th><a href="?sort=nome&direction={{ $direction === 'asc' ? 'desc' : 'asc' }}">Nome</a></th>
            <th>ISBN</th>
            <th>Editora</th>
            <th>Autores</th>
            <th>Preço</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        @foreach($livros as $livro)
        <tr>
            <td>
                @if($livro->imagem_capa)
                    <a href="{{ asset('storage/'.$livro->imagem_capa) }}" target="_blank">
                        <img src="{{ asset('storage/'.$livro->imagem_capa) }}" alt="Capa" class="w-12 h-16 object-cover hover:opacity-80 transition">
                    </a>
                @else
                    —
                @endif
            </td>
            <td>{{ $livro->nome }}</td>
            <td>{{ $livro->isbn }}</td>
            <td>{{ $livro->editora->nome ?? '—' }}</td>
            <td>{{ $livro->autores->pluck('nome')->implode(', ') }}</td>
            <td>{{ number_format($livro->preco, 2, ',', '.') }} €</td>
            <td class="flex gap-2">
                <a href="{{ route('livros.edit', $livro) }}" class="btn btn-sm btn-warning">✏️ Editar</a>
                <form action="{{ route('livros.destroy', $livro) }}" method="POST" onsubmit="return confirm('Tem a certeza?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-error">🗑️ Apagar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="mt-4">
    {{ $livros->links() }}
</div>
@endsection
