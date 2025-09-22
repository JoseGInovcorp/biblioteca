<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Livro;
use Illuminate\Http\Request;
use App\Traits\RegistaLog;

class LivroStockController extends Controller
{
    use RegistaLog;

    public function index()
    {
        $livros = Livro::where('stock_venda', '<=', 5)
            ->orderBy('stock_venda', 'asc')
            ->get();

        return view('admin.livros.stock', compact('livros'));
    }

    public function update(Request $request, Livro $livro)
    {
        // Apenas Admin pode alterar stock
        abort_unless(auth()->user()->isAdmin(), 403);

        $validated = $request->validate([
            'stock_venda' => 'required|integer|min:0'
        ]);

        $stockAntigo = $livro->stock_venda;
        $livro->update(['stock_venda' => $validated['stock_venda']]);

        // 📜 Registar log da alteração de stock
        $this->registarLog(
            'Livros',
            $livro->id,
            "Alterou o stock do livro '{$livro->nome}' de {$stockAntigo} para {$validated['stock_venda']}"
        );

        return back()->with('success', 'Stock atualizado com sucesso.');
    }

    public function todos()
    {
        $livros = Livro::orderBy('nome')->get();

        return view('admin.livros.stock-todos', compact('livros'));
    }
}
