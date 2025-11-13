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
use App\Models\BenchPressAnalysis;
use App\Models\BenchPressRepMetric;


class VideoAnalysisController extends Controller
{
    private function api()
    {
        return Http::timeout(config('services.video_api.timeout'))
            ->baseUrl(config('services.video_api.base'));
    }

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
        $summaryEfficiency = null;
        $result = $apiData['result'];

        if ($va->movement === 'squat') {
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

            $summaryEfficiency = $squatAnalysis->avg_efficiency_pct;

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

            $summaryEfficiency = $deadliftAnalysis->avg_efficiency_pct;

        } elseif ($va->movement === 'bench') {
            $result = $apiData['result'];
            $summary = $result['summary'];
            $repMetrics = $result['metrics'] ?? [];

            $va->benchPressAnalysis()->delete();

            $benchAnalysis = $va->benchPressAnalysis()->create([
                'total_reps'      => $summary['repeticiones_totales'] ?? 0,
                'avg_score'       => $summary['score_promedio'] ?? null,
                'best_rep_num'    => $summary['mejor_rep_num'] ?? null,
                'best_rep_score'  => $summary['mejor_rep_score'] ?? null,
                'worst_rep_num'   => $summary['peor_rep_num'] ?? null,
                'worst_rep_score' => $summary['peor_rep_score'] ?? null,
            ]);

            $repDataToInsert = [];
            foreach ($repMetrics as $repData) {
                $repDataToInsert[] = [
                    'bench_press_analysis_id' => $benchAnalysis->id,
                    'rep_number'              => $repData['rep_num'],
                    'score_general'           => $repData['score_general'] ?? null,
                    'curvatura_j_px'          => $repData['curvatura_j_px'] ?? null,
                    'rectitud_bajada_rmse'    => $repData['rectitud_bajada_rmse'] ?? null,
                    'observacion'             => $repData['observacion'] ?? null,
                    'created_at'              => now(),
                    'updated_at'              => now(),
                ];
            }

            if (!empty($repDataToInsert)) {
                \App\Models\BenchPressRepMetric::insert($repDataToInsert);
            }


            $summaryEfficiency = $benchAnalysis->avg_score;
        }

        $va->update([
            'status'         => 'done',
            'analyzed_at'    => now(),
            'download_url'   => $apiData['result']['download_url'] ?? null,
            'raw_metrics'    => json_encode($apiData['result'] ?? null),
            'efficiency_pct' => $summaryEfficiency,
        ]);
    }
    private function saveRepMetrics(SquatAnalysis $squatAnalysis, array $repMetrics): void
    {
        $repDataToInsert = [];
        foreach ($repMetrics as $repData) {
            $repDataToInsert[] = [
                'squat_analysis_id' => $squatAnalysis->id,
                'rep_number'        => $repData['rep'],
                'min_knee_angle'    => $repData['min_angle_deg'],
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

    // Upload inicial
    public function upload(Request $req)
    {
        @set_time_limit(0);
        @ini_set('max_execution_time', '0');

        $req->validate([
            'movement'   => 'required|in:squat,bench,deadlift',
            'video'      => ['required', 'file', 'mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-matroska', 'max:70000'],
            'bar_manual' => ['nullable'],
            'weight'     => ['nullable', 'numeric', 'min:0'],
        ]);
        $va = VideoAnalysis::create([
            'user_id'  => Auth::id(),
            'movement' => $req->movement,
            'weight'   => $req->input('weight'),
            'status'   => 'processing',
        ]);

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
        $va->update([
            'status'     => $data['status'] ?? 'processing',
            'job_id'     => $data['job_id'] ?? null,
            'frame_url'  => $data['frame_url'] ?? null,
        ]);

        if (in_array($va->status, ['need_pick', 'need_pick_full'])) {
            return redirect(VideoAnalysisResource::getUrl(
                $va->status === 'need_pick_full' ? 'pick-full' : 'pick-bar',
                ['record' => $va]
            ));
        }

        if (($data['status'] ?? null) === 'done') {
            $this->handleSuccessfulAnalysis($va, $data);
            return redirect(VideoAnalysisResource::getUrl('result', ['record' => $va]));
        }
        return redirect(VideoAnalysisResource::getUrl('index'));
    }
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

        $data = $resp->json();
        if ($va) {
            $va->update(['status' => 'need_pick_full', 'frame_url' => $data['frame_url'] ?? null]);
        }

        return redirect(VideoAnalysisResource::getUrl('pick-full', ['record' => $va]));
    }
    // En VideoAnalysisController.php

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

        $va = VideoAnalysis::where('job_id', $req->job_id)->firstOrFail();

        // 1. Determinamos el endpoint correcto
        if ($va->movement === 'deadlift') {
            $endpoint = '/api/process_deadlift';
        } elseif ($va->movement === 'bench') {
            $endpoint = '/api/process_bench_press';
        } else {
            $endpoint = '/api/process_manual';
        }

        // 2. Hacemos UNA SOLA llamada a la API con el endpoint correcto
        $resp = $this->api()
            ->asForm()
            ->post($endpoint, $req->only('job_id', 'cx', 'cy', 'r'));

        if (!$resp->ok()) {
            $va->update(['status' => 'failed']);
            return back()->withErrors(['api' => $resp->json('error') ?? 'Error'])->withInput();
        }

        $data = $resp->json();
        $this->handleSuccessfulAnalysis($va, $data);

        return redirect(VideoAnalysisResource::getUrl('result', ['record' => $va]));
    }
    public function processManualFull(Request $req)
    {
        @set_time_limit(0);
        @ini_set('max_execution_time', '0');

        $req->validate([
            'job_id' => 'required',
            'r'  => 'required|integer|min:3',
        ]);

        $payload = $req->only('job_id', 'hx', 'hy', 'kx', 'ky', 'ax', 'ay', 'cx', 'cy', 'r');
        $resp = $this->api()->asForm()->post('/api/process_manual_full', $payload);

        if (!$resp->ok()) {
            VideoAnalysis::where('job_id', $req->job_id)->update(['status' => 'failed']);
            return back()->withErrors(['api' => $resp->json('error') ?? 'Error'])->withInput();
        }
        $d  = $resp->json();
        $va = VideoAnalysis::where('job_id', $req->job_id)->firstOrFail();

        $this->handleSuccessfulAnalysis($va, $d);

        return redirect(VideoAnalysisResource::getUrl('result', ['record' => $va]));
    }
    public function proxyDownload(Request $req)
    {
        $url = $req->query('url');
        if (! $url) {
            abort(400, 'Missing url');
        }
        $allowedHosts = [
            parse_url(config('services.video_api.base'), PHP_URL_HOST),
            '127.0.0.1',
            'localhost',
            // Se puede añadir dominios externos
        ];
        $host = parse_url($url, PHP_URL_HOST);
        if (! $host || ! in_array($host, $allowedHosts, true)) {
            abort(403, 'Host no permitido');
        }
        $response = \Illuminate\Support\Facades\Http::timeout(config('services.video_api.timeout', 30))->get($url);

        if (! $response->ok()) {
            abort(502, 'No se pudo descargar el archivo remoto');
        }
        $path = parse_url($url, PHP_URL_PATH);
        $filename = basename($path) ?: 'resultado.mp4';
        return response()->streamDownload(function () use ($response) {
            echo $response->body();
        }, $filename, [
            'Content-Type' => $response->header('Content-Type', 'video/mp4'),
            'Content-Length' => $response->header('Content-Length') ?? strlen($response->body()),
        ]);
    }
}
