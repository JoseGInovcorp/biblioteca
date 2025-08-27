@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-4">🏢 Lista de Editoras</h2>

<a href="{{ route('home') }}" class="btn btn-outline btn-secondary mb-4">⬅️ Voltar</a>

@auth
    @if(auth()->user()->isAdmin())
        <a href="{{ route('editoras.create') }}" class="btn btn-success mb-4">➕ Criar Editora</a>
    @endif
@endauth

<form method="GET" class="flex gap-2 mb-4">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Pesquisar editora..." class="input input-bordered" />
    <button class="btn btn-primary">Filtrar</button>
</form>

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
