<?php
// Procesamiento antes de la salida HTML
$resultado = null;
$mensaje_error = '';
$valor1 = '';
$valor2 = '';
$operacion = 'sumar';

// Usamos filter_input para leer y validar datos POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['calcular'])) {
    // Leer entradas crudas para poder reinyectar en el formulario
    $valor1_raw = filter_input(INPUT_POST, 'number_1', FILTER_UNSAFE_RAW);
    $valor2_raw = filter_input(INPUT_POST, 'number_2', FILTER_UNSAFE_RAW);
    $operacion_raw = filter_input(INPUT_POST, 'operaciones', FILTER_UNSAFE_RAW);

    // Guardar para reusar en el formulario (evitar XSS al reinsertar)
    $valor1 = htmlspecialchars($valor1_raw ?? '');
    $valor2 = htmlspecialchars($valor2_raw ?? '');
    $operacion = in_array($operacion_raw, ['sumar','restar','multiplicar','dividir']) ? $operacion_raw : 'sumar';

    // Validar que sean números (aceptamos comas o puntos — normalizamos coma a punto)
    $v1_norm = str_replace(',', '.', $valor1_raw);
    $v2_norm = str_replace(',', '.', $valor2_raw);

    if ($v1_norm === '' || $v2_norm === '') {
        $mensaje_error = 'Rellena ambos valores.';
    } elseif (!is_numeric($v1_norm) || !is_numeric($v2_norm)) {
        $mensaje_error = 'Los valores deben ser numéricos.';
    } else {
        $n1 = floatval($v1_norm);
        $n2 = floatval($v2_norm);

        switch ($operacion) {
            case 'sumar':
                $resultado = $n1 + $n2;
                break;
            case 'restar':
                $resultado = $n1 - $n2;
                break;
            case 'multiplicar':
                $resultado = $n1 * $n2;
                break;
            case 'dividir':
                if ($n2 == 0.0) {
                    $mensaje_error = 'Error: No se puede dividir entre 0.';
                } else {
                    $resultado = $n1 / $n2;
                }
                break;
            default:
                $mensaje_error = 'Operación no válida.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Calculadora PHP Nativa</title>
    <link rel="stylesheet" href="calculadora.css" />
  </head>
  <body>
    <h1>Calculadora en Entorno Nativo (PHP)</h1>
    <div id="valores_calculadora">
      <fieldset>
        <form action="" method="post" novalidate>
          <label for="number_1">Valor 1: </label>
          <input
            type="number"
            name="number_1"
            id="number_1"
            placeholder="Ingrese el primer valor..."
            required
            step="any"
            value="<?php echo $valor1; ?>"
          />
          <br /><br />

          <label for="number_2">Valor 2: </label>
          <input
            type="number"
            name="number_2"
            id="number_2"
            placeholder="Ingrese el segundo valor..."
            required
            step="any"
            value="<?php echo $valor2; ?>"
          />
          <br /><br />

          <label for="operaciones">Operación</label>
          <select id="operaciones" name="operaciones">
            <option value="sumar" <?php if ($operacion === 'sumar') echo 'selected'; ?>>Sumar</option>
            <option value="restar" <?php if ($operacion === 'restar') echo 'selected'; ?>>Restar</option>
            <option value="multiplicar" <?php if ($operacion === 'multiplicar') echo 'selected'; ?>>Multiplicar</option>
            <option value="dividir" <?php if ($operacion === 'dividir') echo 'selected'; ?>>Dividir</option>
          </select>
          <br /><br />

          <input type="submit" name="calcular" value="Calcular" />
          <br />
        </form>

        <?php if ($mensaje_error): ?>
          <p style="color:red;"><?php echo htmlspecialchars($mensaje_error); ?></p>
        <?php elseif ($resultado !== null): ?>
          <h3>Resultado: <?php echo htmlspecialchars((string)$resultado); ?></h3>
        <?php endif; ?>
      </fieldset>

      <img
        src="https://github.com/edumel20/dpl_eduardo/blob/main/ut2/a1/images/calculadora.png?raw=true"
        alt="calculadora"
      />
    </div>
  </body>
</html>

