<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
      body {
        margin: 0;
        padding: 0;
        overflow: hidden;
      }

      .login-container {
        height: 100vh;
        display: flex;
        position: relative;
      }

      .login-bg {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url('../publics/portada.jpg');
        background-size: cover;
        background-position: center;
        z-index: -1;
      }

      .login-form-container {
        width: 50%;
        margin-left: auto;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
      }

      .login-form {
        width: 100%;
        max-width: 400px;
        padding: 40px;
      }

      .login-form h1 {
        color: #2a2b2c;
        font-weight: bold;
        margin-bottom: 10px;
      }

      .login-form p {
        color: #2a2b2c;
        margin-bottom: 30px;
      }

      .login-form .form-control {
        background-color: rgba(255, 255, 255, 0.9);
        border: none;
        margin-bottom: 20px;
        padding: 12px 15px;
      }

      .login-form .form-control::placeholder {
        color: #999;
      }

      .login-form .btn-login {
        width: 100%;
        background-color: #343a40;
        border: none;
        padding: 12px;
        font-weight: bold;
        color: white;
      }

      .login-form .btn-login:hover {
        background-color: #23272b;
        color: white;
      }

      .alert {
        margin-bottom: 20px;
      }

      .back-btn {
        position: absolute;
        top: 20px;
        right: 20px;
        background: rgba(255,255,255,0.8);
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        color: #2a2b2c;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        z-index: 2;
      }

      .back-btn svg {
        width: 18px;
        height: 18px;
        fill: currentColor;
      }
    </style>
    <title>Inicio de sesión</title>
</head>
<body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <div class="login-container">
      <a href="index.php" class="back-btn" aria-label="Volver">
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
        </svg>
      </a>
      <div class="login-bg"></div>

      <div class="login-form-container">
        <div class="login-form">
          <h1>Bienvenido de vuelta</h1>
          <p>Por favor ingrese sus datos</p>

          <div id="mensajeAlerta"></div>

          <form id="formLogin">
            <input
              type="text"
              id="usuario"
              class="form-control"
              placeholder="Usuario"
              required>

            <input
              type="password"
              id="contrasena"
              class="form-control"
              placeholder="Contraseña"
              required>

            <button type="submit" class="btn btn-login">Iniciar Sesión</button>
          </form>
        </div>
      </div>
    </div>

    <script>
      document.addEventListener("DOMContentLoaded", function() {
        if (window.location.pathname.endsWith("login.php") || window.location.pathname.endsWith("gestionar.php") || window.location.pathname.endsWith("carrito.php")) {
          history.replaceState(null, "", "index.php");
        }
      });

      document.getElementById("formLogin").addEventListener("submit", async (e) => {
        e.preventDefault();

        const usuario = document.getElementById("usuario").value;
        const contrasena = document.getElementById("contrasena").value;

        const formData = new FormData();
        formData.append("usuario", usuario);
        formData.append("contrasena", contrasena);

        try {
          const response = await fetch("../api/login.php", {
            method: "POST",
            body: formData
          });

          const data = await response.json();
          const alertaDiv = document.getElementById("mensajeAlerta");

          if (data.success) {
            alertaDiv.innerHTML = `<div class="alert alert-success">Login exitoso. Redirigiendo...</div>`;
            setTimeout(() => {
              window.location.href = "index.php";
            }, 1000);
          } else {
            alertaDiv.innerHTML = `<div class="alert alert-danger">${data.mensaje}</div>`;
          }
        } catch (error) {
          console.error("Error:", error);
          document.getElementById("mensajeAlerta").innerHTML = `<div class="alert alert-danger">Error en la conexión</div>`;
        }
      });
    </script>
</body>
</html>
