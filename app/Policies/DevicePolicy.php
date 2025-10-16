<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Device;
use Illuminate\Auth\Access\HandlesAuthorization;

class DevicePolicy
{
    use HandlesAuthorization;

    // ================================================================
    // MÉTODOS PERSONALIZADOS PARA TUS BOTONES
    // ================================================================

    /**
     * Determina si el usuario puede conectar con un dispositivo.
     * El botón "Conectar" solo aparecerá si esto devuelve true.
     */
    public function connect(User $user, Device $device): bool
    {
        // El dispositivo debe estar disponible (online)
        $isAvailable = $device->is_available;

        // El dispositivo no debe tener una sesión activa (estar libre)
        $isFree = !$device->activeSession()->exists();

        // El usuario debe tener el permiso explícito para conectar
        $hasPermission = $user->can('connect_device');

        return $isAvailable && $isFree && $hasPermission;
    }

    /**
     * Determina si el usuario puede desconectar su propia sesión.
     * El botón "Liberar" solo aparecerá si esto devuelve true.
     */
    public function disconnect(User $user, Device $device): bool
    {
        $activeSession = $device->activeSession()->first();

        // Si no hay sesión activa, no se puede desconectar
        if (!$activeSession) {
            return false;
        }

        // La sesión debe pertenecer al usuario actual Y el usuario debe tener el permiso
        $isOwner = (int) $activeSession->athlete_id === (int) $user->id;
        $hasPermission = $user->can('disconnect_device');

        return $isOwner && $hasPermission;
    }

    /**
     * Determina si un administrador puede forzar la liberación de un dispositivo.
     * El botón "Forzar liberación" solo aparecerá si esto devuelve true.
     */
    public function forceRelease(User $user, Device $device): bool
    {
        // Solo usuarios con este permiso pueden forzar la liberación
        return $user->can('forceRelease_device');
    }


    // ================================================================
    // MÉTODOS CRUD ESTÁNDAR (GENERADOS POR SHIELD)
    // ================================================================

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_device');
    }

    public function view(User $user, Device $device): bool
    {
        return $user->can('view_device');
    }

    public function create(User $user): bool
    {
        return $user->can('create_device');
    }

    public function update(User $user, Device $device): bool
    {
        return $user->can('update_device');
    }

    public function delete(User $user, Device $device): bool
    {
        return $user->can('delete_device');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_device');
    }

    public function forceDelete(User $user, Device $device): bool
    {
        return $user->can('force_delete_device');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_device');
    }

    public function restore(User $user, Device $device): bool
    {
        return $user->can('restore_device');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_device');
    }

    public function replicate(User $user, Device $device): bool
    {
        return $user->can('replicate_device');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_device');
    }
}