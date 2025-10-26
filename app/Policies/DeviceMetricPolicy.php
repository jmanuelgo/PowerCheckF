<?php

namespace App\Policies;

use App\Models\User;
use App\Models\DeviceMetric;
use Illuminate\Auth\Access\HandlesAuthorization;

class DeviceMetricPolicy
{
    use HandlesAuthorization;
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_device::metric');
    }
    public function view(User $user, DeviceMetric $deviceMetric): bool
    {
        return $user->can('view_device::metric');
    }

    public function create(User $user): bool
    {
        return $user->can('create_device::metric');
    }
    public function update(User $user, DeviceMetric $deviceMetric): bool
    {
        return $user->can('update_device::metric');
    }
    public function delete(User $user, DeviceMetric $deviceMetric): bool
    {
        return $user->can('delete_device::metric');
    }
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_device::metric');
    }
    public function forceDelete(User $user, DeviceMetric $deviceMetric): bool
    {
        return $user->can('force_delete_device::metric');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_device::metric');
    }

    public function restore(User $user, DeviceMetric $deviceMetric): bool
    {
        return $user->can('restore_device::metric');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_device::metric');
    }

    public function replicate(User $user, DeviceMetric $deviceMetric): bool
    {
        return $user->can('replicate_device::metric');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_device::metric');
    }
}
