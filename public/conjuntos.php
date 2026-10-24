<?php

require_once __DIR__ . '/../src/autoload.php';

$tituloPagina = 'Operaciones entre Conjuntos';

$entradaA = '';
$entradaB = '';
$resultado = null;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entradaA = trim($_POST['conjuntoA'] ?? '');
    $entradaB = trim($_POST['conjuntoB'] ?? '');

    if ($entradaA === '' || $entradaB === '') {
        $error = 'Debes ingresar los elementos de ambos conjuntos.';
    } else {
        $conjuntosEntrada = [
            'A' => $entradaA,
            'B' => $entradaB
        ];

        $conjuntos = [];
        $patron = '/^[+-]?\d+$/';

        foreach ($conjuntosEntrada as $nombre => $entrada) {
            $partes = preg_split(
                '/[\s,]+/',
                $entrada,
                -1,
                PREG_SPLIT_NO_EMPTY
            );

            $numeros = [];

            foreach ($partes as $parte) {
                if (!preg_match($patron, $parte)) {
                    $error = "El valor «{$parte}» del conjunto {$nombre} no es un entero válido.";
                    break;
                }

                $numero = filter_var($parte, FILTER_VALIDATE_INT);

                if ($numero === false) {
                    $error = "El valor «{$parte}» del conjunto {$nombre} está fuera del rango permitido.";
                    break;
                }

                $numeros[] = $numero;
            }

            if ($error !== '') {
                break;
            }

            if (count($numeros) > 1000) {
                $error = "El conjunto {$nombre} no puede tener más de 1000 elementos.";
                break;
            }

            $conjuntos[$nombre] = $numeros;
        }

        if ($error === '') {
            $operaciones = new Conjuntos();

            $resultado = $operaciones->calcular(
                $conjuntos['A'],
                $conjuntos['B']
            );
        }
    }
}

require_once __DIR__ . '/../src/views/header.php';

?>

<main class="pagina-principal">

    <section class="seccion-ejercicio">

        <div class="encabezado-ejercicio">
            <span class="etiqueta">EJERCICIO 4</span>

            <h1>Operaciones entre Conjuntos</h1>

            <p>
                Ingresa dos conjuntos de números enteros para calcular
                su unión, intersección y diferencias.
            </p>
        </div>

        <form
            method="POST"
            action="conjuntos.php"
            class="formulario-ejercicio">

            <label for="conjuntoA">Elementos del conjunto A</label>

            <textarea
                id="conjuntoA"
                name="conjuntoA"
                rows="3"
                placeholder="Ej. 1, 2, 3, 4"
                maxlength="20000"
                required><?= htmlspecialchars($entradaA, ENT_QUOTES,'UTF-8') ?></textarea>
            <label for="conjuntoB">Elementos del conjunto B</label>

            <textarea
                id="conjuntoB"
                name="conjuntoB"
                rows="3"
                placeholder="Ej. 3, 4, 5, 6"
                maxlength="20000"
                required><?= htmlspecialchars($entradaB, ENT_QUOTES, 'UTF-8') ?></textarea>

            <p class="ayuda-formulario">
                Separa los números con comas o espacios.
                Se permiten enteros negativos y positivos.
                Los elementos repetidos se eliminan automáticamente.
            </p>

            <button type="submit" class="boton-ejercicio">
                Calcular operaciones
            </button>

        </form>

        <?php if ($error !== ''): ?>

            <div class="mensaje-error" role="alert">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>

        <?php endif; ?>

        <?php if ($resultado !== null): ?>

            <section class="resultado-ejercicio" aria-live="polite">

                <h2>Resultados</h2>

                <div class="estadistica-dato">
                    <span>Conjunto A</span>
                    <strong>
                        <?= htmlspecialchars('{' . implode(', ',
							$resultado['conjuntoA']) . '}', ENT_QUOTES, 'UTF-8') ?>
                    </strong>
                </div>

                <div class="estadistica-dato">
                    <span>Conjunto B</span>
                    <strong>
                        <?= htmlspecialchars('{' . implode(', ',
							$resultado['conjuntoB']) . '}', ENT_QUOTES, 'UTF-8') ?>
                    </strong>
                </div>

                <div class="estadistica-dato">
                    <span>Unión (A &#8746; B)</span>
                    <strong>
                        <?= htmlspecialchars('{' . implode(', ',
							$resultado['union']) . '}', ENT_QUOTES, 'UTF-8') ?>
                    </strong>
                </div>

                <div class="estadistica-dato">
                    <span>Intersección (A &#8745; B)</span>
                    <strong>
                        <?= htmlspecialchars('{' . implode(', ',
						$resultado['interseccion']) . '}', ENT_QUOTES, 'UTF-8') ?>
                    </strong>
                </div>

                <div class="estadistica-dato">
                    <span>Diferencia (A - B)</span>
                    <strong>
                        <?= htmlspecialchars('{' . implode(', ',
						$resultado['diferenciaAB']) . '}', ENT_QUOTES, 'UTF-8') ?>
                    </strong>
                </div>

                <div class="estadistica-dato">
                    <span>Diferencia (B - A)</span>
                    <strong>
                        <?= htmlspecialchars('{' . implode(', ',
							$resultado['diferenciaBA']) . '}', ENT_QUOTES, 'UTF-8') ?>
                    </strong>
                </div>

            </section>

        <?php endif; ?>

        <a href="home.php" class="enlace-regreso">
            ← Volver al menú principal
        </a>

    </section>

</main>

<?php require_once __DIR__ . '/../src/views/footer.php'; ?>
