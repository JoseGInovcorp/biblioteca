@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto mt-10">
    <h1 class="text-2xl font-bold mb-6">📜 Registos de Atividade</h1>

    <a href="{{ route('home') }}" 
    class="btn btn-outline btn-primary mb-4">
        ⬅️ Voltar ao Menu
    </a>

    {{-- Formulário de filtros --}}
    <form method="GET" class="flex flex-wrap gap-4 mb-4">
        {{-- Filtro por utilizador --}}
        <div>
            <label for="user_id" class="block text-sm font-medium">Utilizador</label>
            <select name="user_id" id="user_id" class="select select-bordered w-full">
                <option value="">Todos</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Filtro por módulo --}}
        <div>
            <label for="modulo" class="block text-sm font-medium">Módulo</label>
            <select name="modulo" id="modulo" class="select select-bordered w-full">
                <option value="">Todos</option>
                @foreach($modulos as $modulo)
                    <option value="{{ $modulo }}" @selected(request('modulo') == $modulo)>
                        {{ ucfirst($modulo) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex items-end">
            <button type="submit" class="btn btn-primary">Filtrar</button>
        </div>
    </form>
    
    {{-- Tabela de logs --}}
    <div class="overflow-x-auto bg-base-200 rounded shadow">
        <table class="table w-full">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Hora</th>
                    <th>Utilizador</th>
                    <th>Módulo</th>
                    <th>ID Objeto</th>
                    <th>Alteração</th>
                    <th>IP</th>
                    <th>Browser</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->created_at->format('d/m/Y') }}</td>
                        <td>{{ $log->created_at->format('H:i') }}</td>
                        <td>{{ $log->user?->name ?? '—' }}</td>
                        <td>{{ $log->modulo }}</td>
                        <td>{{ $log->objeto_id ?? '—' }}</td>
                        <td>{{ $log->alteracao }}</td>
                        <td>{{ $log->ip }}</td>
                        <td class="max-w-xs truncate" title="{{ $log->browser }}">{{ $log->browser }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">Nenhum registo encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $logs->links() }}
    </div>
</div>
@endsection
