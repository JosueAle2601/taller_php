<?php

class Estadistica
{
    public function calcular(array $numeros): array
    {
        if (count($numeros) === 0) {
            throw new InvalidArgumentException(
                'Debes ingresar al menos un número.'
            );
        }

        $cantidad = count($numeros);
        $suma = array_sum($numeros);
        $promedio = $suma / $cantidad;

        $numerosOrdenados = $numeros;
        sort($numerosOrdenados, SORT_NUMERIC);
        $mitad = (int) ($cantidad / 2);

        if ($cantidad % 2 === 0) {
            $mediana = ($numerosOrdenados[$mitad - 1] + $numerosOrdenados[$mitad]) / 2;
        } else {
            $mediana = $numerosOrdenados[$mitad];
        }

        $frecuencias = [];

        foreach ($numeros as $numero) {
            $clave = sprintf('%.12g', $numero);

            if (!isset($frecuencias[$clave])) {
                $frecuencias[$clave] = [
                    'valor' => $numero,
                    'frecuencia' => 0
                ];
            }

            $frecuencias[$clave]['frecuencia']++;
        }

        $frecuenciaMaxima = max(
            array_column($frecuencias, 'frecuencia')
        );

        $modas = [];

        if ($frecuenciaMaxima > 1) {
            foreach ($frecuencias as $dato) {
                if ($dato['frecuencia'] === $frecuenciaMaxima) {
                    $modas[] = $dato['valor'];
                }
            }
        }

        return [
            'cantidad' => $cantidad,
            'suma' => $suma,
            'promedio' => $promedio,
            'mediana' => $mediana,
            'modas' => $modas,
            'frecuencia_maxima' => $frecuenciaMaxima
        ];
    }
}