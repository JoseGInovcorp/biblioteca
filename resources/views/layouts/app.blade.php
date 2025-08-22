<!DOCTYPE html>
<html lang="pt" data-theme="silk">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name', 'Biblioteca') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-base-100 text-base-content min-h-screen flex flex-col">

<header class="bg-base-200 shadow p-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-primary">📚 Biblioteca</h1>

    @auth
    <div class="flex items-center gap-2">
        <a href="{{ route('profile.show') }}" class="btn btn-sm btn-outline">Perfil</a>
        <span class="text-sm">Olá, {{ Auth::user()->name }}</span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-sm btn-outline btn-error">Sair</button>
        </form>
    </div>
@endauth


    @guest
        <div class="flex gap-2">
            <a href="{{ route('login') }}" class="btn btn-sm btn-primary">Entrar</a>
            <a href="{{ route('register') }}" class="btn btn-sm btn-secondary">Registar</a>
        </div>
    @endguest
</header>

<main class="flex-grow container mx-auto p-4">
    @yield('content')
    {{ $slot ?? '' }}
</main>

<footer class="bg-base-200 text-center p-4 text-sm text-base-content">
    &copy; {{ date('Y') }} Biblioteca. Todos os direitos reservados.
</footer>

@stack('modals')
@livewireScripts
</body>
</html>
