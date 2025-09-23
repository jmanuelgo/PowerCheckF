<x-filament-panels::page>
    <x-filament-panels::header
        :heading="$this->getTitle()"
        :subheading="$this->getSubheading()"
    />

    <div class="space-y-6">
        @foreach($semanas as $semana)
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 bg-primary-100 border-b">
                    <h3 class="text-lg font-semibold">Semana {{ $semana->numero_semana }}</h3>
                </div>

                <div class="p-6 space-y-4">
                    @forelse($semana->diasEntrenamiento as $dia)
                        <div class="border rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-3 flex items-center justify-between">
                                <span>{{ $dia->dia_semana }}</span>
                                <span class="text-sm text-gray-500">
                                    {{ $dia->ejerciciosDia->count() }} ejercicio(s)
                                </span>
                            </h4>

                            @forelse($dia->ejerciciosDia->sortBy('orden') as $ejercicioDia)
                                <div class="bg-gray-50 rounded p-4 mb-3">
                                    <div class="flex justify-between items-start mb-2">
                                        <h5 class="font-semibold text-lg">{{ $ejercicioDia->ejercicio->nombre }}</h5>
                                        <div class="text-sm text-gray-500">#{{ $ejercicioDia->orden }}</div>
                                    </div>

                                    @if($ejercicioDia->notas)
                                        <p class="text-sm text-gray-600 mb-3 italic">"{{ $ejercicioDia->notas }}"</p>
                                    @endif

                                    <div class="grid grid-cols-5 gap-2 text-sm font-medium bg-gray-200 px-2 py-1 rounded">
                                        <div>Serie</div>
                                        <div>Repeticiones</div>
                                        <div>Peso (kg)</div>
                                        <div>Descanso</div>
                                        <div>Acciones</div>
                                    </div>

                                    @foreach($ejercicioDia->seriesEjercicio->sortBy('numero_serie') as $serie)
                                        <div class="grid grid-cols-5 gap-2 text-sm py-2 border-t">
                                            <div>{{ $serie->numero_serie }}</div>
                                            <div>{{ $serie->repeticiones_objetivo }}</div>
                                            <div>{{ $serie->peso_objetivo ? $serie->peso_objetivo . ' kg' : 'Cuerpo' }}</div>
                                            <div>{{ $serie->descanso_segundos }} seg</div>
                                            <div>
                                                <x-filament::icon-button
                                                    icon="heroicon-o-pencil"
                                                    size="sm"
                                                    color="gray"
                                                    tooltip="Editar serie"
                                                />
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @empty
                                <p class="text-sm text-gray-500 italic">No hay ejercicios para este día</p>
                            @endforelse
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 italic">No hay días de entrenamiento para esta semana</p>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>

    <x-filament-actions::modals />
</x-filament-panels::page>
