<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Encomenda extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'morada',
        'total',
        'estado',
    ];

    protected $casts = [
        'morada' => 'array',
    ];

    public function livros()
    {
        return $this->belongsToMany(Livro::class, 'encomenda_livro')
            ->withPivot('quantidade', 'preco_unitario')
            ->withTimestamps();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
