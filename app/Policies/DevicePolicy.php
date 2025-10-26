<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Device;
use Illuminate\Auth\Access\HandlesAuthorization;

class DevicePolicy
{
    use HandlesAuthorization;

 
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
