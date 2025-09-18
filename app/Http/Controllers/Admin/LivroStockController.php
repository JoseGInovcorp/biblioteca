<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Livro;

class LivroStockController extends Controller
{
    public function index()
    {
        $livros = Livro::where('stock_venda', '<=', 5)
            ->orderBy('stock_venda', 'asc')
            ->get();

        return view('admin.livros.stock', compact('livros'));
    }
}
