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

        {{-- Painel de Avisos --}}
        @if(auth()->user()->isAdmin())
            @php
                $avisos = [];
                if($reviewsPendentes > 0) {
                    $avisos[] = [
                        'icone' => '📝',
                        'mensagem' => "{$reviewsPendentes} review" . ($reviewsPendentes > 1 ? 's pendentes' : ' pendente'),
                        'classe' => 'bg-error text-white'
                    ];
                }
                if($requisicoesAtivas > 0) {
                    $avisos[] = [
                        'icone' => '📦',
                        'mensagem' => "{$requisicoesAtivas} requisição" . ($requisicoesAtivas > 1 ? 'es ativas' : ' ativa'),
                        'classe' => 'bg-warning text-black'
                    ];
                }
                if($livrosComStockCritico > 0) {
                    $avisos[] = [
                        'icone' => '📉',
                        'mensagem' => "{$livrosComStockCritico} livro" . ($livrosComStockCritico > 1 ? 's com stock crítico' : ' com stock crítico'),
                        'classe' => 'bg-error text-white'
                    ];
                }
            @endphp

            @if(count($avisos) > 0)
                <div x-data="{ open: false }" class="card bg-base-200 shadow overflow-hidden mb-6">
                    <div class="p-4 border-b border-base-300 flex justify-between items-center cursor-pointer"
                        @click="open = !open">
                        <h2 class="card-title m-0">⚠️ Avisos Importantes</h2>
                        <span x-text="open ? '▲' : '▼'" class="text-xs"></span>
                    </div>
                    <div x-show="open"
                        x-transition:enter="transition-all ease-out duration-300"
                        x-transition:enter-start="max-h-0 opacity-0"
                        x-transition:enter-end="max-h-screen opacity-100"
                        x-transition:leave="transition-all ease-in duration-200"
                        x-transition:leave-start="max-h-screen opacity-100"
                        x-transition:leave-end="max-h-0 opacity-0"
                        class="overflow-hidden">
                        <div class="p-4 space-y-2">
                            @foreach($avisos as $aviso)
                                <div class="p-3 rounded shadow flex items-center gap-2 {{ $aviso['classe'] }}">
                                    <span class="text-lg">{{ $aviso['icone'] }}</span>
                                    <span class="font-semibold">{{ $aviso['mensagem'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
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
        <div class="mt-10 grid grid-cols-1 md:grid-cols-3 gap-6 items-start">

            {{-- Gestão de Utilizadores --}}
            <div x-data="{ open: false }" class="card bg-base-200 shadow overflow-hidden">
                <div class="p-4 border-b border-base-300 flex justify-between items-center cursor-pointer"
                    @click="open = !open">
                    <h2 class="card-title m-0">👥 Gestão de Utilizadores</h2>
                    <span x-text="open ? '▲' : '▼'" class="text-xs"></span>
                </div>
                <div x-show="open"
                    x-transition:enter="transition-all ease-out duration-300"
                    x-transition:enter-start="max-h-0 opacity-0"
                    x-transition:enter-end="max-h-screen opacity-100"
                    x-transition:leave="transition-all ease-in duration-200"
                    x-transition:leave-start="max-h-screen opacity-100"
                    x-transition:leave-end="max-h-0 opacity-0"
                    class="overflow-hidden">
                    <div class="p-4">
                        <p class="text-sm text-gray-600">Aceder à lista de utilizadores e gerir permissões.</p>
                        <a href="{{ route('users.index') }}" class="btn btn-outline mt-3">👥 Utilizadores</a>
                    </div>
                </div>
            </div>

            {{-- Moderação de Reviews --}}
            <div x-data="{ open: false }" class="card bg-base-200 shadow overflow-hidden">
                <div class="p-4 border-b border-base-300 flex justify-between items-center cursor-pointer"
                    @click="open = !open">
                    <h2 class="card-title m-0">📝 Moderação de Reviews</h2>
                    <span x-text="open ? '▲' : '▼'" class="text-xs"></span>
                </div>
                <div x-show="open" x-transition:enter="transition-all ease-out duration-300"
                    x-transition:enter-start="max-h-0 opacity-0"
                    x-transition:enter-end="max-h-screen opacity-100"
                    x-transition:leave="transition-all ease-in duration-200"
                    x-transition:leave-start="max-h-screen opacity-100"
                    x-transition:leave-end="max-h-0 opacity-0"
                    class="overflow-hidden">
                    <div class="p-4">
                        <p class="text-sm text-gray-600">Aprovar ou recusar comentários submetidos pelos cidadãos.</p>
                        <a href="{{ route('reviews.index') }}" class="btn btn-error mt-3">🛠️ Moderar Reviews</a>
                    </div>
                </div>
            </div>

            {{-- Catálogo --}}
            <div x-data="{ open: false }" class="card bg-base-200 shadow overflow-hidden">
                <div class="p-4 border-b border-base-300 flex justify-between items-center cursor-pointer"
                    @click="open = !open">
                    <h2 class="card-title m-0">📦 Catálogo</h2>
                    <span x-text="open ? '▲' : '▼'" class="text-xs"></span>
                </div>
                <div x-show="open" x-transition:enter="transition-all ease-out duration-300"
                    x-transition:enter-start="max-h-0 opacity-0"
                    x-transition:enter-end="max-h-screen opacity-100"
                    x-transition:leave="transition-all ease-in duration-200"
                    x-transition:leave-start="max-h-screen opacity-100"
                    x-transition:leave-end="max-h-0 opacity-0"
                    class="overflow-hidden">
                    <div class="p-4">
                        <p class="text-sm text-gray-600">Adicionar e gerir livros, autores e editoras no sistema.</p>
                        <div class="flex flex-col gap-2 mt-3">
                            <a href="{{ route('livros.index') }}" class="btn btn-outline">📚 Ver Todos os Livros</a>
                            <a href="{{ route('livros.create') }}" class="btn btn-success">➕ Novo Livro</a>
                            <a href="{{ route('autores.create') }}" class="btn btn-warning">➕ Novo Autor</a>
                            <a href="{{ route('editoras.create') }}" class="btn btn-info">➕ Nova Editora</a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Encomendas --}}
            <div x-data="{ open: false }" class="card bg-base-200 shadow overflow-hidden relative">
                <div class="p-4 border-b border-base-300 flex justify-between items-center cursor-pointer"
                    @click="open = !open">
                    <h2 class="card-title m-0">📦 Encomendas</h2>
                    <span x-text="open ? '▲' : '▼'" class="text-xs"></span>
                </div>
                <div x-show="open" x-transition:enter="transition-all ease-out duration-300"
                    x-transition:enter-start="max-h-0 opacity-0"
                    x-transition:enter-end="max-h-screen opacity-100"
                    x-transition:leave="transition-all ease-in duration-200"
                    x-transition:leave-start="max-h-screen opacity-100"
                    x-transition:leave-end="max-h-0 opacity-0"
                    class="overflow-hidden">
                    <div class="p-4">
                        <p class="text-sm text-gray-600">Consultar todas as encomendas efetuadas pelos cidadãos.</p>
                        <div class="flex flex-col gap-2 mt-3">
                            <a href="{{ route('admin.encomendas.index') }}" class="btn btn-outline">📦 Todas as Encomendas</a>
                            <a href="{{ route('admin.encomendas.pendentes') }}" class="btn btn-warning">⏳ Pendentes</a>
                            <a href="{{ route('admin.encomendas.pagas') }}" class="btn btn-success">✅ Pagas</a>
                        </div>
                        @if($encomendasPendentes > 0)
                            <div class="mt-2 inline-block bg-error text-white text-xs font-bold px-2 py-1 rounded-full">
                                {{ $encomendasPendentes }} pendente{{ $encomendasPendentes > 1 ? 's' : '' }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Requisições --}}
            <div x-data="{ open: false }" class="card bg-base-200 shadow overflow-hidden">
                <div class="p-4 border-b border-base-300 flex justify-between items-center cursor-pointer"
                    @click="open = !open">
                    <h2 class="card-title m-0">📚 Requisições</h2>
                    <span x-text="open ? '▲' : '▼'" class="text-xs"></span>
                </div>
                <div x-show="open" x-transition:enter="transition-all ease-out duration-300"
                    x-transition:enter-start="max-h-0 opacity-0"
                    x-transition:enter-end="max-h-screen opacity-100"
                    x-transition:leave="transition-all ease-in duration-200"
                    x-transition:leave-start="max-h-screen opacity-100"
                    x-transition:leave-end="max-h-0 opacity-0"
                    class="overflow-hidden">
                    <div class="p-4">
                        <p class="text-sm text-gray-600">Gerir pedidos de empréstimo e devoluções de livros.</p>
                        <a href="{{ route('requisicoes.index') }}" class="btn btn-outline mt-3">📋 Todas as Requisições</a>
                    </div>
                </div>
            </div>

            {{-- Gestão de Stock --}}
            <div x-data="{ open: false }" class="card bg-base-200 shadow overflow-hidden">
                {{-- Cabeçalho --}}
                <div class="p-4 border-b border-base-300 flex justify-between items-center cursor-pointer"
                    @click="open = !open">
                    <h2 class="card-title m-0">📉 Gestão de Stock</h2>
                    <span x-text="open ? '▲' : '▼'" class="text-xs"></span>
                </div>
                {{-- Conteúdo --}}
                <div x-show="open"
                    x-transition:enter="transition-all ease-out duration-300"
                    x-transition:enter-start="max-h-0 opacity-0"
                    x-transition:enter-end="max-h-screen opacity-100"
                    x-transition:leave="transition-all ease-in duration-200"
                    x-transition:leave-start="max-h-screen opacity-100"
                    x-transition:leave-end="max-h-0 opacity-0"
                    class="overflow-hidden">
                    <div class="p-4">
                        <p class="text-sm text-gray-600">Verificar e atualizar stock de livros.</p>
                        <div class="flex flex-col gap-2 mt-3">
                            <a href="{{ route('admin.livros.stock') }}" class="btn btn-outline">📊 Stock Baixo</a>
                            <a href="{{ route('admin.livros.stock.todos') }}" class="btn btn-outline">📦 Todos os Stocks</a>
                        </div>
                    </div>
                </div>
            </div>


            {{-- Logs de Atividade --}}
            <div x-data="{ open: false }" class="card bg-base-200 shadow overflow-hidden">
                <div class="p-4 border-b border-base-300 flex justify-between items-center cursor-pointer"
                    @click="open = !open">
                    <h2 class="card-title m-0">📜 Logs de Atividade</h2>
                    <span x-text="open ? '▲' : '▼'" class="text-xs"></span>
                </div>
                <div x-show="open"
                    x-transition:enter="transition-all ease-out duration-300"
                    x-transition:enter-start="max-h-0 opacity-0"
                    x-transition:enter-end="max-h-screen opacity-100"
                    x-transition:leave="transition-all ease-in duration-200"
                    x-transition:leave-start="max-h-screen opacity-100"
                    x-transition:leave-end="max-h-0 opacity-0"
                    class="overflow-hidden">
                    <div class="p-4">
                        <p class="text-sm text-gray-600">Consultar todas as ações registadas na aplicação.</p>
                        <a href="{{ route('admin.logs.index') }}" class="btn btn-outline mt-3">📜 Ver Logs</a>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>
@endauth
@endsection
