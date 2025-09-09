<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Livro extends Model
{
    protected $fillable = ['isbn', 'nome', 'editora_id', 'descricao', 'imagem_capa', 'preco'];

    public function autores()
    {
        return $this->belongsToMany(Autor::class, 'autor_livro', 'livro_id', 'autor_id');
    }

    public function editora()
    {
        return $this->belongsTo(Editora::class);
    }

    public function requisicoes()
    {
        return $this->hasMany(Requisicao::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}

// 🔓 Descriptografar ISBNs antigos
foreach (Livro::all() as $livro) {
    $raw = $livro->getRawOriginal('isbn');

    if (is_string($raw) && strlen($raw) > 20 && str_starts_with($raw, 'eyJ')) {
        try {
            $livro->isbn = decrypt($raw);
            $livro->save();
        } catch (\Exception $e) {
            // Ignorar falhas de descriptografia
        }
    }
}
