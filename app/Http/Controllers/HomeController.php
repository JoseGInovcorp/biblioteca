<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Livro;
use App\Models\User;
use App\Models\Review;
use App\Models\Requisicao;
use App\Models\Encomenda;

class HomeController extends Controller
{
    public function index()
    {
        $dados = [];

        if (auth()->check() && auth()->user()->isAdmin()) {
            $dados = [
                'totalLivros'         => Livro::count(),
                'totalUsers'          => User::count(),
                'reviewsPendentes'    => Review::where('estado', 'suspenso')->count(),
                'requisicoesAtivas'   => Requisicao::where('status', 'ativa')->count(),
                'encomendasPendentes' => Encomenda::where('estado', 'pendente')->count(),
            ];
        }

        return view('home', $dados);
    }
}
