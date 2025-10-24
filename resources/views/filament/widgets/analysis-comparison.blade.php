<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Análisis Comparativo: Mejor vs. Peor Repetición
        </x-slot>

        @if ($bestRepMetric && $worstRepMetric)
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Métrica</th>
                            <th scope="col" class="px-6 py-3 text-center text-success-600 dark:text-success-400">
                                Mejor Rep (Nº {{ $bestRepMetric->rep_number }})
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-danger-600 dark:text-danger-400">
                                Peor Rep (Nº {{ $worstRepMetric->rep_number }})
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row"
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Eficiencia de Trayectoria
                            </th>
                            <td class="px-6 py-4 font-bold text-center text-success-600 dark:text-success-400">
                                {{ number_format(data_get($bestRepMetric, 'efficiency_pct'), 1) }}%
                            </td>
                            <td class="px-6 py-4 font-bold text-center text-danger-600 dark:text-danger-400">
                                {{ number_format(data_get($worstRepMetric, 'efficiency_pct'), 1) }}%
                            </td>
                        </tr>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row"
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Desviación Horizontal (RMS)
                            </th>
                            <td class="px-6 py-4 text-center">
                                {{ number_format(data_get($bestRepMetric, 'rms_px'), 1) }} px
                            </td>
                            <td class="px-6 py-4 text-center">
                                {{ number_format(data_get($worstRepMetric, 'rms_px'), 1) }} px
                            </td>
                        </tr>

                        @if ($record->movement === 'squat')
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <th scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    Ángulo Mín. Rodilla (Profundidad)
                                </th>
                                <td class="px-6 py-4 text-center">
                                    {{ number_format(data_get($bestRepMetric, 'min_knee_angle'), 1) }}°
                                </td>
                                <td class="px-6 py-4 text-center">
                                    {{ number_format(data_get($worstRepMetric, 'min_knee_angle'), 1) }}°
                                </td>
                            </tr>
                        @elseif ($record->movement === 'deadlift')
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <th scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    Desviación Hombro-Barra
                                </th>
                                <td class="px-6 py-4 text-center">
                                    {{ number_format(data_get($bestRepMetric, 'avg_shoulder_bar_deviation_px'), 1) }} px
                                </td>
                                <td class="px-6 py-4 text-center">
                                    {{ number_format(data_get($worstRepMetric, 'avg_shoulder_bar_deviation_px'), 1) }}
                                    px
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-sm text-gray-500">No hay suficientes datos para una comparación detallada.</p>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
