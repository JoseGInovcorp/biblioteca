<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Log;
use App\Models\User;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $query = Log::with('user')->latest();

        // Filtro por utilizador
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filtro por módulo
        if ($request->filled('modulo')) {
            $query->where('modulo', $request->modulo);
        }

        $logs = $query->paginate(15)->withQueryString();

        // Dados para popular os selects na view
        $users = User::orderBy('name')->get(['id', 'name']);
        $modulos = Log::select('modulo')->distinct()->pluck('modulo');

        return view('admin.logs.index', compact('logs', 'users', 'modulos'));
    }
}
