<h2>📢 Nova Review Submetida</h2>

<p>O cidadão <strong>{{ $review->user->name }}</strong> submeteu uma review sobre o livro <strong>{{ $review->livro->nome }}</strong>.</p>

<p><strong>Comentário:</strong></p>
<blockquote>{{ $review->comentario }}</blockquote>

<p>
    🔗 <a href="{{ $moderationUrl }}">Ver lista de reviews para moderação</a>
</p>

<p style="font-size: 0.9em; color: #555;">
    Nota: É necessário estar autenticado como administrador para aceder à moderação.
</p>
