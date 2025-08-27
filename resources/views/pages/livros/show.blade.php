@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-4">📖 {{ $livro->nome }}</h2>

<a href="{{ route('livros.index') }}" class="btn btn-outline btn-secondary mb-4">⬅️ Voltar</a>

<div class="mb-4">
    <p><strong>ISBN:</strong> {{ $livro->isbn }}</p>
    <p><strong>Editora:</strong> {{ $livro->editora->nome }}</p>
    <p><strong>Autores:</strong>
        @foreach($livro->autores as $autor)
            {{ $autor->nome }}@if(!$loop->last), @endif
        @endforeach
    </p>
    <p><strong>Preço:</strong> €{{ number_format($livro->preco, 2, ',', '.') }}</p>
    @if($livro->imagem_capa)
        <div class="mt-2">
            <img src="{{ asset('storage/'.$livro->imagem_capa) }}" alt="Capa do livro" class="w-32 h-auto">
        </div>
    @endif
</div>

<h3 class="text-xl font-semibold mt-6 mb-2">📚 Histórico de Requisições</h3>

@if($livro->requisicoes->isEmpty())
    <p class="text-gray-500 italic">Este livro ainda não foi requisitado.</p>
@else
    <table class="table table-sm w-full">
        <thead>
            <tr>
                <th>#</th>
                <th>Cidadão</th>
                <th>Início</th>
                <th>Fim Prevista</th>
                <th>Fim Real</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($livro->requisicoes as $req)
                <tr>
                    <td>{{ $req->numero_sequencial }}</td>
                    <td>
                        @if($req->cidadao)
                            <a href="{{ route('users.show', $req->cidadao) }}" class="link link-primary">
                                {{ $req->cidadao->name }}
                            </a>
                        @else
                            <span class="text-gray-500 italic">Removido</span>
                        @endif
                    </td>
                    <td>{{ $req->data_inicio }}</td>
                    <td>{{ $req->data_fim_prevista }}</td>
                    <td>{{ $req->data_fim_real ?? '—' }}</td>
                    <td>{{ ucfirst($req->status) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
@endsection
