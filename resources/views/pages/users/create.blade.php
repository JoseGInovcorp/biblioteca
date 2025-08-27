@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-4">➕ Criar Novo Utilizador</h2>

<a href="{{ route('users.index') }}" class="btn btn-outline btn-secondary mb-4">⬅️ Voltar</a>

<form action="{{ route('users.store') }}" method="POST" class="space-y-4 max-w-lg">
    @csrf

    <div>
        <label for="name" class="block font-semibold">Nome</label>
        <input type="text" name="name" id="name" class="input input-bordered w-full" value="{{ old('name') }}" required>
        @error('name') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="email" class="block font-semibold">Email</label>
        <input type="email" name="email" id="email" class="input input-bordered w-full" value="{{ old('email') }}" required>
        @error('email') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="password" class="block font-semibold">Password</label>
        <input type="password" name="password" id="password" class="input input-bordered w-full" required>
        @error('password') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="password_confirmation" class="block font-semibold">Confirmar Password</label>
        <input type="password" name="password_confirmation" id="password_confirmation" class="input input-bordered w-full" required>
    </div>

    <div>
        <label for="role" class="block font-semibold">Perfil</label>
        <select name="role" id="role" class="select select-bordered w-full" required>
            <option value="cidadao" @selected(old('role') === 'cidadao')>Cidadão</option>
            <option value="admin" @selected(old('role') === 'admin')>Admin</option>
        </select>
        @error('role') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
    </div>

    <button type="submit" class="btn btn-primary">💾 Guardar</button>
</form>
@endsection
