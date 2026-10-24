<?php

require_once __DIR__ . '/../src/autoload.php';

$tituloPagina = 'Constructor de Árbol Binario';

$entradas = [
    'preorden' => '',
    'inorden' => '',
    'postorden' => ''
];

$resultado = null;
$error = '';

function separarRecorrido(string $entrada): ?array
{
    $entrada = trim($entrada);

    if ($entrada === '') {
        return null;
    }

    $elementos = preg_split(
        '/[\s,;→]+/u',
        $entrada,
        -1,
        PREG_SPLIT_NO_EMPTY
    );

    return $elementos === false ? null : array_values($elementos);
}

function mostrarNodo(?array $nodo): string
{
    if ($nodo === null) {
        return '';
    }

    $html = '<li><span class="nodo-arbol">'
        . htmlspecialchars($nodo['valor'], ENT_QUOTES, 'UTF-8')
        . '</span>';

    if (
        $nodo['izquierda'] !== null ||
        $nodo['derecha'] !== null
    ) {
        $html .= '<ul>';

        if ($nodo['izquierda'] !== null) {
            $html .= mostrarNodo($nodo['izquierda']);
        } else {
            $html .= '<li class="nodo-vacio"></li>';
        }

        if ($nodo['derecha'] !== null) {
            $html .= mostrarNodo($nodo['derecha']);
        } else {
            $html .= '<li class="nodo-vacio"></li>';
        }

        $html .= '</ul>';
    }

    $html .= '</li>';

    return $html;
}														

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($entradas as $nombre => $valor) {
        $entradas[$nombre] = trim($_POST[$nombre] ?? '');
    }

    try {
        $preorden = separarRecorrido($entradas['preorden']);
        $inorden = separarRecorrido($entradas['inorden']);
        $postorden = separarRecorrido($entradas['postorden']);

        $arbol = new ArbolBinario();

        $resultado = $arbol->construir(
            $preorden,
            $inorden,
            $postorden
        );
    } catch (InvalidArgumentException $e) {
        $error = $e->getMessage();
    } catch (Throwable $e) {
        $error = 'Ocurrió un error al procesar los recorridos.';
    }
}

require_once __DIR__ . '/../src/views/header.php';

?>

<main class="pagina-principal">
    <section class="seccion-ejercicio">

        <div class="encabezado-ejercicio">
            <span class="etiqueta">EJERCICIO 6</span>

            <h1>Constructor de Árbol Binario</h1>

            <p>
                Ingresa al menos dos recorridos para reconstruir
                un árbol binario y comprobar su estructura.
            </p>
        </div>

        <form
            method="POST"
            action="arbol-binario.php"
            class="formulario-ejercicio">

            <label for="preorden">Recorrido en preorden</label>
            <input
                type="text"
                id="preorden"
                name="preorden"
                placeholder="Ej. A B D E C"
                value="<?= htmlspecialchars($entradas['preorden'], ENT_QUOTES, 'UTF-8') ?>">
            <label for="inorden">Recorrido en inorden</label>
            <input
                type="text"
                id="inorden"
                name="inorden"
                placeholder="Ej. D B E A C"
                value="<?= htmlspecialchars($entradas['inorden'], ENT_QUOTES, 'UTF-8') ?>">

            <label for="postorden">Recorrido en postorden</label>
            <input
                type="text"
                id="postorden"
                name="postorden"
                placeholder="Ej. D E B C A"
                value="<?= htmlspecialchars($entradas['postorden'], ENT_QUOTES,'UTF-8') ?>">
            <p class="ayuda-formulario">
                Separa los nodos con espacios o comas.
                Puedes dejar vacío uno de los tres campos.
            </p>

            <button type="submit" class="boton-ejercicio">
                Construir árbol
            </button>
        </form>

        <?php if ($error !== ''): ?>
            <div class="mensaje-error" role="alert">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <?php if ($resultado !== null): ?>
            <section class="resultado-ejercicio" aria-live="polite">
                <h2>Árbol construido</h2>

                <p class="ayuda-formulario">
                    Cada círculo representa un nodo del árbol.
                </p>

                <div class="arbol-visual">
                    <ul>
                        <?= mostrarNodo($resultado['raiz']) ?>
                    </ul>
                </div>

                <h2>Recorridos obtenidos</h2>

                <p>
                    <strong>Preorden:</strong>
                    <?= htmlspecialchars(implode(' → ',
						$resultado['preorden']), ENT_QUOTES, 'UTF-8') ?>
                </p>

                <p>
                    <strong>Inorden:</strong>
                    <?= htmlspecialchars(implode(' → ',
						$resultado['inorden']), ENT_QUOTES,'UTF-8') ?>
                </p>

                <p>
                    <strong>Postorden:</strong>
                    <?= htmlspecialchars(implode(' → ',
						$resultado['postorden']),ENT_QUOTES,'UTF-8') ?>
                </p>
            </section>
        <?php endif; ?>

        <a href="home.php" class="enlace-regreso">
            ← Volver al menú principal
        </a>

    </section>
</main>

<?php require_once __DIR__ . '/../src/views/footer.php'; ?>
