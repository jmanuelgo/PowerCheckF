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
        $request->validate([
            'bpm'          => 'required|numeric|min:0|max:400',
            'repeticiones' => 'required|integer|min:0|max:10000',
            'exercise'     => 'nullable|string|max:50|required_without:ejercicio',
            'ejercicio'    => 'nullable|string|max:50|required_without:exercise',
        ]);

        $device = Device::where('ip', $request->ip())->first();
        if (! $device)  return response()->json(['ok'=>false,'err'=>'device_not_found'],404);

        $session = $device->activeSession()->first();
        if (! $session) return response()->json(['ok'=>false,'err'=>'no_active_session'],409);

        $attrs = [
            'device_id'    => $device->id,
            'athlete_id'   => $session->athlete_id,
            'bpm'          => (float) $request->input('bpm'),
            'repeticiones' => (int)   $request->input('repeticiones'),
            // NO 'captured_at' aquí; lo pone la BD con DEFAULT o el hook del modelo
        ];
        if (Schema::hasColumn('device_metrics', 'ejercicio')) {
            $attrs['ejercicio'] = $request->input('ejercicio', $request->input('exercise'));
        }

        $metric = DeviceMetric::create($attrs);

        $device->update(['last_seen' => now()]);
        if (Schema::hasColumn('device_sessions', 'last_seen_at') && $session) {
            $session->update(['last_seen_at' => now()]);
        }

        return response()->json(['ok'=>true,'metric_id'=>$metric->id], 201);

    } catch (ValidationException $e) {
        return response()->json(['ok'=>false,'err'=>'validation','messages'=>$e->errors()],422);
    } catch (QueryException $e) {
        Log::error('METRICS SQL', ['msg'=>$e->getMessage()]);
        return response()->json(['ok'=>false,'err'=>'sql','msg'=>$e->getMessage()],500);
    } catch (\Throwable $e) {
        Log::error('METRICS ERR', ['msg'=>$e->getMessage()]);
        return response()->json(['ok'=>false,'err'=>'server','msg'=>$e->getMessage()],500);
    }
});