{{-- resources/views/filament/widgets/rutina-pendiente-de-hoy-widget.blade.php --}}
<x-filament::section>
    @if (!$this->rutina)
        <div class="text-gray-600">Aún no tienes una rutina asignada.</div>
    @else
        @if (!$this->diaId)
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-500">Rutina</div>
                    <div class="text-lg font-semibold">{{ $this->rutina->nombre }}</div>
                    <div class="mt-1 text-sm text-green-700">🎉 ¡No tienes pendientes! Todo completado.</div>
                </div>
                <x-filament::button tag="a"
                    href="{{ route('filament.powerCheck.resources.rutinas.view', $this->rutina->id) }}">
                    Ver rutina
                </x-filament::button>
            </div>
        @else
            <div class="flex items-center justify-between mb-3">
                <div>
                    <div class="text-sm text-gray-500">Sesión pendiente</div>
                    <div class="text-lg font-semibold">
                        Semana #{{ $this->semanaNum }} — {{ $this->diaLbl }}
                    </div>
                    <div class="text-sm text-gray-500">Rutina: {{ $this->rutina->nombre }}</div>
                </div>

                <x-filament::button tag="a"
                    href="{{ route('filament.powerCheck.resources.rutinas.view', $this->rutina->id) }}">
                    Ver rutina completa
                </x-filament::button>
            </div>

            @if (empty($this->ejercicios))
                <div class="text-gray-600">No hay ejercicios configurados para este día.</div>
            @else
                <div class="space-y-3">
                    @foreach ($this->ejercicios as $ej)
                        <div class="p-4 text-black bg-white border rounded-lg ">
                            <div class="flex items-start justify-between">
                                <div>
                                    <div class="font-semibold">
                                        {{ $ej['orden'] }}. {{ $ej['nombre'] }}
                                        @if ($ej['completo'])
                                            <span class="ml-2 text-green-600">✅</span>
                                        @endif
                                    </div>
                                    @if (!empty($ej['series']))
                                        <div class="mt-2 overflow-x-auto">
                                            <table class="w-full text-sm">
                                                <thead>
                                                    <tr class="bg-gray-100">
                                                        <th class="px-2 py-1 text-left">Serie</th>
                                                        <th class="px-2 py-1 text-left">Reps</th>
                                                        <th class="px-2 py-1 text-left">Peso</th>
                                                        <th class="px-2 py-1 text-left">Descanso</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($ej['series'] as $s)
                                                        <tr class="border-b">
                                                            <td class="px-2 py-1">#{{ $s['n'] }}</td>
                                                            <td class="px-2 py-1">{{ $s['reps'] }}</td>
                                                            <td class="px-2 py-1">
                                                                @if (($s['peso'] ?? 0) > 0)
                                                                    {{ $s['peso'] }} kg
                                                                @else
                                                                    Peso corporal
                                                                @endif
                                                            </td>
                                                            <td class="px-2 py-1">{{ $s['rest'] }}s</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif
    @endif
</x-filament::section>
