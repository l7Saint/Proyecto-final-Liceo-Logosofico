<?php
$backend = rtrim(getenv('BACKEND_HOST') ?: '', '/');
$port    = getenv('BACKEND_PORT') ?: '';
$url     = 'http://' . $backend . ':' . $port;
?>
<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Urbanaut | Iniciar Sesión</title>

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
        href="/assets/css/login.css">
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

            <h1>
                Iniciar Sesión
            </h1>

            <p class="subtitulo">
                Ingresá a tu cuenta de Urbanaut.
            </p>

            <!-- FORMULARIO -->

            <form
                id="formLogin"
                >

                <!-- MAIL -->

                <div class="campo">

                    <label for="email">
                        Correo electrónico
                    </label>

                    <div class="input-contenedor">

                        <i class="bi bi-envelope-fill"></i>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="Ingresá tu correo"
                            required>

                    </div>

                </div>

                <!-- CONTRASEÑA -->

                <div class="campo">

                    <label for="password">
                        Contraseña
                    </label>

                    <div class="input-contenedor">

                        <i class="bi bi-lock-fill"></i>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Ingresá tu contraseña"
                            required>

                    </div>

                </div>

                <!-- LOGIN -->

                <button
                    type="submit"
                    class="btn-login">
                    Iniciar sesión
                </button>

            </form>

            <!-- RECUPERAR CUENTA -->

            <a
                href="recuperar.php"
                class="recuperar">

                ¿Olvidaste tu contraseña?

            </a>

            <!-- ADMINISTRADOR -->

            <div class="admin-seccion">

            </div>

            <!-- VOLVER -->

            <a
                href="../introduccion/index.html"
                class="volver">

                <i class="bi bi-arrow-left"></i>

                Volver

            </a>

        </section>

    </main>

<!--
    Loads the Urbanaut JS client. The `/urbanaut-js-client/` folder must be reachable
    from the frontend web root (Apache serves from `frontend/`). If the client lives
    elsewhere in your deployment, adjust the import URL below.
-->
<script type="module">
  import UrbanautClient from '/urbanaut-js-client/src/urbanaut.js';

  // Backend origin injected from PHP env vars (no trailing slash).
  const BACKEND_URL = <?= json_encode($url) ?>;

  // The JS client stores the token under `urbanaut.token`; the rest of the app
  // (admin panel, map) reads `urbanaut_token`. Mirror on every login so both
  // keys stay in sync and the other pages keep working unchanged.
  const LEGACY_TOKEN_KEY = 'urbanaut_token';

  const api = new UrbanautClient({ baseUrl: BACKEND_URL + '/api' });

  function mirrorLegacyToken(token) {
    if (token) localStorage.setItem(LEGACY_TOKEN_KEY, token);
    else       localStorage.removeItem(LEGACY_TOKEN_KEY);
  }

  // If the user already has a session (either key), keep the legacy one in sync.
  if (api.isAuthenticated) mirrorLegacyToken(api.token);

  function obtenerDatos() {
    const email      = document.getElementById("email").value.trim();
    const contrasena = document.getElementById("password").value.trim();
    return { email, contrasena };
  }

  function validarDatos() {
    const datos = obtenerDatos();

    if (datos.email === "" || datos.contrasena === "") {
      alert("Por favor, completá el correo electrónico y la contraseña.");
      return null;
    }

    const formatoEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!formatoEmail.test(datos.email)) {
      alert("Ingresá un correo electrónico válido.");
      return null;
    }

    return datos;
  }

  async function iniciarSesion(event) {
    if (event) event.preventDefault();

    const datos = validarDatos();
    if (!datos) return;

    try {
      // POST /api/usuario/login — the client persists token + user for us.
      const data = await api.usuario.login(datos);

      if (!data || !data.token) {
        console.error("Login OK pero no vino token en la respuesta.", data);
        alert("Error de sesión: el servidor no devolvió un token.");
        return;
      }

      // Keep the legacy key in sync so the rest of the app finds the session.
      mirrorLegacyToken(data.token);

      if (data.usuario && data.usuario.es_admin == 1) {
        window.location.href = '../administrador/';
      }
      // Non-admin flow: preserves current behavior (no redirect). Add one here
      // (e.g. `else window.location.href = '../inicio/';`) if needed.

    } catch (err) {
      console.error("Error al iniciar sesión:", err);

      if (err?.name === 'ValidationError') {
        alert("Faltan datos o hay datos incorrectos.");
        return;
      }

      if (err?.name === 'NetworkError') {
        alert("No se pudo conectar con el servidor.");
        return;
      }

      // HttpError
      switch (err?.status) {
        case 400:
          alert("Faltan datos o hay datos incorrectos.");
          break;
        case 401:
          alert("Email o contraseña incorrectos.");
          break;
        case 500:
          alert("Error interno del servidor.");
          break;
        default:
          alert("Ocurrió un error inesperado.");
      }
    }
  }

  document
    .getElementById("formLogin")
    .addEventListener("submit", iniciarSesion);
</script>
</body>
</html>

