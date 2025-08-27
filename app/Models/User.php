<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Crypt;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasProfilePhoto, Notifiable, TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // 👈 Adicionado para permitir atribuir role
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // 🔐 Cifrar o nome ao guardar
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = Crypt::encryptString($value);
    }

    // 🔓 Decifrar o nome ao ler
    public function getNameAttribute($value)
    {
        return Crypt::decryptString($value);
    }

    // 👇 Métodos para verificar role
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCidadao(): bool
    {
        return $this->role === 'cidadao';
    }

    public function requisicoes()
    {
        return $this->hasMany(\App\Models\Requisicao::class, 'cidadao_id');
    }
}
