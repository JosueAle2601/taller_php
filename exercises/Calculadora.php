<?php

class Calculadora
{
    public function calcular(
        float $numero1,
        float $numero2,
        string $operacion
    ): float {
        switch ($operacion) {
            case 'suma':
                return $numero1 + $numero2;

            case 'resta':
                return $numero1 - $numero2;

            case 'multiplicacion':
                return $numero1 * $numero2;

            case 'division':
                if ($numero2 == 0.0) {
                    throw new InvalidArgumentException(
                        'No es posible dividir entre cero.'
                    );
                }

                return $numero1 / $numero2;

            case 'porcentaje':
                return ($numero1 / 100) * $numero2;

            default:
                throw new InvalidArgumentException(
                    'La operación seleccionada no es válida.'
                );
        }
    }
}
