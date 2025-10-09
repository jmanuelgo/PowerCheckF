<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Ejercicio;
use Illuminate\Support\Facades\DB;

class EjercicioPolicy
{
    public function viewAny(User $user): bool
    {
        // Permisos Spatie controlan el acceso al index (Filament chequea 'view_any_*')
        return $user->can('view_any_ejercicio');
    }

    public function view(User $user, Ejercicio $ejercicio): bool
    {
        // Si tiene permiso general de ver y NO es atleta, dejar a Spatie manejarlo
        if ($user->hasRole(['super_admin', 'admin', 'entrenador'])) {
            return $user->can('view_ejercicio');
        }

        // Atleta: puede ver solo si el ejercicio está en sus rutinas
        if ($user->hasRole('atleta') && $user->can('view_ejercicio')) {
            return DB::table('ejercicios_dia as ed')
                ->join('dias_entrenamiento as de', 'de.id', '=', 'ed.dia_entrenamiento_id')
                ->join('semanas_rutina as sr', 'sr.id', '=', 'de.semana_rutina_id')
                ->join('rutinas as r', 'r.id', '=', 'sr.rutina_id')
                ->join('atletas as a', 'a.id', '=', 'r.atleta_id')
                ->where('ed.ejercicio_id', $ejercicio->id)
                ->where('a.user_id', $user->id)
                ->exists();
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->can('create_ejercicio');
    }

    public function update(User $user, Ejercicio $ejercicio): bool
    {
        return $user->can('update_ejercicio');
    }

    public function delete(User $user, Ejercicio $ejercicio): bool
    {
        return $user->can('delete_ejercicio');
    }

    public function restore(User $user, Ejercicio $ejercicio): bool
    {
        return $user->can('restore_ejercicio');
    }

    public function forceDelete(User $user, Ejercicio $ejercicio): bool
    {
        return $user->can('force_delete_ejercicio');
    }
}
