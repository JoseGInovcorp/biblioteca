@extends('layouts.app')

@section('content')
<div class="flex justify-center items-center min-h-[70vh]">
    <div class="w-full max-w-md text-center">
        <h2 class="text-2xl font-bold mb-4">📧 Verificar Email</h2>

        @if (session('status') == 'verification-link-sent')
            <div class="alert alert-success mb-4">
                Um novo link de verificação foi enviado para o seu email.
            </div>
        @endif

        <p class="mb-4">
            Obrigado por se registar! Antes de começar, verifique o seu email clicando no link que lhe enviámos.
        </p>

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-primary w-full">Reenviar Email de Verificação</button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="mt-4">
            @csrf
            <button type="submit" class="btn btn-outline w-full">Terminar Sessão</button>
        </form>
    </div>
</div>
@endsection
