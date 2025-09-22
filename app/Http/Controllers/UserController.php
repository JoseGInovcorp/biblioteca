<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Traits\RegistaLog;

class UserController extends Controller
{
    use RegistaLog;

    public function index()
    {
        abort_unless(auth()->check() && auth()->user()->isAdmin(), 403);

        $users = User::paginate(10);
        return view('pages.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $auth = auth()->user();
        abort_unless($auth && ($auth->isAdmin() || $auth->id === $user->id), 403);

        $user->load(['requisicoes' => function ($q) {
            $q->with('livro')->latest();
        }]);

        return view('pages.users.show', compact('user'));
    }

    public function create()
    {
        abort_if(!auth()->user()->isAdmin(), 403);
        return view('pages.users.create');
    }

    public function store(Request $request)
    {
        abort_if(!auth()->user()->isAdmin(), 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,cidadao'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role']
        ]);

        // 📜 Registar log da criação de utilizador
        $this->registarLog(
            'Utilizadores',
            $user->id,
            "Criou o utilizador '{$user->name}' com o papel '{$user->role}'"
        );

        return redirect()
            ->route('users.index')
            ->with('success', 'Utilizador criado com sucesso!');
    }

    public function destroy(User $user)
    {
        abort_if(!auth()->user()->isAdmin(), 403);

        $nome = $user->name;
        $role = $user->role;
        $id   = $user->id;

        // Apagar o utilizador
        $user->delete();

        // 📜 Registar log da eliminação de utilizador
        $this->registarLog(
            'Utilizadores',
            $id,
            "Apagou o utilizador '{$nome}' com o papel '{$role}'"
        );

        return redirect()
            ->route('users.index')
            ->with('success', 'Utilizador apagado com sucesso!');
    }
}
