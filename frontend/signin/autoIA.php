<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Marcas y Modelos</title>
</head>
<body>

<?php
$apiKey    = ''; // Tu clave
$apiSecret = ''; // Tu secreto

// 1. Obtener lista de marcas
$urlMarcas = 'https://carapi.app/api/makes/v2';
$ch = curl_init($urlMarcas);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => [
        'Accept: application/json',
        'api-key: ' . $apiKey,
        'api-secret: ' . $apiSecret,
    ],
]);
$respuestaMarcas = curl_exec($ch);
$httpCode        = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($httpCode !== 200) {
    die("Error al cargar marcas: Código $httpCode");
}

$datosMarcas = json_decode($respuestaMarcas, true);
$marcas      = $datosMarcas['data'] ?? [];
curl_close($ch);

// 2. Si se envió el formulario, cargar modelos
$modelos = [];
$marcaElegida = $_POST['marca_id'] ?? '';

if (!empty($marcaElegida)) {
    $urlModelos = "https://carapi.app/api/models/v2?make_id=" . urlencode($marcaElegida);

    $ch2 = curl_init($urlModelos);
    curl_setopt_array($ch2, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => [
            'Accept: application/json',
            'api-key: ' . $apiKey,
            'api-secret: ' . $apiSecret,
        ],
    ]);

    $respuestaModelos = curl_exec($ch2);
    $httpCode2        = curl_getinfo($ch2, CURLINFO_HTTP_CODE);

    if ($httpCode2 === 200) {
        $datosModelos = json_decode($respuestaModelos, true);
        $modelos      = $datosModelos['data'] ?? [];
    } else {
        echo "<p style='color:red'>Error al cargar modelos: Código $httpCode2</p>";
    }
    curl_close($ch2);
}
?>

<form method="post" action="">
    <h2>Seleccione la Marca</h2>
    <select name="marca_id" id="marca" onchange="this.form.submit()">
        <option value="">Seleccione una marca</option>
        <?php foreach ($marcas as $m): ?>
            <option value="<?= $m['id'] ?>"
                <?= ($marcaElegida == $m['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($m['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>

<h2>Seleccione el Modelo</h2>
<select name="modelo" id="modelo">
    <option value="">Primero elija una marca</option>
    <?php if (!empty($modelos)): ?>
        <?php foreach ($modelos as $mod): ?>
            <option value="<?= $mod['id'] ?>">
                <?= htmlspecialchars($mod['name']) ?>
            </option>
        <?php endforeach; ?>
    <?php elseif ($marcaElegida): ?>
        <option value="">No se encontraron modelos</option>
    <?php endif; ?>
</select>

</body>
</html>