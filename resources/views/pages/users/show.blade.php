@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-4">👤 Detalhes do Cidadão</h2>

<a href="{{ route('users.index') }}" class="btn btn-outline btn-secondary mb-4">⬅️ Voltar</a>

<div class="mb-4">
    <p><strong>Nome:</strong> {{ $user->name }}</p>
    <p><strong>Email:</strong> {{ $user->email }}</p>
    <p><strong>Perfil:</strong> {{ ucfirst($user->role) }}</p>
</div>

<h3 class="text-xl font-semibold mt-6 mb-2">📦 Histórico de Requisições</h3>

@if($user->requisicoes->isEmpty())
    <p class="text-gray-500 italic">Este cidadão ainda não fez requisições.</p>
@else
    <table class="table table-sm w-full">
        <thead>
            <tr>
                <th>#</th>
                <th>Livro</th>
                <th>Início</th>
                <th>Fim Prevista</th>
                <th>Fim Real</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($user->requisicoes as $req)
                <tr>
                    <td>{{ $req->numero_sequencial }}</td>
                    <td>
                        @if($req->livro)
                            <a href="{{ route('livros.show', $req->livro) }}" class="link link-primary">
                                {{ $req->livro->nome }}
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
