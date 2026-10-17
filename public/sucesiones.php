<?php

require_once __DIR__ . '/../src/autoload.php';

$tituloPagina = 'Fibonacci y Factorial';

$numero = '';
$operacion = 'fibonacci';
$resultado = null;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $numero = trim($_POST['numero'] ?? '');
    $operacion = $_POST['operacion'] ?? 'fibonacci';

    if ($numero === '' || !ctype_digit($numero)) {
        $error = 'Ingresa un número entero no negativo.';
    } elseif (
        filter_var($numero, FILTER_VALIDATE_INT) === false
    ) {
        $error = 'El número ingresado está fuera del rango permitido.';
    } elseif (!in_array($operacion, ['fibonacci', 'factorial'], true)) {
        $error = 'Selecciona una operación válida.';
    } else {

        $numeroEntero = (int) $numero;
        $sucesiones = new Sucesiones();

        try {
            if ($operacion === 'fibonacci') {
                $resultado = $sucesiones->fibonacci($numeroEntero);
            } else {
                $resultado = $sucesiones->factorial($numeroEntero);
            }
        } catch (InvalidArgumentException | OverflowException $e) {
            $error = $e->getMessage();
        }
    }
}

require_once __DIR__ . '/../src/views/header.php';

?>

<main class="pagina-principal">

    <section class="seccion-ejercicio">

        <div class="encabezado-ejercicio">
            <span class="etiqueta">EJERCICIO 2</span>

            <h1>Fibonacci y Factorial</h1>

            <p>
                Selecciona una operación e introduce un número
                entero no negativo para calcular el resultado.
            </p>
        </div>

        <form
            method="POST"
            action="sucesiones.php"
            class="formulario-ejercicio">

            <label for="numero">Número entero</label>

            <input
                type="number"
                id="numero"
                name="numero"
                min="0"
                step="1"
                placeholder="Ej. 7"
                value="<?= htmlspecialchars($numero, ENT_QUOTES, 'UTF-8') ?>"
                required>

            <label for="operacion">Operación</label>

            <select id="operacion" name="operacion" required>

                <option
                    value="fibonacci"
                    <?= $operacion === 'fibonacci' ? 'selected' : '' ?>>
                    Sucesión de Fibonacci
                </option>

                <option
                    value="factorial"
                    <?= $operacion === 'factorial' ? 'selected' : '' ?>>
                    Factorial de un número
                </option>

            </select>

            <button type="submit" class="boton-ejercicio">
                Calcular resultado
            </button>

        </form>

        <?php if ($error !== ''): ?>

            <div class="mensaje-error" role="alert">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>

        <?php endif; ?>

        <?php if ($resultado !== null): ?>

            <section class="resultado-ejercicio" aria-live="polite">

                <h2>
                    <?= $operacion === 'fibonacci'
                        ? 'Sucesión de Fibonacci'
                        : 'Resultado del factorial' ?>
                </h2>

                <?php if ($operacion === 'fibonacci'): ?>

                    <?php if (count($resultado) > 0): ?>

                        <p class="serie-resultado">
                            <?= htmlspecialchars(implode(' → ', $resultado), ENT_QUOTES, 'UTF-8') ?>
                        </p>

                    <?php else: ?>

                        <p>No se solicitaron términos de la sucesión.</p>

                    <?php endif; ?>

                    <p class="frase-original">
                        Cantidad de términos: <?= count($resultado) ?>
                    </p>

                <?php else: ?>

                    <p class="acronimo-resultado">
                        <?= htmlspecialchars((string) $resultado, ENT_QUOTES, 'UTF-8') ?>
                    </p>

                    <p class="frase-original">
                        <?= $numeroEntero ?>! =
                        <?= htmlspecialchars((string) $resultado, ENT_QUOTES, 'UTF-8') ?>
                    </p>

                <?php endif; ?>

            </section>

        <?php endif; ?>

        <a href="index.php" class="enlace-regreso">
            ← Volver al menú principal
        </a>

    </section>

</main>

<?php require_once __DIR__ . '/../src/views/footer.php'; ?>
