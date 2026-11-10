<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../login/login.php");
    exit();
}
require_once(__DIR__ . '/../../../back_end/config.php');
require_once(FUNCIONES_TAREAS);
require_once(BARRA_LATERAL);

// Obtención de datos desde la base de datos
$empresas = mostrarEmpresas($pdo);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Empresas | IVSS</title>
    
    <link rel="stylesheet" href="../../../node_modules/datatables.net-dt/css/dataTables.dataTables.min.css">

    <script src="../../../node_modules/jquery/dist/jquery.min.js"></script>
    <script src="../../../node_modules/datatables.net/js/dataTables.min.js"></script>
    <link href="../../css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="../../publico/style_barra_tareas.css">
    <link rel="stylesheet" href="style.css"> 
    <link rel="icon" href="../ivss.png" type="image"> 
</head>
<body>

<main class="main-content">

    <div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center mt-3">
        <div class="mb-3 mb-md-0">
            <h1 class="page-title"><i class="fas fa-building me-2"></i>Directorio de Empresas</h1>
            <p class="text-muted small mb-0 mt-1">Administración, registro y control de sanciones patronales.</p>
        </div>
        <button type="button" class="btn btn-nueva-empresa" data-bs-toggle="modal" data-bs-target="#modalNuevaEmpresa">
            <i class="fas fa-plus-circle me-1"></i> Nueva Empresa
        </button>
    </div>

    <div class="card shadow-sm border-0 rounded-3 mt-4">
        <div class="card-body p-0 p-3">    
            <div class="table-responsive" >
                <table  data-schema="empresas" data-tabla="empresas" class="table table-hover align-middle mb-0 table-borderless" id="example" style="border-collapse: separate; border-spacing: 0 7px;">
                    <thead>
                        <tr>
                            <th class="ps-4 text-muted small text-uppercase">#</th>
                            <th class="text-muted small text-uppercase">Número patronal</th>
                            <th class="text-muted small text-uppercase">Empresa</th>
                            <th class="text-center text-muted small text-uppercase">Dirección</th>
                            <th class="text-center text-muted small text-uppercase">Ubicación (Estado)</th>
                            <th class="text-center text-muted small text-uppercase">Deuda</th>
                            <th class="text-center text-muted small text-uppercase">Industria</th>
                            <th class="text-center text-muted small text-uppercase">Estado</th>
                            <th class="text-center text-muted small text-uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $i = 1;
                        foreach($empresas as $empresa): ?>
                        <tr class="shadow-sm bg-white rounded" data-id="<?=  $empresa['id'] ?>">
                            <th scope="row" class="ps-4 text-muted"><?= $i++ ?></th>
                            <td data-columna="numero_patronal" class="fw-bold text-primary"><?= htmlspecialchars($empresa['numero_patronal']?? '') ?></td>
                            <td data-columna="nombre_empresa" class="fw-bold text-dark"><?= htmlspecialchars($empresa['nombre_empresa'] ?? '') ?></td>
                            <td data-columna="direccion" class="text-center text-muted small"><?= htmlspecialchars($empresa['direccion']?? '') ?></td>
                            
                            <td class="text-center">
                                <span class="badge rounded-pill bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-3">
                                    <i class="fas fa-map-marker-alt me-1"></i>

                                    <!-- LO COMENTE PORQUE NO LO TENGO EN LA BASE DE DATOS -->
                                    <?php  //htmlspecialchars($empresa['ubicacion_estado'] ?: 'No especificado') ?>
                                </span>
                            </td>

                            <td data-columna="estado_deuda" class="text-center fw-semibold text-danger"><?= htmlspecialchars($empresa['estado_deuda']?? '') ?></td>
                            <td data-columna="industria" class="text-center text-muted"><span class="badge bg-light text-dark border"><?= htmlspecialchars($empresa['industria']?? '') ?></span></td>
                            <td class="text-center text-muted">
                                <?php if($empresa['estado'] == 'Activo'): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">
                                        <i class="fas fa-circle-check me-1 small"></i><?= htmlspecialchars($empresa['estado']?? '') ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary rounded-pill px-3 py-2"><?= htmlspecialchars($empresa['estado']?? '') ?></span>
                                <?php endif; ?>
                            </td>
                            
                            <td class="text-center">
                                <form action="../../../back_end/tareas/pruebas_tareas/automatizaciones.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="accion_tarea_crear" value="crear_tarea">
                                    <input type="hidden" name="id_empresa" value="<?= $empresa['id'] ?>">
                                    <input type="hidden" name="nombre_empresa" value="<?= $empresa['nombre_empresa'] ?>">
                                    <input type="hidden" name="estado_empresa" value="<?= $empresa['estado'] ?>">
                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 shadow-sm" title="Aplicar Sanción">
                                        <i class="fas fa-gavel me-1"></i> Sancionar
                                    </button>
                                </form>

                                <a href="../../../back_end/empresas/src/eliminar_empresa.php?id=<?= $empresa['id'] ?>" class="btn-eliminar btn btn-light btn-sm rounded-circle ms-1 shadow-sm text-danger" title="Eliminar Empresa">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</main>

<div class="modal fade" id="modalNuevaEmpresa" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-modal">
            <div class="modal-header custom-modal-header d-flex justify-content-between align-items-center">
                <h5 class="modal-title custom-modal-title"><i class="fas fa-building-circle-check me-2"></i>Registrar Nueva Empresa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="../../../back_end/empresas/src/funcion_empresa.php" method="POST">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Número Patronal</label>
                            <input type="text" name="patronal_empresa" class="form-control bg-light" required 
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '')" 
                                   placeholder="Ej: 012345678">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nombre de la Empresa</label>
                            <input type="text" name="nombre_empresa" class="form-control" required placeholder="Razón Social">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Dirección Fiscal</label>
                            <input type="text" name="direccion_empresa" class="form-control" required placeholder="Av. Principal...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sector / Industria</label>
                            <input type="text" name="industria_empresa" class="form-control" required placeholder="Ej: Tecnología">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Estado (Ubicación)</label>
                            <select name="ubicacion_estado" class="form-select" required>
                                <option value="" selected disabled>Seleccione...</option>
                                <option value="Amazonas">Amazonas</option>
                                <option value="Anzoátegui">Anzoátegui</option>
                                <option value="Apure">Apure</option>
                                <option value="Aragua">Aragua</option>
                                <option value="Barinas">Barinas</option>
                                <option value="Bolívar">Bolívar</option>
                                <option value="Carabobo">Carabobo</option>
                                <option value="Cojedes">Cojedes</option>
                                <option value="Delta Amacuro">Delta Amacuro</option>
                                <option value="Distrito Capital">Distrito Capital</option>
                                <option value="Falcón">Falcón</option>
                                <option value="Guárico">Guárico</option>
                                <option value="Lara">Lara</option>
                                <option value="Mérida">Mérida</option>
                                <option value="Miranda">Miranda</option>
                                <option value="Monagas">Monagas</option>
                                <option value="Nueva Esparta">Nueva Esparta</option>
                                <option value="Portuguesa">Portuguesa</option>
                                <option value="Sucre">Sucre</option>
                                <option value="Táchira">Táchira</option>
                                <option value="Trujillo">Trujillo</option>
                                <option value="Vargas">Vargas</option>
                                <option value="Yaracuy">Yaracuy</option>
                                <option value="Zulia">Zulia</option>
                            </select>
                        </div>
                    </div>
                    <!-- <input type="hidden" name="estado_empresa" value="Activo"> -->
                </div>
                <div class="modal-footer border-0 pb-4 pe-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" name="accionEmpresa" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm">Guardar Registro</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="../../js/bootstrap.bundle.min.js"></script>

<!-- para sweet alert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>





document.addEventListener('DOMContentLoaded', function() {
    // 1. Manejo de Alertas vía URL
    const urlParams = new URLSearchParams(window.location.search);
    
    if (urlParams.get('status') === 'success') {
        Swal.fire({
            icon: 'success',
            title: '¡Registro Exitoso!',
            text: 'La empresa ha sido guardada en el sistema.',
            confirmButtonColor: '#198754',
            timer: 3000,
            showConfirmButton: false
        }).then(() => {
            window.history.replaceState({}, document.title, window.location.pathname);
        });
    } else if (urlParams.get('status') === 'error') {
        Swal.fire({
            icon: 'error',
            title: 'Error al registrar',
            text: urlParams.get('msg') || 'Verifique los datos e intente de nuevo.',
            confirmButtonColor: '#d33'
        }).then(() => {
            window.history.replaceState({}, document.title, window.location.pathname);
        });
    }

    // 2. Alerta para Confirmar Eliminación
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-eliminar')) {
            e.preventDefault();
            const anchor = e.target.closest('.btn-eliminar');
            const url = anchor.getAttribute('href');

            Swal.fire({
                title: '¿Eliminar Empresa?',
                text: "Esta acción borrará los datos permanentemente.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash"></i> Sí, eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        }
    });

    // Inicializar DataTable
    $('#example').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }
    });
});
</script>

<script>
// TAREA CREADA 

const urlParams = new URLSearchParams(window.location.search);
if (urlParams.get('status') === 'tareaCreada') {
    Swal.fire({
        title: '¡Tarea Creada!',
        text: 'La sanción se ha registrado correctamente.',
        icon: 'success',
        confirmButtonColor: '#dc3545' // Color del botón danger de Bootstrap
    });
}

</script>

        <script src="../../../back_end/tareas/js/permisos.js" defer></script>

</body>
</html>