<?php

namespace App\Http\Controllers;

use App\Models\Encomenda;
use Illuminate\Support\Facades\Auth;

class EncomendaCidadaoController extends Controller
{
    public function index()
    {
        $encomendas = Encomenda::with('livros')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('pages.encomendas.cidadao', compact('encomendas'));
    }
}
