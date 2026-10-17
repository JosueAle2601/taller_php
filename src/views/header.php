<?php
$tituloPagina = $tituloPagina ?? 'Inicio';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <title>
        <?= htmlspecialchars($tituloPagina, ENT_QUOTES, 'UTF-8') ?>
        | Ejercicios en PHP
    </title>

    <link rel="stylesheet" href="../css/styles.css">
</head>

<body>

    <header class="cabecera">
        <div class="contenido-cabecera">

            <a href="index.php" class="logo">
                <span class="logo-icono">PHP</span>
                <span class="logo-texto">Ejercicios en PHP</span>
            </a>

        </div>
    </header>
