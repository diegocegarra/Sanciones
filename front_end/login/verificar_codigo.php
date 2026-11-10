<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar Código</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .codigo-input {
            letter-spacing: 0.5rem;
            font-weight: bold;
            font-size: 1.5rem;
            text-align: center;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container vh-100 d-flex justify-content-center align-items-center">
        <div class="card shadow-sm" style="max-width: 400px; width: 100%;">
            <div class="card-body p-4 text-center">
                <div class="mb-3">
                    <span class="badge rounded-pill bg-info-subtle text-info p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-shield-lock" viewBox="0 0 16 16">
                          <path d="M8 1a5 5 0 0 1 5 5v4.5a3.5 3.5 0 0 1-7 0V6a5 5 0 0 1 5-5m3 5a3 3 0 0 0-6 0v4.5a2.5 2.5 0 0 0 5 0z"/>
                          <path d="M4.14 1.824a.5.5 0 0 0-.19-.89L8.412.012a.5.5 0 0 0 .176 0l4.462.922a.5.5 0 0 0 .19.89c.563.186 1.09.444 1.565.767a.5.5 0 0 0 .73-.553L13.627 0H2.373L1.047 2.85a.5.5 0 0 0 .73.553c.474-.323 1.002-.58 1.564-.767"/>
                        </svg>
                    </span>
                </div>
                
                <h3 class="card-title mb-2">Código de Seguridad</h3>
                <p class="text-muted small mb-4">Introduce el código de 6 dígitos enviado a tu bandeja.</p>
                
                <form id="formVerificarToken">
                    <div class="mb-4">
                        <input type="text" name="token" class="form-control codigo-input" 
                               placeholder="000000" maxlength="6" pattern="[A-Za-z0-9]{6}" required>
                        <input type="hidden" name="correo" placeholder="<?php ?>">
                    </div>
                    
                    <button type="submit" class="btn btn-success w-100 mb-3">Verificar Código</button>
                    
                    <p class="small text-muted">
                        ¿No recibiste nada? <a href="#" class="text-decoration-none">Reenviar código</a>
                    </p>
                </form>
            </div>
        </div>
    </div>

     <script src="js/recuperar_acceso.js">
     
    </script>
    <script>
        verificacion_codigo()
    </script>
</body>
</html>