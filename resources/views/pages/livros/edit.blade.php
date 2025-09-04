@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-4">✏️ Editar Livro</h2>
@if(session('warning'))
    <div class="mb-4 p-3 bg-yellow-100 border border-yellow-300 text-yellow-800 rounded">
        {{ session('warning') }}
    </div>
@endif
<a href="{{ route('autores.index') }}" class="btn btn-outline btn-secondary mb-4">⬅️ Voltar para lista de livros</a>
<form method="POST" action="{{ route('livros.update', $livro) }}" enctype="multipart/form-data">
    @include('pages.livros._form')
</form>
@endsection
