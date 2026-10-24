<?php

require_once __DIR__ . '/../src/autoload.php';

$tituloPagina = 'Conversor de Números Binarios';

$entrada = '';
$resultado = null;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entrada = trim($_POST['numero'] ?? '');

    if ($entrada === '') {
        $error = 'Por favor, ingresa un número entero.';
    } elseif (!preg_match('/^[+-]?\d+$/', $entrada)) {
        $error = 'El valor ingresado debe ser un número entero.';
    } else {
        $numero = filter_var($entrada, FILTER_VALIDATE_INT);

        if ($numero === false) {
            $error = 'El número ingresado está fuera del rango permitido.';
        } else {
            $conversor = new Binario();
            $resultado = $conversor->convertir($numero);
        }
    }
}

require_once __DIR__ . '/../src/views/header.php';

?>

<main class="pagina-principal">

    <section class="seccion-ejercicio">

        <div class="encabezado-ejercicio">
            <span class="etiqueta">EJERCICIO 5</span>

            <h1>Conversor de Números Binarios</h1>

            <p>
                Ingresa un número entero y obtén su representación
                en el sistema binario.
            </p>
        </div>

        <form
            method="POST"
            action="binario.php"
            class="formulario-ejercicio">

            <label for="numero">Número entero</label>

            <input
                type="number"
                id="numero"
                name="numero"
                step="1"
                placeholder="Ej. 13"
                value="<?= htmlspecialchars($entrada, ENT_QUOTES, 'UTF-8') ?>"
                required>

            <p class="ayuda-formulario">
                Puedes ingresar números enteros positivos,
                negativos o cero.
            </p>

            <button type="submit" class="boton-ejercicio">
                Convertir a binario
            </button>

        </form>

        <?php if ($error !== ''): ?>

            <div class="mensaje-error" role="alert">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>

        <?php endif; ?>

        <?php if ($resultado !== null): ?>

            <section class="resultado-ejercicio" aria-live="polite">

                <h2>Resultado de la conversión</h2>

                <p class="frase-original">
                    Número decimal:
                    <?= htmlspecialchars($entrada, ENT_QUOTES, 'UTF-8') ?>
                </p>

                <p class="acronimo-resultado">
                    <?= htmlspecialchars($resultado, ENT_QUOTES, 'UTF-8') ?>
                </p>

                <p class="frase-original">
                    Representación en base 2.
                </p>

            </section>

        <?php endif; ?>

        <a href="home.php" class="enlace-regreso">
            ← Volver al menú principal
        </a>

    </section>

</main>

<?php require_once __DIR__ . '/../src/views/footer.php'; ?>
