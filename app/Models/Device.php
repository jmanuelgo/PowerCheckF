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
    public function sessions()
    {
        return $this->hasMany(DeviceSession::class);
    }

    public function activeSession()
    {
        return $this->hasOne(DeviceSession::class)
            ->where('status', 'active')
            ->whereNull('ended_at')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    public function getIsAvailableAttribute(): bool
    {
        return $this->last_seen && $this->last_seen->gt(Carbon::now()->subSeconds(30));
    }

    public function getAssignedAthleteIdAttribute(): ?int
    {
        return optional($this->activeSession()->first())->athlete_id;
    }

    public function isConnectable(): bool
    {
        return $this->activeSession()->doesntExist();
    }
    public function leasedBy(?\App\Models\User $user): bool
    {
        if (!$user) return false;
        $s = $this->activeSession()->first();
        return $s && (int) $s->athlete_id === (int) $user->id;
    }
    public function scopeWithoutActiveSession($query)
    {
        return $query->whereDoesntHave('activeSession');
    }
}
