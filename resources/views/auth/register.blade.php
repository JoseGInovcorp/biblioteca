@extends('layouts.app')

@section('content')
<div class="flex justify-center items-center min-h-[70vh]">
    <div class="w-full max-w-md">
        <h2 class="text-2xl font-bold mb-4">🆕 Criar Conta de Cidadão</h2>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block font-semibold">Nome</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus class="input input-bordered w-full">
                @error('name') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="email" class="block font-semibold">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required class="input input-bordered w-full">
                @error('email') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password" class="block font-semibold">Password</label>
                <input id="password" type="password" name="password" required class="input input-bordered w-full">
                @error('password') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block font-semibold">Confirmar Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required class="input input-bordered w-full">
            </div>

            <button type="submit" class="btn btn-primary w-full">Criar Conta</button>
        </form>
    </div>
</div>
@endsection
