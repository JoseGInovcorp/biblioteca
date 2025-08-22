<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Editora extends Model
{
    protected $fillable = ['nome', 'logotipo'];

    public function livros()
    {
        return $this->hasMany(Livro::class);
    }
}
