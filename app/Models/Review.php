<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'livro_id',
        'requisicao_id',
        'user_id',
        'comentario',
        'estado',
        'justificacao',
    ];

    // Relação com o livro
    public function livro()
    {
        return $this->belongsTo(Livro::class);
    }

    // Relação com a requisição
    public function requisicao()
    {
        return $this->belongsTo(Requisicao::class, 'requisicao_id');
    }


    // Relação com o cidadão que fez o review
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Verifica se está ativo
    public function isAtivo()
    {
        return $this->estado === 'ativo';
    }

    // Verifica se está suspenso
    public function isSuspenso()
    {
        return $this->estado === 'suspenso';
    }

    // Verifica se foi recusado
    public function isRecusado()
    {
        return $this->estado === 'recusado';
    }
}
