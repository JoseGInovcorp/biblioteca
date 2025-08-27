@extends('layouts.app')

@section('content')
<div class="flex justify-center items-center min-h-[70vh]">
    <div class="w-full max-w-md">
        <h2 class="text-2xl font-bold mb-4">🔐 Iniciar Sessão</h2>

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block font-semibold">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="input input-bordered w-full">
                @error('email') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password" class="block font-semibold">Password</label>
                <input id="password" type="password" name="password" required class="input input-bordered w-full">
                @error('password') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="remember">
                    <span>Lembrar-me</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="link link-primary text-sm" href="{{ route('password.request') }}">
                        Esqueceu-se da password?
                    </a>
                @endif
            </div>

            <button type="submit" class="btn btn-primary w-full">Entrar</button>

            <p class="mt-4 text-sm text-center">
                Ainda não tem conta?
                <a href="{{ route('register') }}" class="link link-secondary font-semibold">Criar conta de Cidadão</a>
            </p>
        </form>
    </div>
</div>
@endsection
