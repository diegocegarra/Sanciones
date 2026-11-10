<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once(__DIR__ . '/../../../back_end/config.php');
require_once(CONEXION_PATH);
require_once(FUNCIONES_TAREAS);

require_once(BARRA_LATERAL);

$usuario_roles = mostrar_roles_usuarios($pdo);
$pdo = conectar_db(); // Aseguramos la conexión
$roles =obtener_roles($pdo);

// Aquí cargarías tus datos
$usuarios = obtener_usuarios($pdo);
// $roles = obtener_roles($pdo);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Configuración de Sistema</title>
    <link href="../../css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../publico/style_barra_tareas.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<main class="main-content">
    <div class="container-fluid p-4">
        <h2 class="mb-4"><i class="fa-solid fa-gears me-2"></i>Panel de Configuración</h2>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0">
                <ul class="nav nav-tabs card-header-tabs" id="configTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="usuarios-tab" data-bs-toggle="tab" data-bs-target="#usuarios" type="button">Gestión de Usuarios</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="roles-tab" data-bs-toggle="tab" data-bs-target="#roles" type="button">Roles y Permisos</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="sistema-tab" data-bs-toggle="tab" data-bs-target="#sistema" type="button">Ajustes del Sistema</button>
                    </li>
                </ul>
            </div>


            <?php $logeado = mostrar_logeado($pdo) ;?>
            <div class="card-body p-4">
                <div class="tab-content" id="configTabsContent">
                    
                    <div class="tab-pane fade show active" id="usuarios" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5>Usuarios Registrados</h5>
                            <!-- <button class="btn btn-primary btn-sm"><i class="fa-solid fa-user-plus me-1"></i> Nuevo Usuario</button> -->
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Usuario</th>
                                        
                                        <th>Estado</th>
                                        <!-- <th>Acciones</th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($logeado as $usuario): 
                                            // Determinamos el texto y el color (clase de Bootstrap)
                                            if($usuario['esta_logeado'] === 1) {
                                                $texto_estado = 'en linea';
                                                $clase_badge = 'bg-success'; // Verde
                                            } else {
                                                $texto_estado = 'desconectado';
                                                $clase_badge = 'bg-secondary'; // Gris
                                            }
                                        ?>
                                            <tr>
                                                <td><?=htmlspecialchars($usuario['nombre']." " .$usuario['apellido'])   ?></td>
                                                <td><?=htmlspecialchars($usuario['nombre_usuario']) ?></td>
                                                
                                                <td>
                                                    <span class="badge <?= $clase_badge ?>">
                                                        <?= htmlspecialchars($texto_estado) ?>
                                                    </span>
                                                </td>

                                                
                                            </tr>
                                        <?php endforeach; ?>

                                </tbody>
                            </table>
                        </div>
                    </div>




                    <div class="tab-pane fade" id="roles" role="tabpanel">



                        <h5>Roles y permisos</h5>


                        <div class="row mb-4">
                            <div class="col">
                                <label class="form-label d-block fw-bold text-muted small text-uppercase mb-2">
                                <img src="../barra_lateral/person.svg" alt="" style="width: 15px; filter: opacity(0.5);"> Asignar Roles del Sistema
                                </label>
                                    
                                    <!-- <form action="../../../back_end/tareas/src/funciones.php" method="POST" class="p-3 border rounded bg-light">
                                        

                                            <div class="col-md-3">
                                               

                                                <button type="submit" class="btn btn-success w-100 shadow-sm"> Asignar </button>
                                            </div>

                                        </div>
                                    </form> -->
                                    <form method="POST" action=" ../../../back_end/tareas/src/funciones.php"> <div class="row g-2">
                                    <div class="col-md-5">
                                        <select name="id_usuario" class="form-select" required>
                                            <option value="">Seleccione usuario</option>
                                            <?php foreach ($usuarios as $escoger): ?>
                                                <option value="<?= htmlspecialchars($escoger['id']); ?>">
                                                    <?= htmlspecialchars($escoger['nombre_usuario']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <select name="nuevo_rol_id" class="form-select" required>
                                            <option value="">Rol a asignar</option>
                                            <?php foreach ($roles as $escoger_rol): ?>
                                                <option value="<?= htmlspecialchars($escoger_rol['id_roles']); ?>">
                                                    <?= htmlspecialchars($escoger_rol['nombre_rol']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <input type="hidden" name="accion" value="asignar_rol_directo">
                                        <button type="submit" class="btn btn-success w-100 shadow-sm">Asignar</button>
                                    </div>
                                </div>
                            </form>




                                </div>
                            </div>
                            

                            <div class="row fw-bold border-bottom pb-2 mb-2 d-none d-md-flex">
                                <div class="col-md-4">ID Rol</div>
                                <div class="col-md-6">Nombre de Usuario</div>
                                <!-- <div class="col-md-2 text-center">Acciones</div> -->
                            </div>

                            <div class="usuarios-container">
                                <?php if (!empty($usuario_roles)): 
                                    
                                    ?>
                                    <?php foreach($usuario_roles as $usuarios_rol): 
                                     
                                        ?>
                                        <div class="row align-items-center py-2 border-bottom fila-usuario" 
                                            data-id="<?= htmlspecialchars($usuarios_rol['rol_id']) ?>"
                                            data-nombre="<?= htmlspecialchars($usuarios_rol['nombre_usuario']) ?>">

                                            <?php
                                            
                                               
                                    if($usuarios_rol['rol_id'] === 1){
                                        $usuarios_valor = 'admin';
                                        $clase_badge = 'bg-primary'; // azul

                                        
                                        
                                    }if($usuarios_rol['rol_id'] === 3){
                                         $usuarios_valor  = 'analista';
                                         $clase_badge = 'bg-dark'; // azul

                                        
                                    }if($usuarios_rol['rol_id'] === 2){
                                        $usuarios_valor = 'supervisor';
                                      $clase_badge = 'bg-info-subtle text-info-emphasis'; // azul

                                        
                                    }
                                            ?>
                                            
                                            <div class="col-6 col-md-4">
                                                <span class="d-md-none text-muted small d-block">ID Rol:</span>
                                                <span class="badge <?= $clase_badge ?>">
                                                    <?= htmlspecialchars($usuarios_valor ?? 'N/A') ?>
                                                </span>
                                            </div>

                                            <div class="col-6 col-md-6">
                                                <span class="d-md-none text-muted small d-block">Usuario:</span>
                                                <strong><?= htmlspecialchars($usuarios_rol['nombre_usuario'] ?? 'Sin nombre') ?></strong>
                                            </div>

                                            <!-- <div class="col-12 col-md-2 text-md-center mt-2 mt-md-0">
                                                <button class="btn btn-danger btn-sm w-100 w-md-auto" 
                                                        onclick="filaEliminar = this.closest('.row'); eliminarFila();">
                                                    <i class="bi bi-trash"></i> Eliminar
                                                </button>
                                            </div> -->
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="alert alert-info text-center">No hay registros encontrados.</div>
                                <?php endif; ?>
                            </div>

<!-- 
                        <p class="text-muted small">Configura qué puede hacer cada tipo de usuario.</p>
                        <form action="../../../back_end/usuarios/gestion_configuracion.php" method="POST">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="list-group" id="list-tab" role="tablist">
                                        <a class="list-group-item list-group-item-action active" data-bs-toggle="list" href="#perm-admin">Administrador</a>
                                        <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#perm-fiscal">Fiscal</a>
                                        <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#perm-recep">Recepcionista</a>
                                    </div>
                                </div>
                                 -->
                                
<!--                                 
                                <div class="col-md-8 border-start">
                                    <div class="ps-3">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="p1" checked>
                                            <label class="form-check-label" for="p1">Crear nuevas fiscalizaciones</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="p2" checked>
                                            <label class="form-check-label" for="p2">Eliminar registros del historial</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="p3">
                                            <label class="form-check-label" for="p3">Acceso a reportes financieros</label>
                                        </div>
                                        <button type="submit" class="btn btn-success mt-3">Actualizar Permisos</button>
                                    </div>
                                </div> -->



                            </div>
                        </form>
                    </div>










                    <div class="tab-pane fade" id="sistema" role="tabpanel">
                            <h5>Parámetros Generales</h5>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label  class="form-label fw-bold text-secondary small text-uppercase">Fechas festivas (No laborales)</label>
                                    
                                    <div class="input-group mb-4">
                                        <span class="input-group-text bg-light border-end-0">
                                            <img src="../barra_lateral/clipboard.svg" alt="" style="width: 16px; opacity: 0.6;">
                                        </span>
                                        <input class="form-control border-start-0 " type="date" id="tag-input">
                                        
                                        <button class="btn btn-primary" type="button" id="btnAddDate">Añadir</button>
                                    </div>

                                       


                                    <div id="tag-container" class="d-flex flex-wrap gap-2 mt-3">
                                        </div>
                                </div>

                                <!-- <div class="col-md-6 border-start ps-4">
                                    <label class="form-label fw-bold text-secondary small text-uppercase">Notificaciones por Correo</label>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="notifyTask" checked>
                                        <label class="form-check-label" for="notifyTask">Avisar al asignar nueva tarea</label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="notifyWeekly">
                                        <label class="form-check-label" for="notifyWeekly">Resumen semanal de sanciones</label>
                                    </div>
                                </div> -->



                            </div>
                        </div>




                </div>
            </div>
        </div>
    </div>
</main>

<script src="../../js/bootstrap.bundle.min.js"></script>
      <script type="module" src="../../../back_end/tareas/js/evaluar_trabajadores.js"></script>

</body>
</html>