<x-filament-panels::page>
    @php
        $e = $this->record;
        $s = $this->stats;
        $hist = $this->historial;
    @endphp

    <x-filament-panels::header :heading="'Progreso: ' . $e->nombre" subheading="Desde {{ $s['desde'] }} (últimos 30 días)" />

    {{-- Cards tipo "stats" (ligero, sin widget Livewire) --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
        <x-filament::section>
            <div class="space-y-1">
                <div class="text-sm text-gray-500">RM aprox. actual</div>
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
                <div class="text-sm text-gray-500">Adherencia</div>
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
                <div class="text-sm text-gray-500">Volumen planeado (30d)</div>
                <div class="text-2xl font-bold">{{ number_format($s['volumen_planeado'], 0) }} kg·reps</div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="space-y-1">
                <div class="text-sm text-gray-500">Volumen realizado (30d)</div>
                <div class="text-2xl font-bold">{{ number_format($s['volumen_realizado'], 0) }} kg·reps</div>
            </div>
        </x-filament::section>
    </div>

    {{-- Historial simple (RM por día, últimos 30 días) --}}
    <div class="mt-6 overflow-hidden text-black bg-white border border-gray-200 rounded-xl">
        <div class="px-6 py-4 font-semibold bg-gray-50">Historial de RM (últimos 30 días)</div>
        <div class="p-6 overflow-x-auto">
            @if (count($hist))
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-3 py-2 text-left">Fecha</th>
                            <th class="px-3 py-2 text-left">RM aprox.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($hist as $p)
                            <tr class="border-b">
                                <td class="px-3 py-2">{{ $p['fecha'] }}</td>
                                <td class="px-3 py-2">{{ number_format($p['rm'], 1) }} kg</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-sm italic text-gray-500">Sin registros aún.</p>
            @endif
        </div>
    </div>

    <div class="flex justify-end mt-8">
        <x-filament::button icon="heroicon-o-arrow-left" tag="a"
            href="{{ route('filament.powerCheck.resources.ejercicios.index') }}" color="gray" outlined>
            Volver a ejercicios
        </x-filament::button>
    </div>
</x-filament-panels::page>
