@extends('layouts.app')

@section('content')
<div class="flex justify-center items-center min-h-[70vh]">
    <div class="w-full max-w-md">
        <h2 class="text-2xl font-bold mb-4">🔑 Recuperar Password</h2>

        <p class="mb-4 text-sm text-gray-600">
            Esqueceu-se da sua password? Indique o email e enviaremos um link para redefinir.
        </p>

        @if (session('status'))
            <div class="alert alert-success mb-4">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block font-semibold">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="input input-bordered w-full">
                @error('email') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="btn btn-primary w-full">Enviar link de recuperação</button>
        </form>
    </div>
</div>
@endsection
