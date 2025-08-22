@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-4">🏢 Criar Editora</h2>
<form method="POST" action="{{ route('editoras.store') }}" enctype="multipart/form-data" class="space-y-4">
    @include('pages.editoras._form')
</form>
@endsection
