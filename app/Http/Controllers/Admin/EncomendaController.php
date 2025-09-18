<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Encomenda;

class EncomendaController extends Controller
{
    public function index()
    {
        $encomendas = Encomenda::with('user', 'livros')->latest()->get();
        return view('admin.encomendas.index', compact('encomendas'));
    }

    public function pendentes()
    {
        $encomendas = Encomenda::with('user', 'livros')
            ->where('estado', 'pendente')
            ->latest()
            ->get();

        return view('admin.encomendas.index', compact('encomendas'));
    }

    public function pagas()
    {
        $encomendas = Encomenda::with('user', 'livros')
            ->where('estado', 'paga')
            ->latest()
            ->get();

        return view('admin.encomendas.index', compact('encomendas'));
    }
}
