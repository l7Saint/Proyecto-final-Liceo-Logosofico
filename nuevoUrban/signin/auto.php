<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: url("../imagenes/imagen2.jpg") center center / cover no-repeat fixed;
            font-family: 'Lucida Sans', 'Lucida Sans Regular',
                         'Lucida Grande', 'Lucida Sans Unicode',
                         Geneva, Verdana, sans-serif;

            display: flex;
            justify-content: center;
            align-items: center;

            min-height: 100vh;
        }


        .contenedorPrincipal {
            width: 40%;
            min-width: 400px;
            height: 600px;

            display: flex;
            justify-content: center;
            align-items: center;

            background: rgba(82, 124, 184, .45);
            backdrop-filter: blur(12px);

            border-radius: 20px;
            border: 1px solid rgba(164, 231, 150, .699);

            box-shadow: 0 0 20px rgba(0, 0, 0, .3);

            overflow: hidden;
        }


        .contenido {
            width: 100%;
            height: 100%;

            padding: 30px;

            color: white;

            display: flex;
            justify-content: center;
            align-items: center;
        }


        form {
            width: 100%;

            display: flex;
            justify-content: center;
        }

        #cajaDatosUsuario {
            width: 80%;
            text-align: center;
        }
        .titulo {
            font-family: 'Lucida Sans', 'Lucida Sans Regular',
                         'Lucida Grande', 'Lucida Sans Unicode',
                         Geneva, Verdana, sans-serif;

            font-weight: bold;
            margin-bottom: 25px;
        }

        .subtitulo {
            text-align: center;

            font-size: 16px;

            margin-bottom: 30px;

            color: #d9f7ff;
        }
        .form-group {
            width: 100%;
        }

        .datos {
            width: 70%;
            height: 40px;

            margin: 10px auto;

            border: none;
            border-radius: 10px;

            background: #77acd8;

             color: rgb(8, 82, 92);

            cursor: text;
            transition: .2s;

            text-align: center;
        }

        .datos:focus {
            background: #8bc2e8;
            box-shadow: 0 0 8px rgba(166, 241, 185, .7);
        }

        input::placeholder {
            color: rgb(8, 82, 92);
            opacity: 1;
        }

        /* BOTÓN */

        .btn-menu {
            width: 50%;
            height: 40px;

            border: none;
            border-radius: 10px;

            background: #1e7489;
            color: rgb(172, 234, 206);

            cursor: pointer;
            transition: .2s;

            margin: 20px auto;

            display: block;
        }

        .btn-menu:hover {
            background: #58b57d;
            transform: scale(1.03);
        }


        .texto {
            font-family: 'Lucida Sans', 'Lucida Sans Regular',
                         'Lucida Grande', 'Lucida Sans Unicode',
                         Geneva, Verdana, sans-serif;
        }


        @media (max-width: 800px) {

            .contenedorPrincipal {
                width: 90%;
                min-width: 0;
            }

            #cajaDatosUsuario {
                width: 100%;
            }

            .datos {
                width: 85%;
            }

            .btn-menu {
                width: 65%;
            }
        }

    </style>

</head>

<body>

<?php
// credenciales
$apiKey = '';
$apiSecret = '';

$url = 'https://carapi.app/api/makes/v2';
$url = 'https://carapi.app/api/exterior-colors/v2'




curl_setopt_array($ch, [
CURLOPT_URL => $url,
CURLOPT_RETURNTRANSFER => true, // Para recibir la respuesta en una variable
CURLOPT_HTTPHEADER => [
'Accept: application/json',
'api-key: ' . $apiKey,
'api-secret: ' . $apiSecret
]
]);

$respuesta = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($httpCode !== 200) {
die("Error en la petición: Código " . $httpCode);
}

// Decodificamos el JSON a un array de PHP
$datos = json_decode($respuesta, true);
$marcas = $datos['data'] ?? [];
$colores = $datos


?>

<div class="contenedorPrincipal">
        <div class="contenido">
            <form action="auto.php" method="POST">
                <div id="cajaDatosUsuario">
                    <h1 class="titulo">Ingresa a tu Auto</h1>
                     <p class="subtitulo">Aquí podes poner los datos de tu auto.</p>
                    <div id="cajaCamposUsuario">
                      <br>
                        <select id="marcaAuto" name="marcaAuto" class="datos">
                            <option value="">Seleccioná una marca</option>

                            <?php foreach ($marcas as $marca): ?>
                                <option value="<?= htmlspecialchars($marca['name']) ?>">
                                    <?= htmlspecialchars($marca['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <select id="color" name="color" class="datos">
                            <option value="">Seleccioná un color</option>

                            <?php foreach ($colores as $color): ?>
                                <option value="<?= htmlspecialchars($color['name']) ?>">
                                    <?= htmlspecialchars($color['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                    
                     
                        <button type="submit" class="btn-menu">Crear Cuenta</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js">





    </script>

</body>

</html>