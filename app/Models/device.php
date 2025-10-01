<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Device extends Model
{
    protected $table = 'devices';

    protected $fillable = ['name', 'ip', 'status', 'last_seen'];

    protected $casts = [
        'last_seen' => 'datetime',
    ];

    /** -------------------------------------------
     *  Relaciones
     *  ------------------------------------------- */
    public function sessions()
    {
        return $this->hasMany(DeviceSession::class);
    }

    public function activeSession()
    {
        // Sesión activa, no terminada y no expirada
        return $this->hasOne(DeviceSession::class)
            ->where('status', 'active')
            ->whereNull('ended_at')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    /** -------------------------------------------
     *  Accessors
     *  ------------------------------------------- */

    // Disponible si el último ping fue hace <= 30s
    public function getIsAvailableAttribute(): bool
    {
        return $this->last_seen && $this->last_seen->gt(Carbon::now()->subSeconds(30));
    }

    public function getAssignedAthleteIdAttribute(): ?int
    {
        return optional($this->activeSession()->first())->athlete_id;
    }

    /** -------------------------------------------
     *  Helpers para Policies / UI
     *  ------------------------------------------- */

    // “Conectable” si no hay sesión activa (aunque esté online/offline a nivel ping)
    public function isConnectable(): bool
    {
        return $this->activeSession()->doesntExist();
    }

    // ¿El dispositivo está arrendado por este usuario?
    public function leasedBy(?\App\Models\User $user): bool
    {
        if (!$user) return false;
        $s = $this->activeSession()->first();
        // OJO: si en DeviceSession la columna es user_id en vez de athlete_id, cambia aquí
        return $s && (int) $s->athlete_id === (int) $user->id;
    }

    /** -------------------------------------------
     *  Scopes (opcional)
     *  ------------------------------------------- */

    // Sólo dispositivos sin sesión activa (disponibles para conectar)
    public function scopeWithoutActiveSession($query)
    {
        return $query->whereDoesntHave('activeSession');
    }
}
