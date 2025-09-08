@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-4">📖 {{ $livro->nome }}</h2>

<a href="{{ route('livros.index') }}" class="btn btn-outline btn-secondary mb-4">⬅️ Voltar</a>

@php
    $disponivel = !$livro->requisicoes()->where('status', 'ativa')->exists();
@endphp

<div class="mb-4">
    <p><strong>ISBN:</strong> {{ $livro->isbn }}</p>
    <p><strong>Editora:</strong> {{ $livro->editora->nome }}</p>
    <p><strong>Autores:</strong>
        @foreach($livro->autores as $autor)
            {{ $autor->nome }}@if(!$loop->last), @endif
        @endforeach
    </p>
    <p><strong>Preço:</strong> €{{ number_format($livro->preco, 2, ',', '.') }}</p>

    {{-- Estado de disponibilidade --}}
    <p><strong>Disponibilidade:</strong>
        @if($disponivel)
            <span class="badge badge-success">✅ Disponível</span>
        @else
            <span class="badge badge-error">❌ Indisponível</span>
        @endif
    </p>

    {{-- Botão requisitar (apenas cidadãos e se disponível) --}}
    @auth
        @if(auth()->user()->isCidadao())
            @if($disponivel)
                <a href="{{ route('requisicoes.create', ['livro_id' => $livro->id]) }}" class="btn btn-success mt-2">📦 Requisitar</a>
            @else
                <button class="btn btn-disabled mt-2" disabled>📦 Indisponível</button>
            @endif
        @endif
    @endauth

    @if($livro->imagem_capa)
        <img src="{{ asset('storage/' . $livro->imagem_capa) }}" alt="Capa de {{ $livro->nome }}">
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

{{-- 📢 Opiniões dos leitores --}}
@if($livro->reviews->count())
    <h3 class="text-xl font-semibold mt-6 mb-2">💬 Opiniões dos leitores</h3>
    @foreach($livro->reviews as $review)
        <div class="mb-4 border-b pb-2">
            <strong>{{ $review->user->name }}</strong>
            <small class="text-gray-500">— {{ $review->created_at->format('d/m/Y') }}</small>
            <p>{{ $review->comentario }}</p>
        </div>
    @endforeach
@else
    <h3 class="text-xl font-semibold mt-6 mb-2">💬 Opiniões dos leitores</h3>
    <p class="text-gray-500 italic">Ainda não existem reviews para este livro.</p>
@endif

@endsection
