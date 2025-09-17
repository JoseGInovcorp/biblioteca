@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto text-center mt-12">
    <h2 class="text-3xl font-bold text-green-600 mb-4">✅ Pagamento Confirmado</h2>
    <p class="text-lg mb-6">Obrigado pela sua compra! A sua encomenda foi processada com sucesso.</p>

    <a href="{{ route('home') }}" class="btn btn-primary">
        🏠 Voltar à Página Inicial
    </a>
</div>
@endsection