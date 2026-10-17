<?php

class Sucesiones
{
    public function fibonacci(int $n): array
    {
        if ($n < 0) {
            throw new InvalidArgumentException(
                'La cantidad de términos no puede ser negativa.'
            );
        }

        $limite = PHP_INT_SIZE >= 8 ? 90 : 46;

        if ($n > $limite) {
            throw new InvalidArgumentException(
                "La cantidad máxima permitida es {$limite} términos."
            );
        }

        $serie = [];
        $anterior = 0;
        $actual = 1;

        for ($i = 0; $i < $n; $i++) {
            $serie[] = $anterior;

            if ($i < $n - 1) {
                $siguiente = $anterior + $actual;
                $anterior = $actual;
                $actual = $siguiente;
            }
        }

        return $serie;
    }

    public function factorial(int $n): int
    {
        if ($n < 0) {
            throw new InvalidArgumentException(
                'El factorial no está definido para enteros negativos.'
            );
        }

        $resultado = 1;

        for ($i = 2; $i <= $n; $i++) {
            if ($resultado > intdiv(PHP_INT_MAX, $i)) {
                throw new OverflowException(
                    'El resultado supera el límite de los enteros de PHP.'
                );
            }

            $resultado *= $i;
        }

        return $resultado;
    }
}
