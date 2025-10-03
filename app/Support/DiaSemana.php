<?php
// app/Support/DiaSemana.php
namespace App\Support;

use Carbon\Carbon;

final class DiaSemana
{
    public static function orden(string $dia): int
    {
        $map = [
            'Lunes' => 1,
            'Martes' => 2,
            'Miércoles' => 3,
            'Jueves' => 4,
            'Viernes' => 5,
            'Sábado' => 6,
            'Domingo' => 7,
        ];
        // tolerante a mayúsculas/minúsculas y sin tilde
        $k = self::normaliza($dia);
        foreach ($map as $nombre => $peso) {
            if (self::normaliza($nombre) === $k) return $peso;
        }
        return 99; // desconocidos al final
    }

    private static function normaliza(string $s): string
    {
        $s = mb_strtolower($s, 'UTF-8');
        $s = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ü'], ['a', 'e', 'i', 'o', 'u', 'u'], $s);
        return $s;
    }
}
