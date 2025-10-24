<?php

namespace App\Http\Controllers;

use App\Models\VideoAnalysis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\SquatAnalysis;
use App\Models\SquatRepMetric;
use App\Models\DeadliftAnalysis;
use App\Models\DeadliftRepMetric;
use App\Filament\Resources\VideoAnalysisResource;

class VideoAnalysisController extends Controller
{
    private function api()
    {
        return Http::timeout(config('services.video_api.timeout'))
            ->baseUrl(config('services.video_api.base'));
    }

    // Opcional: formulario inicial si usas una vista fuera de Filament
    public function showUploadForm(string $movement)
    {
        abort_unless(in_array($movement, ['squat', 'bench', 'deadlift']), 404);

        $titles = [
            'squat' => 'Subir video – Sentadilla',
            'bench' => 'Subir video – Press banca',
            'deadlift' => 'Subir video – Peso muerto',
        ];

        return view('filament.video.upload', [
            'movement' => $movement,
            'title'    => $titles[$movement] ?? 'Subir video',
        ]);
    }
    private function handleSuccessfulAnalysis(VideoAnalysis $va, array $apiData): void
    {
        $va->update([
            'status'       => 'done',
            'analyzed_at'  => now(),
            'download_url' => $apiData['result']['download_url'] ?? null,
            'raw_metrics'  => json_encode($apiData['result'] ?? null),
        ]);
        if ($va->movement === 'squat') {
            $result = $apiData['result'];
            $summary = $result['summary'];
            $repMetrics = $result['metrics'] ?? [];

            $squatAnalysis = SquatAnalysis::create([
                'video_analysis_id'    => $va->id,
                'total_reps'           => $result['count'] ?? 0,
                'avg_min_knee_angle'   => $summary['avg_min_knee_angle'] ?? null,
                'avg_efficiency_pct'   => $this->avgEfficiency($repMetrics),
                'avg_rms_px'           => $this->avgRms($repMetrics),
                'depth_label'          => $summary['depth_label'] ?? null,
                'depth_message'        => $summary['depth_message'] ?? null,
                'best_rep_num'         => $summary['best_rep'] ?? null,
                'best_efficiency_pct'  => $summary['best_efficiency_pct'] ?? null,
                'worst_rep_num'        => $summary['worst_rep'] ?? null,
                'worst_efficiency_pct' => $summary['worst_efficiency_pct'] ?? null,
            ]);

            $this->saveRepMetrics($squatAnalysis, $repMetrics);
        } elseif ($va->movement === 'deadlift') {
            $result = $apiData['result'];
            $summary = $result['summary'];
            $repMetrics = $result['metrics'] ?? [];

            $va->deadliftAnalysis()->delete();

            $deadliftAnalysis = $va->deadliftAnalysis()->create([
                'video_analysis_id'             => $va->id,
                'total_reps'                    => $result['count'] ?? 0,
                'avg_efficiency_pct'            => $summary['avg_efficiency_pct'] ?? null,
                'avg_shoulder_bar_deviation_px' => $summary['avg_horizontal_deviation_px'] ?? null,
                'summary_message'               => $summary['summary_message'] ?? null,
            ]);
            $repDataToInsert = [];
            foreach ($repMetrics as $repData) {
                $repDataToInsert[] = [
                    'deadlift_analysis_id'          => $deadliftAnalysis->id,
                    'rep_number'                    => $repData['rep'],
                    'path_length_px'                => $repData['path_len_px'] ?? 0.0,
                    'vertical_range_px'             => $repData['vert_range_px'] ?? 0.0,
                    'excess_path_px'                => $repData['excess_path_px'] ?? 0.0,
                    'efficiency_pct'                => $repData['efficiency_pct'],
                    'rms_px'                        => $repData['rms_px'],
                    'tilt_deg'                      => $repData['tilt_deg'],
                    'avg_shoulder_bar_deviation_px' => $repData['avg_horizontal_deviation_px'] ?? null,
                    'created_at'                    => now(),
                    'updated_at'                    => now(),
                ];
            }

            if (!empty($repDataToInsert)) {
                DeadliftRepMetric::insert($repDataToInsert);
            }
        }
    }
    private function saveRepMetrics(SquatAnalysis $squatAnalysis, array $repMetrics): void
    {
        $repDataToInsert = [];
        foreach ($repMetrics as $repData) {
            $repDataToInsert[] = [
                'squat_analysis_id' => $squatAnalysis->id,
                'rep_number'        => $repData['rep'],
                'min_knee_angle'    => $repData['min_angle_deg'],
                // ... resto de los campos de squat
                'path_length_px'    => $repData['path_len_px'] ?? 0.0,
                'vertical_range_px' => $repData['vert_range_px'] ?? 0.0,
                'excess_path_px'    => $repData['excess_path_px'] ?? 0.0,
                'efficiency_pct'    => $repData['efficiency_pct'],
                'rms_px'            => $repData['rms_px'],
                'tilt_deg'          => $repData['tilt_deg'],
                'created_at'        => now(),
                'updated_at'        => now(),
            ];
        }

        if (!empty($repDataToInsert)) {
            $squatAnalysis->repMetrics()->insert($repDataToInsert);
        }
    }

    private function avgRms(?array $metrics): ?float
    {
        if (!$metrics) return null;
        $vals = [];
        foreach ($metrics as $m) {
            if (is_array($m) && isset($m['rms_px']) && $m['rms_px'] !== null) {
                $vals[] = (float)$m['rms_px'];
            }
        }
        return count($vals) ? round(array_sum($vals) / count($vals), 2) : null;
    }

    private function avgEfficiency(?array $metrics): ?float
    {
        if (!$metrics) return null;
        $vals = [];
        foreach ($metrics as $m) {
            if (is_array($m) && isset($m['efficiency_pct']) && $m['efficiency_pct'] !== null) {
                $vals[] = (float)$m['efficiency_pct'];
            }
        }
        return count($vals) ? round(array_sum($vals) / count($vals), 2) : null;
    }

    // --------- Upload (crea BD y decide a dónde ir) -----------
    public function upload(Request $req)
    {
        @set_time_limit(0);
        @ini_set('max_execution_time', '0');

        $req->validate([
            'movement'   => 'required|in:squat,bench,deadlift',
            'video'      => ['required', 'file', 'mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-matroska', 'max:70000'],
            'bar_manual' => ['nullable'],
        ]);

        // 1) registro inicial
        $va = VideoAnalysis::create([
            'user_id'  => Auth::id(),
            'movement' => $req->movement,
            'status'   => 'processing',
        ]);

        // 2) subir a API
        $file = $req->file('video');
        $resp = $this->api()
            ->attach('video', fopen($file->getRealPath(), 'r'), $file->getClientOriginalName())
            ->post('/api/upload', [
                'bar_manual' => $req->boolean('bar_manual') ? 'on' : '',
            ]);

        if (!$resp->ok()) {
            $va->update(['status' => 'failed']);
            return back()->withErrors(['api' => $resp->json('error') ?? 'Error llamando a la API'])->withInput();
        }

        $data = $resp->json();

        // 3) actualizar registro
        $va->update([
            'status'     => $data['status'] ?? 'processing',
            'job_id'     => $data['job_id'] ?? null,
            'frame_url'  => $data['frame_url'] ?? null,
        ]);

        // a) pick requerido
        if (in_array($va->status, ['need_pick', 'need_pick_full'])) {
            return redirect(VideoAnalysisResource::getUrl(
                $va->status === 'need_pick_full' ? 'pick-full' : 'pick-bar',
                ['record' => $va]
            ));
        }

        // b) done automático (sin barra)
        if (($data['status'] ?? null) === 'done') {
            // REEMPLAZA el bloque $va->update([...]) con esto:
            $this->handleSuccessfulAnalysis($va, $data);
            return redirect(VideoAnalysisResource::getUrl('result', ['record' => $va]));
        }

        // fallback
        return redirect(VideoAnalysisResource::getUrl('index'));
    }

    // --------- Iniciar manual completo (devuelve need_pick_full) -----------
    public function startManual(Request $req)
    {
        $req->validate(['job_id' => 'required']);
        $va = VideoAnalysis::where('job_id', $req->job_id)->first();

        $resp = $this->api()->asForm()->post('/api/start_manual', [
            'job_id' => $req->string('job_id')->toString(),
            'original_filename' => $req->input('original_filename', ''),
        ]);

        if (!$resp->ok()) {
            if ($va) $va->update(['status' => 'failed']);
            return back()->withErrors(['api' => $resp->json('error') ?? 'Error'])->withInput();
        }

        $data = $resp->json(); // need_pick_full
        if ($va) {
            $va->update(['status' => 'need_pick_full', 'frame_url' => $data['frame_url'] ?? null]);
        }

        return redirect(VideoAnalysisResource::getUrl('pick-full', ['record' => $va]));
    }

    // --------- AUTO con pick de barra (usa /api/process_manual) -----------
    public function processManual(Request $req)
    {
        @set_time_limit(0);
        @ini_set('max_execution_time', '0');

        $req->validate([
            'job_id' => 'required',
            'cx'     => 'required|integer',
            'cy'     => 'required|integer',
            'r'      => 'required|integer|min:3',
        ]);

        $resp = $this->api()->asForm()->post('/api/process_manual', $req->only('job_id', 'cx', 'cy', 'r'));
        // Si prefieres el alias nuevo:
        // $resp = $this->api()->asForm()->post('/api/process_auto', $req->only('job_id','cx','cy','r'));
        // 1. Buscamos el análisis por job_id para saber qué ejercicio es
        $va = VideoAnalysis::where('job_id', $req->job_id)->firstOrFail();

        // 2. Decidimos el endpoint basado en el 'movement' guardado
        if ($va->movement === 'deadlift') {
            $endpoint = '/api/process_deadlift';
        } else {
            // Para 'squat' y cualquier otro caso, usamos el endpoint manual general
            $endpoint = '/api/process_manual';
        }

        // 3. Hacemos la llamada al endpoint correcto
        $resp = $this->api()
            ->asForm()
            ->post($endpoint, $req->only('job_id', 'cx', 'cy', 'r'));

        if (!$resp->ok()) {
            $va->update(['status' => 'failed']); // Modificado para usar la instancia que ya tienes
            return back()->withErrors(['api' => $resp->json('error') ?? 'Error'])->withInput();
        }

        $data = $resp->json();
        $this->handleSuccessfulAnalysis($va, $data);

        return redirect(VideoAnalysisResource::getUrl('result', ['record' => $va]));
    }

    // --------- Manual completo (puntos cuerpo + barra) -----------
    public function processManualFull(Request $req)
    {
        @set_time_limit(0);
        @ini_set('max_execution_time', '0');

        $req->validate([
            'job_id' => 'required',
            // ... resto de las validaciones
            'r'  => 'required|integer|min:3',
        ]);

        $payload = $req->only('job_id', 'hx', 'hy', 'kx', 'ky', 'ax', 'ay', 'cx', 'cy', 'r');
        $resp = $this->api()->asForm()->post('/api/process_manual_full', $payload);

        if (!$resp->ok()) {
            VideoAnalysis::where('job_id', $req->job_id)->update(['status' => 'failed']);
            return back()->withErrors(['api' => $resp->json('error') ?? 'Error'])->withInput();
        }

        // --- AJUSTE AQUÍ ---
        $d  = $resp->json();
        // Añade esta línea para buscar el registro antes de pasarlo a la función
        $va = VideoAnalysis::where('job_id', $req->job_id)->firstOrFail();

        $this->handleSuccessfulAnalysis($va, $d);

        return redirect(VideoAnalysisResource::getUrl('result', ['record' => $va]));
    }

    // --------- Proxy descarga -----------
    public function proxyDownload(Request $req)
    {
        $url = $req->query('url');
        if (! $url) {
            abort(400, 'Missing url');
        }

        // Seguridad básica: evitar SSRF. Allowlist de hosts (ajusta según tu entorno).
        $allowedHosts = [
            parse_url(config('services.video_api.base'), PHP_URL_HOST), // API interna
            '127.0.0.1',
            'localhost',
            // añade dominios externos si confías en ellos, p.ej. 'mi-dominio.com'
        ];
        $host = parse_url($url, PHP_URL_HOST);
        if (! $host || ! in_array($host, $allowedHosts, true)) {
            abort(403, 'Host no permitido');
        }

        // Descargar el archivo (usar Http::get para URLs absolutas)
        $response = \Illuminate\Support\Facades\Http::timeout(config('services.video_api.timeout', 30))->get($url);

        if (! $response->ok()) {
            abort(502, 'No se pudo descargar el archivo remoto');
        }

        // Intentar derivar un filename desde la URL o usar uno por defecto:
        $path = parse_url($url, PHP_URL_PATH);
        $filename = basename($path) ?: 'resultado.mp4';

        // Responder como streamed response con headers adecuados
        return response()->streamDownload(function () use ($response) {
            echo $response->body();
        }, $filename, [
            'Content-Type' => $response->header('Content-Type', 'video/mp4'),
            'Content-Length' => $response->header('Content-Length') ?? strlen($response->body()),
        ]);
    }
}
