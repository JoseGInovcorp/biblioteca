@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-4">📚 Lista de Livros</h2>

<a href="{{ route('home') }}" class="btn btn-outline btn-secondary mb-4">⬅️ Voltar</a>

<table class="table table-zebra w-full">
    <thead>
        <tr>
            <th>Nome</th>
            <th>Editora</th>
            <th>Autores</th>
            <th>Preço</th>
            <th>Disponibilidade</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        @foreach($livros as $livro)
        @php
            $disponivel = !$livro->requisicoes()->where('status', 'ativa')->exists();
        @endphp
        <tr>
            <td>{{ $livro->nome }}</td>
            <td>{{ $livro->editora->nome }}</td>
            <td>
                @foreach($livro->autores as $autor)
                    {{ $autor->nome }}@if(!$loop->last), @endif
                @endforeach
            </td>
            <td>€{{ number_format($livro->preco, 2, ',', '.') }}</td>
            <td>
                @if($disponivel)
                    <span class="badge badge-success">✅ Disponível</span>
                @else
                    <span class="badge badge-error">❌ Indisponível</span>
                @endif
            </td>
            <td class="flex gap-2">
                <a href="{{ route('livros.show', $livro) }}" class="btn btn-sm btn-info">👁️ Ver</a>

                @auth
                    @if(auth()->user()->isCidadao())
                        @if($disponivel)
                            <a href="{{ route('requisicoes.create', ['livro_id' => $livro->id]) }}"
                               class="btn btn-sm btn-success">📦 Requisitar</a>
                        @else
                            <button class="btn btn-sm btn-disabled" disabled>📦 Indisponível</button>
                        @endif
                    @endif
                @endauth
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="mt-4">
    {{ $livros->links() }}
</div>
@endsection