<?php
session_start();
require_once(__DIR__ . '/../../../back_end/config.php');

require_once(CONEXION_PATH); // Asegúrate de que esta constante apunte a tu conexión PDO
// 1. Verificar sesión activa
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../login/login.php");
    exit();
}
require_once(BARRA_LATERAL);

$id_usuario = $_SESSION['usuario_id'];
$pdo= conectar_db();
// 2. Consultar datos del usuario actual
try {
      $pdo->exec("USE usuarios");
    $query = "SELECT nombre, cedula,correo, nombre_usuario FROM usuario WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':id' => $id_usuario]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        echo "Error: Usuario no encontrado.";
        exit();
    }
} catch (PDOException $e) {
    echo "Error de base de datos: " . $e->getMessage();
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Configuración de Cuenta</title>
    
    <link href="../../css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../publico/style_barra_tareas.css">
    <link rel="stylesheet" href="../../publico/style_perfil.css"> </head>
<body>

    <!-- ESTE MAIN CONTENT HACE QUE EL SIDEBAR NO SE PONGA ENCIMA -->
    <main class="main-content">

    <div class="content-area">

    <!-- MENSAJE PARA INDICAR QUE SE GUARDARON LOS CAMBIOS O HUBO UN ERROR -->
        <div class="container-fluid max-w-1200">

            <?php if (isset($_GET['status'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                 <strong>¡Éxito!</strong> Los cambios se han guardado correctamente.
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
             <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                     <strong>Error:</strong> 
             <?php 
                 echo ($_GET['error'] == 'pass_no_coinciden') ? 'Las contraseñas nuevas no coinciden.' : 
                        (($_GET['error'] == 'pass_incorrecta') ? 'La contraseña actual no es válida.' : 'Hubo un problema al procesar la solicitud.');
             ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
            <h3 class="mb-4 fw-bold text-dark">Configuración de Cuenta</h3>
            
            <!-- BOTONES PARA NAVEGAR ENTRE SECCIONES DEL PERFIL -->
            <div class="row">
                <div class="col-md-3 mb-4">
                    <div class="card-custom p-3">
                        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">

                            <button class="nav-link text-start active" id="perfil-tab" data-bs-toggle="pill" data-bs-target="#perfil" type="button" role="tab" aria-selected="true">
                                <i class="fa-solid fa-user"></i> Mi Perfil
                            </button>
                            <button class="nav-link text-start" id="seguridad-tab" data-bs-toggle="pill" data-bs-target="#seguridad" type="button" role="tab" aria-selected="false">
                                <i class="fa-solid fa-shield-halved"></i> Seguridad
                            </button>
                            <button class="nav-link text-start" id="equipo-tab" data-bs-toggle="pill" data-bs-target="#equipo" type="button" role="tab" aria-selected="false">
                                <i class="fa-solid fa-users"></i> Equipo
                            </button>
                            <hr class="text-muted">
                            <button class="nav-link text-start text-danger bg-transparent" id="eliminar-tab" type="button">
                                <i class="fa-solid fa-trash"></i> Eliminar cuenta
                            </button>
                        </div>
                    </div>
                </div>
                <!-- FIN DE BOTONES DE NAVEGACION -->

                <!-- SECCION MI PERFIL -->
                <div class="col-md-9">
                    <div class="tab-content" id="v-pills-tabContent">
                        
                       <div class="tab-pane fade show active" id="perfil" role="tabpanel">
                        <div class="card-custom p-4 mb-4">
                         <h5 class="fw-bold mb-4">Información Personal</h5>
      
                           <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                            <img src="https://ui-avatars.com/api/?name=<?php echo $usuario['nombre'].'+'.$usuario['apellido']; ?>&background=0D8ABC&color=fff" class="avatar-lg me-4" alt="Avatar">
                             <div>
                              <h5 class="mb-1 fw-bold"><?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?></h5>
                               <p class="text-muted mb-2">
                                <?php echo htmlspecialchars($usuario['nombre_usuario']); ?> | CI: <?php echo htmlspecialchars($usuario['cedula']); ?>
                               </p>
                                  <button class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-camera"></i> Cambiar foto</button>
                             </div>
                          </div>

                          <form action="../../../back_end/usuarios/gestion_usuario.php" method="POST">
                           <input type="hidden" name="accion" value="actualizar_datos">

                            <div class="row g-3">
                              <div class="col-md-6">
                                <label class="form-label small text-muted fw-bold text-uppercase">Nombre</label>
                                <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($usuario['nombre']); ?>">
                              </div>

                                <div class="col-md-6">
                                 <label class="form-label small text-muted fw-bold text-uppercase">Apellido</label>
                                 <input type="text" name="apellido" class="form-control" value="<?php echo htmlspecialchars($usuario['apellido']); ?>">
                                </div>

                                  <div class="col-md-6">
                                   <label class="form-label small text-muted fw-bold text-uppercase">Correo Electrónico</label>
                                   <input type="email" name="correo" class="form-control" value="<?php echo htmlspecialchars($usuario['correo']); ?>">
                                  </div>

                                    <div class="col-md-6">
                                     <label class="form-label small text-muted fw-bold text-uppercase">Teléfono</label>
                                     <input type="text" name="telefono" class="form-control" value="<?php echo htmlspecialchars($usuario['nombre_usuario']); ?>">
                                    </div>

                                        <div class="col-12 text-end mt-4">
                                            <button type="submit" class="btn btn-success px-4">Guardar Cambios</button>
                                        </div>
                            </div>
                         </form>
                    </div>
                </div>

                        <!-- SECCION SEGURIDAD --> 
                        <div class="tab-pane fade" id="seguridad" role="tabpanel" tabindex="0">
                            <div class="card-custom p-4 mb-4">
                                <h5 class="fw-bold mb-4">Cambiar Contraseña</h5>
                                <form action="../../../back_end/usuarios/gestion_usuario.php" method="POST">
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <label class="form-label small text-muted fw-bold text-uppercase">Contraseña Actual</label>
                                            <input type="password" name="pass_actual" class="form-control" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small text-muted fw-bold text-uppercase">Nueva Contraseña</label>
                                            <input type="password" name="pass_nueva" class="form-control" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small text-muted fw-bold text-uppercase">Confirmar Nueva Contraseña</label>
                                            <input type="password" name="pass_confirmar" class="form-control" required>
                                        </div>
                                        <div class="col-12 mt-4 text-end">
                                            <button type="submit" class="btn btn-primary px-4">Actualizar Contraseña</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                        <!-- SECCION EQUIPO -->
                        </div>
                        <div class="tab-pane fade" id="equipo" role="tabpanel" tabindex="0">
                            <div class="card-custom p-4">
                                <h5 class="fw-bold mb-4">Mi Equipo de Trabajo</h5>
                                
                                <div class="list-group">
                                    <div class="list-group-item d-flex justify-content-between align-items-center p-3 border-0 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <img src="https://ui-avatars.com/api/?name=Jean+C&background=random" class="rounded-circle me-3" width="45">
                                            <div>
                                                <h6 class="mb-0 fw-bold">Jean Christopher</h6>
                                                <small class="text-muted">Desarrollador Facturación</small>
                                            </div>
                                        </div>
                                        <span class="badge bg-success rounded-pill">Activo</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center p-3 border-0 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <img src="https://ui-avatars.com/api/?name=Maria+Perez&background=random" class="rounded-circle me-3" width="45">
                                            <div>
                                                <h6 class="mb-0 fw-bold">Maria Perez</h6>
                                                <small class="text-muted">Supervisora</small>
                                            </div>
                                        </div>
                                        <span class="badge bg-secondary rounded-pill">Desconectada</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div> </div>
            </div>
        </div>
    </div>

    <script src="../../js/bootstrap.bundle.min.js"></script>
</div>
</main>
</body>
</html>