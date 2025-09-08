@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-4">🛠️ Moderação de Reviews</h2>

<a href="{{ route('home') }}" class="btn btn-outline mb-6">🏠 Voltar ao Menu</a>

{{-- 🔍 Reviews Pendentes --}}
<h3 class="text-xl font-semibold mb-2">⏳ Pendentes ({{ $pendentes->count() }})</h3>

@if($pendentes->isEmpty())
    <div class="alert alert-info mb-6">Não existem reviews pendentes neste momento.</div>
@else
    @foreach($pendentes as $review)
        <div class="border p-4 rounded mb-4 bg-base-100 shadow-sm">
            <p><strong>📚 Livro:</strong> {{ $review->livro->nome }}</p>
            <p><strong>👤 Cidadão:</strong> {{ $review->user->name }}</p>
            <p><strong>💬 Comentário:</strong></p>
            <blockquote class="italic text-gray-700 border-l-4 border-primary pl-3">
                {{ $review->comentario }}
            </blockquote>

            <form action="{{ route('reviews.update', $review) }}" method="POST" class="mt-3">
                @csrf
                @method('PATCH')

                <label for="estado-{{ $review->id }}" class="block font-semibold mb-1">Estado:</label>
                <select id="estado-{{ $review->id }}" name="estado" class="select select-bordered w-full mb-2">
                    <option value="ativo">✅ Aprovar</option>
                    <option value="recusado">❌ Recusar</option>
                </select>

                <label for="justificacao-{{ $review->id }}" class="block font-semibold mb-1">Justificação (se recusar):</label>
                <textarea id="justificacao-{{ $review->id }}" name="justificacao" rows="2" class="textarea textarea-bordered w-full mb-3" placeholder="Escreva aqui a justificação..."></textarea>

                <div class="flex gap-2">
                    <button type="submit" class="btn btn-success">💾 Moderar</button>
                    <a href="{{ route('livros.show', $review->livro) }}" class="btn btn-outline">🔍 Ver Livro</a>
                </div>
            </form>
        </div>
    @endforeach
@endif

{{-- ✅ Reviews Aprovadas --}}
<h3 class="text-xl font-semibold mt-8 mb-2">✅ Aprovadas ({{ $ativas->count() }})</h3>

@if($ativas->isEmpty())
    <p class="text-gray-500 italic mb-6">Ainda não existem reviews aprovadas.</p>
@else
    @foreach($ativas as $review)
        <div class="border p-4 rounded mb-4 bg-base-100 shadow-sm">
            <p><strong>📚 Livro:</strong> {{ $review->livro->nome }}</p>
            <p><strong>👤 Cidadão:</strong> {{ $review->user->name }}</p>
            <blockquote class="italic text-gray-700 border-l-4 border-green-500 pl-3">
                {{ $review->comentario }}
            </blockquote>
        </div>
    @endforeach
@endif

{{-- ❌ Reviews Recusadas --}}
<h3 class="text-xl font-semibold mt-8 mb-2">❌ Recusadas ({{ $recusadas->count() }})</h3>

@if($recusadas->isEmpty())
    <p class="text-gray-500 italic">Ainda não existem reviews recusadas.</p>
@else
    @foreach($recusadas as $review)
        <div class="border p-4 rounded mb-4 bg-base-100 shadow-sm">
            <p><strong>📚 Livro:</strong> {{ $review->livro->nome }}</p>
            <p><strong>👤 Cidadão:</strong> {{ $review->user->name }}</p>
            <blockquote class="italic text-gray-700 border-l-4 border-red-500 pl-3">
                {{ $review->comentario }}
            </blockquote>
            @if($review->justificacao)
                <p class="mt-2 text-sm text-gray-600"><strong>Justificação:</strong> {{ $review->justificacao }}</p>
            @endif
        </div>
    @endforeach
@endif
@endsection
