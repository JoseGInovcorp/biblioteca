@extends('layouts.app')

@section('content')
@auth
    <div class="max-w-5xl mx-auto mt-10">
        <h1 class="text-3xl font-bold text-primary text-center">📚 Bem-vindo à Biblioteca</h1>

        @if(auth()->user()->isAdmin())
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mt-6 mb-8">
                <div class="stat bg-base-200 p-4 rounded shadow">
                    <div class="stat-title text-sm text-gray-500">📚 Livros</div>
                    <div class="stat-value text-xl font-bold">{{ $totalLivros }}</div>
                </div>
                <div class="stat bg-base-200 p-4 rounded shadow">
                    <div class="stat-title text-sm text-gray-500">👥 Utilizadores</div>
                    <div class="stat-value text-xl font-bold">{{ $totalUsers }}</div>
                </div>
                <div class="stat bg-base-200 p-4 rounded shadow">
                    <div class="stat-title text-sm text-gray-500">📝 Reviews Pendentes</div>
                    <div class="stat-value text-xl font-bold text-error">{{ $reviewsPendentes }}</div>
                </div>
                <div class="stat bg-base-200 p-4 rounded shadow">
                    <div class="stat-title text-sm text-gray-500">📦 Requisições Ativas</div>
                    <div class="stat-value text-xl font-bold">{{ $requisicoesAtivas }}</div>
                </div>
                <div class="stat bg-base-200 p-4 rounded shadow">
                    <div class="stat-title text-sm text-gray-500">📉 Livros com Stock Crítico</div>
                    <div class="stat-value text-xl font-bold text-warning">{{ $livrosComStockCritico }}</div>
                </div>
            </div>
        @endif

        @if(!auth()->user()->isAdmin())
            <h2 class="text-xl font-semibold text-center mt-10 mb-4">🎯 Ações Disponíveis</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
                {{-- Livros --}}
                <div class="card bg-base-200 shadow">
                    <div class="card-body">
                        <h3 class="card-title">📘 Livros</h3>
                        <p class="text-sm text-gray-600">Consulta o catálogo completo da biblioteca.</p>
                        <a href="{{ route('livros.index') }}" class="btn btn-primary mt-3">Ver Livros</a>
                    </div>
                </div>

                {{-- Autores & Editoras --}}
                <div class="card bg-base-200 shadow">
                    <div class="card-body">
                        <h3 class="card-title">👤 Autores & 🏢 Editoras</h3>
                        <p class="text-sm text-gray-600">Explora os autores e editoras disponíveis.</p>
                        <div class="flex flex-col gap-2 mt-3">
                            <a href="{{ route('autores.index') }}" class="btn btn-secondary">Autores</a>
                            <a href="{{ route('editoras.index') }}" class="btn btn-accent">Editoras</a>
                        </div>
                    </div>
                </div>

                {{-- Requisições --}}
                <div class="card bg-base-200 shadow">
                    <div class="card-body">
                        <h3 class="card-title">📦 Requisições</h3>
                        <p class="text-sm text-gray-600">Consulta os livros que requisitaste.</p>
                        <a href="{{ route('requisicoes.index') }}" class="btn btn-neutral mt-3">Ver Requisições</a>
                    </div>
                </div>

                {{-- Encomendas --}}
                <div class="card bg-base-200 shadow">
                    <div class="card-body">
                        <h3 class="card-title">🛒 Encomendas</h3>
                        <p class="text-sm text-gray-600">Consulta o histórico das tuas encomendas.</p>
                        <a href="{{ route('encomendas.cidadao') }}" class="btn btn-outline btn-primary mt-3">Ver Encomendas</a>
                    </div>
                </div>
            </div>
        @endif

        {{-- Secção exclusiva para Admins --}}
        @if(auth()->user()->isAdmin())
            <div class="mt-10 grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Gestão de Utilizadores --}}
                <div class="card bg-base-200 shadow">
                    <div class="card-body">
                        <h2 class="card-title">👥 Gestão de Utilizadores</h2>
                        <p class="text-sm text-gray-600">Aceder à lista de utilizadores e gerir permissões.</p>
                        <a href="{{ route('users.index') }}" class="btn btn-outline mt-3">👥 Utilizadores</a>
                    </div>
                </div>

                {{-- Moderação de Reviews --}}
                <div class="card bg-base-200 shadow">
                    <div class="card-body">
                        <h2 class="card-title">📝 Moderação de Reviews</h2>
                        <p class="text-sm text-gray-600">Aprovar ou recusar comentários submetidos pelos cidadãos.</p>
                        <a href="{{ route('reviews.index') }}" class="btn btn-error mt-3">🛠️ Moderar Reviews</a>
                    </div>
                </div>

                {{-- Ações Rápidas de Catálogo --}}
                <div class="card bg-base-200 shadow">
                    <div class="card-body">
                        <h2 class="card-title">📦 Catálogo</h2>
                        <p class="text-sm text-gray-600">Adicionar novos livros, autores e editoras ao sistema.</p>
                        <div class="flex flex-col gap-2 mt-3">
                            <a href="{{ route('livros.create') }}" class="btn btn-success">➕ Novo Livro</a>
                            <a href="{{ route('autores.create') }}" class="btn btn-warning">➕ Novo Autor</a>
                            <a href="{{ route('editoras.create') }}" class="btn btn-info">➕ Nova Editora</a>
                        </div>
                    </div>
                </div>

                {{-- Gestão de Encomendas --}}
                <div class="card bg-base-200 shadow relative">
                    <div class="card-body">
                        <h2 class="card-title">📦 Encomendas</h2>
                        <p class="text-sm text-gray-600">Consultar todas as encomendas efetuadas pelos cidadãos.</p>
                        <div class="flex flex-col gap-2 mt-3">
                            <a href="{{ route('admin.encomendas.index') }}" class="btn btn-outline">📦 Todas as Encomendas</a>
                            <a href="{{ route('admin.encomendas.pendentes') }}" class="btn btn-warning">⏳ Pendentes</a>
                            <a href="{{ route('admin.encomendas.pagas') }}" class="btn btn-success">✅ Pagas</a>
                        </div>
                    </div>

                    @if($encomendasPendentes > 0)
                        <div class="absolute top-2 right-2 bg-error text-white text-xs font-bold px-2 py-1 rounded-full">
                            {{ $encomendasPendentes }} pendente{{ $encomendasPendentes > 1 ? 's' : '' }}
                        </div>
                    @endif
                </div>

                {{-- Gestão de Stock --}}
                <div class="card bg-base-200 shadow">
                    <div class="card-body">
                        <h2 class="card-title">📉 Gestão de Stock</h2>
                        <p class="text-sm text-gray-600">Verificar livros com stock baixo ou esgotado.</p>
                        <a href="{{ route('admin.livros.stock') }}" class="btn btn-outline mt-3">📊 Ver Stock</a>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endauth
@endsection
