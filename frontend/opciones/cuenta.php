<?php
$apiKey = ''; 
$apiSecret = ''; 

if (isset($_GET['marca_id'])) {
    $url = 'https://carapi.app/api/models/v2?make_id=' . urlencode($_GET['marca_id']);
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
    
    header('Content-Type: application/json');
    if ($httpCode !== 200) {
        http_response_code($httpCode);
        echo json_encode([]);
        exit;
    }
    $datos = json_decode($respuesta, true);
    echo json_encode($datos['data'] ?? []);
    exit; 
}

$urlMarcas = 'https://carapi.app/api/makes/v2';
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $urlMarcas,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Accept: application/json',
        'api-key: ' . $apiKey,
        'api-secret: ' . $apiSecret
    ]
]);
$respuestaMarcas = curl_exec($ch);
$httpCodeMarcas = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$marcas = [];
if ($httpCodeMarcas === 200) {
    $datosMarcas = json_decode($respuestaMarcas, true);
    $marcas = $datosMarcas['data'] ?? [];
}

$backend = rtrim(getenv('BACKEND_HOST') ?: '', '/');
$port    = getenv('BACKEND_PORT') ?: '';
$url     = 'http://' . $backend . ':' . $port;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Urbanaut | Mi Cuenta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            width: 100%;
            min-height: 100%;
        }

        body {
            min-height: 100vh;
            font-family: "Segoe UI", Arial, sans-serif;
            background: url("../imagenes/imagen2.jpg") center center / cover no-repeat fixed;
            overflow: hidden;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background: linear-gradient(135deg, rgba(0, 180, 255, 0.15), rgba(0, 255, 170, 0.08));
            backdrop-filter: blur(2px);
            z-index: -1;
        }

        .contenedor {
            width: 100%;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 8px;
        }

        .card-urbanaut {
            width: min(92vw, 850px);
            max-height: calc(100vh - 16px);
            overflow-y: auto;
            padding: 16px 22px;
            border-radius: 25px;
            background: linear-gradient(135deg, rgba(255,255,255,0.74), rgba(230,250,255,0.48));
            border: 1px solid rgba(255,255,255,0.85);
            box-shadow: 0 18px 50px rgba(0,40,70,0.28);
            backdrop-filter: blur(22px);
        }

        .titulo {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-bottom: 2px;
        }

        .titulo i {
            color: #238bff;
            font-size: 28px;
        }

        h1 {
            color: #123d69;
            font-size: 28px;
            font-weight: 750;
            margin: 0;
        }

        .subtitulo {
            text-align: center;
            color: #58738e;
            font-size: 13px;
            margin-bottom: 11px;
        }

        .contenido {
            display: grid;
            grid-template-columns: 0.8fr 1.2fr;
            gap: 12px;
            margin-bottom: 12px;
            align-items: start;
        }

        .bloque {
            background: rgba(255,255,255,0.55);
            border: 1px solid rgba(255,255,255,0.8);
            border-radius: 17px;
            padding: 12px;
            box-shadow: 0 6px 16px rgba(0,40,70,0.09);
            margin-bottom: 12px;
        }

        .bloque-titulo {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #123d69;
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .bloque-titulo i {
            color: #238bff;
            font-size: 19px;
        }

        .perfil {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .avatar {
            width: 82px;
            height: 82px;
            border-radius: 50%;
            background: #bde9ff;
            border: 4px solid rgba(255,255,255,0.85);
            box-shadow: 0 7px 17px rgba(0,60,100,0.18);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 46px;
            margin-bottom: 5px;
            cursor: pointer;
            transition: 0.25s;
        }

        .avatar:hover {
            transform: scale(1.04);
        }

        .cambiar-foto {
            border: none;
            background: transparent;
            color: #238bff;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
        }

        .colores {
            display: flex;
            justify-content: center;
            gap: 6px;
            margin-top: 5px;
        }

        .color {
            width: 17px;
            height: 17px;
            border-radius: 50%;
            border: 2px solid white;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0,0,0,0.15);
        }

        .azul { background: #9bddff; }
        .rosa { background: #ffb6d9; }
        .verde { background: #a9e6c1; }
        .violeta { background: #cbb5ff; }
        .amarillo { background: #ffe59a; }

        .eliminar {
            width: 100%;
            margin-top: 12px;
            border: none;
            border-radius: 9px;
            padding: 7px;
            background: #dc3545;
            color: white;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.25s;
        }

        .eliminar:hover {
            background: #b02a37;
            transform: translateY(-1px);
        }

        .dato {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 5px;
            padding: 6px 8px;
            border-radius: 9px;
            background: rgba(255,255,255,0.45);
        }

        .dato i {
            color: #238bff;
            font-size: 15px;
        }

        .dato span {
            color: #58738e;
            font-size: 10px;
            display: block;
        }

        .dato strong {
            display: block;
            color: #123d69;
            font-size: 12px;
        }

        .editar {
            width: 100%;
            border: none;
            border-radius: 9px;
            padding: 7px;
            background: #238bff;
            color: white;
            font-size: 11px;
            font-weight: 700;
            margin-top: 1px;
            cursor: pointer;
            transition: 0.25s;
        }

        .editar:hover {
            background: #126dcc;
            transform: translateY(-1px);
        }

        .auto {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            align-items: center;
        }

        .auto-info {
            display: flex;
            flex-direction: column;
            gap: 6px;
            width: 100%;
        }

        .auto-dato {
            background: rgba(255,255,255,0.45);
            padding: 8px 12px;
            border-radius: 9px;
            color: #58738e;
            font-size: 12px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .auto-dato strong {
            color: #123d69;
            display: block;
            font-size: 15px;
            margin-bottom: 2px;
        }

        .auto-visual {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 60px;
        }

        .auto-icono {
            font-size: 80px;
            color: #238bff;
            filter: drop-shadow(0 5px 5px rgba(0,60,100,0.18));
            transition: 0.25s;
        }

        .colores-auto {
            display: flex;
            justify-content: center;
            gap: 7px;
            margin-top: 4px;
        }

        .color-auto {
            width: 19px;
            height: 19px;
            border-radius: 50%;
            border: 2px solid white;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0,0,0,0.15);
            transition: 0.2s;
        }

        .color-auto:hover {
            transform: scale(1.12);
        }

        .auto-azul { background: #238bff; }
        .auto-rojo { background: #e85d75; }
        .auto-verde { background: #43b883; }
        .auto-negro { background: #263238; }
        .auto-blanco { background: #ffffff; }

        .texto-color {
            text-align: center;
            color: #58738e;
            font-size: 10px;
            margin-top: 15px;
            margin-bottom: 4px;
        }

        .editar-auto {
            width: 100%;
            border: none;
            border-radius: 9px;
            padding: 7px;
            margin-top: 8px;
            background: #238bff;
            color: white;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.25s;
        }

        .editar-auto:hover {
            background: #126dcc;
            transform: translateY(-1px);
        }

        .botones {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }

        .volver {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 8px 18px;
            border-radius: 11px;
            background: #1b3237;
            color: white;
            text-decoration: none;
            font-weight: 700;
            font-size: 12px;
            transition: 0.25s;
        }

        .volver:hover {
            background: #238bff;
            color: white;
            transform: translateY(-2px);
        }

        .modal-contenido {
            border-radius: 20px;
            border: none;
            background: rgba(245,252,255,0.96);
            backdrop-filter: blur(15px);
        }

        .modal-titulo {
            color: #123d69;
            font-weight: 750;
        }

        .form-label {
            color: #123d69;
            font-size: 13px;
            font-weight: 700;
        }

        .form-control {
            border-radius: 11px;
            font-size: 13px;
        }

        .guardar {
            border: none;
            background: #238bff;
            color: white;
            border-radius: 10px;
            padding: 8px 17px;
            font-weight: 700;
        }

        .modal-eliminar {
            border-radius: 20px;
            border: none;
            background: rgba(255,248,248,0.97);
            backdrop-filter: blur(15px);
        }

        .titulo-eliminar {
            color: #b02a37;
            font-weight: 750;
        }

        .icono-eliminar {
            color: #dc3545;
            font-size: 45px;
            margin-bottom: 10px;
        }

        .texto-eliminar {
            color: #58738e;
            font-size: 14px;
            text-align: center;
        }

        .btn-confirmar-eliminar {
            border: none;
            background: #dc3545;
            color: white;
            border-radius: 10px;
            padding: 8px 17px;
            font-weight: 700;
            transition: 0.2s;
        }

        .btn-confirmar-eliminar:hover {
            background: #b02a37;
        }

        .texto-vacio {
            color: #58738e;
            font-size: 12px;
            text-align: center;
            margin: 8px 0;
        }

        @media (max-width: 650px) {
            body {
                overflow: auto;
            }
            .contenedor {
                min-height: 100vh;
                height: auto;
                align-items: flex-start;
                padding: 7px;
            }
            .card-urbanaut {
                width: 100%;
                max-height: none;
                margin-top: 4px;
                padding: 15px 13px;
            }
            .contenido {
                grid-template-columns: 1fr;
            }
            .auto {
                grid-template-columns: 1fr;
            }
            .auto-visual {
                min-height: 65px;
            }
            .auto-icono {
                font-size: 55px;
            }
        }
    </style>
</head>
<body>

    <main class="contenedor">
        <section class="card-urbanaut">
            <div class="titulo">
                <i class="bi bi-person-circle"></i>
                <h1>Mi Cuenta</h1>
            </div>
            <p class="subtitulo">Administrá tus datos y tu vehículo.</p>

            <div class="contenido">
                <div class="bloque perfil">
                    <div class="bloque-titulo">
                        <i class="bi bi-person"></i> Mi perfil
                    </div>
                    <div class="avatar" id="avatar" onclick="cambiarAvatar()">🐵</div>
                    <button class="cambiar-foto" onclick="cambiarAvatar()">Cambiar foto de perfil</button>
                    <div class="colores">
                        <span class="color azul" onclick="cambiarFondo('#9bddff')"></span>
                        <span class="color rosa" onclick="cambiarFondo('#ffb6d9')"></span>
                        <span class="color verde" onclick="cambiarFondo('#a9e6c1')"></span>
                        <span class="color violeta" onclick="cambiarFondo('#cbb5ff')"></span>
                        <span class="color amarillo" onclick="cambiarFondo('#ffe59a')"></span>
                    </div>
                    <button type="button" class="eliminar" data-bs-toggle="modal" data-bs-target="#modalEliminar">
                        <i class="bi bi-trash3-fill"></i> Eliminar cuenta
                    </button>
                </div>

                <div class="bloque">
                    <div class="bloque-titulo">
                        <i class="bi bi-person-vcard"></i> Mis datos
                    </div>
                    <div class="dato">
                        <i class="bi bi-person-fill"></i>
                        <div>
                            <span>Nombre</span>
                            <strong id="nombre">Cargando...</strong>
                        </div>
                    </div>
                    <div class="dato">
                        <i class="bi bi-person-fill"></i>
                        <div>
                            <span>Apellido</span>
                            <strong id="apellido">Cargando...</strong>
                        </div>
                    </div>
                    <div class="dato">
                        <i class="bi bi-envelope-fill"></i>
                        <div>
                            <span>Correo electrónico</span>
                            <strong id="correo">Cargando...</strong>
                        </div>
                    </div>
                    <button class="editar" data-bs-toggle="modal" data-bs-target="#modalEditar">
                        <i class="bi bi-pencil"></i> Editar mis datos
                    </button>
                </div>
            </div>

            <div class="bloque">
                <div class="bloque-titulo">
                    <i class="bi bi-calendar-week"></i> Mis horarios
                </div>
                <div id="lista-horarios">
                    <p class="texto-vacio">Cargando horarios…</p>
                </div>
	        <button class="editar" data-bs-toggle="modal" data-bs-target="#modalEditarHorarios" style="margin-top: 10px;">
        <i class="bi bi-pencil"></i> Editar mis horarios
    </button>
            </div>

            <div class="bloque">
                <div class="bloque-titulo">
                    <i class="bi bi-car-front-fill"></i> Mi auto
                </div>
                <div class="auto">
                    <div class="auto-info">
                        <div class="auto-dato">
                            <strong>Marca</strong>
                            <span id="auto-marca">Cargando…</span>
                        </div>
                        <div class="auto-dato">
                            <strong>Modelo</strong>
                            <span id="auto-modelo">Cargando…</span>
                        </div>
                        <div class="auto-dato">
                            <strong>Color</strong>
                            <span id="auto-color">Cargando…</span>
                        </div>
                        <div class="auto-dato">
                            <strong>Tamaño</strong>
                            <span id="auto-tamano">Cargando…</span>
                        </div>
                    </div>
                    <div class="auto-visual">
                        <i class="bi bi-car-front-fill auto-icono" id="autoIcono"></i>
                    </div>
                </div>
                <div class="texto-color">Cambiar color del auto (solo visual)</div>
                <div class="colores-auto">
                    <span class="color-auto auto-azul" onclick="cambiarAuto('#238bff', 'Azul')"></span>
                    <span class="color-auto auto-rojo" onclick="cambiarAuto('#e85d75', 'Rojo')"></span>
                    <span class="color-auto auto-verde" onclick="cambiarAuto('#43b883', 'Verde')"></span>
                    <span class="color-auto auto-negro" onclick="cambiarAuto('#263238', 'Negro')"></span>
                    <span class="color-auto auto-blanco" onclick="cambiarAuto('#ffffff', 'Blanco')"></span>
                </div>
                <button type="button" class="editar-auto" data-bs-toggle="modal" data-bs-target="#modalEditarAuto">
                    <i class="bi bi-pencil"></i> Editar mi auto
                </button>
            </div>

            <div class="botones">
                <a href="../inicio/index.html" class="volver">
                    <i class="bi bi-house"></i> Volver a Inicio
                </a>
            </div>
        </section>
    </main>

    <div class="modal fade" id="modalEditar" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-contenido">
                <div class="modal-header">
                    <h5 class="modal-titulo">Editar mis datos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" id="editarNombre" class="form-control" value="">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Apellido</label>
                        <input type="text" id="editarApellido" class="form-control" value="">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Correo electrónico</label>
                        <input type="email" id="editarCorreo" class="form-control" value="">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="guardar" onclick="guardarDatos()">Guardar cambios</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditarAuto" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-contenido">
                <div class="modal-header">
                    <h5 class="modal-titulo">Editar mi auto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Marca</label>
                        <select id="editarMarca" class="form-control" required>
                            <option value="" selected disabled>Seleccione la marca</option>
                            <?php foreach ($marcas as $m): ?>
                                <option value="<?= htmlspecialchars($m['name']) ?>" data-id="<?= htmlspecialchars($m['id']) ?>">
                                    <?= htmlspecialchars($m['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Modelo</label>
                        <select id="editarModelo" class="form-control" required>
                            <option value="" selected disabled>Seleccione primero una marca</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tamaño</label>
                        <select id="editarTamano" class="form-control">
                            <option value="chico">Chico</option>
                            <option value="mediano">Mediano</option>
                            <option value="grande">Grande</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Color</label>
                        <select id="editarColor" class="form-control">
                            <option value="Azul">Azul</option>
                            <option value="Rojo">Rojo</option>
                            <option value="Verde">Verde</option>
                            <option value="Negro">Negro</option>
                            <option value="Blanco">Blanco</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="guardar" onclick="guardarAuto()">Guardar cambios</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEliminar" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-eliminar">
                <div class="modal-header">
                    <h5 class="modal-title titulo-eliminar">Eliminar cuenta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="bi bi-exclamation-triangle-fill icono-eliminar"></i>
                    <p class="texto-eliminar">¿Seguro que querés borrar tu cuenta en Urbanaut?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn-confirmar-eliminar" onclick="eliminarCuenta()">
                        <i class="bi bi-trash3-fill"></i> Eliminar cuenta
                    </button>
                </div>
            </div>
        </div>
    </div>

<div class="modal fade" id="modalEditarHorarios" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content modal-contenido">
            <div class="modal-header">
                <h5 class="modal-titulo">Editar mis horarios</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="cuerpo-modal-horarios" style="max-height: 60vh; overflow-y: auto;">
                <p class="texto-vacio">Cargando catálogo de horarios...</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="guardar" onclick="guardarHorarios()">Guardar cambios</button>
            </div>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <script type="module">
        import UrbanautClient, { HttpError } from '/assets/js/urbanaut-js-client/src/urbanaut.js';

        const api = new UrbanautClient({
            baseUrl: '<?= $url . "/api" ?>'
        });

        if (!api.isAuthenticated) {
            window.location.href = '../login/login.php';
        }

        async function cargarPerfil() {
            try {
                const u = await api.usuario.getUser();
                document.getElementById('nombre').textContent = u?.nombre || '—';
                document.getElementById('apellido').textContent = u?.apellido || '—';
                document.getElementById('correo').textContent = u?.email  || '—';
                
                document.getElementById('editarNombre').value = u?.nombre || '';
                document.getElementById('editarApellido').value = u?.apellido || '';
                document.getElementById('editarCorreo').value = u?.email  || '';
            } catch (error) {
                if (error instanceof HttpError && error.status === 401) {
                    window.location.href = '../login/login.php';
                    return;
                }
                console.error('usuario.getUser', error);
            }
        }
        cargarPerfil();

        async function cargarVehiculo() {
            try {
                const lista = await api.vehiculo.obtener();
                const auto = lista?.[0];

                if (!auto) {
                    document.getElementById('auto-marca').textContent  = 'Sin vehículo';
                    document.getElementById('auto-modelo').textContent = '—';
                    document.getElementById('auto-color').textContent  = '—';
                    document.getElementById('auto-tamano').textContent = '—';
                    return;
                }

                document.getElementById('auto-marca').textContent  = auto.marca  || '—';
                document.getElementById('auto-modelo').textContent = auto.modelo || '—';
                document.getElementById('auto-color').textContent  = auto.color  || '—';
                document.getElementById('auto-tamano').textContent = auto.tamano || '—';

                const colores = { 
                    Azul: '#238bff', 
                    Rojo: '#e85d75', 
                    Verde: '#43b883', 
                    Negro: '#263238', 
                    Blanco: '#ffffff', 
                    Gris: '#9e9e9e', 
                    Amarillo: '#ffe59a', 
                    Naranja: '#ff9f43', 
                    Otro: '#238bff' 
                };
                document.getElementById('autoIcono').style.color = colores[auto.color] || '#238bff';

                window.__autoActual = auto;

                try {
                    document.getElementById('editarMarca').value = auto.marca || '';
                    
                    if (auto.marca) {
                        const selectMarca = document.getElementById('editarMarca');
                        const event = new Event('change');
                        selectMarca.dispatchEvent(event);
                        
                        setTimeout(() => {
                            document.getElementById('editarModelo').value = auto.modelo || '';
                        }, 800);
                    }

                    document.getElementById('editarTamano').value = auto.tamano || 'chico';
                    document.getElementById('editarColor').value = auto.color || 'Azul';
                } catch (e) {
                    console.warn("No se pudo prellenar el modal", e);
                }

            } catch (error) {
                if (error instanceof HttpError && error.status === 401) {
                    window.location.href = '../login/login.php';
                    return;
                }
                console.error('vehiculo.obtener', error);
            }
        }
        cargarVehiculo();

        document.getElementById('editarMarca').addEventListener('change', async function() {
            const selectModelo = document.getElementById('editarModelo');
            selectModelo.innerHTML = '<option value="" selected disabled>Cargando modelos...</option>';
            
            const opcionElegida = this.options[this.selectedIndex];
            const marcaId = opcionElegida.getAttribute('data-id');

            if (!marcaId) {
                selectModelo.innerHTML = '<option value="" selected disabled>Seleccione primero una marca</option>';
                return;
            }

            try {
                const res = await fetch(window.location.pathname + '?marca_id=' + marcaId);
                const modelos = await res.json();
                
                selectModelo.innerHTML = '<option value="" selected disabled>Seleccione el modelo</option>';
                modelos.forEach(m => {
                    const opt = document.createElement('option');
                    opt.value = m.name;
                    opt.textContent = m.name;
                    selectModelo.appendChild(opt);
                });
            } catch (e) {
                console.error("Error cargando modelos", e);
                selectModelo.innerHTML = '<option value="" selected disabled>Error al cargar modelos</option>';
            }
        });

        async function cargarHorarios() {
            const contenedor = document.getElementById('lista-horarios');
            try {
                const horarios = await api.tiene.obtener();
                const catalogo = await api.tiene.obtenertodos();

                if (!horarios || horarios.length === 0) {
                    contenedor.innerHTML = '<p class="texto-vacio">No tenés horarios cargados.</p>';
                    return;
                }

                const porDia = {};
                for (const h of horarios) {
                    const dia = h.dia_semana;
                    porDia[dia] = porDia[dia] || [];
                    const info = catalogo.find(c => c.numero_hora === h.numero_hora && c.dia_semana === dia);
                    porDia[dia].push(info?.horario || `Hora ${h.numero_hora}`);
                }

                let html = '';
                for (const [dia, horas] of Object.entries(porDia)) {
                    html += `<div class="dato"><i class="bi bi-calendar-day"></i><div><span>${dia}</span><strong>${horas.join(' · ')}</strong></div></div>`;
                }
                contenedor.innerHTML = html;
            } catch (error) {
                if (error instanceof HttpError && error.status === 401) {
                    window.location.href = '../login/login.php';
                    return;
                }
                contenedor.innerHTML = '<p class="texto-vacio">No se pudieron cargar los horarios.</p>';
            }
        }
        cargarHorarios();

        window.cambiarFondo = (color) => { 
            document.getElementById('avatar').style.backgroundColor = color; 
        };
        
        window.cambiarAvatar = () => {
            const avatar = document.getElementById('avatar');
            const avatares = ['🐵', '🙈', '🙉', '🙊'];
            let i = avatares.indexOf(avatar.textContent.trim());
            avatar.textContent = avatares[(i + 1) % avatares.length];
        };
        
        window.cambiarAuto = (color, nombre) => {
            document.getElementById('autoIcono').style.color = color;
            document.getElementById('auto-color').textContent = nombre;
        };

        window.guardarDatos = async function () {
            const nombre = document.getElementById('editarNombre').value.trim();
            const apellido = document.getElementById('editarApellido').value.trim();
            const correo = document.getElementById('editarCorreo').value.trim();

            if (!nombre || !correo) {
                alert('Completá el nombre y el correo.');
                return;
            }

            try {
                await api.usuario.modificar({ nombre: nombre, apellido: apellido, email: correo });

                document.getElementById('nombre').textContent = nombre;
                document.getElementById('apellido').textContent = apellido;
                document.getElementById('correo').textContent = correo;

                const modalElement = document.getElementById('modalEditar');
                const modalInstance = bootstrap.Modal.getInstance(modalElement);
                if (modalInstance) {
                    modalInstance.hide();
                }
                
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
                
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) {
                    backdrop.remove();
                }

                alert('¡Datos actualizados con éxito!');
            } catch (error) {
                console.error('Error al modificar usuario', error);
                alert('No se pudieron guardar los datos. Intentá de nuevo.');
            }
        };

        window.guardarAuto = async function () {
            if (!window.__autoActual) {
                alert('No tenés un vehículo registrado para editar.');
                return;
            }

            const marca = document.getElementById('editarMarca').value;
            const modelo = document.getElementById('editarModelo').value;
            const tamano = document.getElementById('editarTamano').value;
            const color = document.getElementById('editarColor').value;

            if (!marca || !modelo) {
                alert('Completá la marca y el modelo.');
                return;
            }

            try {
                await api.vehiculo.modificar({
                    id: window.__autoActual.id,
                    marca: marca,
                    modelo: modelo,
                    color: color,
                    tamano: tamano
                });

                document.getElementById('auto-marca').textContent = marca;
                document.getElementById('auto-modelo').textContent = modelo;
                document.getElementById('auto-color').textContent = color;
                document.getElementById('auto-tamano').textContent = tamano;

                const modalElement = document.getElementById('modalEditarAuto');
                const modalInstance = bootstrap.Modal.getInstance(modalElement);
                if (modalInstance) {
                    modalInstance.hide();
                }
                
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
                
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) {
                    backdrop.remove();
                }

                alert('¡Auto actualizado con éxito!');
            } catch (error) {
                console.error('Error al modificar auto', error);
                alert('No se pudo guardar el auto. Intentá de nuevo.');
            }
        };

        async function prepararModalHorarios() {
            const contenedorModal = document.getElementById('cuerpo-modal-horarios');
            contenedorModal.innerHTML = '<p class="texto-vacio">Cargando...</p>';
            
            try {
                const catalogo = await api.tiene.obtenertodos();
                const mios = await api.tiene.obtener();
                const misIds = mios.map(m => `${m.dia_semana}-${m.numero_hora}`);

                const porDia = {};
                catalogo.forEach(c => {
                    if (!porDia[c.dia_semana]) porDia[c.dia_semana] = [];
                    porDia[c.dia_semana].push(c);
                });

                let html = '<div class="row">';
                for (const [dia, items] of Object.entries(porDia)) {
                    html += `<div class="col-md-6 mb-3">
                        <div class="bloque" style="margin-bottom:0; padding: 8px;">
                            <div style="font-weight: bold; margin-bottom: 5px; text-transform: capitalize; color: #123d69;">${dia}</div>
                            <div style="display: flex; flex-direction: column; gap: 4px;">`;
                    
                    items.forEach(item => {
                        const checked = misIds.includes(`${item.dia_semana}-${item.numero_hora}`) ? 'checked' : '';
                        html += `
                            <label style="font-size: 12px; display: flex; align-items: center; gap: 6px; cursor: pointer;">
                                <input type="checkbox" class="check-horario" 
                                    data-dia="${item.dia_semana}" 
                                    data-hora="${item.numero_hora}" 
                                    ${checked}>
                                ${item.horario}
                            </label>`;
                    });
                    html += `</div></div></div>`;
                }
                html += '</div>';
                contenedorModal.innerHTML = html;
            } catch (error) {
                console.error(error);
                contenedorModal.innerHTML = '<p class="texto-vacio">Error al cargar los horarios.</p>';
            }
        }

        document.getElementById('modalEditarHorarios').addEventListener('show.bs.modal', prepararModalHorarios);

        window.guardarHorarios = async function () {
            const checkboxes = document.querySelectorAll('.check-horario');
            const botonGuardar = document.querySelector('#modalEditarHorarios .guardar');
            botonGuardar.disabled = true;
            botonGuardar.textContent = 'Guardando...';

            try {
                const mios = await api.tiene.obtener();
                const misIds = mios.map(m => `${m.dia_semana}-${m.numero_hora}`);
                const seleccionados = [];

                for (const cb of checkboxes) {
                    const dia = cb.dataset.dia;
                    const hora = parseInt(cb.dataset.hora);
                    const id = `${dia}-${hora}`;
                    
                    if (cb.checked) {
                        seleccionados.push(id);
                        if (!misIds.includes(id)) {
                            await api.tiene.asignar({ numero_hora: hora, dia_semana: dia });
                        }
                    }
                }

                for (const idActual of misIds) {
                    if (!seleccionados.includes(idActual)) {
                        const [dia, hora] = idActual.split('-');
                        await api.tiene.desasignar({ numero_hora: parseInt(hora), dia_semana: dia });
                    }
                }

                bootstrap.Modal.getInstance(document.getElementById('modalEditarHorarios')).hide();
                cargarHorarios(); 
                
                document.body.classList.remove('modal-open');
                document.querySelector('.modal-backdrop')?.remove();
                
            } catch (error) {
                console.error('Error al guardar horarios:', error);
                alert('Hubo un error al guardar los horarios.');
            } finally {
                botonGuardar.disabled = false;
                botonGuardar.textContent = 'Guardar cambios';
            }
        };

        window.eliminarCuenta = () => {
            alert('Eliminar cuenta no disponible aún.');
        };
    </script>
</body>
</html>
