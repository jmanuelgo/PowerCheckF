<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Policies\RolePolicy;
use App\Policies\PermissionPolicy;

// <-- NUEVO
use App\Models\Device;
use App\Policies\DevicePolicy;
// (Opcional) si quieres policy para sesiones:
// use App\Models\DeviceSession;
// use App\Policies\DeviceSessionPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Role::class        => RolePolicy::class,
        Permission::class  => PermissionPolicy::class,

        // <-- NUEVO: registra tu policy de Device
        Device::class      => DevicePolicy::class,

        // (Opcional)
        // DeviceSession::class => DeviceSessionPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Solo super_admin tiene bypass total
        Gate::before(function ($user, $ability) {
            return $user?->hasRole('super_admin') ? true : null;
        });
    }
}
