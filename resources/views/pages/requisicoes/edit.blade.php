@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-4">✏️ Editar Requisição #{{ $requisicao->numero_sequencial }}</h2>

<a href="{{ route('requisicoes.index') }}" class="btn btn-outline btn-secondary mb-4">⬅️ Voltar</a>

<form action="{{ route('requisicoes.update', $requisicao) }}" method="POST" class="space-y-4">
    @csrf
    @method('PUT')

    <div>
        <label class="block mb-1">Data Fim Real</label>
        <input type="date" name="data_fim_real" value="{{ $requisicao->data_fim_real }}" class="input input-bordered w-full" />
    </div>

    <div>
        <label class="block mb-1">Status</label>
        <select name="status" class="select select-bordered w-full">
            <option value="ativa" @selected($requisicao->status === 'ativa')>Ativa</option>
            <option value="entregue" @selected($requisicao->status === 'entregue')>Entregue</option>
        </select>
    </div>

    <button class="btn btn-primary">Guardar Alterações</button>
</form>
@endsection
