<?php

class Conjuntos
{
    public function calcular(array $conjuntoA, array $conjuntoB): array
    {
        $conjuntoA = array_values(array_unique($conjuntoA));
        $conjuntoB = array_values(array_unique($conjuntoB));

        return [
            'conjuntoA' => $conjuntoA,
            'conjuntoB' => $conjuntoB,
            'union' => array_values(
                array_unique(array_merge($conjuntoA, $conjuntoB))
            ),
            'interseccion' => array_values(
                array_intersect($conjuntoA, $conjuntoB)
            ),
            'diferenciaAB' => array_values(
                array_diff($conjuntoA, $conjuntoB)
            ),
            'diferenciaBA' => array_values(
                array_diff($conjuntoB, $conjuntoA)
            )
        ];
    }
}
