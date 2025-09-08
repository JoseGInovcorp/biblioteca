@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-4">📄 Detalhes da Requisição #{{ $requisicao->numero_sequencial }}</h2>

@if(session('success'))
    <div class="alert alert-success mb-4">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-error mb-4">
        {{ session('error') }}
    </div>
@endif

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

{{-- Submissão de review (apenas se ainda não existe e requisição está entregue) --}}
@if(auth()->user()->isCidadao() && $requisicao->status === 'entregue' && !$requisicao->review)
    <div class="mt-6">
        <h3 class="text-lg font-semibold mb-2">📝 Deixe a sua Review</h3>
        <form action="{{ route('reviews.store', $requisicao) }}" method="POST">
            @csrf
            <textarea name="comentario" rows="4" class="textarea textarea-bordered w-full mb-2" placeholder="Escreva aqui a sua opinião..." required></textarea>
            <button type="submit" class="btn btn-primary">📨 Submeter Review</button>
        </form>
    </div>
@endif

{{-- Exibição da review (se já existe e está ativa) --}}
@if($requisicao->review && $requisicao->review->isAtivo())
    <div class="mt-6 bg-gray-100 p-4 rounded">
        <h3 class="text-lg font-semibold mb-2">🗣️ Review do Cidadão</h3>
        <p><strong>{{ $requisicao->cidadao->name }}:</strong></p>
        <blockquote class="italic text-gray-700">{{ $requisicao->review->comentario }}</blockquote>
    </div>
@endif

@if(request()->has('review'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('form-review');
            if (form) {
                form.scrollIntoView({ behavior: 'smooth', block: 'start' });
                form.querySelector('textarea')?.focus();
            }
        });
    </script>
@endif

@endsection
