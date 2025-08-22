@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-4">👤 Lista de Autores</h2>

<a href="{{ route('home') }}" class="btn btn-outline btn-secondary mb-4">⬅️ Voltar</a>
<a href="{{ route('autores.create') }}" class="btn btn-success mb-4">➕ Criar Autor</a>

<form method="GET" class="flex gap-2 mb-4">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Pesquisar autor..." class="input input-bordered" />
    <button class="btn btn-primary">Filtrar</button>
</form>

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
                <a href="{{ route('autores.edit', $autor) }}" class="btn btn-sm btn-warning">✏️ Editar</a>
                <form action="{{ route('autores.destroy', $autor) }}" method="POST" onsubmit="return confirm('Tem a certeza?')">
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
    {{ $autores->links() }}
</div>
@endsection