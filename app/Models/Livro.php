<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Livro extends Model
{
    protected $fillable = ['isbn', 'nome', 'editora_id', 'bibliografia', 'imagem_capa', 'preco'];

    // 🔐 Mutator para cifrar o ISBN ao guardar
    public function setIsbnAttribute($value)
    {
        $this->attributes['isbn'] = Crypt::encryptString($value);
    }

    // 🔓 Accessor para decifrar o ISBN ao ler
    public function getIsbnAttribute($value)
    {
        return Crypt::decryptString($value);
    }

    public function autores()
    {
        return $this->belongsToMany(Autor::class, 'autor_livro', 'livro_id', 'autor_id');
    }

    public function editora()
    {
        return $this->belongsTo(Editora::class);
    }
}
