<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container vh-100 d-flex justify-content-center align-items-center">
        <div class="card shadow-sm" style="max-width: 400px; width: 100%;">
            <div class="card-body p-4">
                <h3 class="card-title text-center mb-4">¿Olvidaste tu clave?</h3>
                <p class="text-muted small text-center mb-4">
                    Ingresa tu correo electrónico y te enviaremos un código para restablecer tu contraseña.
                </p>
                

                <form id="formRecuperar">
                    <div class="mb-3">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" name="email" class="form-control" placeholder="ejemplo@correo.com" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Enviar código</button>
                </form>

                
                <div class="text-center mt-3">
                    <a href="login.php" class="text-decoration-none small text-secondary">Volver al inicio</a>
                </div>
            </div>
        </div>
    </div>

    <script src="js/recuperar_acceso.js">
     
    </script>
    <script>
        enviar_email()
    </script>
</body>
</html>