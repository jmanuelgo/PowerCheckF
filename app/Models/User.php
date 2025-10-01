<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;   // <-- agrega esto
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, Authorizable;  // <-- agrega Authorizable

    // Para Spatie (evita mismatch de guard)
    protected $guard_name = 'web';                       // <-- agrega esto

    protected $fillable = [
        'name',
        'apellidos',
        'celular',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function entrenador()
    {
        return $this->hasOne(Entrenador::class);
    }

    public function atleta()
    {
        return $this->hasOne(Atleta::class);
    }
}
