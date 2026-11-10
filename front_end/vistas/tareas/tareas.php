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
// require_once(SESSION);


//VERIFICAR LA SECCION


// Si no existe la variable de sesión, el usuario no está validado


// $sessionUsuario = obtenerUsuariosSession($pdo, $usuario_input);
$tareas = mostrarTareas($pdo);
$usuarios =obtener_usuarios($pdo);

?>
<br>
<br>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tareas</title>

<!-- datatables -->
<link rel="stylesheet" href="../../../node_modules/datatables.net-dt/css/dataTables.dataTables.min.css">
<script src="../../../node_modules/jquery/dist/jquery.min.js"></script>
<script src="../../../node_modules/datatables.net/js/dataTables.min.js"></script>
<!-- fin datables -->



<!-- BOSTRAP -->
    <link href="../../css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <!-- !-- Latest Font Awesome CDN -->
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
          <link rel="stylesheet" href="../../publico/style_barra_tareas.css">

</head>
<body>

<style>
    /* El CSS usa ; al final de cada propiedad */
    table.dataTable thead > tr > th.sorting::before,
    table.dataTable thead > tr > th.sorting::after {
        content: "" !important;
        display: none !important;
    } /* Aquí cierra la llave del selector */

    table.dataTable thead > tr > th.sorting_asc {
        color: #0d6efd !important;
        border-bottom: 3px solid #0d6efd !important;
    }
</style>



      <style> .oculto{
        display:none;
    }
    .modal-backdrop {
  display: none !important;
}

th, td { min-width: 10rem; }
/* file */





    </style>

    <!-- IMPORTANTE DIV DE TODA LA PAGINA -->
<div style="margin-left: 13rem; padding: 10px; width: calc(100% - 200px);">









        <h1 style="margin-left:300px;">TAREAS</h1>


                <!-- FIN DE SECCION LATERAL -->

            



        <!-- barra lateral -->
        <!-- BARRA DE BUSQUEDA -->

            <!-- <div class="input-container" style="margin-left: 250px; padding: 20px; width: calc(100% - 250px);">
                <input type="search" id="busqueda" placeholder="busca una tarea">
            </div> 
            <div class="error_busqueda "  style="display:none;">
                <p></p>
            </div> <br> -->
            

            <div class="row mb-3 align-items-center">
    <div class="col-md-4  mb-4 float-end">
        <input type="text" id="miBuscador" class="form-control" placeholder="Buscar...">
    </div>

    <!-- <div class="col-md-2 ms-auto">
        <div class="input-group">
            <label class="input-group-text" for="miSelectorLongitud">Mostrar</label>
            <select class="form-select" id="miSelectorLongitud">
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>
    </div>
</div> -->
        

<div class="mb-3">
    <span class="text-muted small me-2">Ordenar por:</span>
    <button id="btnOrdenarNombre" class="btn btn-outline-primary btn-sm rounded-pill">Nombre (A-Z)</button>
    <button id="btnOrdenarFecha" class="btn btn-outline-primary btn-sm rounded-pill">Más recientes</button>
</div>


            <!-- 
            miBuscador -->

            <!-- BARRA DE BUSQUEDA  JEAN-->
        <!-- <div class="input-container main-content-wrapper" style="margin-left: 250px; padding: 30px; background-color: #f8f9fa; ">
            <div class="search-card p-4 bg-white shadow-sm rounded-4 mb-4">
                <h5 class="fw-bold text-dark mb-3">Filtrar Tareas</h5>
                <div class="input-group custom-search-group">
                    <span class="input-group-text bg-light border-end-0 text-muted">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="search" 
                        id="busqueda" 
                        class="form-control border-start-0 bg-light shadow-none" 
                        placeholder="Escribe el título, descripción o estado de la tarea..."
                        style="border-radius: 0 10px 10px 0;">
                </div>
                <div class="error_busqueda "  style="display:none;">
                <p></p>
            </div> 
            </div>
        </div> -->
        
            <div id="resultados" class="table-responsive">
    <table id="results" data-schema="tareas" data-tabla="tareas" class="table table-hover table-sm" style="border-collapse: separate; border-spacing: 0 7px; width: 100%;">
        <thead>
            <tr class="text-center">
                <th scope="col" style="width: 50px;">#</th>
                <th scope="col" class="text-muted text-start">CÓDIGO</th>
                <th scope="col" class="text-muted text-start">TAREA / EMPRESA</th>
                <th scope="col" class="text-muted">ASIGNADO A</th>
                <th scope="col" class="text-muted">ESTADO</th>
                <th scope="col" class="text-muted">ESTATUS</th>
                <th scope="col" class="text-muted">PERIODO 
                    <a onclick="ocultar()" id="ocultar_columna" style="cursor:pointer;"><img src="recursos/eye-slash-fill (1).svg" width="15"></a>
                </th>
                <th scope="col">ACCIONES</th>
                <th scope="col">FACTURA</th>
                
            </tr>
        </thead>
        
        <tbody id="tareas-table-body">
        <?php 
        $i = 1;
        $fechaActual = null; 

        foreach($tareas as $index => $tarea):
            $fechaTarea = date('Y', strtotime($tarea['creado_en']));

            // --- FILA DE SEPARACIÓN POR AÑO ---
            if ($fechaTarea !== $fechaActual): 
                $fechaActual = $fechaTarea;
        ?>
            <tr class="group-header no-data">
                <td colspan="9" class="seccionFecha fw-bold" style="background-color: #f8f9fa; border-left: 5px solid #0d6efd;">
                    <div style="cursor: pointer; padding: 5px 10px;" onclick="toggleSeccion('<?= $fechaActual ?>', this)">
                        <strong style="color: #6c6c6c;" class="fs-5">
                            <i class="fa-solid fa-angle-down"></i> <?= $fechaActual ?>
                        </strong> 
                    </div>
                </td>
            </tr>
        <?php endif; ?>

        <tr class="fila-contenido fechaDesplegable-<?= $fechaActual ?> shadow-sm align-middle" data-id="<?= $tarea['id'] ?>">
            <td class="text-muted text-center"><?= $i++ ?></td>
            <td class="text-muted"><?= $tarea['codigo_tarea'] ?></td>
            <td class="fw-bold px-3"><?= htmlspecialchars($tarea['nombre_empresa'] . " " . $tarea['cantidad_tarea']) ?></td> 
            
            <td class="text-center">
                <?php 
                $valores = $tarea['nombre_empresa']." ".$tarea['cantidad_tarea'];
                $asignadoA = mostrar_asignacion($pdo, $valores); 
                ?>
                <select class="form-select form-select-sm rounded-pill border-primary text-primary bg-transparent mx-auto" style="width: 140px;">
                    <?php foreach ($asignadoA as $asignado): ?>
                        <option selected><?= $asignado['usuario_asignado']; ?></option>
                    <?php endforeach; ?>
                    <?php foreach ($usuarios as $escoger): ?>
                        <option value="<?= htmlspecialchars($escoger['nombre_usuario']); ?>"><?= htmlspecialchars($escoger['nombre_usuario']); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>

            <td class="text-center"><?= $tarea['estado'] ?></td>

            <td class="text-center">
                <?php
                $estatus_actual = trim(strtolower($tarea['estatus_nombre']?? ''));
                $clase_bg = match($estatus_actual) {
                    'esperando' => 'bg-warning text-dark',
                    'procesando' => 'bg-primary text-white',
                    'finalizada' => 'bg-success text-white',
                    default => 'bg-secondary text-white',
                };
                ?>
                <select class="form-select form-select-sm <?= $clase_bg ?> fw-bold mx-auto" style="width: 130px; font-size: 0.75rem;">
                    <option selected><?= $tarea['estatus_nombre'] ?></option>
                    <?php if($tarea['estatus_nombre'] != 'procesando') echo '<option>procesando</option>'; ?>
                    <?php if($tarea['estatus_nombre'] != 'finalizada') echo '<option>finalizada</option>'; ?>
                    <?php if($tarea['estatus_nombre'] != 'esperando') echo '<option>esperando</option>'; ?>
                </select>
            </td>

            <td class="text-center"><?= !empty($tarea['creado_en']) ? (new DateTime($tarea['creado_en']))->format('Y-m-d') : '-' ?></td>

            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#subirEmpleadosModal<?= $tarea['id'] ?>">
                    Subir nómina
                </button>
            </td>

            <td class="text-center"> 
                <form action="../facturacion/facturacion.php" method="get" class="m-0">
                    <input type="hidden" name="id_tarea" value="<?= $tarea['id'] ?>">
                    <input type="hidden" name='empleado_empresa' value="<?= $tarea['nombre_empresa'] . ' ' . $tarea['cantidad_tarea'] ?>">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <img src="recursos/clipboard-check-fill.svg" style="filter: brightness(0) invert(1);" width="16">
                    </button>
                </form>
            </td>
            <td></td>
            <td></td>


        </tr>

        <div class="modal fade" id="subirEmpleadosModal<?= $tarea['id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <form action="../../../back_end/tareas/controladores/importar_datos_excel.php" method="POST" enctype="multipart/form-data">
                        <div class="modal-header border-0 px-4 pt-4">
                            <div>
                                <h5 class="fw-bold mb-0 text-primary">Carga de Nómina</h5>
                                <small class="text-muted">Selecciona los archivos de Ingresos y Egresos</small>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body px-4">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="drop-zone border-2 py-4 text-center bg-light rounded-3" style="cursor:pointer; border: 2px dashed #ccc;" onclick="document.getElementById('ingresoInput<?= $tarea['id'] ?>').click()">
                                        <i class="fa-solid fa-file-excel text-success fs-2"></i>
                                        <p class="small fw-bold mb-0">Excel Ingreso</p>
                                        <input type="file" name="excel_ingresos" id="ingresoInput<?= $tarea['id'] ?>" class="d-none" accept=".xlsx, .xls" required onchange="updateLabel(this, 'labelIngreso<?= $tarea['id'] ?>', '<?= $tarea['id'] ?>')">
                                        <span id="labelIngreso<?= $tarea['id'] ?>" class="badge bg-secondary mt-2 text-truncate d-inline-block" style="max-width: 100px;">Vacío</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="drop-zone border-2 py-4 text-center bg-light rounded-3" style="cursor:pointer; border: 2px dashed #ccc;" onclick="document.getElementById('egresoInput<?= $tarea['id'] ?>').click()">
                                        <i class="fa-solid fa-file-excel text-danger fs-2"></i>
                                        <p class="small fw-bold mb-0">Excel Egreso</p>
                                        <input type="file" name="excel_egresos" id="egresoInput<?= $tarea['id'] ?>" class="d-none" accept=".xlsx, .xls" required onchange="updateLabel(this, 'labelEgreso<?= $tarea['id'] ?>', '<?= $tarea['id'] ?>')">
                                        <span id="labelEgreso<?= $tarea['id'] ?>" class="badge bg-secondary mt-2 text-truncate d-inline-block" style="max-width: 100px;">Vacío</span>
                                    </div>
                                </div>
                            </div>
                            <div id="fileListContainer<?= $tarea['id'] ?>" class="mt-3 small"></div>
                        </div>
                        <div class="modal-footer border-0 px-4 pb-4">
                            <input type="hidden" name="nombre_tarea" value="<?= $tarea['nombre_empresa'] . ' ' . $tarea['cantidad_tarea'] ?>">
                            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold rounded-3 shadow-sm" name="enviar_ingresos" id="btnSubmit<?= $tarea['id'] ?>" disabled>
                                Importar Archivos Seleccionados
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- style de carga de archivos css -->

<style>
    .drop-zone {
    border: 2px dashed #dee2e6;
    border-radius: 15px;
    background-color: #fafafa;
    transition: all 0.3s ease;
    cursor: pointer;
}

.drop-zone:hover, .drop-zone.dragover {
    background-color: #f1f4ff;
    border-color: #0d6efd;
}

.file-item {
    border: 1px solid #eee;
    border-radius: 12px;
    padding: 12px;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
       transition: transform 0.2s ease;
}


.drop-zone {
    border: 2px dashed #0d6efd;
    background-color: #f8fbff;
    transition: 0.3s;
}

.file-item {
    border-left: 4px solid #198754 !important; /* Verde Excel */
}

.animate__fadeIn {
    animation: fadeIn 0.4s;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

</style>
<!-- modal carga script -->
<script>
// MODAL DE SUBIR EMPLEADOS
function updateLabel(input, labelId, tareaId) {
    const label = document.getElementById(labelId);
    
    // Si hay un archivo seleccionado, mostramos el nombre
    if (input.files && input.files.length > 0) {
        label.innerText = input.files[0].name;
        label.classList.replace('bg-secondary', 'bg-success');
    } else {
        label.innerText = "Vacío";
        label.classList.replace('bg-success', 'bg-secondary');
    }

    // Lógica para habilitar el botón de envío
    const ingreso = document.getElementById('ingresoInput' + tareaId).files.length;
    const egreso = document.getElementById('egresoInput' + tareaId).files.length;
    const btn = document.getElementById('btnSubmit' + tareaId);

    // Solo se habilita si AMBOS inputs tienen un archivo
    if (ingreso > 0 && egreso > 0) {
        btn.disabled = false;
    } else {
        btn.disabled = true;
    }
}





// ESTO ES PARA SUBIR ARCHIVOS

// // Objeto global para persistir los archivos de cada modal de forma independiente
// let storageFiles = {};

// document.addEventListener('change', function(e) {
//     if (e.target && e.target.id.startsWith('excelInput')) {
//         const input = e.target;
//         const modalId = input.id.replace('excelInput', '');
//         const container = document.getElementById('fileListContainer' + modalId);
//         const btnSubmit = document.getElementById('btnSubmit' + modalId);

//         // Inicializar el "almacén" para este modal si no existe
//         if (!storageFiles[modalId]) {
//             storageFiles[modalId] = new DataTransfer();
//         }

//         // 1. Añadimos los archivos recién seleccionados al almacén global
//         Array.from(input.files).forEach(file => {
//             // Evitar duplicados por nombre
//             const names = Array.from(storageFiles[modalId].files).map(f => f.name);
//             if (!names.includes(file.name)) {
//                 storageFiles[modalId].items.add(file);
//             }
//         });

//         // 2. Sincronizamos el input real con lo que tenemos en memoria
//         input.files = storageFiles[modalId].files;

//         // 3. Dibujamos las tarjetas en la pantalla
//         renderizarTarjetas(modalId);
//     }
// });

// function renderizarTarjetas(modalId) {
//     const container = document.getElementById('fileListContainer' + modalId);
//     const btnSubmit = document.getElementById('btnSubmit' + modalId);
//     const archivos = storageFiles[modalId].files;

//     container.innerHTML = ''; // Limpiar lista visual

//     if (archivos.length > 0) {
//         btnSubmit.disabled = false;

//         Array.from(archivos).forEach((file, index) => {
//             // Creamos el diseño exacto de la imagen que enviaste
//             container.innerHTML += `
//                 <div class="file-item p-3 mb-2 border rounded-3 d-flex align-items-center bg-white shadow-sm position-relative animate__animated animate__fadeIn">
//                     <div class="file-icon bg-light rounded-circle p-2 me-3">
//                         <i class="bi bi-file-earmark-spreadsheet-fill text-success fs-4"></i>
//                     </div>
//                     <div class="flex-grow-1">
//                         <div class="d-flex justify-content-between align-items-center mb-1">
//                             <span class="small fw-bold text-dark text-truncate" style="max-width: 200px;">${file.name}</span>
//                             <button type="button" class="btn-close" style="font-size: 0.6rem" 
//                                     onclick="removerDelLista('${modalId}', ${index})"></button>
//                         </div>
//                         <div class="progress mt-1" style="height: 6px; border-radius: 10px;">
//                             <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" style="width: 100%"></div>
//                         </div>
//                         <div class="d-flex justify-content-between mt-1">
//                              <small class="text-muted" style="font-size: 0.7rem">${(file.size / 1024).toFixed(1)} KB</small>
//                              <small class="text-success fw-bold" style="font-size: 0.7rem">Listo para importar</small>
//                         </div>
//                     </div>
//                 </div>
//             `;
//         });
//     } else {
//         btnSubmit.disabled = true;
//     }
// }

// function removerDelLista(modalId, index) {
//     const dt = new DataTransfer();
//     const input = document.getElementById('excelInput' + modalId);
//     const { files } = storageFiles[modalId];

//     // Reconstruimos el almacén omitiendo el índice eliminado
//     for (let i = 0; i < files.length; i++) {
//         if (i !== index) dt.items.add(files[i]);
//     }

//     // Actualizamos memoria e input
//     storageFiles[modalId] = dt;
//     input.files = dt.files;

//     // Refrescamos la vista
//     renderizarTarjetas(modalId);
// }
</script>



                <!--  ESTE ES EL MODAL PARA CARGAR LAS OPCIONES COMO ELIMINAR ARCHIVAR O ELIMINAR AL HACER CLICK DERECHO -->

        <!-- <br><br><br>   -->





<!-- fin div toda la pagina -->
</div>








        <!-- Menú contextual oculto -->
        <div id="contextMenu" class="dropdown-menu">
        <!-- <button class="dropdown-item" data-action="editar">Editar</button> -->
        <button class="dropdown-item" data-action="eliminar">Eliminar  <img src="../empresas/recursos/trash3.svg" alt=""></button>
        <!-- <button class="dropdown-item" data-action="achivar">archivar</button> -->
        </div>


        <!-- confirmar cambios  click -->
        <!-- Modal para editar -->
        <div class="modal fade" id="editarModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm"> <!-- modal-sm para hacerlo pequeño -->
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar fila</h5> 
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

            <!-- form de editar -->
            <form id="formEditar">

                <div class="mb-3">
                    <label for="campoNombre" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="campoNombre">
                </div>

                <div class="mb-3">
                    <label for="campoId" class="form-label">ID</label>
                    <input type="text" class="form-control" id="campoId" readonly>
                </div>

                <button type="submit" class="btn btn-primary w-100">Guardar cambios</button>
            
                </form>
            
            </div>
            </div>
        </div>
        </div>




        <!-- Modal de confirmación  para eliminar un valor-->
        <div class="modal fade" id="confirmarEliminarModal" tabindex="-1" aria-hidden="true">
        
        <div class="modal-dialog modal-sm">
            <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Confirmar eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p id="mensajeEliminar"></p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmarEliminar">Eliminar</button>
            </div>

            </div>
        </div>
        </div>


<style>
    /* Elimina el padding extra que mete DataTables y que descuadra todo */
table.dataTable thead th, 
table.dataTable thead td {
    padding: 10px 5px !important;
    white-space: nowrap; /* Evita que los títulos se rompan en dos líneas */
}

/* Alinea los separadores de año a la izquierda */
.seccionFecha {
    text-align: left !important;
    background-color: #f8f9fa !important;
}
</style>

<!-- frameworks -->
        <script src="../../js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>


        <!-- sweet alert -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>





        <!-- archivos -->

        <script type="module" src="../../../back_end/tareas/js/buscar.js" defer></script>


        <script src="../../../back_end/tareas/js/permisos.js" defer></script>

        <script src="../../../back_end/tareas/js/empleados.js" defer></script>
        <script src="../../../back_end/tareas/js/automatizaciones.js" defer></script>


<script>$(document).ready(function() {
    // 1. CONFIGURACIÓN DE DATATABLES
    // -------------------------------------------------------------------------
    if ($.fn.DataTable.isDataTable('#results')) {
        $('#results').DataTable().destroy();
    }

    var table = $('#results').DataTable({
        "paging": true,
        "pageLength": 10,
        "ordering": false, // Mantiene los grupos de años en su posición original
        "autoWidth": false,
        "dom": 'ltip',
        "columnDefs": [
            { "defaultContent": "", "targets": "_all" }
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
        }
    });

    // Buscador externo vinculado a la tabla
    $('#miBuscador').on('keyup', function() {
        table.search(this.value).draw();
    });


    // 2. ASIGNACIÓN DINÁMICA DE USUARIOS (FETCH)
    // -------------------------------------------------------------------------
    // Usamos delegación de eventos por si DataTables redibuja la tabla
    $(document).on('change', '.select-asignar', async function() {
        const usuarioId = this.value;
        const nombreTarea = this.getAttribute('data-tarea');

        if (!usuarioId) return;

        const formData = new FormData();
        formData.append('accion', 'asignar_tarea_usuario');
        formData.append('usuario_asignado', usuarioId);
        formData.append('nombre_tarea', nombreTarea);

        try {
            const response = await fetch('../../../back_end/tareas/src/funciones_jason.php', {
                method: 'POST',
                body: formData
            });

            const datos = await response.json();

            if (datos.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Asignado!',
                    text: `Tarea asignada correctamente`,
                    timer: 2000,
                    showConfirmButton: false
                });
                this.classList.replace('border-primary', 'border-success');
            } else {
                throw new Error(datos.message);
            }
        } catch (error) {
            Swal.fire('Error', 'No se pudo asignar: ' + error.message, 'error');
        }
    });
});

// 3. FUNCIONES GLOBALES (Fuera del Document Ready)
// -------------------------------------------------------------------------

/**
 * Controla el plegado y desplegado de las filas por año
 */
function toggleSeccion(anio, el) {
    // Seleccionamos las filas con la clase correspondiente
    var filas = $('.fechaDesplegable-' + anio);
    
    // Alternamos visibilidad
    filas.toggle(); 
    
    // Animamos/Cambiamos el icono de la flecha
    $(el).find('i').toggleClass('fa-angle-down fa-angle-right');
}

/**
 * Actualiza el texto de los inputs de archivo en los modales
 */
function updateLabel(input, id) {
    if (input.files && input.files[0]) {
        const fileName = input.files[0].name;
        // Si tienes un span para el nombre del archivo, puedes actualizarlo aquí
        console.log("Archivo seleccionado para tarea ID " + id + ": " + fileName);
        
        // Ejemplo: habilitar botón de subir si ambos archivos están presentes (opcional)
        // checkFilesReady(id); 
    }
}</script>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

    
        if (resultado.status === "success" || resultado.success === true) {
            // alert("¡Pago registrado con éxito!");
            // LANZAMOS SWEET ALERT
            Swal.fire({
                title: '¡empleados subidos!',
                text: resultado.message,
                icon: 'success',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#3085d6',
                timer: 3000, // Se cierra solo en 3 seg
                timerProgressBar: true
            }).then((result) => {
                // CUANDO EL USUARIO CIERRE EL ALERT O PASE EL TIEMPO
                window.location.href = "login.php"; 
            });

        } else {
            // Sweet Alert para errores
            Swal.fire({
                title: 'Error',
                text: resultado.message,
                icon: 'error'
            });
        }
</script>
</body>
</html>