@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-4">👥 Lista de Utilizadores</h2>

<a href="{{ route('home') }}" class="btn btn-outline btn-secondary mb-4">⬅️ Voltar</a>

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
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="mt-4">
    {{ $users->links() }}
</div>
@endsection
