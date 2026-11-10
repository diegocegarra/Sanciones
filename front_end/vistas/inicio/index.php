<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../login/login.php");
    exit();
}
require_once(__DIR__ . '/../../../back_end/config.php');
require_once(BARRA_LATERAL);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio - Gestión IVSS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../publico/style_barra_tareas.css"> </head>
    <link rel="icon" href="../ivss.png" type="image"> 

<body>

<div class="d-flex">
    <?php  ?>

    <main class="main-content flex-grow-1 bg-light" style="min-height: 100vh; padding: 40px !important;">
        <div class="container-fluid">
            
            <div class="card shadow-sm border-0 p-5 rounded-4 bg-white mt-2">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="display-5 fw-bold text-primary-emphasis">¡Hola, Bienvenido al Sistema de Autogestión!</h1>
                        <p class="lead mt-3 text-muted">
                            Veamos que <strong> Tareas </strong>  requieren tu atención. 
                        </p>
                        
                        <div class="d-grid gap-2 d-md-flex mt-4">
                            <a href="../tareas/tareas.php" class="btn btn-primary btn-lg px-4 shadow">
                                <i class="bi bi-play-fill"></i> Empezar a trabajar
                            </a>
                            <a href="../empresas/empresas.php" class="btn btn-outline-secondary btn-lg px-4">
                                Ver Directorio
                            </a>
                        </div>
                    </div>
                    <div class="col-md-4 d-none d-md-block text-center">
                        <img src="../ivss.png" alt="Logo" style="width: 150px;">
                    </div>
                </div>
            </div>

            <div class="row mt-4 g-3">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm p-3 bg-white">
                        <small class="text-muted fw-bold">TAREAS PENDIENTES</small>
                        <h2 class="text-warning">08</h2>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm p-3 bg-white">
                        <small class="text-muted fw-bold">SANCIONES EN CURSO</small>
                        <h2 class="text-primary">03</h2>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm p-3 bg-white">
                        <small class="text-muted fw-bold">COMPLETADAS HOY</small>
                        <h2 class="text-success">12</h2>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>