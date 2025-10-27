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
                                {{-- CORRECCIÓN DE SINTAXIS: Usamos ['rep_number'] --}}
                                Mejor Rep (Nº {{ $bestRepMetric['rep_number'] }})
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-danger-600 dark:text-danger-400">
                                {{-- CORRECCIÓN DE SINTAXIS: Usamos ['rep_number'] --}}
                                Peor Rep (Nº {{ $worstRepMetric['rep_number'] }})
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- INICIO DE LA LÓGICA DINÁMICA --}}

                        {{-- Métricas para Press de Banca --}}
                        @if ($record->movement === 'bench')
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <th scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    Score de Técnica
                                </th>
                                <td class="px-6 py-4 font-bold text-center text-success-600 dark:text-success-400">
                                    {{ number_format($bestRepMetric['score_general'], 1) }}%
                                </td>
                                <td class="px-6 py-4 font-bold text-center text-danger-600 dark:text-danger-400">
                                    {{ number_format($worstRepMetric['score_general'], 1) }}%
                                </td>
                            </tr>
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <th scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    Curva "J"
                                </th>
                                <td class="px-6 py-4 text-center">
                                    {{ number_format($bestRepMetric['curvatura_j_px'], 1) }} px
                                </td>
                                <td class="px-6 py-4 text-center">
                                    {{ number_format($worstRepMetric['curvatura_j_px'], 1) }} px
                                </td>
                            </tr>

                            {{-- Métricas para Sentadilla y Peso Muerto --}}
                        @else
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <th scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    Eficiencia de Trayectoria
                                </th>
                                <td class="px-6 py-4 font-bold text-center text-success-600 dark:text-success-400">
                                    {{ number_format($bestRepMetric['efficiency_pct'], 1) }}%
                                </td>
                                <td class="px-6 py-4 font-bold text-center text-danger-600 dark:text-danger-400">
                                    {{ number_format($worstRepMetric['efficiency_pct'], 1) }}%
                                </td>
                            </tr>
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <th scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    Desviación Horizontal (RMS)
                                </th>
                                <td class="px-6 py-4 text-center">
                                    {{ number_format($bestRepMetric['rms_px'], 1) }} px
                                </td>
                                <td class="px-6 py-4 text-center">
                                    {{ number_format($worstRepMetric['rms_px'], 1) }} px
                                </td>
                            </tr>

                            {{-- Métrica específica de Sentadilla o Peso Muerto --}}
                            @if ($record->movement === 'squat')
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <th scope="row"
                                        class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        Ángulo Mín. Rodilla (Profundidad)
                                    </th>
                                    <td class="px-6 py-4 text-center">
                                        {{ number_format($bestRepMetric['min_knee_angle'], 1) }}°
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        {{ number_format($worstRepMetric['min_knee_angle'], 1) }}°
                                    </td>
                                </tr>
                            @elseif ($record->movement === 'deadlift')
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <th scope="row"
                                        class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        Desviación Hombro-Barra
                                    </th>
                                    <td class="px-6 py-4 text-center">
                                        {{ number_format($bestRepMetric['avg_shoulder_bar_deviation_px'], 1) }} px
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        {{ number_format($worstRepMetric['avg_shoulder_bar_deviation_px'], 1) }} px
                                    </td>
                                </tr>
                            @endif
                        @endif
                        {{-- FIN DE LA LÓGICA DINÁMICA --}}
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-sm text-gray-500">No hay suficientes datos para una comparación detallada.</p>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
