{{-- resources/views/filament/widgets/rutina-pendiente-de-hoy-widget.blade.php --}}
<x-filament-widgets::widget>
    {{-- Se inicializa Alpine.js para los inputs --}}
    <div x-data>
        <x-filament::section>
            @if (!$this->rutina)
                {{-- Mensaje para cuando no hay rutina --}}
                <div class="text-center text-gray-400">Aún no tienes una rutina asignada.</div>
            @elseif (!$this->diaId)
                {{-- Mensaje de completado --}}
                <div class="text-center text-green-400">🎉 ¡Felicidades! Has completado toda la rutina.</div>
            @else
                {{-- Encabezado del día de entrenamiento --}}
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h2 class="flex items-center gap-2 text-lg font-semibold text-gray-200">
                            <x-heroicon-o-clock class="w-6 h-6 text-gray-400" />
                            {{ strtoupper($this->diaLbl) }}
                            <span class="px-2 py-1 text-xs font-medium text-gray-400 bg-gray-700 rounded-full">
                                {{ count($this->ejercicios) }} ejercicio(s)
                            </span>
                        </h2>
                        <p class="text-sm text-gray-400">
                            Semana #{{ $this->semanaNum }} | Rutina: {{ $this->rutina->nombre }}
                        </p>
                    </div>
                    {{-- BOTÓN AÑADIDO --}}
                    <div>
                        <x-filament::button tag="a"
                            href="{{ route('filament.powerCheck.resources.rutinas.index') }}"
                            icon="heroicon-o-rectangle-stack" color="gray">
                            Ver Rutinas
                        </x-filament::button>
                    </div>
                </div>

                {{-- Lista de ejercicios --}}
                <div class="space-y-6">
                    @foreach ($this->ejercicios as $ej)
                        @php
                            $estaCompletado = $ej['completo'];
                        @endphp
                        <div class="p-4 bg-gray-800 border border-gray-700 rounded-lg shadow-sm">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-bold text-gray-100">
                                    {{ $ej['orden'] }}. {{ $ej['nombre'] }}
                                    @if ($estaCompletado)
                                        <span class="ml-2 text-green-400">✅</span>
                                    @endif
                                </h3>
                                @if (!empty($ej['descripcion']))
                                    <p class="mt-1 text-sm text-gray-400">
                                        {{ $ej['descripcion'] }}
                                    </p>
                                @endif
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-1 text-xs font-medium text-blue-300 bg-blue-900 rounded">
                                        {{ count($ej['series']) }} {{ count($ej['series']) > 1 ? 'series' : 'serie' }}
                                    </span>
                                    <x-filament::button size="sm"
                                        color="{{ $estaCompletado ? 'success' : 'gray' }}" outlined
                                        wire:click="toggleEjercicio({{ $ej['id'] }})">
                                        {{ $estaCompletado ? 'Completado' : 'Marcar como hecho' }}
                                    </x-filament::button>
                                </div>
                            </div>

                            {{-- Tabla de Series --}}
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-left">
                                    <thead class="text-xs text-gray-300 uppercase bg-gray-700">
                                        <tr>
                                            <th class="px-4 py-2">Serie</th>
                                            <th class="px-4 py-2">Repeticiones Objetivo</th>
                                            <th class="px-4 py-2">Peso Objetivo</th>
                                            <th class="px-4 py-2">Repeticiones Realizadas</th>
                                            <th class="px-4 py-2">Peso Realizado</th>
                                            <th class="px-4 py-2 text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($ej['series'] as $s)
                                            @php
                                                $serieRealizada = $this->getSerieRealizada($s['id']);
                                            @endphp
                                            <tr class="bg-gray-800 border-b border-gray-700 hover:bg-gray-700">
                                                <td class="px-4 py-3 font-bold text-gray-200">#{{ $s['n'] }}</td>
                                                <td class="px-4 py-3">
                                                    <span
                                                        class="px-2 py-1 text-sm font-medium text-green-300 bg-green-900 rounded-full">
                                                        {{ $s['reps'] }} reps
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <span
                                                        class="px-2 py-1 text-sm font-medium text-orange-300 bg-orange-900 rounded-full">
                                                        {{ $s['peso'] }} kg
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    @if ($serieRealizada)
                                                        <span
                                                            class="font-semibold text-gray-200">{{ $serieRealizada->repeticiones_realizadas }}</span>
                                                    @else
                                                        <x-filament::input.wrapper
                                                            class="bg-gray-700 border-gray-600 focus-within:ring-primary-500 focus-within:border-primary-500">
                                                            <x-filament::input type="text" inputmode="numeric"
                                                                wire:model.defer="repeticiones.{{ $s['id'] }}"
                                                                placeholder="0"
                                                                class="text-gray-100 placeholder-gray-400 bg-gray-700"
                                                                x-on:input="$event.target.value = $event.target.value.replace(/[^0-9]/g, '')" />
                                                        </x-filament::input.wrapper>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3">
                                                    @if ($serieRealizada)
                                                        <span
                                                            class="font-semibold text-gray-200">{{ $serieRealizada->peso_realizado }}
                                                            kg</span>
                                                    @else
                                                        <x-filament::input.wrapper
                                                            class="bg-gray-700 border-gray-600 focus-within:ring-primary-500 focus-within:border-primary-500">
                                                            <x-filament::input type="text" inputmode="decimal"
                                                                wire:model.defer="peso.{{ $s['id'] }}"
                                                                placeholder="0.0"
                                                                class="text-gray-100 placeholder-gray-400 bg-gray-700"
                                                                x-on:input="$event.target.value = $event.target.value.replace(/[^0-9.]/g, '')" />
                                                        </x-filament::input.wrapper>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    @if ($serieRealizada)
                                                        <button wire:click="editarSerie({{ $s['id'] }})"
                                                            class="font-medium text-blue-400 hover:underline">
                                                            Editar
                                                        </button>
                                                    @else
                                                        <button wire:click="guardarSerie({{ $s['id'] }})"
                                                            class="font-medium text-primary-400 hover:underline">
                                                            Guardar
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-filament::section>
    </div>
</x-filament-widgets::widget>
