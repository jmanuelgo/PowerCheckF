<x-filament-panels::page>
    <x-filament-panels::header :heading="'Resultados del Análisis'" :subheading="ucfirst($record->movement ?? '-') . ' — Job ' . Str::substr($record->job_id ?? '-', 0, 8)" />


    <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-3">


        <div class="bg-white shadow-sm fi-section rounded-xl ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="p-4 fi-section-header sm:px-6">
                <h3 class="text-base font-semibold fi-section-header-heading text-gray-950 dark:text-white">Repeticiones
                </h3>
            </div>
            <div class="p-4 sm:px-6">
                <div class="text-3xl font-semibold">

                    {{ data_get($summary, 'total_reps', data_get($summary, 'count', is_countable($metrics) ? count($metrics) : 0)) }}
                </div>
            </div>
        </div>

        <div class="bg-white shadow-sm fi-section rounded-xl ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="p-4 fi-section-header sm:px-6">
                <h3 class="text-base font-semibold fi-section-header-heading text-gray-950 dark:text-white">Eficiencia
                    Promedio</h3>
            </div>
            <div class="p-4 sm:px-6">
                <div class="text-3xl font-semibold">
                    {{ data_get($summary, 'avg_efficiency_pct') ? number_format(data_get($summary, 'avg_efficiency_pct'), 1) . '%' : '—' }}
                </div>
            </div>
        </div>


        @if ($record->movement === 'deadlift')
            <div
                class="bg-white shadow-sm fi-section rounded-xl ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="p-4 fi-section-header sm:px-6">
                    <h3 class="text-base font-semibold fi-section-header-heading text-gray-950 dark:text-white">
                        Desviación Promedio</h3>
                </div>
                <div class="p-4 sm:px-6">
                    <div class="text-xl font-semibold">
                        {{ data_get($summary, 'avg_horizontal_deviation_px') ? number_format(data_get($summary, 'avg_horizontal_deviation_px'), 1) . ' px' : '—' }}
                    </div>
                    <p class="mt-2 text-sm text-gray-500">
                        Menor desviación entre el hombro y la barra indica una mejor técnica vertical.
                    </p>
                </div>
            </div>
        @else
            <div
                class="bg-white shadow-sm fi-section rounded-xl ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="p-4 fi-section-header sm:px-6">
                    <h3 class="text-base font-semibold fi-section-header-heading text-gray-950 dark:text-white">
                        Profundidad</h3>
                </div>
                <div class="p-4 sm:px-6">
                    <div class="text-lg font-semibold capitalize">
                        {{ data_get($summary, 'depth_label', '—') }}
                    </div>
                    @if (data_get($summary, 'depth_message'))
                        <p class="mt-2 text-sm text-gray-500">{{ data_get($summary, 'depth_message') }}</p>
                    @endif
                </div>
            </div>
        @endif
    </div>

    {{-- Acciones principales (Descarga) y Video Renderizado se mantienen igual --}}
    <div class="flex flex-wrap gap-3 mb-6">
        @if ($record->download_url)
            <x-filament::button tag="a"
                href="{{ route('video.proxyDownload', ['url' => $record->download_url]) }}"
                icon="heroicon-o-arrow-down-tray">
                Descargar Video Procesado
            </x-filament::button>
        @endif
    </div>
    @if (in_array($record->movement, ['squat', 'deadlift']))
        <div class="grid grid-cols-1 gap-6 my-6 lg:grid-cols-2">
            <div>
                @livewire(\App\Filament\Widgets\EfficiencyPerRepChart::class, [
                    'record' => $record,
                ])
            </div>
            <div>
                @livewire(\App\Filament\Widgets\AnalysisComparison::class, [
                    'record' => $record,
                ])
            </div>
        </div>
    @endif

    {{-- Tabla de métricas por repetición --}}
    @if (!empty($metrics))
        <x-filament::section class="mt-6">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm fi-table">
                    <thead class="bg-gray-500/10">
                        <tr>
                            <th class="px-3 py-2 font-semibold text-left fi-table-header-cell">Rep
                            </th>
                            <th class="px-3 py-2 font-semibold text-left fi-table-header-cell">Eficiencia
                                (%)</th>
                            <th class="px-3 py-2 font-semibold text-left fi-table-header-cell">Rango Vert.
                                (px)</th>
                            <th class="px-3 py-2 font-semibold text-left fi-table-header-cell">Exceso (px)
                            </th>
                            <th class="px-3 py-2 font-semibold text-left fi-table-header-cell">RMS (px)
                            </th>
                            <th class="px-3 py-2 font-semibold text-left fi-table-header-cell">Tilt (°)
                            </th>
                            @if ($record->movement === 'deadlift')
                                <th class="px-3 py-2 font-semibold text-left 0 fi-table-header-cell">
                                    Desviación H-B (px)
                                </th>
                            @else
                                <th class="px-3 py-2 font-semibold text-left fi-table-header-cell">Ángulo
                                    min. rodilla
                                    (°)</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                        @foreach ($metrics as $m)
                            <tr class="fi-table-row">
                                {{-- Usamos rep_number (columna DB) --}}
                                <td class="px-3 py-2 fi-table-cell">{{ data_get($m, 'rep_number', '-') }}</td>
                                <td
                                    class="px-3 py-2 fi-table-cell font-medium {{ data_get($m, 'efficiency_pct', 0) >= 95 ? 'text-success-600' : (data_get($m, 'efficiency_pct', 0) >= 90 ? 'text-warning-600' : 'text-danger-600') }}">
                                    {{ isset($m['efficiency_pct']) ? number_format($m['efficiency_pct'], 1) : '—' }}
                                </td>
                                <td class="px-3 py-2 fi-table-cell">
                                    {{ isset($m['vertical_range_px']) ? number_format($m['vertical_range_px'], 1) : '—' }}
                                </td>
                                <td class="px-3 py-2 fi-table-cell">
                                    {{ isset($m['excess_path_px']) ? number_format($m['excess_path_px'], 1) : '—' }}
                                </td>
                                <td class="px-3 py-2 fi-table-cell">
                                    {{ isset($m['rms_px']) ? number_format($m['rms_px'], 1) : '—' }}</td>
                                <td class="px-3 py-2 fi-table-cell">
                                    {{ isset($m['tilt_deg']) ? number_format($m['tilt_deg'], 1) : '—' }}</td>

                                @if ($record->movement === 'deadlift')
                                    <td class="px-3 py-2 fi-table-cell">
                                        {{ isset($m['avg_shoulder_bar_deviation_px']) ? number_format($m['avg_shoulder_bar_deviation_px'], 1) : '—' }}
                                    </td>
                                @else
                                    <td class="px-3 py-2 fi-table-cell">
                                        {{ isset($m['min_knee_angle']) ? number_format($m['min_knee_angle'], 1) : '—' }}
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
