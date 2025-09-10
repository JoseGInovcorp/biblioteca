@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-4">📖 {{ $livro->nome }}</h2>

<a href="{{ url()->previous() }}" class="btn btn-outline btn-secondary mb-4">⬅️ Voltar</a>

@php
    $disponivel = !$livro->requisicoes()->where('status', 'ativa')->exists();
@endphp

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    {{-- Coluna 1: Capa --}}
    <div>
        @if($livro->imagem_capa)
            <img src="{{ asset('storage/' . $livro->imagem_capa) }}"
                 alt="Capa de {{ $livro->nome }}"
                 class="w-full max-w-xs mx-auto mb-6">
        @endif
    </div>

    {{-- Coluna 2 e 3: Detalhes + Descrição + Opiniões --}}
    <div class="md:col-span-2">
        <p><strong>ISBN:</strong> {{ $livro->isbn }}</p>
        <p><strong>Editora:</strong> {{ $livro->editora->nome }}</p>
        <p><strong>Géneros:</strong>
            @if($livro->generos->isNotEmpty())
                {{ $livro->generos->pluck('nome')->join(', ') }}
            @else
                —
            @endif
        </p>
        <p><strong>Autores:</strong>
            @foreach($livro->autores as $autor)
                {{ $autor->nome }}@if(!$loop->last), @endif
            @endforeach
        </p>
        <p><strong>Preço:</strong> €{{ number_format($livro->preco, 2, ',', '.') }}</p>

        <p class="mt-2"><strong>Disponibilidade:</strong>
            @if($disponivel)
                <span class="badge badge-success">✅ Disponível</span>
            @else
                <span class="badge badge-error">❌ Indisponível</span>
            @endif
        </p>

        @auth
            @if(auth()->user()->isCidadao())
                @if($disponivel)
                    <a href="{{ route('requisicoes.create', ['livro_id' => $livro->id]) }}" class="btn btn-success mt-2">📦 Requisitar</a>
                @else
                    <button class="btn btn-disabled mt-2" disabled>📦 Indisponível</button>

                    @php
                        $alerta = $livro->alertas()->where('user_id', auth()->id())->latest()->first();
                        $mostrarBotaoAlerta = !$alerta || $alerta->notificado_em !== null;
                    @endphp

                    @if($mostrarBotaoAlerta)
                        <form method="POST" action="{{ route('alertas.store', $livro) }}" class="mt-2">
                            @csrf
                            <button class="btn btn-warning">🔔 Avisar-me quando disponível</button>
                        </form>
                    @else
                        <p class="text-sm text-yellow-600 mt-2">Já será notificado quando este livro estiver disponível.</p>
                    @endif

                @endif
            @endif
        @endauth

        @if(!empty($livro->descricao))
            <div class="mt-6">
                <h3 class="text-lg font-semibold">Descrição</h3>
                <p class="mt-2 text-gray-700 whitespace-pre-line">
                    {{ $livro->descricao }}
                </p>
            </div>
        @endif

        <div class="mt-6">
            <h3 class="text-lg font-semibold">Opiniões dos leitores</h3>
            @if($livro->reviews->count())
                @foreach($livro->reviews as $review)
                    <div class="mb-4 border-b pb-2">
                        <strong>{{ $review->user->name }}</strong>
                        <small class="text-gray-500">— {{ $review->created_at->format('d/m/Y') }}</small>
                        <p>{{ $review->comentario }}</p>
                    </div>
                @endforeach
            @else
                <p class="text-gray-500 italic">Ainda não existem reviews para este livro.</p>
            @endif
        </div>
    </div>
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

@if(isset($relacionados) && $relacionados->count())
    <div class="mt-8">
        <h3 class="text-xl font-semibold mb-3">📚 Livros Relacionados</h3>

        @php
            $mesmoAutor = $relacionados->filter(function($rel) use ($livro) {
                return $rel->autores->pluck('id')->intersect($livro->autores->pluck('id'))->isNotEmpty();
            });
            $mesmoTema = $relacionados->reject(function($rel) use ($livro) {
                return $rel->autores->pluck('id')->intersect($livro->autores->pluck('id'))->isNotEmpty();
            });
        @endphp

        @if($mesmoAutor->count())
            <h4 class="text-lg font-semibold mt-4 mb-2">✍️ Do mesmo autor</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($mesmoAutor as $rel)
                    <a href="{{ route('livros.show', $rel) }}" class="p-3 border rounded hover:shadow transition">
                        <div class="flex gap-3">
                            @if($rel->imagem_capa)
                                <img src="{{ asset('storage/' . $rel->imagem_capa) }}"
                                     alt="Capa de {{ $rel->nome }}"
                                     class="w-16 h-24 object-cover rounded">
                            @endif
                            <div>
                                <div class="font-semibold">{{ $rel->nome }}</div>
                                <div class="text-sm text-gray-500">
                                    {{ optional($rel->editora)->nome }}
                                    @if($rel->autores->isNotEmpty())
                                        • {{ $rel->autores->pluck('nome')->join(', ') }}
                                    @endif
                                    @if($rel->generos->isNotEmpty())
                                        • {{ $rel->generos->pluck('nome')->join(', ') }}
                                    @endif
                                </div>
                                @php
                                    $overlap = array_values(array_intersect($livro->keywords ?? [], $rel->keywords ?? []));
                                @endphp
                                @if(!empty($overlap))
                                    <div class="mt-1 text-xs text-gray-400">
                                        {{ implode(' · ', array_slice($overlap, 0, 5)) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif

        @if($mesmoTema->count())
            <h4 class="text-lg font-semibold mt-6 mb-2">📌 Semelhantes no tema</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($mesmoTema as $rel)
                    <a href="{{ route('livros.show', $rel) }}" class="p-3 border rounded hover:shadow transition">
                        <div class="flex gap-3">
                            @if($rel->imagem_capa)
                                <img src="{{ asset('storage/' . $rel->imagem_capa) }}"
                                     alt="Capa de {{ $rel->nome }}"
                                     class="w-16 h-24 object-cover rounded">
                            @endif
                            <div>
                                <div class="font-semibold">{{ $rel->nome }}</div>
                                <div class="text-sm text-gray-500">
                                    {{ optional($rel->editora)->nome }}
                                    @if($rel->autores->isNotEmpty())
                                        • {{ $rel->autores->pluck('nome')->join(', ') }}
                                    @endif
                                    @if($rel->generos->isNotEmpty())
                                        • {{ $rel->generos->pluck('nome')->join(', ') }}
                                    @endif
                                </div>
                                @php
                                    $overlap = array_values(array_intersect($livro->keywords ?? [], $rel->keywords ?? []));
                                @endphp
                                @if(!empty($overlap))
                                    <div class="mt-1 text-xs text-gray-400">
                                        {{ implode(' · ', array_slice($overlap, 0, 5)) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
@endif

@endsection