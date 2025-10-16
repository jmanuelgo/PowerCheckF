<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VideoAnalysisController;

Route::get('/', function () {
    return view('inicioPowerCheck');
});



Route::middleware(['auth'])->group(function () {
    Route::get('/video-analisis/upload/{movement}', [VideoAnalysisController::class, 'showUploadForm'])
        ->whereIn('movement', ['squat', 'bench', 'deadlift'])
        ->name('video.upload.form');

    Route::post('/video-analisis/upload',       [VideoAnalysisController::class, 'upload'])->name('video.upload');
    Route::post('/video-analisis/start-manual', [VideoAnalysisController::class, 'startManual'])->name('video.startManual');
    Route::post('/video-analisis/manual',       [VideoAnalysisController::class, 'processManual'])->name('video.manual');
    Route::post('/video-analisis/manual-full',  [VideoAnalysisController::class, 'processManualFull'])->name('video.manualFull');

    // <-- aquí cambiamos el name para que coincida con la vista
    Route::get('/video-analisis/proxy',         [VideoAnalysisController::class, 'proxyDownload'])
        ->name('video.proxyDownload');

    Route::get('/devices/available', function () {
        $devices = Device::orderByDesc('last_seen')->get()
            ->map(fn($d) => [
                'id'         => $d->id,
                'name'       => $d->name,
                'ip'         => $d->ip,
                'isAvailable'=> $d->is_available,
                'assigned'   => (bool)$d->assigned_athlete_id,
            ]);
        return view('devices.available', compact('devices'));
    })->name('devices.available');

    // Reclamar un dispositivo (el atleta lo conecta a su sesión)
    Route::post('/devices/{device}/claim', function (Request $request, Device $device) {
        if (!$device->is_available) {
            return back()->withErrors('El dispositivo no está disponible ahora.');
        }

        $active = $device->activeSession()->first();
        if ($active && $active->athlete_id !== auth()->id()) {
            return back()->withErrors('El dispositivo ya está ocupado.');
        }

        if (!$active) {
            DeviceSession::create([
                'device_id'  => $device->id,
                'athlete_id' => auth()->id(),
                'status'     => 'active',
                'started_at' => Carbon::now(),
                'expires_at' => Carbon::now()->addMinutes(30), // opcional
            ]);
        }

        return back()->with('status', '¡Dispositivo conectado!');
    })->name('devices.claim');

    // Liberar dispositivo
    Route::post('/devices/{device}/release', function (Request $request, Device $device) {
        $active = $device->activeSession()->first();
        if ($active && $active->athlete_id === auth()->id()) {
            $active->update(['status' => 'ended', 'ended_at' => now()]);
        }
        return back()->with('status', 'Dispositivo liberado.');
    })->name('devices.release');
});
