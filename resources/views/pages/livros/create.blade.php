@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-4">📘 Criar Livro</h2>

<div class="flex gap-2 mb-6">
    <a href="{{ route('livros.index') }}" class="btn btn-outline btn-secondary">⬅️ Voltar</a>

    {{-- Botão para aceder à pesquisa da Google Books --}}
    <a href="{{ route('google-books.index') }}" class="btn btn-outline btn-info">
        🔍 Usar Google Books
    </a>
</div>

<form method="POST" action="{{ route('livros.store') }}" enctype="multipart/form-data">
    @include('pages.livros._form')
</form>
@endsection
