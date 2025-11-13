{{-- resources/views/filament/pages/mi-rutina-de-hoy.blade.php --}}
<x-filament-panels::page>
    @php
        /** @var \App\Models\Rutina|null $rutina */
        $rutina = $rutina ?? $this->rutina; // por si lo llamas $this->rutina desde la Page
        $semanaActiva = $semanaActiva ?? $this->semanaActiva;
        $diaDeHoy = $diaDeHoy ?? $this->diaDeHoy;

        $semana =
            $rutina?->semanasRutina?->firstWhere('numero_semana', $semanaActiva) ??
            $rutina?->semanasRutina?->sortBy('numero_semana')->first();

        $diaHoy =
            $semana?->diasEntrenamiento?->firstWhere('id', $this->diaId) ??
            $semana?->diasEntrenamiento?->firstWhere('dia_semana', $diaDeHoy);
    @endphp

    <x-filament-panels::header :heading="$rutina ? 'Mi rutina de hoy — ' . $diaDeHoy : 'Mi rutina de hoy'" :subheading="$rutina ? 'Rutina: ' . $rutina->nombre : null" />

    @if (!$rutina)
        <div class="p-8 text-center text-gray-600 bg-white border rounded-xl">
            Aún no tienes una rutina asignada.
        </div>
    @else
        {{-- Tarjeta Info --}}
        <div class="p-6 mb-6 bg-white border border-gray-200 shadow-lg rounded-xl">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <div>
                    <div class="text-sm font-semibold text-gray-600">ATLETA</div>
                    <div class="text-lg font-medium">{{ $rutina->atleta->user->name ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-sm font-semibold text-gray-600">OBJETIVO</div>
                    <div class="text-lg font-medium">{{ $rutina->objetivo }}</div>
                </div>
                <div>
                    <div class="text-sm font-semibold text-gray-600">SEMANA ACTUAL</div>
                    <div class="text-lg font-medium">#{{ $semana?->numero_semana ?? '—' }}</div>
                </div>
            </div>
        </div>

        @if (!$diaHoy)
            <div class="p-8 text-center text-gray-600 bg-white border rounded-xl">
                No hay entrenamiento programado para hoy ({{ $diaDeHoy }}). ¡Descanso activo! 🧘
            </div>
        @else
            {{-- SOLO EL DÍA DE HOY --}}
            <div class="overflow-hidden bg-white border border-gray-200 shadow-lg rounded-xl">
                <div class="px-6 py-4 bg-gradient-to-r from-primary-600 to-primary-700">
                    <h3 class="text-xl font-bold text-white">Semana {{ $semana->numero_semana }} —
                        {{ strtoupper($diaHoy->dia_semana) }}</h3>
                </div>

                <div class="p-6 space-y-6">
                    <div class="p-6 border border-gray-200 rounded-lg bg-gray-50">
                        <div class="flex items-center justify-between mb-6">
                            <h4 class="text-lg font-semibold text-gray-800">Ejercicios de hoy</h4>
                            <span class="px-3 py-1 text-sm font-medium rounded-full bg-primary-100 text-primary-800">
                                {{ $diaHoy->ejerciciosDia->count() }} ejercicio(s)
                            </span>
                        </div>

                        <div class="space-y-4">
                            @forelse($diaHoy->ejerciciosDia->sortBy('orden') as $ejercicioDia)
                                <div class="p-5 bg-white border border-gray-200 rounded-lg shadow-sm">
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="flex-1">
                                            <h5 class="text-lg font-bold text-gray-800">
                                                {{ $ejercicioDia->orden }}. {{ $ejercicioDia->ejercicio->nombre }}
                                            </h5>
                                           @if ($ejercicioDia->ejercicio->descripcion)
                                                <p class="mt-1 text-sm text-gray-600">
                                                {{ $ejercicioDia->ejercicio->descripcion }}
                                                </p>
                                            @endif
                                            @if ($ejercicioDia->notas)
                                                <p class="mt-1 text-sm italic text-gray-600">📝
                                                    {{ $ejercicioDia->notas }}</p>
                                            @endif
                                        </div>
                                        <span
                                            class="px-2 py-1 ml-4 text-sm font-medium text-blue-800 bg-blue-100 rounded">
                                            {{ $ejercicioDia->seriesEjercicio->count() }} series
                                        </span>
                                    </div>

                                    <div class="overflow-x-auto">
                                        <table class="w-full text-sm">
                                            <thead>
                                                <tr class="bg-gray-100">
                                                    <th class="px-3 py-2 font-semibold text-left text-gray-700">Serie
                                                    </th>
                                                    <th class="px-3 py-2 font-semibold text-left text-gray-700">
                                                        Repeticiones</th>
                                                    <th class="px-3 py-2 font-semibold text-left text-gray-700">Peso
                                                    </th>
                                                    <th class="px-3 py-2 font-semibold text-left text-gray-700">Descanso
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($ejercicioDia->seriesEjercicio->sortBy('numero_serie') as $serie)
                                                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                                                        <td class="px-3 py-3 font-medium text-gray-700">
                                                            #{{ $serie->numero_serie }}</td>
                                                        <td class="px-3 py-3">
                                                            <span
                                                                class="px-2 py-1 text-sm font-medium text-green-800 bg-green-100 rounded-full">
                                                                {{ $serie->repeticiones_objetivo }} reps
                                                            </span>
                                                        </td>
                                                        <td class="px-3 py-3">
                                                            @if ($serie->peso_objetivo > 0)
                                                                <span
                                                                    class="px-2 py-1 text-sm font-medium text-orange-800 bg-orange-100 rounded-full">
                                                                    {{ $serie->peso_objetivo }} kg
                                                                </span>
                                                            @else
                                                                <span
                                                                    class="px-2 py-1 text-sm font-medium text-gray-600 bg-gray-100 rounded-full">
                                                                    Peso corporal
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td class="px-3 py-3">
                                                            <span
                                                                class="px-2 py-1 text-sm font-medium text-blue-800 bg-blue-100 rounded-full">
                                                                {{ $serie->descanso_segundos }} seg
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @empty
                                <div class="text-gray-600">No hay ejercicios cargados para hoy.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- Botones --}}
            <div class="flex justify-end mt-8 space-x-4">
                <x-filament::button icon="heroicon-o-eye" tag="a"
                    href="{{ route('filament.powerCheck.resources.rutinas.view', $rutina->id) }}" color="gray"
                    outlined>
                    Ver rutina completa
                </x-filament::button>

                @can('update', $rutina)
                    <x-filament::button icon="heroicon-o-pencil" tag="a"
                        href="{{ route('filament.powerCheck.resources.rutinas.edit', $rutina->id) }}" color="primary">
                        Editar rutina
                    </x-filament::button>
                @endcan
            </div>
        @endif
    @endif
</x-filament-panels::page>
