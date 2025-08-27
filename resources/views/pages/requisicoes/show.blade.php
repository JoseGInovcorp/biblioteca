@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-4">📄 Detalhes da Requisição #{{ $requisicao->numero_sequencial }}</h2>

<a href="{{ route('requisicoes.index') }}" class="btn btn-outline btn-secondary mb-4">⬅️ Voltar</a>

<ul class="list-disc pl-6">
    @if($requisicao->livro)
        <li><strong>Livro:</strong> {{ $requisicao->livro->nome }}</li>
    @else
        <li><strong>Livro:</strong> <span class="text-gray-500 italic">⚠️ Livro removido</span></li>
    @endif

    @if($requisicao->cidadao)
        <li><strong>Cidadão:</strong> {{ $requisicao->cidadao->name }}</li>
    @else
        <li><strong>Cidadão:</strong> <span class="text-gray-500 italic">⚠️ Cidadão removido</span></li>
    @endif

    <li><strong>Data Início:</strong> {{ $requisicao->data_inicio }}</li>
    <li><strong>Data Fim Prevista:</strong> {{ $requisicao->data_fim_prevista }}</li>
    <li><strong>Status:</strong> {{ ucfirst($requisicao->status) }}</li>
</ul>

@if($requisicao->foto_cidadao)
    <div class="mt-4">
        <strong>Foto do Cidadão:</strong><br>
        <img src="{{ asset('storage/'.$requisicao->foto_cidadao) }}" alt="Foto" class="w-32 h-32 object-cover rounded">
    </div>
@endif
@endsection
