@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-4">➕ Nova Requisição</h2>

<a href="{{ route('requisicoes.index') }}" class="btn btn-outline btn-secondary mb-4">⬅️ Voltar</a>

{{-- Mensagens de erro --}}
@if($errors->any())
    <div class="alert alert-error mb-4">
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('requisicoes.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
    @csrf

    <div>
        <label class="block mb-1">Livro</label>
        <select name="livro_id" class="select select-bordered w-full" required>
            <option value="">Selecione um livro</option>
            @foreach($livrosDisponiveis as $livro)
                <option value="{{ $livro->id }}" @selected(old('livro_id') == $livro->id)>
                    {{ $livro->nome }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block mb-1">Foto do Cidadão (opcional)</label>
        <input type="file" name="foto_cidadao" class="file-input file-input-bordered w-full" />
    </div>

    <button class="btn btn-primary">Criar Requisição</button>
</form>
@endsection
