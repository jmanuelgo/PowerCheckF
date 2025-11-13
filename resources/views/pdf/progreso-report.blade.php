<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Progreso - {{ $ejercicio->nombre }}</title>
    <style>
        .logo {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 120px;
        }
        body { font-family: sans-serif; margin: 20px; color: #333; }
        h1, h2 { color: #1d4ed8; border-bottom: 1px solid #ddd; padding-bottom: 5px; margin-bottom: 15px; }
        h1 { font-size: 24px; }
        h2 { font-size: 18px; margin-top: 25px; }
        .stats-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; } /* Simple grid simulation */
        .stat-card { border: 1px solid #eee; padding: 10px; border-radius: 5px; background-color: #f9f9f9; }
        .stat-label { font-size: 12px; color: #777; margin-bottom: 3px; }
        .stat-value { font-size: 16px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 12px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        thead th { background-color: #e2e8f0; }
    </style>
</head>
<body>

    <h1>Reporte de Progreso: {{ $ejercicio->nombre }}</h1>
    <p>Generado el: {{ now()->translatedFormat('d M Y') }}</p>

    <h2>Estadísticas Generales</h2>
    <table>
        <tbody>
            <tr>
                <td><strong>RM aprox. actual (30d):</strong></td>
                <td>{{ number_format($stats['rm_aprox_actual'] ?? 0, 1) }} kg</td>
            </tr>
            <tr>
                <td><strong>Mejor RM histórico:</strong></td>
                <td>{{ number_format($stats['rm_mejor'] ?? 0, 1) }} kg</td>
            </tr>
            <tr>
                <td><strong>Adherencia (30d):</strong></td>
                <td>
                    @if (!is_null($stats['adherencia']))
                        {{ $stats['adherencia'] }}% ({{ $stats['sets_realizadas'] }}/{{ $stats['sets_planeadas'] }} sets)
                    @else
                        —
                    @endif
                </td>
            </tr>
             <tr>
                <td><strong>Total Series Histórico:</strong></td>
                <td>{{ $stats['total_sets'] ?? 0 }}</td>
            </tr>
        </tbody>
    </table>


    <h2>Progresión Máxima por Día</h2>
    @if (!empty($progreso))
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Max Peso (kg)</th>
                    <th>Max Reps</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($progreso as $dia)
                    <tr>
                        <td>{{ $dia['fecha'] instanceof \Carbon\Carbon ? $dia['fecha']->format('d/m/Y') : $dia['fecha'] }}</td>
                        <td>{{ number_format($dia['max_peso'] ?? 0, 1) }}</td>
                        <td>{{ $dia['max_reps'] ?? 0 }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No hay datos de progresión disponibles.</p>
    @endif


    <h2>Historial General de Series</h2>
    @if (!empty($historial))
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Peso Realizado (kg)</th>
                    <th>Repeticiones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($historial as $serie)
                    <tr>
                        <td>{{ $serie['fecha'] }}</td>
                        <td>{{ number_format($serie['peso'] ?? 0, 1) }}</td>
                        <td>{{ $serie['repeticiones'] ?? 0 }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No hay historial de series disponible.</p>
    @endif

</body>
</html>
