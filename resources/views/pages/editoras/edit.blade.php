@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-4">✏️ Editar Editora</h2>
<a href="{{ route('autores.index') }}" class="btn btn-outline btn-secondary mb-4">⬅️ Voltar para lista de editoras</a>
<form method="POST" action="{{ route('editoras.update', $editora) }}" enctype="multipart/form-data" class="space-y-4">
    @include('pages.editoras._form')
</form>
@endsection
