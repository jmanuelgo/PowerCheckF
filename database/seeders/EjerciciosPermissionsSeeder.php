<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class EjerciciosPermissionsSeeder extends Seeder
{
    protected string $guard = 'web';

    public function run(): void
    {
        // Crea el set de permisos al estilo Filament Shield
        $perms = [
            'view_ejercicio',
            'view_any_ejercicio',
            'create_ejercicio',
            'update_ejercicio',
            'restore_ejercicio',
            'restore_any_ejercicio',
            'replicate_ejercicio',
            'reorder_ejercicio',
            'delete_ejercicio',
            'delete_any_ejercicio',
            'force_delete_ejercicio',
            'force_delete_any_ejercicio',
        ];

        foreach ($perms as $name) {
            Permission::firstOrCreate([
                'name'       => $name,
                'guard_name' => $this->guard,
            ]);
        }

        // Roles existentes (según tu seeder/BD)
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => $this->guard]);
        $admin      = Role::firstOrCreate(['name' => 'admin',       'guard_name' => $this->guard]);
        $entrenador = Role::firstOrCreate(['name' => 'entrenador',  'guard_name' => $this->guard]);
        $atleta     = Role::firstOrCreate(['name' => 'atleta',      'guard_name' => $this->guard]);

        // 1) super_admin: todo
        $superAdmin->givePermissionTo($perms);

        // 2) admin: solo ver (index + show)
        $admin->givePermissionTo([
            'view_any_ejercicio',
            'view_ejercicio',
        ]);

        // 3) entrenador: todo
        $entrenador->givePermissionTo($perms);

        // 4) atleta: solo ver (NO index global, verás cómo limitar más abajo con Policy)
        //    - Si NO quieres que vea el index global, NO le des 'view_any_ejercicio'.
        //    - Le damos 'view_ejercicio' para que pueda ver el detalle si está asignado (Policy).
        $atleta->givePermissionTo([
            'view_ejercicio',
            // 'view_any_ejercicio', // <- déjalo comentado si no debe ver el listado completo
        ]);

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
