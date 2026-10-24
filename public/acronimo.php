<?php

require_once __DIR__ . '/../src/autoload.php';

$tituloPagina = 'Generador de Acrónimos';

$resultado = '';
$frase = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $frase = trim($_POST['frase'] ?? '');

    if ($frase === '') {
        $error = 'Por favor, escribe una frase.';
    } else {
        $generador = new Acronimo();
        $resultado = $generador->generar($frase);

        if ($resultado === '') {
            $error = 'No se encontraron palabras válidas en la frase.';
        }
    }
}

require_once __DIR__ . '/../src/views/header.php';

?>

<main class="pagina-principal">

    <section class="seccion-ejercicio">

        <div class="encabezado-ejercicio">
            <span class="etiqueta">EJERCICIO 1</span>

            <h1>Generador de Acrónimos</h1>

            <p>
                Escribe una frase y obtén un acrónimo formado
                por la primera letra de cada palabra.
            </p>
        </div>

        <form method="POST" action="acronimo.php" class="formulario-ejercicio">

            <label for="frase">Frase que deseas convertir</label>

            <input
                type="text"
                id="frase"
                name="frase"
                placeholder="Ej. PHP Hypertext Preprocessor"
                value="<?= htmlspecialchars($frase, ENT_QUOTES, 'UTF-8') ?>"
                maxlength="500"
                required>

            <button type="submit" class="boton-ejercicio">
                Generar acrónimo
            </button>

        </form>

        <?php if ($error !== ''): ?>

            <div class="mensaje-error" role="alert">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>

        <?php endif; ?>

        <?php if ($resultado !== ''): ?>

            <section class="resultado-ejercicio" aria-live="polite">

                <h2>Resultado</h2>

                <p class="acronimo-resultado">
                    <?= htmlspecialchars($resultado, ENT_QUOTES, 'UTF-8') ?>
                </p>

                <p class="frase-original">
                    Frase original:
                    <?= htmlspecialchars($frase, ENT_QUOTES, 'UTF-8') ?>
                </p>

            </section>

        <?php endif; ?>

        <a href="home.php" class="enlace-regreso">
            ← Volver al menú principal
        </a>

    </section>

</main>

<?php require_once __DIR__ . '/../src/views/footer.php'; ?>
