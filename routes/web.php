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
});
