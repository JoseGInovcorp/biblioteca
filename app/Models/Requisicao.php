<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Requisicao extends Model
{
    protected $table = 'requisicoes';

    protected $fillable = [
        'numero_sequencial',
        'livro_id',
        'cidadao_id',
        'foto_cidadao',
        'data_inicio',
        'data_fim_prevista',
        'data_fim_real',
        'status',
    ];

    public function livro()
    {
        return $this->belongsTo(Livro::class);
    }

    public function cidadao()
    {
        return $this->belongsTo(User::class, 'cidadao_id');
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    protected static function booted()
    {
        static::creating(function ($requisicao) {
            $ultimo = Requisicao::max('numero_sequencial');
            $requisicao->numero_sequencial = $ultimo ? $ultimo + 1 : 1;
        });
    }
}
