<?php

namespace App\Policies;

use App\Models\Device;
use App\Models\User;

class DevicePolicy
{
    /**
     * Ver menú/página de dispositivos disponibles.
     */
    public function viewAvailable(User $user): bool
    {
        return $user->can('devices.view_available');
    }

    /**
     * Conectar (crear sesión) si tiene permiso y el dispositivo está “conectable”.
     */
    public function connect(User $user, Device $device): bool
    {
        if (! $user->can('devices.connect')) {
            return false;
        }
        // Helper recomendado en Device: isConnectable()
        if (method_exists($device, 'isConnectable')) {
            return $device->isConnectable();
        }
        // Fallback si no tienes el helper:
        $active = $device->activeSession()->first();
        return $active === null; // no hay sesión activa
    }

    /**
     * Desconectar si tiene permiso y es dueño de la sesión activa.
     */
    public function disconnect(User $user, Device $device): bool
    {
        if (! $user->can('devices.disconnect')) {
            return false;
        }
        if (method_exists($device, 'leasedBy')) {
            return $device->leasedBy($user);
        }
        $active = $device->activeSession()->first();
        return $active && (int) $active->athlete_id === (int) $user->id; // ajusta a user_id si corresponde
    }

    /**
     * Forzar liberación (solo admin u otro rol con permiso).
     */
    public function forceRelease(User $user): bool
    {
        return $user->can('devices.force_release');
    }
}
