<?php

namespace App\Exports;

use App\Models\Livro;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LivrosExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Livro::with('editora', 'autores')->get()->map(function ($livro) {
            return [
                'ID' => $livro->id,
                'Nome' => $livro->nome,
                'ISBN' => $livro->isbn, // decifrado automaticamente
                'Bibliografia' => $livro->bibliografia,
                'Editora' => $livro->editora->nome ?? '—',
                'Autores' => $livro->autores->pluck('nome')->implode(', '),
                'Preço (€)' => $livro->preco,
            ];
        });
    }

    public function headings(): array
    {
        return ['ID', 'Nome', 'ISBN', 'Bibliografia', 'Editora', 'Autores', 'Preço (€)'];
    }
}

