<?php
$apiKey = '';
$apiSecret = '';


if (isset($_GET['marca_id'])) {


    $url = 'https://carapi.app/api/models/v2?make_id=' . $_GET['marca_id'];


} else {


    $url = 'https://carapi.app/api/makes/v2';


}


$ch = curl_init();


curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Accept: application/json',
        'api-key: ' . $apiKey,
        'api-secret: ' . $apiSecret
    ]
]);


$respuesta = curl_exec($ch);


$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);


curl_close($ch);


if ($httpCode !== 200) {
    die("Error en la petición: Código " . $httpCode);
}


$datos = json_decode($respuesta, true);


if (isset($_GET['marca_id'])) {


    header('Content-Type: application/json');


    echo json_encode($datos['data'] ?? []);


    exit;
}


$marcas = $datos['data'] ?? [];


?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Urbanaut | Datos del Auto</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <link
        rel="stylesheet"
        href="/assets/css/style.css">
    <link
        rel="stylesheet"
        href="/assets/css/auto.css">
</head>

<body>

<main class="contenedor">

<section class="card-urbanaut">

    <!-- LOGO -->

    <div class="logo">

        <img
            src="../imagenes/logoNuevo2.png"
            alt="Logo Urbanaut">

    </div>


    <!-- TÍTULO -->

    <div class="titulo">

        <i class="bi bi-car-front-fill"></i>

        <h1>
            Tus Autos
        </h1>

    </div>


    <p class="subtitulo">

        ¿Cuántos vehículos querés ingresar?

    </p>


    <!-- ELEGIR CANTIDAD -->

    <div class="selector-autos">

        <!-- 1 AUTO -->

        <button
            type="button"
            class="opcion-auto"
            id="unAuto"
            onclick="seleccionarAutos(1)">

            <i class="bi bi-car-front-fill"></i>

            <strong>
                1 auto
            </strong>

            <span>
                Ingresar un vehículo
            </span>

        </button>


        <!-- 2 AUTOS -->

        <button
            type="button"
            class="opcion-auto"
            id="dosAutos"
            onclick="seleccionarAutos(2)">

            <i class="bi bi-car-front-fill"></i>

            <strong>
                2 autos
            </strong>

            <span>
                Ingresar dos vehículos
            </span>

        </button>

    </div>


    <!-- FORMULARIO -->

    <form id="formularioAuto">

        <div id="datosAutos">

            <!-- AUTO 1 -->

            <div class="auto">

                <div class="auto-titulo">

                    <i class="bi bi-1-circle-fill"></i>

                    Auto 1

                </div>


                <div class="campo">

                    <i class="bi bi-car-front"></i>

                    <select id="marca1" name="marca1" required>
                       <option value="" selected disabled>Seleccione la marca del vehículo</option>
                        <?php foreach ($marcas as $marca): ?>
                         <option value="<?= htmlspecialchars($marca['id']) ?>">
                        <?= htmlspecialchars($marca['name']) ?>
                       </option>
                        <?php endforeach; ?>

                    </select>

                </div>


                <div class="campo">

                    <i class="bi bi-car-front"></i>

                     <select id="modelo1" name="modelo1" required>
                        <option value="" selected disabled> Seleccione primero una marca </option>
                    </select>

                </div>


                <div class="campo">

                    <i class="bi bi-palette"></i>

                    <select
                        id="color1"
                        name="color1"
                        required>

                        <option
                            value=""
                            selected
                            disabled>

                            Seleccioná el color

                        </option>

                        <option value="Negro">Negro</option>
                        <option value="Blanco">Blanco</option>
                        <option value="Gris">Gris</option>
                        <option value="Azul">Azul</option>
                        <option value="Rojo">Rojo</option>
                        <option value="Verde">Verde</option>
                        <option value="Amarillo">Amarillo</option>
                        <option value="Naranja">Naranja</option>
                        <option value="Otro">Otro</option>

                    </select>

                </div>

            </div>


            <!-- AUTO 2 -->

            <div
                class="auto"
                id="auto2"
                style="display: none;">

                <div class="auto-titulo">

                    <i class="bi bi-2-circle-fill"></i>

                    Auto 2

                </div>


                <div class="campo">

                    <i class="bi bi-car-front"></i>

                    <select id="marca2" name="marca2" required>
                        <option value="" selected disabled>Seleccione la marca del vehículo</option>

                        <?php foreach ($marcas as $marca): ?>
                            <option value="<?= htmlspecialchars($marca['id']) ?>">
                                <?= htmlspecialchars($marca['name']) ?>
                            </option>
                        <?php endforeach; ?>

                    </select>

                </div>


                <div class="campo">

                    <i class="bi bi-car-front"></i>

                    <select id="modelo2" name="modelo2" required>
                        <option value="" selected disabled> Seleccione primero una marca </option>
                    
                    </select>
                </div>


                <div class="campo">

                    <i class="bi bi-palette"></i>

                    <select
                        id="color2"
                        name="color2">

                        <option
                            value=""
                            selected
                            disabled>

                            Seleccioná el color

                        </option>

                        <option value="Negro">Negro</option>
                        <option value="Blanco">Blanco</option>
                        <option value="Gris">Gris</option>
                        <option value="Azul">Azul</option>
                        <option value="Rojo">Rojo</option>
                        <option value="Verde">Verde</option>
                        <option value="Amarillo">Amarillo</option>
                        <option value="Naranja">Naranja</option>
                        <option value="Otro">Otro</option>

                    </select>

                </div>

            </div>


            <!-- BOTÓN -->

            <button
                type="submit"
                class="btn-continuar">

                Crear Cuenta

                <span class="flecha">
                    ✓
                </span>

            </button>

        </div>

    </form>


    <div class="info">

        <i class="bi bi-shield-check"></i>

        Podés registrar hasta dos vehículos.

    </div>

</section>

</main>


<script>

let cantidadAutos = 0;


function seleccionarAutos(cantidad) {

    cantidadAutos = cantidad;

    const datosAutos =
        document.getElementById("datosAutos");

    const auto2 =
        document.getElementById("auto2");

    const unAuto =
        document.getElementById("unAuto");

    const dosAutos =
        document.getElementById("dosAutos");


    /* MOSTRAR FORMULARIO */

    datosAutos.style.display =
        "block";


    /* QUITAR SELECCIÓN ANTERIOR */

    unAuto.classList.remove(
        "activa"
    );

    dosAutos.classList.remove(
        "activa"
    );


    /* SI ELIGE 1 AUTO */

    if (cantidad === 1) {

        unAuto.classList.add(
            "activa"
        );

        auto2.style.display =
            "none";

        document
            .getElementById("marca2")
            .required = false;

        document
            .getElementById("modelo2")
            .required = false;

        document
            .getElementById("color2")
            .required = false;

    }


    /* SI ELIGE 2 AUTOS */

    else {

        dosAutos.classList.add(
            "activa"
        );

        auto2.style.display =
            "block";

        document
            .getElementById("marca2")
            .required = true;

        document
            .getElementById("modelo2")
            .required = true;

        document
            .getElementById("color2")
            .required = true;

    }


    /* BAJAR SUAVEMENTE AL FORMULARIO */

    setTimeout(function () {

        datosAutos.scrollIntoView({
            behavior: "smooth",
            block: "nearest"
        });

    }, 100);

}


/* CUANDO TOCA CREAR CUENTA */

document
    .getElementById("formularioAuto")
    .addEventListener(
        "submit",
        function(event) {

            event.preventDefault();


            if (cantidadAutos === 0) {

                alert(
                    "Seleccioná primero cuántos autos querés ingresar."
                );

                return;
            }


            /* =====================================
               IR AL INICIO
               ===================================== */

            window.location.href =
                "../inicio/index.html";

        }
    );

    
    /*TRAER MODELOS SEGÚN MARCA SELECCIONADA*/
    document.getElementById("marca1").addEventListener("change", function() {
    cargarModelos(this.value, "modelo1");
    });


    document.getElementById("marca2").addEventListener("change", function() {
    cargarModelos(this.value, "modelo2");
    });

    function cargarModelos(marcaId, modeloId) {
    const selectModelo = document.getElementById(modeloId);
    selectModelo.innerHTML = '<option value="" selected disabled>Cargando modelos...</option>';
    fetch("?marca_id=" + marcaId)
        .then(response => response.json())
        .then(modelos => {
            selectModelo.innerHTML = '<option value="" selected disabled>Seleccione el modelo</option>';

            modelos.forEach(modelo => {
                const option = document.createElement("option");
                option.value = modelo.id;
                option.textContent = modelo.name;
                selectModelo.appendChild(option);
            });

        })
        .catch(error => {
            console.error(error);
            selectModelo.innerHTML = '<option value="" selected disabled>Error al cargar modelos</option>';
        });
}


</script>

</body>

</html>

