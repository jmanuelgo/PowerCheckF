<x-filament-panels::page>
    @php
        $rutina = $this->getRutinaCompleta();
        $user = auth()->user();
        $isEntrenador = $user->hasRole('entrenador');
        $isAtleta = $user->hasRole('atleta');
    @endphp

    <x-filament-panels::header :heading="'Rutina: ' . $rutina->nombre" :subheading="$isEntrenador ? 'Atleta: ' . $rutina->atleta->user->name : 'Entrenador: ' . $rutina->entrenador->name" />

    <div class="space-y-6">
        <div class="p-6 bg-white border border-gray-200 shadow-lg rounded-xl">
            <h2 class="flex items-center gap-2 mb-6 text-2xl font-bold text-gray-800">
                <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Información General
            </h2>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                @if ($isEntrenador)
                    <div class="p-4 rounded-lg bg-gray-50">
                        <div class="flex items-center gap-3 mb-2">
                            <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c极-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                            <span class="text-sm font-semibold text-gray-600">ATLETA</span>
                        </div>
                        <p class="text-lg font-medium text-gray-800">{{ $rutina->atleta->user->name }}</p>
                    </div>
                @else
                    <div class="p-4 rounded-lg bg-gray-50">
                        <div class="flex items-center gap-3 mb-2">
                            <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0">
                                </path>
                            </svg>
                            <span class="text-sm font-semibold text-gray-600">ENTRENADOR</span>
                        </div>
                        <p class="text-lg font-medium text-gray-800">{{ $rutina->entrenador->name }}</p>
                    </div>
                @endif

                <div class="p-4 rounded-lg bg-gray-50">
                    <div class="flex items-center gap-3 mb-2">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-line极="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        <span class="text-sm font-semibold text-gray-600">OBJETIVO</span>
                    </div>
                    <p class="text-lg font-medium text-gray-800">{{ $rutina->objetivo }}</p>
                </div>

                <div class="p-4 rounded-lg bg-gray-50">
                    <div class="flex items-center gap-3 mb-2">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm font-semibold text-gray-600">DURACIÓN</span>
                    </div>
                    <p class="text-lg font-medium text-gray-800">{{ $rutina->duracion_semanas }} semanas</p>
                </div>

                <div class="p-4 rounded-lg bg-gray-50">
                    <div class="极lex items-center gap-3 mb-2">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                        </svg>
                        <span class="text-sm font-semibold text-gray-600">DÍAS</span>
                    </div>
                    <p class="text-lg font-medium text-gray-800">{{ $rutina->dias_por_semana }} días</p>
                </div>
            </div>
        </div>

        <!-- Semanas de Entrenamiento -->
        @if ($rutina->semanasRutina && $rutina->semanasRutina->count() > 0)
            @foreach ($rutina->semanasRutina->sortBy('numero_semana') as $semana)
                <div class="overflow-hidden bg-white border border-gray-200 shadow-lg rounded-xl">
                    <div class="px-6 py-4 bg-gradient-to-r from-primary-600 to-primary-700">
                        <h3 class="flex items-center gap-2 text-xl font-bold text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            SEMANA {{ $semana->numero_s极ana }}
                        </h3>
                    </div>

                    <div class="p-6 space-y-6">
                        @if ($semana->diasEntrenamiento && $semana->diasEntrenamiento->count() > 0)
                            @php
                                $ordenDias = [
                                    'Lunes' => 1,
                                    'Martes' => 2,
                                    'Miércoles' => 3,
                                    'Jueves' => 4,
                                    'Viernes' => 5,
                                    'Sábado' => 6,
                                    'Domingo' => 7,
                                ];
                                $diasOrdenados = $semana->diasEntrenamiento
                                    ->sortBy(fn($d) => $ordenDias[$d->dia_semana] ?? 99)
                                    ->values();
                            @endphp

                            @foreach ($diasOrdenados as $dia)
                                <div class="p-6 border border-gray-200 rounded-lg bg-gray-50">
                                    <div class="flex items-center justify-between mb-6">
                                        <h4 class="flex items-center gap-2 text-lg font-semibold text-gray-800">
                                            <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ strtoupper($dia->dia_semana) }}
                                        </h4>
                                        <span
                                            class="px-3 py-1 text-sm font-medium rounded-full bg-primary-100 text-primary-800">
                                            {{ $dia->ejerciciosDia->count() }} ejercicio(s)
                                        </span>
                                    </div>

                                    <!-- Ejercicios -->
                                    @if ($dia->ejerciciosDia && $dia->ejerciciosDia->count() > 0)
                                        <div class="space-y-4">
                                            @foreach ($dia->ejerciciosDia->sortBy('orden') as $ejercicioDia)
                                                @php
                                                    $ejercicioCompletado = $this->getEjercicioCompletado(
                                                        $ejercicioDia->id,
                                                    );
                                                    $estaCompletado = $ejercicioCompletado
                                                        ? $ejercicioCompletado->completado
                                                        : false;
                                                @endphp

                                                <div
                                                    class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 {{ $estaCompletado ? 'border-green-500 bg-green-50' : '' }}">
                                                    <div class="flex items-start justify-between mb-4">
                                                        <div class="flex-1">
                                                            <h5 class="text-lg font-bold text-gray-800">
                                                                {{ $ejercicioDia->orden }}.
                                                                {{ $ejercicioDia->ejercicio->nombre }}
                                                                @if ($estaCompletado)
                                                                    <span class="ml-2 text-green-600">✅</span>
                                                                @endif
                                                            </h5>
                                                            @if ($ejercicioDia->notas)
                                                                <p class="mt-1 text-sm italic text-gray-600">📝
                                                                    {{ $ejercicioDia->notas }}</p>
                                                            @endif
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <span
                                                                class="px-2 py-1 text-sm font-medium text-blue-800 bg-blue-100 rounded">
                                                                {{ $ejercicioDia->seriesEjercicio->count() }} series
                                                            </span>
                                                            @if ($isAtleta)
                                                                <button
                                                                    wire:click="toggleEjercicio({{ $ejercicioDia->id }})"
                                                                    class="px-3 py-1 text-sm font-medium rounded {{ $estaCompletado ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }} hover:bg-opacity-80 transition-colors">
                                                                    {{ $estaCompletado ? 'Completado' : 'Marcar como hecho' }}
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <!-- Series -->
                                                    @if ($ejercicioDia->seriesEjercicio && $ejercicioDia->seriesEjercicio->count() > 0)
                                                        <div class="mt-4 overflow-x-auto">
                                                            <table class="w-full text-sm">
                                                                <thead>
                                                                    <tr class="bg-gray-100">
                                                                        <th
                                                                            class="px-3 py-2 font-semibold text-left text-gray-700">
                                                                            Serie</th>
                                                                        <th
                                                                            class="px-3 py-2 font-semibold text-left text-gray-700">
                                                                            Repeticiones Objetivo</th>
                                                                        <th
                                                                            class="px-3 py-2 font-semibold text-left text-gray-700">
                                                                            Peso Objetivo</th>
                                                                        @if ($isAtleta)
                                                                            <th
                                                                                class="px-3 py-2 font-semibold text-left text-gray-700">
                                                                                Repeticiones Realizadas</th>
                                                                            <th
                                                                                class="px-3 py-2 font-semibold text-left text-gray-700">
                                                                                Peso Realizado</th>
                                                                            <th
                                                                                class="px-3 py-2 font-semibold text-left text-gray-700">
                                                                                Acciones</th>
                                                                        @elseif($isEntrenador)
                                                                            <th
                                                                                class="px-3 py-2 font-semibold text-left text-gray-700">
                                                                                Repeticiones Realizadas</th>
                                                                            <th
                                                                                class="px-3 py-2 font-semibold text-left text-gray-700">
                                                                                Peso Realizado</th>
                                                                            <th
                                                                                class="px-3 py-2 font-semibold text-left text-gray-700">
                                                                                Estado</th>
                                                                        @endif
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($ejercicioDia->seriesEjercicio->sortBy('numero_serie') as $serie)
                                                                        @php
                                                                            $serieRealizada = $this->getSerieRealizada(
                                                                                $serie->id,
                                                                            );
                                                                        @endphp
                                                                        <tr
                                                                            class="border-b border-gray-200 hover:bg-gray-50 {{ $serieRealizada ? 'bg-green-50' : '' }}">
                                                                            <td
                                                                                class="px-3 py-3 font-medium text-gray-700">
                                                                                #{{ $serie->numero_serie }}</td>
                                                                            <td class="px-3 py-3">
                                                                                <span
                                                                                    class="px-2 py-1 text-sm font-medium text-green-800 bg-green-100 rounded-full">
                                                                                    {{ $serie->repeticiones_objetivo }}
                                                                                    reps
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

                                                                            @if ($isAtleta)
                                                                                <td class="px-3 py-3">
                                                                                    @if ($serieRealizada)
                                                                                        <span
                                                                                            class="px-2 py-1 text-sm font-medium text-blue-800 bg-blue-100 rounded-full">
                                                                                            {{ $serieRealizada->repeticiones_realizadas }}
                                                                                            reps
                                                                                        </span>
                                                                                    @else
                                                                                        <input type="number"
                                                                                            wire:model="repeticiones.{{ $serie->id }}"
                                                                                            min="0"
                                                                                            class="w-20 px-2 py-1 text-sm text-gray-800 bg-white border border-gray-300 rounded focus:ring-primary-500 focus:border-primary-500"
                                                                                            placeholder="0"
                                                                                            style="color: black !important;">
                                                                                    @endif
                                                                                </td>
                                                                                <td class="px-3 py-3">
                                                                                    @if ($serieRealizada)
                                                                                        <span
                                                                                            class="px-2 py-1 text-sm font-medium text-purple-800 bg-purple-100 rounded-full">
                                                                                            {{ $serieRealizada->peso_realizado }}
                                                                                            kg
                                                                                        </span>
                                                                                    @else
                                                                                        <input type="number"
                                                                                            wire:model="peso.{{ $serie->id }}"
                                                                                            step="0.5"
                                                                                            min="0"
                                                                                            class="w-20 px-2 py-1 text-sm text-gray-800 bg-white border border-gray-300 rounded focus:ring-primary-500 focus:border-primary-500"
                                                                                            placeholder="0.0"
                                                                                            style="color: black !important;">
                                                                                    @endif
                                                                                </td>
                                                                                <td class="px-3 py-3">
                                                                                    @if ($serieRealizada)
                                                                                        <button
                                                                                            wire:click="editarSerie({{ $serie->id }})"
                                                                                            class="px-2 py-1 text-sm font-medium text-blue-600 transition-colors rounded hover:text-blue-800 bg-blue-50 hover:bg-blue-100">
                                                                                            Editar
                                                                                        </button>
                                                                                    @else
                                                                                        <button
                                                                                            wire:click="guardarSerie({{ $serie->id }})"
                                                                                            class="px-2 py-1 text-sm font-medium text-blue-600 transition-colors rounded hover:text-blue-800 bg-blue-50 hover:bg-blue-100 disabled:opacity-50 disabled:cursor-not-allowed"
                                                                                            {{ empty($this->repeticiones[$serie->id] ?? null) && !$estaCompletado ? 'disabled' : '' }}>
                                                                                            Guardar
                                                                                        </button>
                                                                                    @endif
                                                                                </td>
                                                                            @elseif($isEntrenador)
                                                                                <td class="px-3 py-3">
                                                                                    @if ($serieRealizada)
                                                                                        <span
                                                                                            class="px-2 py-1 text-sm font-medium text-blue-800 bg-blue-100 rounded-full">
                                                                                            {{ $serieRealizada->repeticiones_realizadas }}
                                                                                            reps
                                                                                        </span>
                                                                                    @else
                                                                                        <span
                                                                                            class="px-2 py-1 text-sm font-medium text-gray-600 bg-gray-100 rounded-full">
                                                                                            No registrado
                                                                                        </span>
                                                                                    @endif
                                                                                </td>
                                                                                <td class="px-3 py-3">
                                                                                    @if ($serieRealizada)
                                                                                        <span
                                                                                            class="px-2 py-1 text-sm font-medium text-purple-800 bg-purple-100 rounded-full">
                                                                                            {{ $serieRealizada->peso_realizado }}
                                                                                            kg
                                                                                        </span>
                                                                                    @else
                                                                                        <span
                                                                                            class="px-2 py-1 text-sm font-medium text-gray-600 bg-gray-100 rounded-full">
                                                                                            No registrado
                                                                                        </span>
                                                                                    @endif
                                                                                </td>
                                                                                <td class="px-3 py-3">
                                                                                    @if ($serieRealizada)
                                                                                        <span
                                                                                            class="px-2 py-1 text-sm font-medium text-green-800 bg-green-100 rounded-full">
                                                                                            Completado
                                                                                        </span>
                                                                                    @else
                                                                                        <span
                                                                                            class="px-2 py-1 text-sm font-medium text-yellow-800 bg-yellow-100 rounded-full">
                                                                                            Pendiente
                                                                                        </span>
                                                                                    @endif
                                                                                </td>
                                                                            @endif
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    @else
                                                        <p class="mt-4 text-sm italic text-gray-500">No hay series
                                                            configuradas para este ejercicio.</p>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="italic text-center text-gray-500">No hay ejercicios configurados para
                                            este día.</p>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <p class="italic text-center text-gray-500">No hay días configurados para esta semana.</p>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <div class="p-8 text-center bg-white border border-gray-200 shadow-lg rounded-xl">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                <h3 class="mb-2 text-lg font-medium text-gray-600">No hay ejercicios configurados</h3>
                <p class="text-gray-500">Esta rutina no tiene semanas de entrenamiento configuradas.</p>
            </div>
        @endif
    </div>

    <div class="flex justify-end mt-8 space-x-4">
        <x-filament::button icon="heroicon-o-arrow-left" tag="a"
            href="{{ route('filament.powerCheck.resources.rutinas.index') }}" color="gray" outlined>
            Volver a la lista
        </x-filament::button>

        @if ($isEntrenador)
            <x-filament::button icon="heroicon-o-pencil" tag="a"
                href="{{ route('filament.powerCheck.resources.rutinas.edit', $rutina->id) }}" color="primary">
                Editar Rutina
            </x-filament::button>
        @endif
    </div>

</x-filament-panels::page>
