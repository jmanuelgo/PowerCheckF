{{-- resources/views/filament/video/result.blade.php --}}
<x-filament-panels::page>
    <x-filament-panels::header :heading="'Resultados del Análisis'" :subheading="ucfirst($record->movement ?? '-') . ' — Job ' . Str::substr($record->job_id ?? '-', 0, 8)" />

    {{-- KPIs / resumen corto --}}
    <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-3">
        <x-filament::card>
            <x-slot name="header">Repeticiones</x-slot>
            <div class="text-3xl font-semibold">
                {{-- Código mejorado para obtener el conteo de reps de cualquier resumen --}}
                {{ data_get($summary, 'total_reps', data_get($summary, 'count', is_countable($metrics) ? count($metrics) : 0)) }}
            </div>
        </x-filament::card>

        <x-filament::card>
            <x-slot name="header">Eficiencia Promedio</x-slot>
            <div class="text-3xl font-semibold">
                {{ $record->efficiency_pct ? number_format($record->efficiency_pct, 1) . '%' : '—' }}
            </div>
        </x-filament::card>

        {{-- ========================================================== --}}
        {{-- CAMBIO 1: Tarjeta de Métrica Principal (Dinámica) --}}
        {{-- ========================================================== --}}
        @if ($record->movement === 'deadlift')
            <x-filament::card>
                <x-slot name="header">Desviación Promedio</x-slot>
                <div class="text-xl font-semibold">
                    {{ data_get($summary, 'avg_horizontal_deviation_px') ? number_format(data_get($summary, 'avg_horizontal_deviation_px'), 1) . ' px' : '—' }}
                </div>
                <p class="mt-2 text-sm text-gray-500">
                    Menor desviación entre el hombro y la barra indica una mejor técnica vertical.
                </p>
            </x-filament::card>
        @else
            {{-- Por defecto, se muestra la tarjeta de Sentadilla --}}
            <x-filament::card>
                <x-slot name="header">Profundidad</x-slot>
                <div class="text-lg font-semibold capitalize">
                    {{ data_get($summary, 'depth_label', '—') }}
                </div>
                @if (data_get($summary, 'depth_message'))
                    <p class="mt-2 text-sm text-gray-500">{{ data_get($summary, 'depth_message') }}</p>
                @endif
            </x-filament::card>
        @endif
        {{-- ===================== FIN DEL CAMBIO 1 ===================== --}}
    </div>

    {{-- Acciones principales --}}
    <div class="flex flex-wrap gap-3 mb-6">
        @if ($record->download_url)
            <x-filament::button tag="a"
                href="{{ route('video.proxyDownload', ['url' => $record->download_url]) }}"
                icon="heroicon-o-arrow-down-tray">
                Descargar Video Procesado
            </x-filament::button>
        @endif
    </div>

    {{-- Video renderizado --}}
    @if ($record->download_url)
        <x-filament::section>
            <video controls preload="metadata" class="w-full border border-gray-200 rounded-xl dark:border-gray-700"
                src="{{ route('video.proxyDownload', ['url' => $record->download_url]) }}#t=0.1"></video>
        </x-filament::section>
    @endif

    {{-- Tabla de métricas por repetición --}}
    @if (!empty($metrics))
        <x-filament::section class="mt-6">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm fi-table">
                    <thead class="bg-gray-50 dark:bg-gray-500/10">
                        <tr>
                            <th class="px-3 py-2 font-semibold text-left fi-table-header-cell">Rep</th>
                            <th class="px-3 py-2 font-semibold text-left fi-table-header-cell">Eficiencia (%)</th>
                            <th class="px-3 py-2 font-semibold text-left fi-table-header-cell">Rango Vert. (px)</th>
                            <th class="px-3 py-2 font-semibold text-left fi-table-header-cell">Exceso (px)</th>
                            <th class="px-3 py-2 font-semibold text-left fi-table-header-cell">RMS (px)</th>
                            <th class="px-3 py-2 font-semibold text-left fi-table-header-cell">Tilt (°)</th>

                            {{-- ========================================================== --}}
                            {{-- CAMBIO 2: Cabecera de Columna (Dinámica) --}}
                            {{-- ========================================================== --}}
                            @if ($record->movement === 'deadlift')
                                <th class="px-3 py-2 font-semibold text-left fi-table-header-cell">Desviación H-B (px)
                                </th>
                            @else
                                <th class="px-3 py-2 font-semibold text-left fi-table-header-cell">Ángulo min. rodilla
                                    (°)</th>
                            @endif
                            {{-- ===================== FIN DEL CAMBIO 2 ===================== --}}

                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                        @foreach ($metrics as $m)
                            <tr class="fi-table-row">
                                <td class="px-3 py-2 fi-table-cell">{{ data_get($m, 'rep', '-') }}</td>
                                <td
                                    class="px-3 py-2 fi-table-cell font-medium {{ data_get($m, 'efficiency_pct', 0) >= 95 ? 'text-success-600' : (data_get($m, 'efficiency_pct', 0) >= 90 ? 'text-warning-600' : 'text-danger-600') }}">
                                    {{ isset($m['efficiency_pct']) ? number_format($m['efficiency_pct'], 1) : '—' }}
                                </td>
                                <td class="px-3 py-2 fi-table-cell">
                                    {{ isset($m['vert_range_px']) ? number_format($m['vert_range_px'], 1) : '—' }}</td>
                                <td class="px-3 py-2 fi-table-cell">
                                    {{ isset($m['excess_path_px']) ? number_format($m['excess_path_px'], 1) : '—' }}
                                </td>
                                <td class="px-3 py-2 fi-table-cell">
                                    {{ isset($m['rms_px']) ? number_format($m['rms_px'], 1) : '—' }}</td>
                                <td class="px-3 py-2 fi-table-cell">
                                    {{ isset($m['tilt_deg']) ? number_format($m['tilt_deg'], 1) : '—' }}</td>

                                {{-- ========================================================== --}}
                                {{-- CAMBIO 3: Contenido de Celda (Dinámico) --}}
                                {{-- ========================================================== --}}
                                @if ($record->movement === 'deadlift')
                                    <td class="px-3 py-2 fi-table-cell">
                                        {{ isset($m['avg_horizontal_deviation_px']) ? number_format($m['avg_horizontal_deviation_px'], 1) : '—' }}
                                    </td>
                                @else
                                    <td class="px-3 py-2 fi-table-cell">
                                        {{ isset($m['min_angle_deg']) ? number_format($m['min_angle_deg'], 1) : '—' }}
                                    </td>
                                @endif
                                {{-- ===================== FIN DEL CAMBIO 3 ===================== --}}
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
