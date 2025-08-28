@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-4">➕ Nova Requisição</h2>

<a href="{{ route('requisicoes.index') }}" class="btn btn-outline btn-secondary mb-4">⬅️ Voltar</a>

{{-- Mensagens de erro globais --}}
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

    {{-- Campo Cidadão (apenas para Admins) --}}
    @auth
        @if(auth()->user()->isAdmin())
            <div>
                <label for="cidadao_id" class="block mb-1 font-semibold">Cidadão</label>
                <select name="cidadao_id" id="cidadao_id" class="select select-bordered w-full" required>
                    <option value="">-- Escolha um cidadão --</option>
                    @foreach($cidadaos as $cidadao)
                        <option value="{{ $cidadao->id }}" @selected(old('cidadao_id') == $cidadao->id)>
                            {{ $cidadao->name }}
                        </option>
                    @endforeach
                </select>
                @error('cidadao_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        @endif
    @endauth

    {{-- Campo Livro --}}
    <div>
        <label for="livro_id" class="block mb-1 font-semibold">Livro</label>
        <select name="livro_id" id="livro_id" class="select select-bordered w-full" required>
            <option value="">-- Escolhe um livro --</option>
            @foreach($livrosDisponiveis as $livro)
                <option value="{{ $livro->id }}"
                    @selected(old('livro_id', $livroSelecionado ?? null) == $livro->id)>
                    {{ $livro->nome }}
                </option>
            @endforeach
        </select>
        @error('livro_id')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Campo Foto --}}
    <div>
        <label for="foto_cidadao" class="block mb-1 font-semibold">Foto do Cidadão (opcional)</label>
        <input type="file" name="foto_cidadao" id="foto_cidadao" class="file-input file-input-bordered w-full" />
        @error('foto_cidadao')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <button class="btn btn-primary">Criar Requisição</button>
</form>
@endsection
