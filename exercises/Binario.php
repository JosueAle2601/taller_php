<?php

class Binario
{
    public function convertir(int $numero): string
    {
        if ($numero === 0) {
            return '0';
        }

        $negativo = $numero < 0;
        $digitos = '';

        while ($numero !== 0) {
            $resto = abs($numero % 2);
            $digitos .= (string) $resto;

            $numero = intdiv($numero, 2);
        }

        $binario = strrev($digitos);

        return $negativo ? '-' . $binario : $binario;
    }
}
