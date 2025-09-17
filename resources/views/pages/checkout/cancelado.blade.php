@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto text-center mt-12">
    <h2 class="text-3xl font-bold text-red-600 mb-4">❌ Pagamento Cancelado</h2>
    <p class="text-lg mb-6">O pagamento foi cancelado ou não foi concluído. Pode tentar novamente ou rever a sua encomenda.</p>

    <div class="flex justify-center gap-4">
        <a href="{{ route('checkout.pagamento') }}" class="btn btn-warning">
            🔁 Tentar Novamente
        </a>
        <a href="{{ route('carrinho.index') }}" class="btn btn-outline btn-secondary">
            🛒 Rever Carrinho
        </a>
    </div>
</div>
@endsection