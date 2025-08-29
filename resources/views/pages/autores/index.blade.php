@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-4">👤 Lista de Autores</h2>

<a href="{{ route('home') }}" class="btn btn-outline btn-secondary mb-4">⬅️ Voltar</a>

<form method="GET" action="{{ route('autores.index') }}" class="mb-6 flex flex-wrap gap-4 items-center">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="🔍 Pesquisar autor"
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
        <a href="{{ route('autores.create') }}" class="btn btn-success mb-4">➕ Criar Autor</a>
    @endif
@endauth

<table class="table table-zebra w-full">
    <thead>
        <tr>
            <th>Foto</th>
            <th><a href="?sort=nome&direction={{ $direction === 'asc' ? 'desc' : 'asc' }}">Nome</a></th>
            <th><a href="?sort=livros_count&direction={{ $direction === 'asc' ? 'desc' : 'asc' }}">Nº de Livros</a></th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        @foreach($autores as $autor)
        <tr>
            <td>
                @if($autor->foto)
                    <a href="{{ asset('storage/'.$autor->foto) }}" target="_blank">
                        <img src="{{ asset('storage/'.$autor->foto) }}" alt="{{ $autor->nome }}" class="w-12 h-12 object-cover rounded-full hover:opacity-80 transition">
                    </a>
                @else
                    —
                @endif
            </td>
            <td>{{ $autor->nome }}</td>
            <td>{{ $autor->livros_count }}</td>
            <td class="flex gap-2">
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('autores.edit', $autor) }}" class="btn btn-sm btn-warning">✏️ Editar</a>
                        <form action="{{ route('autores.destroy', $autor) }}" method="POST" onsubmit="return confirm('Tem a certeza?')">
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
    {{ $autores->links() }}
</div>
@endsection
