<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(10);
        return view('pages.users.index', compact('users'));
    }

    public function show(User $user)
    {
        // Carrega as requisições com o livro associado
        $user->load(['requisicoes.livro']);

        return view('pages.users.show', compact('user'));
    }

    // Os outros métodos (create, store, edit, update, destroy) ficam para quando/ se forem necessários
}
