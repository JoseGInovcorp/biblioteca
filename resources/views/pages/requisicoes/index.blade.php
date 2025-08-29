@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-4">📦 Lista de Requisições</h2>

@if(auth()->user()->isAdmin())
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="card bg-base-100 shadow-md text-center">
            <div class="card-body p-4">
                <h3 class="font-bold text-lg">Requisições Ativas</h3>
                <p class="text-3xl font-extrabold text-primary">{{ $totalAtivas }}</p>
            </div>
        </div>
        <div class="card bg-base-100 shadow-md text-center">
            <div class="card-body p-4">
                <h3 class="font-bold text-lg">Últimos 30 dias</h3>
                <p class="text-3xl font-extrabold text-primary">{{ $ultimos30Dias }}</p>
            </div>
        </div>
        <div class="card bg-base-100 shadow-md text-center">
            <div class="card-body p-4">
                <h3 class="font-bold text-lg">Livros entregues hoje</h3>
                <p class="text-3xl font-extrabold text-primary">{{ $entreguesHoje }}</p>
            </div>
        </div>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-error mb-4">
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<a href="{{ route('home') }}" class="btn btn-outline btn-secondary mb-4">⬅️ Voltar</a>

@auth
    <a href="{{ route('requisicoes.create') }}" class="btn btn-success mb-4">➕ Nova Requisição</a>
@endauth

<form method="GET" action="{{ route('requisicoes.index') }}" class="mb-4 flex items-center gap-4">
    <label for="status" class="font-semibold">Filtrar por status:</label>
    <select name="status" id="status" class="select select-bordered" onchange="this.form.submit()">
        <option value="">Todos</option>
        <option value="ativa" @selected(request('status') === 'ativa')>Ativa</option>
        <option value="entregue" @selected(request('status') === 'entregue')>Entregue</option>
    </select>
</form>

<table class="table table-zebra w-full">
    <thead>
        <tr>
            <th>#</th>
            <th>Livro</th>
            <th>Cidadão</th>
            <th>Início</th>
            <th>Fim Previsto</th>
            <th>Status</th>
            @if(auth()->user()->isAdmin())
                <th>Ações</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach($requisicoes as $req)
        <tr>
            <td>{{ $req->numero_sequencial }}</td>
            <td>{{ $req->livro->nome ?? 'Removido' }}</td>
            <td>
                @if($req->cidadao)
                    <a href="{{ route('users.show', $req->cidadao) }}" class="link link-primary">{{ $req->cidadao->name }}</a>
                @else
                    <span class="text-gray-500 italic">Removido</span>
                @endif
            </td>
            <td>{{ $req->data_inicio }}</td>
            <td>{{ $req->data_fim_prevista }}</td>
            <td>{{ ucfirst($req->status) }}</td>
            @if(auth()->user()->isAdmin())
                <td class="flex gap-2">
                    @if($req->status === 'ativa')
                        <a href="{{ route('requisicoes.edit', $req) }}" class="btn btn-sm btn-warning">📥 Receber Livro</a>
                    @endif
                    
                    <form action="{{ route('requisicoes.destroy', $req) }}" method="POST" onsubmit="return confirm('Tem a certeza?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-error">🗑️ Apagar</button>
                    </form>
                </td>
            @endif
        </tr>
        @endforeach
    </tbody>
</table>

<div class="mt-4">
    {{ $requisicoes->links() }}
</div>
@endsection
