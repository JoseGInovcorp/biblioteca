@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-4">🏢 Lista de Editoras</h2>

<a href="{{ route('home') }}" class="btn btn-outline btn-secondary mb-4">⬅️ Voltar</a>

<form method="GET" action="{{ route('editoras.index') }}" class="mb-6 flex flex-wrap gap-4 items-center">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="🔍 Pesquisar editora"
        class="input input-bordered w-full md:w-1/3" />

    <select name="sort" class="select select-bordered w-full md:w-1/4">
        <option value="nome" @selected(request('sort') == 'nome')>Ordenar por Nome</option>
        <option value="created_at" @selected(request('sort') == 'created_at')>Mais Recentes</option>
    </select>

    <select name="direction" class="select select-bordered w-full md:w-1/4">
        <option value="asc" @selected(request('direction') == 'asc')>Ascendente</option>
        <option value="desc" @selected(request('direction') == 'desc')>Descendente</option>
    </select>

    <button type="submit" class="btn btn-primary w-full md:w-auto">🔍 Filtrar</button>
</form>

@auth
    @if(auth()->user()->isAdmin())
        <a href="{{ route('editoras.create') }}" class="btn btn-success mb-4">➕ Criar Editora</a>
    @endif
@endauth

<table class="table table-zebra w-full">
    <thead>
        <tr>
            <th>Logótipo</th>
            <th><a href="?sort=nome&direction={{ $direction === 'asc' ? 'desc' : 'asc' }}">Nome</a></th>
            <th><a href="?sort=livros_count&direction={{ $direction === 'asc' ? 'desc' : 'asc' }}">Nº de Livros</a></th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        @foreach($editoras as $editora)
        <tr>
            <td>
                @if($editora->logotipo)
                    <a href="{{ asset('storage/'.$editora->logotipo) }}" target="_blank">
                        <img src="{{ asset('storage/'.$editora->logotipo) }}" alt="{{ $editora->nome }}" class="w-12 h-12 object-cover rounded hover:opacity-80 transition">
                    </a>
                @else
                    —
                @endif
            </td>
            <td>{{ $editora->nome }}</td>
            <td>{{ $editora->livros_count }}</td>
            <td class="flex gap-2">
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('editoras.edit', $editora) }}" class="btn btn-sm btn-warning">✏️ Editar</a>
                        <form action="{{ route('editoras.destroy', $editora) }}" method="POST" onsubmit="return confirm('Tem a certeza?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-error">🗑️ Apagar</button>
                        </form>
                    @endif
                @endauth
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="mt-4">
    {{ $editoras->links() }}
</div>
@endsection
