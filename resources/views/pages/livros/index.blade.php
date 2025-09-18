@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-4">📚 Lista de Livros</h2>

<a href="{{ route('home') }}" class="btn btn-outline btn-secondary mb-4">⬅️ Voltar</a>

<form method="GET" action="{{ route('livros.index') }}" class="mb-6 flex flex-wrap gap-4 items-center">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="🔍 Pesquisar título ou ISBN"
        class="input input-bordered w-full md:w-1/3" />

    <select name="editora_id" class="select select-bordered w-full md:w-1/4">
        <option value="">Todas as Editoras</option>
        @foreach($editoras as $editora)
            <option value="{{ $editora->id }}" @selected(request('editora_id') == $editora->id)>
                {{ $editora->nome }}
            </option>
        @endforeach
    </select>

    <select name="autor_id" class="select select-bordered w-full md:w-1/4">
        <option value="">Todos os Autores</option>
        @foreach($autores as $autor)
            <option value="{{ $autor->id }}" @selected(request('autor_id') == $autor->id)>
                {{ $autor->nome }}
            </option>
        @endforeach
    </select>

    <select name="sort" class="select select-bordered w-full md:w-1/5">
        <option value="nome" @selected(request('sort') == 'nome')>Ordenar por Nome</option>
        <option value="preco" @selected(request('sort') == 'preco')>Ordenar por Preço</option>
        <option value="created_at" @selected(request('sort') == 'created_at')>Mais Recentes</option>
    </select>

    <select name="direction" class="select select-bordered w-full md:w-1/5">
        <option value="asc" @selected(request('direction') == 'asc')>Ascendente</option>
        <option value="desc" @selected(request('direction') == 'desc')>Descendente</option>
    </select>

    <button type="submit" class="btn btn-primary w-full md:w-auto">🔍 Filtrar</button>
</form>

@auth
    @if(auth()->user()->isAdmin())
        <div class="flex gap-2 mb-4">
            <a href="{{ route('livros.create') }}" class="btn btn-success">➕ Criar Livro</a>
            <a href="{{ route('livros.exportar') }}" class="btn btn-outline btn-info">📤 Exportar para Excel</a>
            <a href="{{ route('google-books.index') }}" class="btn btn-outline btn-warning">🔍 Google Books</a>
        </div>
    @endif
@endauth

<div x-data="{ aberto: null }">
    <table class="table table-zebra w-full">
    <thead>
        <tr>
            <th>Capa</th>
            <th>Nome / Descrição</th>
            <th>Editora</th>
            <th>Autores</th>
            <th>Requisição (€)</th>
            <th>Venda (€)</th>
            <th>Stock</th>
            <th>Disponibilidade</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        @foreach($livros as $livro)
        @php
            $disponivel = !$livro->requisicoes()->where('status', 'ativa')->exists();
        @endphp
        <tr @click="aberto = (aberto === {{ $livro->id }}) ? null : {{ $livro->id }}" class="cursor-pointer hover:bg-gray-100">
            <td>
                @if($livro->imagem_capa)
                    <a href="{{ asset('storage/' . $livro->imagem_capa) }}" target="_blank">
                        <img src="{{ asset('storage/' . $livro->imagem_capa) }}"
                            alt="Capa de {{ $livro->nome }}"
                            class="w-16 h-24 object-cover rounded shadow">
                    </a>
                @else
                    <span class="text-gray-400 italic">Sem capa</span>
                @endif
            </td>

            <td>
                <div class="font-semibold">{{ $livro->nome }}</div>
                @if(!empty($livro->descricao))
                    <div class="text-sm text-gray-500">
                        {{ Str::limit(strip_tags($livro->descricao), 80, '...') }}
                    </div>
                @endif
            </td>

            <td>{{ $livro->editora->nome }}</td>
            <td>
                @foreach($livro->autores as $autor)
                    {{ $autor->nome }}@if(!$loop->last), @endif
                @endforeach
            </td>

            <td>€{{ number_format($livro->preco, 2, ',', '.') }}</td>
            <td>€{{ number_format($livro->preco_venda, 2, ',', '.') }}</td>
            <td>{{ $livro->stock_venda }}</td>

            <td>
                @if($disponivel)
                    <span class="badge badge-success">✅ Requisição disponível</span>
                @else
                    <span class="badge badge-error">❌ Requisição indisponível</span>
                @endif

                @if($livro->stock_venda > 0)
                    <span class="badge badge-info mt-1">🛒 Venda disponível</span>
                @else
                    <span class="badge badge-neutral mt-1">Sem stock de venda</span>
                @endif
            </td>

            <td class="min-w-[140px]">
                <div class="flex gap-2 items-center" @click.stop>
                    <a href="{{ route('livros.show', ['livro' => $livro->id, 'from' => request()->fullUrl()]) }}" class="btn btn-sm btn-info">👁️ Ver</a>

                    @auth
                        @if(auth()->user()->isCidadao())
                            @if($disponivel)
                                <a href="{{ route('requisicoes.create', ['livro_id' => $livro->id]) }}"
                                   class="btn btn-sm btn-success">📦 Requisitar</a>
                            @else
                                <button class="btn btn-sm btn-disabled" disabled>📦 Indisponível</button>
                            @endif
                        @endif

                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('livros.edit', ['livro' => $livro->id, 'page' => request('page')]) }}" class="btn btn-sm btn-primary">✏️ Editar</a>
                        @endif
                    @endauth
                </div>
            </td>
        </tr>

        @auth
            @if(auth()->user()->isAdmin())
                <tr x-show="aberto === {{ $livro->id }}" class="bg-gray-50">
                    <td colspan="9" class="p-4">
                        {{-- Erros de validação inline --}}
                        @if ($errors->any())
                            <div class="mb-2 p-2 bg-red-100 text-red-700 rounded">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <form method="POST" action="{{ route('livros.update', ['livro' => $livro->id, 'page' => request('page', 1)]) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="page" value="{{ request('page', 1) }}">
                            <input type="hidden" name="inline" value="1">

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="label">Preço de requisição (€)</label>
                                    <input type="number" step="0.01" name="preco"
                                           value="{{ old('preco', $livro->preco) }}"
                                           class="input input-bordered w-full" required>
                                </div>

                                <div>
                                    <label class="label">Preço de venda (€)</label>
                                    <input type="number" step="0.01" name="preco_venda"
                                           value="{{ old('preco_venda', $livro->preco_venda) }}"
                                           class="input input-bordered w-full" required>
                                </div>

                                <div>
                                    <label class="label">Stock para venda</label>
                                    <input type="number" name="stock_venda"
                                           value="{{ old('stock_venda', $livro->stock_venda) }}"
                                           class="input input-bordered w-full" min="0" required>
                                </div>
                            </div>

                            <div class="mt-4 flex gap-2" @click.stop>
                                <button type="submit" class="btn btn-primary">💾 Guardar</button>
                                <button type="button" class="btn btn-outline" @click="aberto = null">❌ Fechar</button>
                            </div>
                        </form>
                    </td>
                </tr>
            @endif
        @endauth
        @endforeach
    </tbody>
</table>
</div>

<div class="mt-4">
    {{ $livros->links() }}
</div>
@endsection