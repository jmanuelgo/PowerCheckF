<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use App\Models\Device;
use App\Models\DeviceSession;
use App\Models\DeviceMetric;
use App\Models\Atleta;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;


Route::get('/test', fn() => ['ok' => true]);

// 1) HEARTBEAT / PING del ESP32 (sin token)
// Recomendado: deja 'throttle' para limitar abuso en LAN
Route::post('/devices/ping', function (Request $request) {
    $device = Device::updateOrCreate(
        ['ip' => $request->ip()],
        [
            'name'      => $request->input('name', 'ESP32'),
            'status'    => 'ready',
            'last_seen' => now(),
        ]
    );

    $session = $device->activeSession()->first(); // sesión activa (si hay)
    $assignedAthleteId = $session?->athlete_id;

    // NUEVO: altura y nombre del atleta (si hay sesión)
    $athleteHeight = null;
    $athleteName   = null;

    if ($assignedAthleteId) {
        $athleteHeight = Atleta::where('user_id', $assignedAthleteId)->value('altura'); // en cm
        if ($user = User::find($assignedAthleteId)) {
            $athleteName = trim(($user->name ?? '') . ' ' . ($user->apellidos ?? '')) ?: ($user->name ?? null);
        }
    }

    return response()->json([
        'ok'                  => true,
        'device_id'           => $device->id,
        'is_available'        => $device->is_available,
        'assigned_athlete_id' => $assignedAthleteId,
        'athlete_height'      => $athleteHeight, 
        'athlete_name'        => $athleteName,  
        'at'                  => now()->toISOString(),
    ]);
})->middleware('throttle:30,1');

// 2) RECEPCIÓN DE MÉTRICAS (sin token)
Route::post('/devices/metrics', function (Request $request) {
    try {
        Log::info('METRICS_HIT', ['ip'=>$request->ip(), 'payload'=>$request->all()]);

        // Validación mínima (lo que llega desde el ESP32)
        $request->validate([
            'bpm'          => 'required|numeric|min:0|max:400',
            'repeticiones' => 'required|integer|min:0|max:10000',
            'exercise'     => 'nullable|string|max:50',
            'ejercicio'    => 'nullable|string|max:50',
            'device_id'    => 'nullable|integer',
            'device_name'  => 'nullable|string|max:100',
            'athlete_id'   => 'nullable|integer', // el servidor usará el de la sesión; esto es opcional
        ]);

        // 1) Resolver el Device de forma robusta: device_id > device_name > ip
        $device = null;
        if ($request->filled('device_id')) {
            $device = Device::find((int) $request->input('device_id'));
        }
        if (! $device && $request->filled('device_name')) {
            $device = Device::where('name', $request->input('device_name'))->first();
        }
        if (! $device) {
            $device = Device::where('ip', $request->ip())->first();
        }
        if (! $device) {
            Log::warning('METRICS_DEVICE_NOT_FOUND', ['ip'=>$request->ip()]);
            return response()->json(['ok'=>false,'err'=>'device_not_found'],404);
        }

        // 2) Buscar sesión activa del device
        $session = $device->activeSession()->first();
        if (! $session) {
            Log::warning('METRICS_NO_ACTIVE_SESSION', ['device_id'=>$device->id]);
            return response()->json(['ok'=>false,'err'=>'no_active_session'],409);
        }

        // 3) Construir el registro mapeando nombres de columnas existentes
        $metric = new DeviceMetric();

        // device_id / athlete_id
        if (Schema::hasColumn($metric->getTable(), 'device_id')) {
            $metric->device_id = $device->id;
        }
        if (Schema::hasColumn($metric->getTable(), 'athlete_id')) {
            $metric->athlete_id = $session->athlete_id; // del servidor (de la sesión), no del payload
        }

        // bpm
        if (Schema::hasColumn($metric->getTable(), 'bpm')) {
            $metric->bpm = (float) $request->input('bpm');
        }

        // repeticiones vs reps
        $repsVal = (int) $request->input('repeticiones');
        if (Schema::hasColumn($metric->getTable(), 'repeticiones')) {
            $metric->repeticiones = $repsVal;
        } elseif (Schema::hasColumn($metric->getTable(), 'reps')) {
            $metric->reps = $repsVal;
        }

        // ejercicio vs exercise
        $exercise = $request->input('ejercicio', $request->input('exercise'));
        if ($exercise !== null) {
            if (Schema::hasColumn($metric->getTable(), 'ejercicio')) {
                $metric->ejercicio = (string) $exercise;
            } elseif (Schema::hasColumn($metric->getTable(), 'exercise')) {
                $metric->exercise = (string) $exercise;
            }
        }

        // captured_at si existe
        if (Schema::hasColumn($metric->getTable(), 'captured_at') && ! $metric->captured_at) {
            $metric->captured_at = now();
        }

        // 4) Guardar (sin mass-assignment)
        $metric->save();

        // 5) Actualizar last_seen
        $device->update(['last_seen' => now()]);
        if (Schema::hasColumn('device_sessions', 'last_seen_at') && $session) {
            $session->update(['last_seen_at' => now()]);
        }

        Log::info('METRICS_OK', ['metric_id'=>$metric->id]);
        return response()->json(['ok'=>true,'metric_id'=>$metric->id], 201);

    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::warning('METRICS_VALIDATION', $e->errors());
        return response()->json(['ok'=>false,'err'=>'validation','messages'=>$e->errors()],422);
    } catch (\Illuminate\Database\QueryException $e) {
        Log::error('METRICS_SQL', ['msg'=>$e->getMessage()]);
        return response()->json(['ok'=>false,'err'=>'sql','msg'=>$e->getMessage()],500);
    } catch (\Throwable $e) {
        Log::error('METRICS_ERR', ['msg'=>$e->getMessage(), 'trace'=>$e->getTraceAsString()]);
        return response()->json(['ok'=>false,'err'=>'server','msg'=>$e->getMessage()],500);
    }
});