<?php

spl_autoload_register(function (string $nombreClase): void {
    $rutaClase = __DIR__ . '/../exercises/' . $nombreClase . '.php';

    if (is_file($rutaClase)) {
        require_once $rutaClase;
    }
});