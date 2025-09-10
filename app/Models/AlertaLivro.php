<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlertaLivro extends Model
{
    protected $table = 'alertas_livros';

    protected $fillable = ['livro_id', 'user_id', 'notificado_em'];

    public function livro()
    {
        return $this->belongsTo(Livro::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
