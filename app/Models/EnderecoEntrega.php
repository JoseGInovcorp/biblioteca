<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnderecoEntrega extends Model
{
    protected $table = 'enderecos_entrega'; // 👈 nome exato da tabela

    protected $fillable = [
        'user_id',
        'nome',
        'telefone',
        'morada',
        'codigo_postal',
        'localidade',
        'pais'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
