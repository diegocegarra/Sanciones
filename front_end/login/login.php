<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inicio de Sesión</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
          <link href="../css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

  <link rel="stylesheet" href="style.css">
  <link rel="icon" href="recursos/ivss.png">
</head>
<!-- <body class="login-body"> -->
<body >
<div class="container-fluid d-flex align-items-center justify-content-center vh-100 bg-light">
    
    <div class="row w-100 shadow-lg bg-white rounded border overflow-hidden" style="max-width: 1100px; min-height: 600px;">
        
   <div class="container-fluid p-0 vh-100 overflow-hidden">
    <div class="row g-0 vh-100">
        
        <div class="col-md-8 d-none d-md-block position-relative vh-100 bg-dark border-end">
            
            <video autoplay muted loop playsinline class="w-100 h-100" style="object-fit: cover; position: absolute; top: 0; left: 0;">
                <source src="recursos/8293014-hd_1920_1080_30fps.mp4" type="video/mp4">
            </video>

            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center text-white" 
                 style="background: rgba(0, 0, 0, 0.4); z-index: 2;">
                <h2 class="fw-bold display-4">Bienvenido al IVSS</h2>
                <p class="fs-5 text-center">Tu portal para la seguridad social.</p>
            </div>

        </div>
        <!-- log in  -->
        <div class="col-md-4 p-4 p-lg-5 d-flex flex-column justify-content-center bg-white">
            
            <div class="text-center mb-4">
              
                <img src="recursos/ivss.png" alt="Logo IVSS" class="d-md-none mb-3" style="width: 80px;">
                <h2 class="fw-bold text-dark">Bienvenido</h2>
                <p class="text-muted small">Inicia sesión para continuar</p>
            </div>

            <form id="formLogin">
                <div class="mb-3">
                    <input type="text" class="form-control py-2 bg-light" id="usuario" name="nombre_usuario" placeholder="Usuario" required>
                </div>

                <div class="mb-3">
                    <div class="input-group">
                        <input type="password" class="form-control py-2 bg-light border-end-0" id="clave" name="password" placeholder="Contraseña" required>
                        <span class="input-group-text bg-light border-start-0 cursor-pointer" onclick="toggleClave()">
                            <img src="recursos/eye-fill.svg" style="width: 16px;" alt="ver">
                        </span>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 fw-bold py-2 mt-2" style="background-color: #003366; border: none;">Entrar</button>

                <div class="text-center mt-3">
                    <a href="recuperar.php" class="text-decoration-none small fw-bold" style="color: #003366;">¿Olvidaste la contraseña?</a>
                </div>

                <div class="d-flex align-items-center my-4">
                    <hr class="flex-grow-1 text-muted">
                    <span class="mx-2 text-muted small fw-bold">O</span>
                    <hr class="flex-grow-1 text-muted">
                </div>

                <div class="text-center">
                    <small class="text-muted">¿No estás registrado? 
                        <a href="registro.php" class="text-decoration-none fw-bold" style="color: #003366;">Regístrate</a>
                    </small>
                </div>
            </form>
        </div>
        
    </div>
</div>
<style>
  .carousel-item {
    transition: transform 1s ease-in-out, opacity 1s ease-in-out;
}

/* Para que las imágenes de fondo no se vean estiradas */
.carousel-item div {
    background-size: cover !important;
    background-position: center !important;
}
</style>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  function toggleClave() {
    const input = document.getElementById("clave");
    input.type = input.type === "password" ? "text" : "password";
  }

  
  document.getElementById("formLogin").addEventListener("submit", function(e) {
    e.preventDefault();// evita que el evento se recargue
    const formData = new FormData(this);

    fetch("../../back_end/login/verificar_session.php", {
        method: "POST",
        body: formData
    })

    .then(response => response.json()) // Convertimos la respuesta a objeto JS
    .then(data => {
        if (data.success) {
            // SOLO si el PHP envió success: true
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: 'Iniciaste sesión correctamente',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location.href = "../vistas/inicio/index.php";
            });
        } else {
            // Si el PHP envió success: false (datos inválidos)
            Swal.fire({
                icon: 'error',
                title: 'Credenciales inválidas',
                text: data.message // Muestra el mensaje que definimos en PHP
            });
        }
    })
    .catch(error => {
        console.error("Error:", error);
        Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: 'No se pudo establecer contacto con el servidor'
        });
    });
});
</script>


<script src="../bootstrap/js/bootstrap.bundle.min.js" ></script>

</body>
</html>