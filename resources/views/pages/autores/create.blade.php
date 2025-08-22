@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-4">👤 Criar Autor</h2>
<form method="POST" action="{{ route('autores.store') }}" enctype="multipart/form-data" class="space-y-4">
    @include('pages.autores._form')
</form>
@endsection
