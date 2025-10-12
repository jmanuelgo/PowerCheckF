{{-- resources/views/filament/video/result.blade.php --}}
<x-filament-panels::page>
    <x-filament-panels::header :heading="'Resultados'" :subheading="'Análisis de ' . ($record->movement ?? '-') . ' — Job ' . ($record->job_id ?? '-')" />

    {{-- KPIs / resumen corto --}}
    <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-3">
        <x-filament::card>
            <x-slot name="header">Reps</x-slot>
            <div class="text-3xl font-semibold">
                {{ data_get($summary, 'count', $record->rep_count ?? (is_countable($metrics) ? count($metrics) : 0)) }}
            </div>
        </x-filament::card>

        <x-filament::card>
            <x-slot name="header">Eficiencia promedio (%)</x-slot>
            <div class="text-3xl font-semibold">
                {{ $record->efficiency_pct ? number_format($record->efficiency_pct, 1) : '—' }}
            </div>
        </x-filament::card>

        <x-filament::card>
            <x-slot name="header">Profundidad</x-slot>
            <div class="text-lg">
                {{ data_get($summary, 'depth_label', '—') }}
            </div>
            @if (data_get($summary, 'depth_message'))
                <p class="mt-2 text-sm text-gray-500">{{ data_get($summary, 'depth_message') }}</p>
            @endif
        </x-filament::card>
    </div>

    {{-- Acciones principales --}}
    <div class="flex flex-wrap gap-3 mb-6">
        @if ($record->download_url)
            <x-filament::button tag="a"
                href="{{ route('video.proxyDownload', ['url' => $record->download_url]) }}"
                icon="heroicon-o-arrow-down-tray">
                Descargar MP4
            </x-filament::button>
        @endif

        {{-- Si luego quieres permitir Manual Completo desde aquí, descomenta: --}}
        {{--
        <form method="POST" action="{{ route('video.startManual') }}">
            @csrf
            <input type="hidden" name="job_id" value="{{ $record->job_id }}">
            <x-filament::button color="gray" outlined icon="heroicon-o-pencil-square">
                Análisis manual completo
            </x-filament::button>
        </form>
        --}}
    </div>

    {{-- Video renderizado --}}
    @if ($record->download_url)
        <x-filament::section>
            <video controls class="w-full border border-gray-200 rounded-xl" src="{{ $record->download_url }}"></video>
        </x-filament::section>
    @endif

    {{-- Tabla de métricas por repetición --}}
    @if (!empty($metrics))
        <x-filament::section class="mt-6">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900/40">
                            <th class="px-3 py-2 font-semibold text-left">Rep</th>
                            <th class="px-3 py-2 font-semibold text-left">Rango vertical (px)</th>
                            <th class="px-3 py-2 font-semibold text-left">Exceso (px)</th>
                            <th class="px-3 py-2 font-semibold text-left">Eficiencia (%)</th>
                            <th class="px-3 py-2 font-semibold text-left">RMS (px)</th>
                            <th class="px-3 py-2 font-semibold text-left">Tilt (°)</th>
                            <th class="px-3 py-2 font-semibold text-left">Ángulo min rodilla (°)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach ($metrics as $m)
                            <tr>
                                <td class="px-3 py-2">{{ $m['rep'] ?? '-' }}</td>
                                <td class="px-3 py-2">{{ $m['vert_range_px'] ?? '-' }}</td>
                                <td class="px-3 py-2">{{ $m['excess_path_px'] ?? '-' }}</td>
                                <td class="px-3 py-2">
                                    {{ isset($m['efficiency_pct']) ? number_format($m['efficiency_pct'], 1) : '—' }}
                                </td>
                                <td class="px-3 py-2">
                                    {{ isset($m['rms_px']) ? number_format($m['rms_px'], 1) : '—' }}
                                </td>
                                <td class="px-3 py-2">
                                    {{ isset($m['tilt_deg']) ? number_format($m['tilt_deg'], 1) : '—' }}
                                </td>
                                <td class="px-3 py-2">
                                    {{ isset($m['min_angle_deg']) ? number_format($m['min_angle_deg'], 1) : '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
