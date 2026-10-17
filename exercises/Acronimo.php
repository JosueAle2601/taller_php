<?php

class Acronimo
{
    public function generar(string $frase): string
    {
        $frase = trim($frase);

        $frase = mb_strtoupper($frase, 'UTF-8');

        $frase = preg_replace(
            '/[^\p{L}\p{N}\s-]/u',
            '',
            $frase
        );

        $palabras = preg_split(
            '/[\s-]+/u',
            $frase,
            -1,
            PREG_SPLIT_NO_EMPTY
        );

        $acronimo = '';

        foreach ($palabras as $palabra) {
            $acronimo .= mb_substr($palabra, 0, 1, 'UTF-8');
        }

        return $acronimo;
    }
}
