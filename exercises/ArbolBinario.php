<?php

class ArbolBinario
{
    public function construir(
        ?array $preorden,
        ?array $inorden,
        ?array $postorden
    ): array {
        $recorridos = [
            'preorden' => $preorden,
            'inorden' => $inorden,
            'postorden' => $postorden
        ];

        $disponibles = array_filter(
            $recorridos,
            fn($recorrido) => $recorrido !== null
        );

        if (count($disponibles) < 2) {
            throw new InvalidArgumentException(
                'Debes ingresar al menos dos recorridos.'
            );
        }

        foreach ($disponibles as $recorrido) {
            if (empty($recorrido)) {
                throw new InvalidArgumentException(
                    'Los recorridos no pueden estar vacíos.'
                );
            }

            if (count($recorrido) !== count(array_unique($recorrido))) {
                throw new InvalidArgumentException(
                    'Cada nodo debe aparecer una sola vez en cada recorrido.'
                );
            }
        }

        $referencia = reset($disponibles);
        $nodosReferencia = $referencia;
        sort($nodosReferencia);

        foreach ($disponibles as $recorrido) {
            $nodosActuales = $recorrido;
            sort($nodosActuales);

            if ($nodosActuales !== $nodosReferencia) {
                throw new InvalidArgumentException(
                    'Todos los recorridos deben contener los mismos nodos.'
                );
            }
        }

        // Si se proporcionan los tres, se priorizan preorden e inorden.
        if ($preorden !== null && $inorden !== null) {
            $raiz = $this->desdePreordenInorden($preorden, $inorden);
        } elseif ($inorden !== null && $postorden !== null) {
            $raiz = $this->desdeInordenPostorden($inorden, $postorden);
        } else {
            // Preorden + postorden: los hijos únicos se colocan a la izquierda.
            $raiz = $this->desdePreordenPostorden($preorden, $postorden);
        }

        $calculados = [
            'preorden' => $this->obtenerPreorden($raiz),
            'inorden' => $this->obtenerInorden($raiz),
            'postorden' => $this->obtenerPostorden($raiz)
        ];

        foreach ($disponibles as $nombre => $recorrido) {
            if ($calculados[$nombre] !== $recorrido) {
                throw new InvalidArgumentException(
                    'Los recorridos ingresados no corresponden a un mismo árbol binario.'
                );
            }
        }

        return [
            'raiz' => $raiz,
            'preorden' => $calculados['preorden'],
            'inorden' => $calculados['inorden'],
            'postorden' => $calculados['postorden']
        ];
    }

    private function crearNodo(string $valor): array
    {
        return [
            'valor' => $valor,
            'izquierda' => null,
            'derecha' => null
        ];
    }

    private function desdePreordenInorden(
        array $preorden,
        array $inorden
    ): ?array {
        if (empty($preorden)) {
            return null;
        }

        $valorRaiz = $preorden[0];
        $posicion = array_search($valorRaiz, $inorden, true);

        if ($posicion === false) {
            throw new InvalidArgumentException(
                'No se pudo reconstruir el árbol con esos recorridos.'
            );
        }

        $cantidadIzquierda = $posicion;

        $raiz = $this->crearNodo($valorRaiz);

        $raiz['izquierda'] = $this->desdePreordenInorden(
            array_slice($preorden, 1, $cantidadIzquierda),
            array_slice($inorden, 0, $cantidadIzquierda)
        );

        $raiz['derecha'] = $this->desdePreordenInorden(
            array_slice($preorden, $cantidadIzquierda + 1),
            array_slice($inorden, $cantidadIzquierda + 1)
        );

        return $raiz;
    }

    private function desdeInordenPostorden(
        array $inorden,
        array $postorden
    ): ?array {
        if (empty($postorden)) {
            return null;
        }

        $valorRaiz = $postorden[count($postorden) - 1];
        $posicion = array_search($valorRaiz, $inorden, true);

        if ($posicion === false) {
            throw new InvalidArgumentException(
                'No se pudo reconstruir el árbol con esos recorridos.'
            );
        }

        $cantidadIzquierda = $posicion;

        $raiz = $this->crearNodo($valorRaiz);

        $raiz['izquierda'] = $this->desdeInordenPostorden(
            array_slice($inorden, 0, $cantidadIzquierda),
            array_slice($postorden, 0, $cantidadIzquierda)
        );

        $raiz['derecha'] = $this->desdeInordenPostorden(
            array_slice($inorden, $cantidadIzquierda + 1),
            array_slice(
                $postorden,
                $cantidadIzquierda,
                count($postorden) - $cantidadIzquierda - 1
            )
        );

        return $raiz;
    }

    private function desdePreordenPostorden(
        array $preorden,
        array $postorden
    ): ?array {
        if (empty($preorden)) {
            return null;
        }

        $raiz = $this->crearNodo($preorden[0]);

        if (count($preorden) === 1) {
            return $raiz;
        }

        $raizIzquierda = $preorden[1];
        $posicion = array_search($raizIzquierda, $postorden, true);

        if ($posicion === false) {
            throw new InvalidArgumentException(
                'No se pudo reconstruir el árbol con esos recorridos.'
            );
        }

        $cantidadIzquierda = $posicion + 1;

        // Si solo hay un hijo, se interpreta como hijo izquierdo.
        if ($cantidadIzquierda >= count($preorden) - 1) {
            $cantidadIzquierda = count($preorden) - 1;
        }

        $raiz['izquierda'] = $this->desdePreordenPostorden(
            array_slice($preorden, 1, $cantidadIzquierda),
            array_slice($postorden, 0, $cantidadIzquierda)
        );

        $raiz['derecha'] = $this->desdePreordenPostorden(
            array_slice($preorden, $cantidadIzquierda + 1),
            array_slice(
                $postorden,
                $cantidadIzquierda,
                count($postorden) - $cantidadIzquierda - 1
            )
        );

        return $raiz;
    }

    private function obtenerPreorden(?array $nodo): array
    {
        if ($nodo === null) {
            return [];
        }

        return array_merge(
            [$nodo['valor']],
            $this->obtenerPreorden($nodo['izquierda']),
            $this->obtenerPreorden($nodo['derecha'])
        );
    }

    private function obtenerInorden(?array $nodo): array
    {
        if ($nodo === null) {
            return [];
        }

        return array_merge(
            $this->obtenerInorden($nodo['izquierda']),
            [$nodo['valor']],
            $this->obtenerInorden($nodo['derecha'])
        );
    }

    private function obtenerPostorden(?array $nodo): array
    {
        if ($nodo === null) {
            return [];
        }

        return array_merge(
            $this->obtenerPostorden($nodo['izquierda']),
            $this->obtenerPostorden($nodo['derecha']),
            [$nodo['valor']]
        );
    }
}
