@extends('layouts.app')

@section('content')
    @auth
        <div class="text-center mt-10">
            <h1 class="text-3xl font-bold text-primary">📚 Bem-vindo à Biblioteca</h1>
            <p class="mt-4 text-base-content">Escolhe uma opção abaixo:</p>

            <div class="mt-6 flex justify-center gap-4">
                <a href="{{ route('livros.index') }}" class="btn btn-primary">📘 Livros</a>
                <a href="{{ route('autores.index') }}" class="btn btn-secondary">👤 Autores</a>
                <a href="{{ route('editoras.index') }}" class="btn btn-accent">🏢 Editoras</a>
            </div>
        </div>
    @endauth
@endsection
