<?php
$backend = rtrim(getenv('BACKEND_HOST') ?: '', '/');
$port    = getenv('BACKEND_PORT') ?: '';
$url     = 'http://' . $backend . ':' . $port;
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Urbanaut | Crear Cuenta</title>

    <!-- Bootstrap -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />

    <!-- Bootstrap Icons -->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
    />

    <style>
      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
      }

      body {
        min-height: 100vh;
        overflow: hidden;

        font-family: "Segoe UI", Arial, sans-serif;

        background: url("../imagenes/imagen2.jpg") center center / cover
          no-repeat fixed;

        display: flex;
        align-items: center;
        justify-content: center;

        position: relative;
      }

      body::before {
        content: "";

        position: fixed;
        inset: 0;

        background: linear-gradient(
          135deg,
          rgba(0, 180, 255, 0.15),
          rgba(0, 255, 170, 0.08)
        );

        backdrop-filter: blur(2px);

        z-index: -1;
      }

      .contenedor {
        width: min(95%, 900px);

        min-height: 100vh;

        display: flex;
        justify-content: center;
        align-items: center;

        padding: 15px;
      }

      .card-urbanaut {
        width: 560px;
        max-width: 100%;
        max-height: calc(100vh - 30px);

        padding: 18px 50px 15px;

        border-radius: 30px;

        background: linear-gradient(
          135deg,
          rgba(255, 255, 255, 0.72),
          rgba(230, 250, 255, 0.45)
        );

        border: 1px solid rgba(255, 255, 255, 0.8);

        box-shadow:
          0 25px 70px rgba(0, 40, 70, 0.3),
          inset 0 1px 0 rgba(255, 255, 255, 0.8);

        backdrop-filter: blur(22px);
        -webkit-backdrop-filter: blur(22px);

        animation: aparecer 0.8s ease;

        overflow: hidden;
      }

      @keyframes aparecer {
        from {
          opacity: 0;
          transform: translateY(25px) scale(0.97);
        }

        to {
          opacity: 1;
          transform: translateY(0) scale(1);
        }
      }

      .logo {
        display: flex;
        justify-content: center;
        align-items: center;

        margin-bottom: 4px;
      }

      .logo img {
        width: 150px;
        height: auto;
        max-height: 65px;

        object-fit: contain;

        display: block;
      }

      h1 {
        text-align: center;

        color: #123d69;

        font-size: 38px;
        font-weight: 750;

        margin-bottom: 3px;

        letter-spacing: -1.5px;
      }

      .subtitulo {
        text-align: center;

        color: #58738e;

        font-size: 15px;

        margin-bottom: 13px;
      }

      .campo {
        position: relative;

        margin-bottom: 7px;
      }

      .campo > i {
        position: absolute;

        left: 18px;
        top: 50%;

        transform: translateY(-50%);

        color: #2772b8;

        font-size: 19px;

        z-index: 2;

        pointer-events: none;
      }

      .campo input {
        width: 100%;
        height: 52px;

        border-radius: 15px;

        border: 1px solid rgba(60, 120, 170, 0.15);

        background: rgba(255, 255, 255, 0.72);

        padding: 0 52px 0 52px;

        font-size: 16px;

        color: #163f62;

        outline: none;

        transition: all 0.25s ease;

        box-shadow: 0 4px 15px rgba(30, 90, 120, 0.05);
      }

      .campo input::placeholder {
        color: #7790a8;
      }

      .campo input:focus {
        background: white;

        border-color: #3b9cff;

        box-shadow: 0 0 0 4px rgba(59, 156, 255, 0.12);

        transform: translateY(-1px);
      }

      .mostrar-password {
        position: absolute;

        right: 15px;
        top: 50%;

        transform: translateY(-50%);

        width: 32px;
        height: 32px;

        display: flex;
        align-items: center;
        justify-content: center;

        border: none;

        background: transparent;

        color: #7890a5;

        font-size: 18px;

        cursor: pointer;

        padding: 0;

        z-index: 5;
      }

      .mostrar-password:hover {
        color: #1879c9;
      }

      .btn-continuar {
        width: 100%;
        height: 54px;

        border: none;
        border-radius: 16px;

        margin-top: 5px;

        background: linear-gradient(100deg, #238bff, #19b7c9, #63d85f);

        background-size: 200% auto;

        color: white;

        font-size: 17px;
        font-weight: 700;

        letter-spacing: 0.3px;

        cursor: pointer;

        transition:
          transform 0.25s ease,
          box-shadow 0.25s ease,
          background-position 0.4s ease;

        box-shadow: 0 10px 25px rgba(30, 145, 220, 0.25);
      }

      .btn-continuar:hover {
        transform: translateY(-2px);

        background-position: right center;

        box-shadow: 0 15px 30px rgba(30, 145, 220, 0.35);
      }

      .flecha {
        margin-left: 8px;

        font-size: 21px;
      }

      /* INICIAR SESIÓN */

      .login {
        text-align: center;

        margin-top: 8px;

        color: #607991;

        font-size: 14px;
      }

      .login a {
        color: #1683e9;

        font-weight: 700;

        text-decoration: none;
      }

      .login a:hover {
        text-decoration: underline;
      }

      /* BENEFICIOS */

      .beneficios {
        display: grid;

        grid-template-columns: repeat(3, 1fr);

        gap: 12px;

        margin-top: 10px;

        padding-top: 9px;

        border-top: 1px solid rgba(70, 120, 150, 0.15);
      }

      .beneficio {
        text-align: center;

        color: #526f88;

        font-size: 11px;
      }

      .beneficio-icon {
        width: 34px;
        height: 34px;

        margin: 0 auto 3px;

        border-radius: 50%;

        display: flex;
        justify-content: center;
        align-items: center;

        background: rgba(255, 255, 255, 0.75);

        color: #1674c7;

        font-size: 16px;

        box-shadow: 0 5px 12px rgba(30, 90, 120, 0.08);
      }

      .beneficio strong {
        display: block;

        color: #246294;

        font-size: 12px;

        margin-bottom: 2px;
      }

      /* CELULAR */

      @media (max-width: 650px) {
        body {
          overflow-y: auto;
        }

        .contenedor {
          min-height: 100vh;

          height: auto;

          padding: 15px;
        }

        .card-urbanaut {
          width: 94%;

          max-height: none;

          padding: 25px 25px 20px;

          border-radius: 24px;

          overflow: visible;
        }

        h1 {
          font-size: 32px;
        }

        .logo img {
          width: 135px;

          max-height: 60px;
        }

        .beneficios {
          gap: 7px;
        }
      }

      @media (max-width: 430px) {
        .card-urbanaut {
          padding: 25px 18px 22px;
        }

        h1 {
          font-size: 30px;
        }

        .subtitulo {
          font-size: 14px;
        }

        .logo img {
          width: 125px;

          max-height: 55px;
        }

        .beneficios {
          grid-template-columns: 1fr;

          gap: 12px;
        }

        .beneficio {
          display: flex;

          align-items: center;

          text-align: left;

          gap: 10px;
        }

        .beneficio-icon {
          margin: 0;

          flex-shrink: 0;
        }
      }
    </style>
  </head>

  <body>
    <main class="contenedor">
      <section class="card-urbanaut">
        <!-- LOGO -->

        <div class="logo">
          <img src="../imagenes/logoNuevo2.png" alt="Logo Urbanaut" />
        </div>

        <!-- TÍTULO -->

        <h1>Crear Cuenta</h1>

        <p class="subtitulo">
          Poné tus datos y empezá a encontrar tu lugar ideal.
        </p>

        <!-- FORMULARIO -->

        <form id="formulario">
          <!-- action="horarios.php"
                method="POST"> -->

          <!-- NOMBRE -->

          <div class="campo">
            <i class="bi bi-person"></i>

            <input
              type="text"
              id="nombre"
              name="nombre"
              placeholder="Nombre"
              required
            />
          </div>

          <!-- APELLIDO -->

          <div class="campo">
            <i class="bi bi-person"></i>

            <input
              type="text"
              id="apellido"
              name="apellido"
              placeholder="Apellido"
              required
            />
          </div>

          <!-- CORREO -->

          <div class="campo">
            <i class="bi bi-envelope"></i>

            <input
              type="email"
              id="email"
              name="email"
              placeholder="Correo electrónico"
              required
            />
          </div>

          <!-- CONTRASEÑA -->

          <div class="campo">
            <i class="bi bi-lock"></i>

            <input
              type="password"
              id="contrasena"
              name="contrasena"
              placeholder="Contraseña"
              required
            />

            <button
              type="button"
              class="mostrar-password"
              onclick="mostrarPassword('contrasena', this)"
            >
              <i class="bi bi-eye"></i>
            </button>
          </div>

          <!-- CONFIRMAR CONTRASEÑA -->

          <div class="campo">
            <i class="bi bi-lock"></i>

            <input
              type="password"
              id="confirmar"
              name="confirmar"
              placeholder="Confirmar contraseña"
              required
            />

            <button
              type="button"
              class="mostrar-password"
              onclick="mostrarPassword('confirmar', this)"
            >
              <i class="bi bi-eye"></i>
            </button>
          </div>

          <!-- CONTINUAR -->

          <button
            type="submit"
            class="btn-continuar"
          >
            Continuar

            <span class="flecha"> → </span>
          </button>
        </form>

        <!-- INICIAR SESIÓN -->

        <div class="login">
          ¿Ya tenés una cuenta?

          <a href="../login/index.html"> Iniciá sesión </a>
        </div>

        <!-- BENEFICIOS -->

        <div class="beneficios">
          <div class="beneficio">
            <div class="beneficio-icon">
              <i class="bi bi-shield-check"></i>
            </div>

            <div>
              <strong> Seguro </strong>

              Tus datos protegidos
            </div>
          </div>

          <div class="beneficio">
            <div class="beneficio-icon">
              <i class="bi bi-geo-alt"></i>
            </div>

            <div>
              <strong> Confiable </strong>

              Estacionamientos cerca
            </div>
          </div>

          <div class="beneficio">
            <div class="beneficio-icon">
              <i class="bi bi-clock"></i>
            </div>

            <div>
              <strong> Rápido </strong>

              Encontrá tu lugar ideal
            </div>
          </div>
        </div>
      </section>
    </main>

    <script>
        function mostrarPassword(id, boton) {
          const input = document.getElementById(id);

          const icono = boton.querySelector("i");

          if (input.type === "password") {
            input.type = "text";

            icono.classList.remove("bi-eye");

            icono.classList.add("bi-eye-slash");
          } else {
            input.type = "password";

            icono.classList.remove("bi-eye-slash");

            icono.classList.add("bi-eye");
          }
        }

        document
          .getElementById("formulario")
          .addEventListener("submit", function (event) {
            event.preventDefault();
            const contrasena = document.getElementById("contrasena").value;
            const confirmar = document.getElementById("confirmar").value;
            if (contrasena !== confirmar) {
              alert("Las contraseñas no coinciden.");
              return;
            }

	    enviarDatos(obtenerDatos());
          });

        function obtenerDatos() {
          const nombre = document.getElementById("nombre").value.trim();
          const apellido = document.getElementById("apellido").value.trim();
          const email = document.getElementById("email").value.trim();
          const contrasena = document.getElementById("contrasena").value;

          const datos = {
            nombre,
            apellido,
            email,
            contrasena,
          };

          return datos;
        }

        async function enviarDatos(datos) {
          try {
               const response = await fetch(
	       <?= "\"".$url.'/api/usuario.php/signin'."\"" ?>,
                  {
                      method: "POST",

                      headers: {
                          "Content-Type": "application/json"
                      },

                      body: JSON.stringify(datos)
                  }
              );
              let data = await response.json();

                if (response.status === 200) {

                  alert(
                      "Usuario registrado correctamente."
                  );

                  document
                      .getElementById("formulario")
                      .reset();


              } else if (response.status === 409) {

                  alert(
                      "Ese correo ya está registrado."
                  );


              } else if (response.status === 400) {

                  alert(
                      "Faltan datos o hay datos incorrectos."
                  );

                  console.error(data);


              } else if (response.status === 500) {

                  alert(
                      "Error interno del servidor."
                  );

                  console.error(data);


              } else {

                  alert(
                      "Ocurrió un error inesperado."
                  );

                  console.error(data);
              }


          } catch (error) {

              console.error(
                  "Error al conectar con la API:",
                  error
              );

              alert(
                  "No se pudo conectar con el servidor."
              );
          }
      }
    </script>
  </body>
</html>
