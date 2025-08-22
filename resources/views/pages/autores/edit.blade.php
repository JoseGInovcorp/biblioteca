@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-4">✏️ Editar Autor</h2>
<a href="{{ route('autores.index') }}" class="btn btn-outline btn-secondary mb-4">⬅️ Voltar para lista de autores</a>
<form method="POST" action="{{ route('autores.update', $autor) }}" enctype="multipart/form-data" class="space-y-4">
    @include('pages.autores._form')
</form>
@endsection
