@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-4">📥 Confirmar Devolução — Requisição #{{ $requisicao->numero_sequencial }}</h2>

<p class="mb-4 text-gray-600">
    Aqui pode confirmar a devolução do livro, registando a data real de entrega e alterando o estado da requisição.
</p>

<a href="{{ route('requisicoes.index') }}" class="btn btn-outline btn-secondary mb-4">⬅️ Voltar</a>

<form action="{{ route('requisicoes.update', $requisicao) }}" method="POST" class="space-y-4">
    @csrf
    @method('PUT')

    <div>
        <label for="data_fim_real" class="block mb-1 font-semibold">📅 Data de Entrega Real</label>
        <input type="date" name="data_fim_real" id="data_fim_real"
               value="{{ old('data_fim_real', $requisicao->data_fim_real) }}"
               class="input input-bordered w-full" />
        @error('data_fim_real')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="status" class="block mb-1 font-semibold">📌 Estado da Requisição</label>
        <select name="status" id="status" class="select select-bordered w-full">
            <option value="ativa" @selected($requisicao->status === 'ativa')>Ativa</option>
            <option value="entregue" @selected($requisicao->status === 'entregue')>Entregue</option>
        </select>
        @error('status')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit" class="btn btn-success">✅ Confirmar Devolução</button>
</form>
@endsection
