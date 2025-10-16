<x-filament-panels::page>
    @php
        $e = $this->record;
        $s = $this->stats;
        $hist = $this->historial;
    @endphp

    <x-filament-panels::header :heading="'Progreso: ' . $e->nombre" subheading="Estadísticas generales de tu rendimiento" />

    {{-- Cards de Estadísticas --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
        <x-filament::section>
            <div class="space-y-1">
                <div class="text-sm text-gray-500">RM aprox. actual (30d)</div>
                <div class="text-2xl font-bold">{{ number_format($s['rm_aprox_actual'], 1) }} kg</div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="space-y-1">
                <div class="text-sm text-gray-500">Mejor RM histórico</div>
                <div class="text-2xl font-bold">{{ number_format($s['rm_mejor'], 1) }} kg</div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="space-y-1">
                <div class="text-sm text-gray-500">Adherencia (30d)</div>
                <div class="text-2xl font-bold">
                    @if (!is_null($s['adherencia']))
                        {{ $s['adherencia'] }}%
                        <span class="ml-2 text-sm text-gray-500">({{ $s['sets_realizadas'] }}/{{ $s['sets_planeadas'] }}
                            sets)</span>
                    @else
                        —
                    @endif
                </div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="space-y-1">
                <div class="text-sm text-gray-500">Total Series Histórico</div>
                <div class="text-2xl font-bold">{{ $s['total_sets'] }}</div>
            </div>
        </x-filament::section>
    </div>

    {{-- AQUÍ ESTÁ EL CAMBIO: Llamamos a los widgets --}}
    <div class="grid grid-cols-1 gap-6 mt-6 lg:grid-cols-2">
        @livewire(\App\Filament\Widgets\PesoProgresoChart::class, ['record' => $this->record])
        @livewire(\App\Filament\Widgets\RepsProgresoChart::class, ['record' => $this->record])
    </div>

    {{-- Historial General de Series --}}
    <div class="mt-6 overflow-hidden bg-white border border-gray-200 rounded-xl dark:bg-gray-800 dark:border-gray-700">
        <div class="px-6 py-4 font-semibold bg-gray-50 dark:bg-gray-700/50">Historial General de Series</div>
        <div class="p-6 overflow-x-auto">
            @if (count($hist))
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="text-gray-500">
                            <th wire:click="sortBy('fecha')" class="px-3 py-2 cursor-pointer hover:text-gray-300">Fecha
                            </th>
                            <th wire:click="sortBy('peso')" class="px-3 py-2 cursor-pointer hover:text-gray-300">Peso
                                Realizado (kg)</th>
                            <th class="px-3 py-2">Repeticiones</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-400">
                        @foreach ($hist as $p)
                            <tr class="border-t border-gray-700">
                                <td class="px-3 py-2">{{ $p['fecha'] }}</td>
                                <td class="px-3 py-2">{{ number_format($p['peso'], 1) }}</td>
                                <td class="px-3 py-2">{{ $p['repeticiones'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-sm italic text-gray-500">Sin registros de series.</p>
            @endif
        </div>
    </div>

    {{-- Botón de volver --}}
    <div class="flex justify-end mt-8">
        <x-filament::button icon="heroicon-o-arrow-left" tag="a"
            href="{{ route('filament.powerCheck.resources.ejercicios.index') }}" color="gray" outlined>
            Volver a ejercicios
        </x-filament::button>
    </div>
</x-filament-panels::page>
