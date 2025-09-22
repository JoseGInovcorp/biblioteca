@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-4">👥 Lista de Utilizadores</h2>

<a href="{{ route('home') }}" class="btn btn-outline btn-secondary mb-4">⬅️ Voltar</a>

{{-- Botão apenas para Admins --}}
@if(auth()->user()->isAdmin())
    <a href="{{ route('users.create') }}" class="btn btn-success mb-4">➕ Novo Utilizador</a>
@endif

<table class="table table-zebra w-full">
    <thead>
        <tr>
            <th>Nome</th>
            <th>Email</th>
            <th>Perfil</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ ucfirst($user->role) }}</td>
            <td class="flex gap-2">
                <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-info">👁️ Ver</a>

                @if(auth()->user()->isAdmin() && auth()->id() !== $user->id)
                    <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Tem a certeza que quer apagar este utilizador?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-error">🗑️ Apagar</button>
                    </form>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="mt-4">
    {{ $users->links() }}
</div>
@endsection
