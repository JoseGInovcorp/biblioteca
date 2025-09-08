<h2>📬 Estado da sua Review</h2>

<p>Olá {{ $review->user->name }},</p>

<p>A sua review sobre o livro <strong>{{ $review->livro->nome }}</strong> foi <strong>{{ $review->estado }}</strong>.</p>

@if($review->estado === 'recusado' && $review->justificacao)
    <p><strong>Justificação da recusa:</strong></p>
    <blockquote>{{ $review->justificacao }}</blockquote>
@endif

@if($review->estado === 'ativo')
    <p>Pode vê-la publicada na página do livro através do link abaixo:</p>
    <p>
        🔗 <a href="{{ route('livros.show', $review->livro) }}">Ver página do livro</a>
    </p>
@endif

<p>Obrigado por contribuir com a sua opinião.</p>
