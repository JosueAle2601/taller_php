<?php

require_once __DIR__ . '/../src/autoload.php';

$tituloPagina = 'Cálculo Estadístico';

$entrada = '';
$resultado = null;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $entrada = trim($_POST['numeros'] ?? '');

    if ($entrada === '') {
        $error = 'Ingresa al menos un número.';
    } else {
        $partes = preg_split('/[\s,]+/', $entrada, -1, PREG_SPLIT_NO_EMPTY);

        $numeros = [];

        foreach ($partes as $parte) {
            // Enteros, decimales y notación científica.
            $patron = '/^[+-]?(?:\d+(?:\.\d*)?|\.\d+)(?:[eE][+-]?\d+)?$/';

            if (!preg_match($patron, $parte)) {
                $error = "El valor «{$parte}» no es un número válido.";
                break;
            }

            $numero = (float) $parte;

            if (!is_finite($numero)) {
                $error = 'Todos los valores deben ser números finitos.';
                break;
            }

            $numeros[] = $numero;
        }

        if ($error === '' && count($numeros) > 1000) {
            $error = 'Puedes ingresar un máximo de 1000 números.';
        }

        if ($error === '' && count($numeros) > 0) {
            try {
                $estadistica = new Estadistica();
                $resultado = $estadistica->calcular($numeros);
            } catch (InvalidArgumentException $e) {
                $error = $e->getMessage();
            }
        }
    }
}

require_once __DIR__ . '/../src/views/header.php';

?>

<main class="pagina-principal">

    <section class="seccion-ejercicio">

        <div class="encabezado-ejercicio">
            <span class="etiqueta">EJERCICIO 3</span>

            <h1>Cálculo Estadístico</h1>

            <p>
                Ingresa una serie de números reales para calcular
                el promedio, la mediana y la moda.
            </p>
        </div>

        <form
			method="POST"
        	action="estadistica.php"
            class="formulario-ejercicio">

            <label for="numeros">Números que deseas analizar</label>

            <textarea
                id="numeros"
                name="numeros"
                rows="4"
                placeholder="Ej. 2.5, 3, 3, 4.75, 8"
                maxlength="20000"
                required>
            <?= htmlspecialchars($entrada, ENT_QUOTES, 'UTF-8') ?></textarea>

            <p class="ayuda-formulario">
                Separa los valores con comas o espacios.
                Utiliza punto para los decimales.
            </p>

            <button type="submit" class="boton-ejercicio">
                Calcular estadísticas
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
                    <span>Cantidad de números</span>
                    <strong><?= $resultado['cantidad'] ?></strong>
                </div>

                <div class="estadistica-dato">
                    <span>Suma total</span>
                    <strong><?= htmlspecialchars((string)
						$resultado['suma'], ENT_QUOTES, 'UTF-8') ?>
					</strong>
                </div>

                <div class="estadistica-dato">
                    <span>Promedio</span>
                    <strong><?= round($resultado['promedio'], 6) ?></strong>
                </div>

                <div class="estadistica-dato">
                    <span>Mediana</span>
                    <strong><?= round($resultado['mediana'], 6) ?></strong>
                </div>

                <div class="estadistica-dato">
                    <span>Moda</span>

                    <strong>
                        <?php if (count($resultado['modas']) > 0): ?>

                            <?= htmlspecialchars(implode(', ',
                                    array_map(
                                        fn($valor) => (string) $valor,
                                        $resultado['modas'])
                                ), ENT_QUOTES, 'UTF-8') ?>

                        <?php else: ?>
                            No hay moda
                        <?php endif; ?>
                    </strong>
                </div>

                <?php if (count($resultado['modas']) > 0): ?>
                    <p class="frase-original">
                        Frecuencia de cada moda:
                        <?= $resultado['frecuencia_maxima'] ?>
                        apariciones.
                    </p>
                <?php endif; ?>

            </section>

        <?php endif; ?>

        <a href="home.php" class="enlace-regreso">
            ← Volver al menú principal
        </a>

    </section>

</main>

<?php require_once __DIR__ . '/../src/views/footer.php'; ?>