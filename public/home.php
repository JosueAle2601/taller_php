<?php
$tituloPagina = 'Inicio';

require_once __DIR__ . '/../src/views/header.php';

$ejercicios = [
    [
        'numero' => 1,
        'titulo' => 'Generador de Acrónimos',
        'descripcion' => 'Convierte frases en sus iniciales.',
        'enlace' => 'acronimo.php',
        'icono' => 'Aa'
    ],
    [
        'numero' => 2,
        'titulo' => 'Fibonacci y Factorial',
		'descripcion' => 'Calcula sucesiones y factoriales.',
        'enlace' => 'sucesiones.php',
        'icono' => '&#934; !'
    ],
    [
        'numero' => 3,
        'titulo' => 'Cálculo Estadístico',
		'descripcion' => 'Calcula medidas estadísticas.',
        'enlace' => 'estadistica.php',
        'icono' => '&#931;'
    ],
    [
        'numero' => 4,
        'titulo' => 'Operaciones entre Conjuntos',
		'descripcion' => 'Resuelve operaciones entre A y B.',
        'enlace' => 'conjuntos.php',
        'icono' => 'A&#8746;B'
    ],
    [
        'numero' => 5,
        'titulo' => 'Convertir a Números Binarios',
		'descripcion' => 'Convierte enteros a base dos.',
        'enlace' => 'binario.php',
        'icono' => '01'
    ],
    [
        'numero' => 6,
        'titulo' => 'Constructor de Árboles Binarios',
		'descripcion' => 'Construye árboles a partir de recorridos.',
        'enlace' => 'arbol-binario.php',
        'icono' => 'T'
    ],
    [
        'numero' => 7,
        'titulo' => 'Calculadora Básica',
		'descripcion' => 'Realiza operaciones y consulta el historial.',
        'enlace' => 'calculadora.php',
        'icono' => '&#177;'
    ]
];
?>

<main class="pagina-principal">

    <section class="presentacion">
        <span class="etiqueta">Práctica de PHP y POO</span>

        <h1>Selecciona un ejercicio</h1>

    </section>

    <section class="rejilla-ejercicios" aria-label="Lista de ejercicios">

        <?php foreach ($ejercicios as $ejercicio): ?>

            <a class="tarjeta-ejercicio"
               href="<?= htmlspecialchars($ejercicio['enlace'], ENT_QUOTES, 'UTF-8') ?>">

                <span class="icono-ejercicio" aria-hidden="true">
                    <?= $ejercicio['icono'] ?>
                </span>

                <span class="contenido-tarjeta">
                    <span class="numero-ejercicio">
                        EJERCICIO <?= $ejercicio['numero'] ?>
                    </span>

                    <span class="nombre-ejercicio">
                        <?= htmlspecialchars($ejercicio['titulo'], ENT_QUOTES, 'UTF-8') ?>
                    </span>

                    <span class="descripcion-tarjeta">
                        <?= htmlspecialchars($ejercicio['descripcion'], ENT_QUOTES, 'UTF-8') ?>
                    </span>
                </span>

                <span class="flecha-tarjeta" aria-hidden="true">↗</span>
            </a>

        <?php endforeach; ?>

    </section>

</main>

<?php require_once __DIR__ . '/../src/views/footer.php'; ?>