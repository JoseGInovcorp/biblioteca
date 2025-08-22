@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-4">📘 Criar Livro</h2>
<form method="POST" action="{{ route('livros.store') }}" enctype="multipart/form-data">
    @include('pages.livros._form')
</form>
@endsection