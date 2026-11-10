<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
        <link href="../css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

  <link rel="icon" href="recursos/ivss.png">
</head>
<body class="auth-body">

<style>
 
</style>
<div class="container d-flex justify-content-center">
  <div class="card auth-card p-4 shadow">
    <div class="text-center mb-4">
        <img src="recursos/ivss.png" alt="Logo IVSS" style="width: 70px;">
        <h2 class="mt-3 fw-bold text-dark">Crear Cuenta</h2>
        <p class="text-muted small">Completa los datos para registrarte</p>
    </div>

    <!-- <form id="registroForm" method="POST"> -->
      <div class="row">
        <div class="col-md-12 mb-3">
          <label for="nombre" class="form-label text-muted">Nombre completo</label>
          <input type="text" class="form-control" id="nombre" name="nombre_registro" pattern="[a-zA-Z0-9]+" required placeholder="Juan Pérez">
        </div>

        <div class="col-md-12 mb-3">
          <label for="correo" class="form-label text-muted">Correo electrónico</label>
          <input type="email" class="form-control" id="correo" name="correo_registro" required placeholder="correo@ejemplo.com">
        </div>

        <div class="col-md-6 mb-3">
          <label for="cedula" class="form-label text-muted">Cédula</label>
          <input type="number" class="form-control" id="cedula" name="cedula_registro" pattern="[a-zA-Z0-9]+" required>
        </div>

        <div class="col-md-6 mb-3 "  >
          <label for="usuario" class="text-muted small">Usuario</label>
          <input type="text" class="form-control" id="usuario" name="usuario_registro" pattern="[a-zA-Z0-9]+" required>
        </div>

        <div class="col-md-12 mb-3">
          <label for="clave" class="form-label fw-semibold">Contraseña</label>
          <div class="input-group">
            <input type="password" class="form-control" id="clave" name="clave_regis" pattern="[a-zA-Z0-9]+" required>
            <button class="btn btn-outline-secondary" type="button" onclick="toggleClave()"><img src="recursos/eye-fill.svg" alt=""></button>
          </div>
        </div>

        <div class="col-md-12 mb-3">
          <label for="confirmar" class="form-label fw-semibold">Confirmar contraseña</label>
          <div class="input-group">
            <input type="password" class="form-control" id="confirmar" name="acceso" pattern="[a-zA-Z0-9]+" required>
            <button class="btn btn-outline-secondary" type="button" onclick="toggleConfirmar()"><img src="recursos/eye-fill.svg" alt=""></button>
          </div>
        </div>
      </div>

      <div class="alert alert-danger mt-2" role="alert" id="errorMsg" style="display: none;">
        Por favor completa todos los campos.
      </div>

      <div class="col-md-6 mb-3">
          <label for="codigoAdmin" class="form-label text-muted">Opcional </label>
             <input type="text"class="form-control " id="codigoAdmin"  placeholder="Código Admin" pattern="[a-zA-Z0-9]+">
        
        </div>
      
      <button type="button" id="btnRegistrar" class="btn btn-primary btn-auth w-100 mt-2">Registrarse</button>

      <div class="text-center mt-4">
        <small class="text-muted">
          ¿Ya estás registrado?
          <a href="../login/login.php" class="text-decoration-none fw-bold">Iniciar sesión</a>
        </small>
      </div>
    <!-- </form> -->
  </div>
</div>

<script>
  function toggleClave() {
    const input = document.getElementById("clave");
    input.type = input.type === "password" ? "text" : "password";
  }

  function toggleConfirmar() {
    const input = document.getElementById("confirmar");
    input.type = input.type === "password" ? "text" : "password";
  }
</script>
<script src="../login/registro.js"></script>


<script>
 
</script>
</body>
</html>