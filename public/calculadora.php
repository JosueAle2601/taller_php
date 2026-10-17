
<?php

session_start();

require_once __DIR__ . '/../src/autoload.php';

$tituloPagina = 'Calculadora';

if (!isset($_SESSION['historial_calculadora'])) {
    $_SESSION['historial_calculadora'] = [];
}

if (!isset($_SESSION['estado_calculadora'])) {
    $_SESSION['estado_calculadora'] = [
        'pantalla' => '0',
        'guardado' => null,
        'operador' => null,
        'esperando' => false,
        'expresion' => '',
        'error' => ''
    ];
}

$estado = &$_SESSION['estado_calculadora'];
$historial = &$_SESSION['historial_calculadora'];
$estado['error'] = '';

$simbolos = [
    'suma' => '+',
    'resta' => '−',
    'multiplicacion' => '×',
    'division' => '÷',
    'porcentaje' => '%'
];

$nombres = [
    'suma' => 'Suma',
    'resta' => 'Resta',
    'multiplicacion' => 'Multiplicación',
    'division' => 'División',
    'porcentaje' => 'Porcentaje'
];

$formatear = static function (float $numero): string {
    if (!is_finite($numero)) {
        return '0';
    }

    if ($numero == 0.0) {
        return '0';
    }

    return rtrim(
        rtrim(number_format($numero, 10, '.', ''), '0'),
        '.'
    );
};

$calcular = static function (
    float $a,
    float $b,
    string $operador
) use ($simbolos): float {
    $calculadora = new Calculadora();
    $resultado = $calculadora->calcular($a, $b, $operador);

    if (!is_finite($resultado)) {
        throw new InvalidArgumentException(
            'El resultado está fuera del rango permitido.'
        );
    }

    return $resultado;
};

$reiniciarEstado = static function () use (&$estado): void {
    $estado = [
        'pantalla' => '0',
        'guardado' => null,
        'operador' => null,
        'esperando' => false,
        'expresion' => '',
        'error' => ''
    ];
};

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    try {
        if ($accion === 'borrar_historial') {
            $historial = [];
        } elseif ($accion === 'limpiar') {
            $reiniciarEstado();
        } elseif ($accion === 'retroceso') {
            if (!$estado['esperando']) {
                $pantalla = $estado['pantalla'];

                if (strlen($pantalla) <= 1 ||
                    (strlen($pantalla) === 2 && $pantalla[0] === '-')) {
                    $estado['pantalla'] = '0';
                } else {
                    $estado['pantalla'] = substr($pantalla, 0, -1);
                }
            }
        } elseif ($accion === 'decimal') {
            if ($estado['esperando']) {
                $estado['pantalla'] = '0.';
                $estado['esperando'] = false;
            } elseif (strpos($estado['pantalla'], '.') === false) {
                $estado['pantalla'] .= '.';
            }
        } elseif ($accion === 'signo') {
            if ((float) $estado['pantalla'] !== 0.0) {
                $estado['pantalla'] = str_starts_with(
                    $estado['pantalla'],
                    '-'
                )
                    ? substr($estado['pantalla'], 1)
                    : '-' . $estado['pantalla'];
            }
        } elseif ($accion === 'porcentaje') {
            $actual = (float) $estado['pantalla'];

            if (
                $estado['operador'] !== null &&
                $estado['guardado'] !== null
            ) {
                $actual = $calcular(
                    $actual,
                    (float) $estado['guardado'],
                    'porcentaje'
                );
            } else {
                $actual /= 100;
            }

            $estado['pantalla'] = $formatear($actual);
            $estado['esperando'] = false;
        } elseif (strpos($accion, 'digito:') === 0) {
            $digito = substr($accion, 7);

            if (preg_match('/^[0-9]$/', $digito)) {
                if ($estado['esperando']) {
                    $estado['pantalla'] = $digito;
                    $estado['esperando'] = false;
                } elseif (
                    $estado['pantalla'] === '0'
                ) {
                    $estado['pantalla'] = $digito;
                } elseif ($estado['pantalla'] === '-0') {
                    $estado['pantalla'] = '-' . $digito;
                } elseif (strlen($estado['pantalla']) < 24) {
                    $estado['pantalla'] .= $digito;
                }
            }
        } elseif (in_array(
            $accion,
            ['suma', 'resta', 'multiplicacion', 'division'],
            true
        )) {
            $actual = (float) $estado['pantalla'];

            if (
                $estado['operador'] !== null &&
                $estado['guardado'] !== null &&
                !$estado['esperando']
            ) {
                $actual = $calcular(
                    (float) $estado['guardado'],
                    $actual,
                    $estado['operador']
                );

                $estado['pantalla'] = $formatear($actual);
            }

            $estado['guardado'] = $actual;
            $estado['operador'] = $accion;
            $estado['expresion'] =
                $formatear($actual) . ' ' . $simbolos[$accion];
            $estado['esperando'] = true;
        } elseif ($accion === 'igual') {
            if (
                $estado['operador'] !== null &&
                $estado['guardado'] !== null &&
                !$estado['esperando']
            ) {
                $izquierdo = (float) $estado['guardado'];
                $derecho = (float) $estado['pantalla'];
                $operador = $estado['operador'];

                $resultado = $calcular(
                    $izquierdo,
                    $derecho,
                    $operador
                );

                $expresion = $formatear($izquierdo)
                    . ' ' . $simbolos[$operador]
                    . ' ' . $formatear($derecho);

                $registro = [
                    'expresion' => $expresion,
                    'operacion' => $nombres[$operador],
                    'resultado' => $formatear($resultado)
                ];

                array_unshift($historial, $registro);
                $historial = array_slice($historial, 0, 20);

                $estado['pantalla'] = $formatear($resultado);
                $estado['expresion'] = $expresion . ' =';
                $estado['guardado'] = null;
                $estado['operador'] = null;
                $estado['esperando'] = true;
            }
        }
    } catch (InvalidArgumentException $e) {
        $estado['error'] = $e->getMessage();
    }
}

$operacionPendiente = '';

if (
    $estado['operador'] !== null &&
    $estado['guardado'] !== null
) {
    $operacionPendiente = $formatear(
        (float) $estado['guardado']
    ) . ' ' . $simbolos[$estado['operador']];
}

require_once __DIR__ . '/../src/views/header.php';

?>

<main class="pagina-principal calculadora-contenedor">

    <section class="calculadora-ventana">

        <div class="calculadora-barra">
            <div class="calculadora-titulo">
                <span class="calculadora-icono">±</span>
                <span>Calculadora</span>
            </div>

            <a href="index.php" class="calculadora-volver">
                Volver al menú
            </a>
        </div>

        <div class="calculadora-cuerpo">

            <section class="calculadora-principal">

                <div class="calculadora-modo">
                    <span class="modo-icono">☰</span>
                    <h1>Estándar</h1>
                </div>

                <div class="calculadora-pantalla" aria-live="polite">

                    <p class="calculadora-expresion">
                        <?= htmlspecialchars($estado['expresion'],
                            ENT_QUOTES, 'UTF-8') ?>
                    </p>

                    <p class="calculadora-valor">
                        <?= htmlspecialchars($estado['pantalla'],
                            ENT_QUOTES, 'UTF-8') ?>
                    </p>

                </div>

                <?php if ($estado['error'] !== ''): ?>
                    <p class="calculadora-error" role="alert">
                        <?= htmlspecialchars($estado['error'],
                            ENT_QUOTES, 'UTF-8') ?>
                    </p>
                <?php endif; ?>

                <form
                    method="POST"
                    action="calculadora.php"
                    class="calculadora-teclado">

                    <button type="submit" name="accion"
                        value="porcentaje" class="tecla tecla-funcion">%</button>

                    <button type="submit" name="accion"
                        value="limpiar" class="tecla tecla-funcion">CE</button>

                    <button type="submit" name="accion"
                        value="limpiar" class="tecla tecla-funcion">C</button>

                    <button type="submit" name="accion"
                        value="retroceso" class="tecla tecla-funcion">⌫</button>

                    <button type="submit" name="accion"
                        value="digito:7" class="tecla">7</button>

                    <button type="submit" name="accion"
                        value="digito:8" class="tecla">8</button>

                    <button type="submit" name="accion"
                        value="digito:9" class="tecla">9</button>

                    <button type="submit" name="accion"
                        value="division" class="tecla tecla-operador">÷</button>

                    <button type="submit" name="accion"
                        value="digito:4" class="tecla">4</button>

                    <button type="submit" name="accion"
                        value="digito:5" class="tecla">5</button>

                    <button type="submit" name="accion"
                        value="digito:6" class="tecla">6</button>

                    <button type="submit" name="accion"
                        value="multiplicacion" class="tecla tecla-operador">×</button>

                    <button type="submit" name="accion"
                        value="digito:1" class="tecla">1</button>

                    <button type="submit" name="accion"
                        value="digito:2" class="tecla">2</button>

                    <button type="submit" name="accion"
                        value="digito:3" class="tecla">3</button>

                    <button type="submit" name="accion"
                        value="resta" class="tecla tecla-operador">−</button>

                    <button type="submit" name="accion"
                        value="signo" class="tecla">+/−</button>

                    <button type="submit" name="accion"
                        value="digito:0" class="tecla">0</button>

                    <button type="submit" name="accion"
                        value="decimal" class="tecla">.</button>

					<button type="submit" name="accion"
						value="suma" class="tecla tecla-operador tecla-suma">+</button>

                    <button type="submit" name="accion"
                        value="igual" class="tecla tecla-igual">=</button>


                </form>

                <p class="calculadora-ayuda">
                    Introduce los números utilizando el teclado de la pantalla.
                </p>

            </section>

            <aside class="calculadora-historial">

                <div class="historial-encabezado">
                    <h2>Historial</h2>
                    <span><?= count($historial) ?>/20</span>
                </div>

                <?php if (empty($historial)): ?>

                    <div class="historial-vacio">
                        <span class="historial-vacio-icono">↶</span>
                        <p>Aún no hay operaciones.</p>
                        <small>
                            Tus cálculos aparecerán aquí.
                        </small>
                    </div>

                <?php else: ?>

                    <ol class="historial-lista">
                        <?php foreach ($historial as $registro): ?>
                            <li class="historial-item">
                                <span>
                                    <?= htmlspecialchars($registro['operacion'],
										ENT_QUOTES, 'UTF-8') ?>
                                </span>

                                <p>
                                    <?= htmlspecialchars($registro['expresion'],
                                        ENT_QUOTES, 'UTF-8') ?>
                                </p>

                                <strong>
                                    <?= htmlspecialchars($registro['resultado'],
                                        ENT_QUOTES, 'UTF-8') ?>
                                </strong>
                            </li>
                        <?php endforeach; ?>
                    </ol>

                    <form method="POST" action="calculadora.php">
                        <button
                            type="submit"
                            name="accion"
                            value="borrar_historial"
                            class="boton-borrar-historial">
                            Borrar historial
                        </button>
                    </form>

                <?php endif; ?>

            </aside>

        </div>

    </section>

</main>

<?php require_once __DIR__ . '/../src/views/footer.php'; ?>
