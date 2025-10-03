<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ejercicio;

class EjerciciosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ejercicios = [
            // Lunes
            [
                'nombre' => 'SQ Low Bar',
                'descripcion' => 'Sentadilla con barra baja para trabajar fuerza en piernas y glúteos.',
                'tipo' => 'Fuerza',
                'grupo_muscular' => 'Piernas'
            ],
            [
                'nombre' => 'BP Pause',
                'descripcion' => 'Press de banca con pausa en el pecho para mejorar control y fuerza.',
                'tipo' => 'Fuerza',
                'grupo_muscular' => 'Pectorales'
            ],
            [
                'nombre' => 'Press militar sentado',
                'descripcion' => 'Ejercicio de empuje vertical para hombros y tríceps.',
                'tipo' => 'Fuerza',
                'grupo_muscular' => 'Hombros'
            ],
            [
                'nombre' => 'Remo barra',
                'descripcion' => 'Remo con barra para trabajar dorsales y bíceps.',
                'tipo' => 'Fuerza',
                'grupo_muscular' => 'Espalda'
            ],
            [
                'nombre' => 'Tríceps (JB Press)',
                'descripcion' => 'Extensión de tríceps estilo JM Press.',
                'tipo' => 'Fuerza',
                'grupo_muscular' => 'Tríceps'
            ],
            [
                'nombre' => 'Dominadas',
                'descripcion' => 'Ejercicio de tracción para espalda y bíceps.',
                'tipo' => 'Fuerza',
                'grupo_muscular' => 'Espalda'
            ],

            // Martes
            [
                'nombre' => 'DL Sumó Beltless sobre bloque',
                'descripcion' => 'Peso muerto sumo sin cinturón desde bloque, trabaja fuerza en glúteos y femorales.',
                'tipo' => 'Fuerza',
                'grupo_muscular' => 'Piernas'
            ],
            [
                'nombre' => 'BP Kodama',
                'descripcion' => 'Variante de press de banca enfocado en fuerza y control.',
                'tipo' => 'Fuerza',
                'grupo_muscular' => 'Pectorales'
            ],
            [
                'nombre' => 'Hack SQ',
                'descripcion' => 'Sentadilla hack para trabajar cuádriceps y glúteos.',
                'tipo' => 'Fuerza',
                'grupo_muscular' => 'Piernas'
            ],
            [
                'nombre' => 'SQ Búlgaro mancuerna',
                'descripcion' => 'Sentadilla búlgara con mancuernas para pierna y glúteos.',
                'tipo' => 'Fuerza',
                'grupo_muscular' => 'Piernas'
            ],
            [
                'nombre' => 'Curl femoral',
                'descripcion' => 'Ejercicio de aislamiento para los isquiotibiales.',
                'tipo' => 'Fuerza',
                'grupo_muscular' => 'Piernas'
            ],

            // Jueves
            [
                'nombre' => 'BP TNG',
                'descripcion' => 'Bench Press Touch and Go, sin pausa en el pecho.',
                'tipo' => 'Fuerza',
                'grupo_muscular' => 'Pectorales'
            ],
            [
                'nombre' => 'SQ Frontal',
                'descripcion' => 'Sentadilla frontal para trabajar cuádriceps y core.',
                'tipo' => 'Fuerza',
                'grupo_muscular' => 'Piernas'
            ],
            [
                'nombre' => 'Press militar mancuerna',
                'descripcion' => 'Press militar con mancuernas, trabaja hombros y tríceps.',
                'tipo' => 'Fuerza',
                'grupo_muscular' => 'Hombros'
            ],
            [
                'nombre' => 'Remo polea agarre abierto',
                'descripcion' => 'Remo en polea con agarre abierto para espalda media.',
                'tipo' => 'Fuerza',
                'grupo_muscular' => 'Espalda'
            ],
            [
                'nombre' => 'Tríceps (JB Press mancuerna)',
                'descripcion' => 'Extensión de tríceps estilo JM Press con mancuernas.',
                'tipo' => 'Fuerza',
                'grupo_muscular' => 'Tríceps'
            ],
            [
                'nombre' => 'Jalón al pecho supino',
                'descripcion' => 'Jalón al pecho con agarre supino para dorsales y bíceps.',
                'tipo' => 'Fuerza',
                'grupo_muscular' => 'Espalda'
            ],

            // Viernes
            [
                'nombre' => 'DL Sumó Pause Beltless',
                'descripcion' => 'Peso muerto sumo con pausa, sin cinturón.',
                'tipo' => 'Fuerza',
                'grupo_muscular' => 'Piernas'
            ],
            [
                'nombre' => 'SLDL',
                'descripcion' => 'Peso muerto rumano a una pierna para isquiotibiales y glúteos.',
                'tipo' => 'Fuerza',
                'grupo_muscular' => 'Piernas'
            ],
            [
                'nombre' => 'BP Inclinado Close Grip',
                'descripcion' => 'Press de banca inclinado con agarre cerrado.',
                'tipo' => 'Fuerza',
                'grupo_muscular' => 'Pectorales'
            ],
            [
                'nombre' => 'Zancadas static',
                'descripcion' => 'Zancadas estáticas para cuádriceps y glúteos.',
                'tipo' => 'Fuerza',
                'grupo_muscular' => 'Piernas'
            ],
            // Piernas / básicos
            [
                'nombre' => 'SQ',
                'descripcion' => 'Sentadilla convencional enfocada en cuádriceps, glúteos y core.',
                'tipo' => 'Fuerza',
                'grupo_muscular' => 'Piernas',
            ],
            [
                'nombre' => 'Box SQ Pause 2"',
                'descripcion' => 'Sentadilla a caja con pausa de 2 segundos para control y potencia.',
                'tipo' => 'Fuerza',
                'grupo_muscular' => 'Piernas',
            ],
            [
                'nombre' => 'Hack SQ Tempo 300',
                'descripcion' => 'Sentadilla hack con tempo 3-0-0 para énfasis en cuádriceps.',
                'tipo' => 'Fuerza',
                'grupo_muscular' => 'Piernas',
            ],
            [
                'nombre' => 'Curl Femoral Sentado',
                'descripcion' => 'Aislamiento de isquiotibiales en máquina sentado.',
                'tipo' => 'Fuerza',
                'grupo_muscular' => 'Piernas',
            ],
            [
                'nombre' => 'Pull Through Polea',
                'descripcion' => 'Bisagra de cadera con cuerda en polea para glúteos e isquios.',
                'tipo' => 'Fuerza',
                'grupo_muscular' => 'Glúteos',
            ],
            [
                'nombre' => 'DL',
                'descripcion' => 'Peso muerto convencional para cadena posterior.',
                'tipo' => 'Fuerza',
                'grupo_muscular' => 'Piernas',
            ],
            [
                'nombre' => 'Hip Thrust',
                'descripcion' => 'Elevación de cadera con barra para glúteos.',
                'tipo' => 'Fuerza',
                'grupo_muscular' => 'Glúteos',
            ],
            [
                'nombre' => 'Zancadas Dinamic',
                'descripcion' => 'Zancadas caminando/dinámicas para cuádriceps y glúteos.',
                'tipo' => 'Fuerza',
                'grupo_muscular' => 'Piernas',
            ],

            // Pecho / empuje
            [
                'nombre' => 'BP Kodama + Larsen',
                'descripcion' => 'Variante de press de banca combinando Kodama y Larsen para control y estabilidad.',
                'tipo' => 'Fuerza',
                'grupo_muscular' => 'Pectorales',
            ],
            [
                'nombre' => 'Flexiones',
                'descripcion' => 'Flexiones de pecho con enfoque en pectorales, hombros y tríceps.',
                'tipo' => 'Calistenia',
                'grupo_muscular' => 'Pectorales',
            ],
            [
                'nombre' => 'BP Inclinado Mancuerna',
                'descripcion' => 'Press inclinado con mancuernas para parte superior del pecho.',
                'tipo' => 'Fuerza',
                'grupo_muscular' => 'Pectorales',
            ],

            // Hombros / empuje
            [
                'nombre' => 'Press militar a 1 brazo de Pie',
                'descripcion' => 'Press unilateral de hombro de pie, mejora estabilidad del core.',
                'tipo' => 'Fuerza',
                'grupo_muscular' => 'Hombros',
            ],

            // Espalda / tracción
            [
                'nombre' => 'Remo Apoyo Mancuerna',
                'descripcion' => 'Remo con mancuerna apoyando el torso para dorsales y trapecios.',
                'tipo' => 'Fuerza',
                'grupo_muscular' => 'Espalda',
            ],
            [
                'nombre' => 'Seal Row',
                'descripcion' => 'Remo con apoyo completo en banco (seal row) para aislar dorsales.',
                'tipo' => 'Fuerza',
                'grupo_muscular' => 'Espalda',
            ],
            [
                'nombre' => 'Jalón al pecho en Polea Prono',
                'descripcion' => 'Jalón al pecho con agarre prono para dorsales y bíceps.',
                'tipo' => 'Fuerza',
                'grupo_muscular' => 'Espalda',
            ],

            // Brazos
            [
                'nombre' => 'BICEPS (Curl Martillo)',
                'descripcion' => 'Curl martillo para braquiorradial y bíceps.',
                'tipo' => 'Fuerza',
                'grupo_muscular' => 'Bíceps',
            ],
            [
                'nombre' => 'BICEPS (Curl Inclinado Mancuerna)',
                'descripcion' => 'Curl con mancuernas en banco inclinado para mayor estiramiento del bíceps.',
                'tipo' => 'Fuerza',
                'grupo_muscular' => 'Bíceps',
            ],
            [
                'nombre' => 'TRICEPS (Press Francés Mancuerna)',
                'descripcion' => 'Extensión de tríceps estilo press francés con mancuernas.',
                'tipo' => 'Fuerza',
                'grupo_muscular' => 'Tríceps',
            ],

            // Core
            [
                'nombre' => 'Abdomen Isométrico en GHD',
                'descripcion' => 'Trabajo isométrico de core en máquina GHD.',
                'tipo' => 'Resistencia',
                'grupo_muscular' => 'Abdomen',
            ],
        ];

        foreach ($ejercicios as $ejercicio) {
            Ejercicio::create($ejercicio);
        }
    }
}
