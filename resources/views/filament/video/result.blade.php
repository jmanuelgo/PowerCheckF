<x-filament-panels::page>
    <x-filament-panels::header :heading="'Resultados del Análisis'" :subheading="ucfirst($record->movement ?? '-') . ' — Job ' . Str::substr($record->job_id ?? '-', 0, 8)" />

    <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-3">
        <div
            class="p-4 bg-white shadow-sm rounded-xl fi-section ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <h3 class="text-base font-semibold fi-section-header-heading text-gray-950 dark:text-white">Repeticiones</h3>
            <div class="text-3xl font-semibold">
                {{ data_get($summary, 'total_reps', 0) }}
            </div>
        </div>

        <div
            class="p-4 bg-white shadow-sm rounded-xl fi-section ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <h3 class="text-base font-semibold fi-section-header-heading text-gray-950 dark:text-white">
                {{ $record->movement === 'bench' ? 'Score Promedio' : 'Eficiencia Promedio' }}
            </h3>
            <div class="text-3xl font-semibold">
                {{ data_get($summary, 'avg_efficiency_pct') ? number_format(data_get($summary, 'avg_efficiency_pct'), 1) . '%' : '—' }}
            </div>
        </div>

        <div
            class="p-4 bg-white shadow-sm rounded-xl fi-section ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            @if ($record->movement === 'squat')
                <h3 class="text-base font-semibold fi-section-header-heading text-gray-950 dark:text-white">Profundidad
                </h3>
                <div class="text-lg font-semibold capitalize">{{ data_get($summary, 'depth_label', '—') }}</div>
                @if (data_get($summary, 'depth_message'))
                    <p class="mt-2 text-sm text-gray-500">{{ data_get($summary, 'depth_message') }}</p>
                @endif
            @elseif ($record->movement === 'deadlift')
                <h3 class="text-base font-semibold fi-section-header-heading text-gray-950 dark:text-white">Desviación
                    Promedio</h3>
                <div class="text-xl font-semibold">
                    {{ data_get($summary, 'avg_shoulder_bar_deviation_px') ? number_format(data_get($summary, 'avg_shoulder_bar_deviation_px'), 1) . ' px' : '—' }}
                </div>
                <p class="mt-2 text-sm text-gray-500">Menor desviación indica mejor técnica.</p>

            @elseif ($record->movement === 'bench')
                <h3 class="text-base font-semibold fi-section-header-heading text-gray-950 dark:text-white">Mejor
                    Repetición</h3>
                <div class="text-3xl font-semibold">
                    {{ data_get($summary, 'best_rep_score') ? number_format(data_get($summary, 'best_rep_score'), 1) . '%' : '—' }}
                </div>
                <p class="mt-2 text-sm text-gray-500">Score de la repetición con mejor técnica.</p>
            @endif

        </div>
    </div>


    <div class="flex flex-wrap gap-3 mb-6">
        @if ($record->download_url)
            <x-filament::button tag="a"
                href="{{ route('video.proxyDownload', ['url' => $record->download_url]) }}"
                icon="heroicon-o-arrow-down-tray">
                Descargar Video Procesado
            </x-filament::button>
        @endif
    </div>
    <div class="grid grid-cols-1 gap-6 my-6 lg:grid-cols-2">
        @if (in_array($record->movement, ['squat', 'deadlift']))
                <div>@livewire(\App\Filament\Widgets\EfficiencyPerRepChart::class, ['record' => $record])</div>
                <div>@livewire(\App\Filament\Widgets\AnalysisComparison::class, ['record' => $record])</div>

        @elseif ($record->movement =='bench')
                <div>@livewire(\App\Filament\Widgets\JCurvePerRepChart::class, ['record' => $record])</div>
                <div>@livewire(\App\Filament\Widgets\AnalysisComparison::class, ['record' => $record])</div>
        @endif
    </div>
    @if (!empty($metrics))
        <x-filament::section class="mt-6">
            <div class="overflow-x-auto">

                @if (in_array($record->movement, ['squat', 'deadlift']))
                    <table class="min-w-full text-sm fi-table">
                        <thead class="bg-gray-500/10">
                            <tr>
                                <th class="px-3 py-2 font-semibold text-left">Rep</th>
                                <th class="px-3 py-2 font-semibold text-left">Eficiencia (%)</th>
                                <th class="px-3 py-2 font-semibold text-left">Rango Vert. (px)</th>
                                <th class="px-3 py-2 font-semibold text-left">Exceso (px)</th>
                                <th class="px-3 py-2 font-semibold text-left">RMS (px)</th>
                                <th class="px-3 py-2 font-semibold text-left">Tilt (°)</th>
                                @if ($record->movement === 'deadlift')
                                    <th class="px-3 py-2 font-semibold text-left">Desviación H-B (px)</th>
                                @else
                                    <th class="px-3 py-2 font-semibold text-left">Ángulo min. rodilla (°)</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                            @foreach ($metrics as $m)
                                <tr>
                                    <td class="px-3 py-2">{{ data_get($m, 'rep_number', '-') }}</td>
                                    <td
                                        class="px-3 py-2 font-medium {{ data_get($m, 'efficiency_pct', 0) >= 95 ? 'text-success-600' : (data_get($m, 'efficiency_pct', 0) >= 90 ? 'text-warning-600' : 'text-danger-600') }}">
                                        {{ isset($m['efficiency_pct']) ? number_format($m['efficiency_pct'], 1) : '—' }}
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ isset($m['vertical_range_px']) ? number_format($m['vertical_range_px'], 1) : '—' }}
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ isset($m['excess_path_px']) ? number_format($m['excess_path_px'], 1) : '—' }}
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ isset($m['rms_px']) ? number_format($m['rms_px'], 1) : '—' }}</td>
                                    <td class="px-3 py-2">
                                        {{ isset($m['tilt_deg']) ? number_format($m['tilt_deg'], 1) : '—' }}</td>
                                    @if ($record->movement === 'deadlift')
                                        <td class="px-3 py-2">
                                            {{ isset($m['avg_shoulder_bar_deviation_px']) ? number_format($m['avg_shoulder_bar_deviation_px'], 1) : '—' }}
                                        </td>
                                    @else
                                        <td class="px-3 py-2">
                                            {{ isset($m['min_knee_angle']) ? number_format($m['min_knee_angle'], 1) : '—' }}
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                @elseif($record->movement === 'bench')
                    <table class="min-w-full text-sm fi-table">
                        <thead class="bg-gray-500/10">
                            <tr>
                                <th class="px-3 py-2 font-semibold text-left">Rep</th>
                                <th class="px-3 py-2 font-semibold text-left">Score Técnica (%)</th>
                                <th class="px-3 py-2 font-semibold text-left">Curva "J" (px)</th>
                                <th class="px-3 py-2 font-semibold text-left">Rectitud Bajada (RMSE)</th>
                                <th class="px-3 py-2 font-semibold text-left">Observaciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                            @foreach ($metrics as $m)
                                <tr>
                                    <td class="px-3 py-2">{{ data_get($m, 'rep_number', '-') }}</td>
                                    <td
                                        class="px-3 py-2 font-medium {{ data_get($m, 'score_general', 0) >= 85 ? 'text-success-600' : (data_get($m, 'score_general', 0) >= 60 ? 'text-warning-600' : 'text-danger-600') }}">
                                        {{ isset($m['score_general']) ? number_format($m['score_general'], 1) : '—' }}
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ isset($m['curvatura_j_px']) ? number_format($m['curvatura_j_px'], 1) : '—' }}
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ isset($m['rectitud_bajada_rmse']) ? number_format($m['rectitud_bajada_rmse'], 1) : '—' }}
                                    </td>
                                    <td class="px-3 py-2 text-xs">{{ data_get($m, 'observacion', '-') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
