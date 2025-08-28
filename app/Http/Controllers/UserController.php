<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        // Apenas Admin pode aceder à lista de utilizadores
        abort_unless(auth()->check() && auth()->user()->isAdmin(), 403);

        $users = User::paginate(10);
        return view('pages.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $auth = auth()->user();

        // Apenas Admin pode ver outros; Cidadão só vê o próprio
        abort_unless($auth && ($auth->isAdmin() || $auth->id === $user->id), 403);

        $user->load(['requisicoes' => function ($q) {
            $q->with('livro')->latest();
        }]);

        return view('pages.users.show', compact('user'));
    }

    public function create()
    {
        abort_if(!auth()->user()->isAdmin(), 403); // só Admin pode aceder
        return view('pages.users.create');
    }

    public function store(Request $request)
    {
        abort_if(!auth()->user()->isAdmin(), 403); // só Admin pode criar

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,cidadao'
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role']
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', 'Utilizador criado com sucesso!');
    }
}
