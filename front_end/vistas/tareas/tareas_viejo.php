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
        
            

        <div id="resultados"    class="table-responsive">

        <?php  
            $fechaActual = null; 
        foreach($tareas as $index => $tarea){

                $fechaTarea = date('Y', strtotime($tarea['creado_en']));
                // Si el año cambia, cerramos el grupo anterior y abrimos uno nuevo
                if ($fechaTarea !== $fechaActual): 
                    if ($fechaActual !== null) echo ''; 
                    $fechaActual = $fechaTarea;
            ?>

            
            <!-- seccion para el anio  AFUERA DE LA TABLA-->
                <div class="selectNull" onclick="toggleSeccion('<?= $fechaActual ?>', this)" >
                    <div colspan="40" class="seccionFecha fw-bold " style="background-color:rgba(254, 255, 255, 1); cursor: pointer;">
                        <strong style="-webkit-text-fill-color:rgba(108, 108, 108, 1);" class="flecha-desplegable fs-5"><i class="fa-solid fa-angle-down"></i> <?= $fechaActual ?></strong> 
                </div>
                </div>



        

        <!-- 

        Data tables cdn

        f: Filtro (barra de búsqueda).

        l: Length (selector de cantidad de filas).

        i: Info (mostrando X de Y).

        p: Paginación.

        t: La tabla. -->
                
        <!-- tabla de tareas -->
            <table id="results" data-schema="tareas" data-tabla="tareas" class="table  table-hover table-sm" style="border-collapse: separate; border-spacing: 0 7px;">
            <thead  class="">
                <tr >
                <!-- <th><input type="checkbox" id="selectAll"></th> -->
                <th scope="col"></th>
                <th scope="col" class="text-muted">CODIGO </th>
                <th scope="col" class="text-center text-muted">TAREA</th>

                <th scope="col" class="text-center text-muted">ASIGNADO A</th>
                <th scope="col" class="text-center text-muted">ESTADO</th>
                <th scope="col" class="text-center text-muted">ESTATUS</th>
                <th scope="col"class="columna-ocultable text-center text-muted">PERIODO 
                    <a onclick="ocultar()"  id="ocultar_columna" ><img src="recursos/eye-slash-fill (1).svg"></a>
                </th>
            
                <th scope="col"></th>
                    <th scope="col"></th>
                    <!-- <th scope="col"></th> -->
                        <!-- archivos -->
                        <!-- <th scope="col">PDF</th> -->
                        <!-- archivos -->
                        <!-- <th scope="col">Sanciones</th> -->

                </tr>
            </thead>
            
            

        <tbody id="tareas-table-body">
                    
        <?php 
            $i = 1;

            foreach($tareas as $index => $tarea){

                $fechaTarea = date('Y', strtotime($tarea['creado_en']));
                // Si el año cambia, cerramos el grupo anterior y abrimos uno nuevo
                if ($fechaTarea !== $fechaActual): 
                    if ($fechaActual !== null) echo ''; 
                    $fechaActual = $fechaTarea;
            ?>

            
            <!-- seccion para el anio  -->
                <!-- <tr class="selectNull" onclick="toggleSeccion('<= $fechaActual ?>', this)" >
                    <td colspan="40" class="seccionFecha fw-bold " style="background-color:rgba(254, 255, 255, 1); cursor: pointer;">
                        <strong style="-webkit-text-fill-color:rgba(108, 108, 108, 1);" class="flecha-desplegable fs-5"><i class="fa-solid fa-angle-down"></i> <?= $fechaActual ?></strong> 
                    </td>
                </tr> -->
            <?php endif; ?>
            
            
        <tr  class="fila-contenido fechaDesplegable-<?= $fechaActual ?> shadow-sm  "
        data-id="<?= $tarea['id'] ?>"
        data-nombre="<?= htmlspecialchars($tarea['nombre_empresa'] . ' ' . $tarea['cantidad_tarea'], ENT_QUOTES, 'UTF-8') ?>"
        >

                        
                        <!-- <td ><input type="checkbox" class="task-checkbox"></td>         -->
                
                        <td  class="text-muted" ><?= $i++ ?></td>


                        <td data-columna="codigo_tarea" class="text-muted "><?= $tarea['codigo_tarea']?></td>

                        <td  data-columna="nombre_empresa" class=" fw-bold px-4" ><?= $tarea['nombre_empresa']. " ". $tarea['cantidad_tarea']?></td>    
                    
                        
                        <!-- asignado a -->

                         <?php
                         $valores= $tarea['nombre_empresa']." ".$tarea['cantidad_tarea'];
                         
                         $asignadoA=  mostrar_asignacion($pdo, $valores );?>
                        <td class="selectNull">
                            
                            <select class="form-select-sm rounded-pill text-center bg-transparent border-primary text-primary select-asignar" 
                                    data-tarea="<?= htmlspecialchars($tarea['nombre_empresa']." ".$tarea['cantidad_tarea']) ?>">
                                
                                    <?php foreach ($asignadoA as $asignado): ?>
                                    <option ata-columna="selecionado_usuario"  selected>
                                        <?= $asignado['usuario_asignado']; ?>
                                    </option>
                                <?php endforeach; ?>
                                    
                                <?php foreach ($usuarios as $escoger): ?>
                                    <option value="<?= htmlspecialchars($escoger['nombre_usuario']); ?>">
                                        <?= htmlspecialchars($escoger['nombre_usuario']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>



                        <td class="px-4 text-center" ><?= $tarea['estado']?></td>


                                <!-- >>>>>>>>>>>>>>>>>>>>>>>SECCION DE CAMBIO DE TAREA -->
                            <!--  seccion de estatus de la tarea -->
            
                            <td class="selectNull">
                            
                                <?php
                                    // 1. Usamos una lógica más robusta para asignar la clase
                                    $estatus_actual = trim(strtolower($tarea['estatus_nombre']?? '') );

                                    switch ($estatus_actual) {
                                        case 'esperando':
                                            $clase_bg = 'bg-warning text-dark'; // Amarillo (letras oscuras para que se lean)
                                            break;
                                        case 'procesando':
                                            $clase_bg = 'bg-primary text-white'; // Azul
                                            break;
                                        case 'finalizada':
                                            $clase_bg = 'bg-success text-white'; // Verde
                                            break;
                                        default:
                                            // Si entra aquí, es que el nombre no coincide con los anteriores
                                            $clase_bg = 'bg-secondary text-white'; 
                                            break;
                                    }
                                                                
                                            ?>

                                            <select  name="estatus" class="form-select form-select-sm <?= $clase_bg ?> estatus_selecionado" 
                                            style="font-weight: bold; width: auto; min-width: 120px; font-size: 0.8rem;">


                                                <option ata-columna="estatus_nombre_tarea"  selected>
                                                <?= $tarea['estatus_nombre'] ?>
                                                </option>
                                                
                                                <?php
                                                    if($tarea['estatus_nombre'] != 'procesando'){?>
                                                    <option data-columna='procesando' >procesando</option>;
                                                <?php 
                                            
                                                } ?>
                                                    <?php
                                                    if($tarea['estatus_nombre'] != 'finalizada'){?>
                                                        <option data-columna='finalizada' >finalizada</option>;
                                                <?php  }?> 
                                                
                                                <?php
                                                    if($tarea['estatus_nombre'] != 'esperando'){?>
                                                        <option data-columna='esperando'  >esperando</option>;
                                                <?php  
                                            
                                            }?>
                                        

                                            
                                            </select>
                            <!-- </form> -->
                            </td>

                            <!-- CREADO TAREA EN -->
                            <td class="columna-ocultable text-center "><?php 
                                if (!empty($tarea['creado_en'])) {
                                    $fecha = new DateTime($tarea['creado_en']);
                                    echo $fecha->format('Y-m-d'); // solo fecha
                                }
                                ?>
                            </td>
                    
                    

                    
                        

                                <td class="selectNull">
                                <!-- MODAL para cargar empleados a la empresa -->
                                <button type="button" class="btn badge bg-primary " data-bs-toggle="modal" data-bs-target="#subirEmpleadosModal<?= $tarea['id'] ?>">
                                    subir empleados
                                    </button>
                                </td>
                                <!-- boton para la facturacion  para pasarle la empresa -->
                            <form action="../facturacion/facturacion.php" method="get">
                                <input type="hidden" name="id_tarea" value="<?= $tarea['id'] ?>">
                                    <input type="hidden" name='empleado_empresa' value="<?=  $tarea['nombre_empresa'] . ' ' . $tarea['cantidad_tarea']?>">
                                    <td class="selectNull"> 
                                        
                                            <button type="submit" class="btn btn-primary "  >
                                            <img src="recursos/clipboard-check-fill.svg" style="filter: brightness(0) invert(1);" alt="">
                                        </button>

                                    </td>
                            </form>
                            
                                
<!--                          -->
                            
        
                </tr>
                    <!--  POP OUT PARA CARGAR LOS ARCHIVOS EXCEL -->
                    <!-- Modal de cargar los archivos en excel-->
                    <!-- <div class="modal fade" id="subirEmpleadosModal<?= $tarea['id'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel<?= $tarea['id'] ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                        <form action="../../../back_end/tareas/controladores/importar_datos_excel.php" method="POST" enctype="multipart/form-data">
                            
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel<?= $tarea['id'] ?>">Cargar Archivos Excel</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                
                                <div class="row">
                                    <div class="col">
                                        <label class="form-label">Excel INGRESOS (*)</label>
                                        <input type="file" name="excel_ingresos" class="form-control"  placeholder="ingresos">
                                    </div>
                                    <br>
                                    <div class="col">
                                        <label class="form-label">Excel EGRESOS (*)</label>
                                        <input type="file" name="excel_egresos" class="form-control" placeholder="egresos">
                                    </div>
                                </div>

                            </div>
                            <div class="modal-footer">

                                <input type="hidden" name="nombre_tarea" value="<?= $tarea['nombre_empresa'] . ' ' . $tarea['cantidad_tarea'] ?>">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-primary" name="enviar_ingresos">Importar Ingresos</button>
                            </div>
                        </form>
                        </div>
                    </div>
                    </div> -->


<!-- modal de carga -->
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

                    <!-- ingresos -->
                        <div class="col-6">
                            <div class="drop-zone border-2 py-4 text-center bg-light rounded-3" 
                                 style="cursor:pointer; border: 2px dashed #ccc;"
                                 onclick="document.getElementById('ingresoInput<?= $tarea['id'] ?>').click()">
                                
                                <i class="bi bi-file-earmark-excel text-success fs-2"></i>
                                <p class="small fw-bold mb-0">Excel Ingreso</p>
                                


                                <input type="file" name="excel_ingresos" id="ingresoInput<?= $tarea['id'] ?>" 
                                       class="d-none" accept=".xlsx, .xls" required 
                                       onchange="updateLabel(this, 'labelIngreso<?= $tarea['id'] ?>', '<?= $tarea['id'] ?>')">
                                

                                <span id="labelIngreso<?= $tarea['id'] ?>" class="badge bg-secondary mt-2 text-truncate d-inline-block" style="max-width: 100px;">Vacío</span>
                            </div>
                        </div>



<!-- egresos -->
                        <div class="col-6">


                            <div class="drop-zone border-2 py-4 text-center bg-light rounded-3" 
                                 style="cursor:pointer; border: 2px dashed #ccc;"
                                 onclick="document.getElementById('egresoInput<?= $tarea['id'] ?>').click()">
                                
                                <i class="bi bi-file-earmark-excel text-danger fs-2"></i>
                                <p class="small fw-bold mb-0">Excel Egreso</p>

                                

                                <input type="file" name="excel_egresos" id="egresoInput<?= $tarea['id'] ?>" 
                                       class="d-none" accept=".xlsx, .xls" required 
                                       onchange="updateLabel(this, 'labelEgreso<?= $tarea['id'] ?>', '<?= $tarea['id'] ?>')">


                                
                                <span id="labelEgreso<?= $tarea['id'] ?>" class="badge bg-secondary mt-2 text-truncate d-inline-block" style="max-width: 100px;">Vacío</span>
                            </div>
                        </div>
                    </div>
                    <div id="fileListContainer<?= $tarea['id'] ?>" class="mt-3 small"></div>
                </div>


                <div class="modal-footer border-0 px-4 pb-4">
                    <!-- <input type="hidden" name="id_tarea" value="<= $tarea['id'] ?>"> -->
                    
                    <input type="hidden" name="nombre_tarea" value="<?= $tarea['nombre_empresa'] . ' ' . $tarea['cantidad_tarea'] ?>">
                    
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold rounded-3 shadow-sm" name="enviar_ingresos" id="btnSubmit<?= $tarea['id'] ?>" disabled>
                        Importar Archivos Seleccionados
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
        
        <?php } ?>
                        
        

            

            

            </tbody>

            <!-- tabla para el menu al hacer el click izquierdo  -->

            </table>

            <?php  endif;  }?>
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




<!-- frameworks -->
        <script src="../../js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>


        <!-- sweet alert -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>





        <!-- archivos -->

        <script type="module" src="../../../back_end/tareas/js/buscar.js" defer></script>


        <script src="../../../back_end/tareas/js/permisos.js" defer></script>

        <script src="../../../back_end/tareas/js/empleados.js" defer></script>
        <script src="../../../back_end/tareas/js/automatizaciones.js" defer></script>



        <!-- DATATABLES -->
        <script>
            //SCrip para los datatables
            $(document).ready(function() {
           var table = $('#results').DataTable({

           searching: true, // Esto activa el filtro/buscador por defecto
//3. Desactivar el orden en columnas específicas
           "columnDefs": [
        { "orderable": false, "targets": [0, 5, 6] } // Desactiva el orden en la col 0, 5 y 6
    ],

            "dom": 'itp', // Quitamos la 'f' (busqueda) y la 'l' (longitud)
        "pageLength": 5, // Número de filas por defecto al cargar
        "lengthMenu": [5, 10, 25, 50, 100], // Las opciones que permitiremos, // Cambia el orden de las letras para mover los elementos
        "paging": false, // Esto elimina la paginación por completo


// columnas adjustar tamano
"autoWidth": false, // Paso obligatorio
    "columnDefs": [
        { "width": "5%", "targets": 0 },  // Columna # (muy pequeña)
        { "width": "25%", "targets": 1 }, // Columna Trabajador
        { "width": "15%", "targets": 2 }, // Columna Cédula
        { "width": "100px", "targets": 3 } // También puedes usar píxeles
    ],

                language: {
                    "decimal": "",
                    "emptyTable": "No hay información",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ entradas",
                    "infoEmpty": "Mostrando 0 a 0 de 0 entradas",
                    "infoFiltered": "(Filtrado de _MAX_ total entradas)",
                    "infoPostFix": "",
                    "thousands": ",",
                    "lengthMenu": "Mostrar _MENU_ entradas",

                    "loadingRecords": "Cargando...",

                    "processing": "Procesando...",

                    "search": "Buscar:",

                    "zeroRecords": "Sin resultados encontrados",

                    "paginate": {
                        "first": "Primero",
                        "last": "Ultimo",
                        "next": "Siguiente",
                        "previous": "Anterior"
                    }
                },



                initComplete: function () {
            this.api().columns().every(function () {
                var column = this;
                var select = $('<select class="form-select small"><option value=""></option></select>')
                    .appendTo($(column.footer()).empty())
                    .on('change', function () {
                        var val = $.fn.dataTable.util.escapeRegex($(this).val());
                        column.search(val ? '^' + val + '$' : '', true, false).draw();
                    });

                column.data().unique().sort().each(function (d, j) {
                    select.append('<option value="' + d + '">' + d + '</option>');
                });
            });
        }

            });

                // Vincular tu input externo con la tabla
            $('#miBuscador').on('keyup', function() {
                table.search(this.value).draw();
            });


            // --- Vincular tu SELECTOR DE RESULTADOS personalizado ---
    $('#miSelectorLongitud').on('change', function() {
        table.page.len($(this).val()).draw();
    });



    // Botón personalizado para ordenar por la columna 1 (Trabajador) de forma ascendente
    $('#btnOrdenarNombre').on('click', function() {
        table.order([1, 'asc']).draw();
    });

    // Botón para ordenar por la columna 4 (Fecha) de forma descendente
    $('#btnOrdenarFecha').on('click', function() {
        table.order([4, 'desc']).draw();
    });

//filtrar

    // Al hacer clic en un botón personalizado
$('#btnFiltrarActivos').on('click', function() {
    table.column(3).search('Activo').draw(); // Filtra la columna 4 (índice 3)
});

// Botón para limpiar filtros
$('#btnLimpiar').on('click', function() {
    table.search('').columns().search('').draw();
});

        });
        </script>

<!--                                                                                    ASIGNAR USUARIOS -->
<script >

    document.querySelectorAll('.select-asignar').forEach(select => {
    select.addEventListener('change', async function() {
        const usuarioId = this.value;
        const nombreTarea = this.getAttribute('data-tarea');

        if (!usuarioId) return; // Si elige "asignar" de nuevo, no hacer nada

        // Crear el cuerpo de la solicitud
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
                    text: `Tarea asignada a ${this.options[this.selectedIndex].text}`,
                    timer: 2000,
                    showConfirmButton: false
                });
                // Opcional: deshabilitar el select o cambiarle el color tras asignar
                this.classList.replace('border-primary', 'border-success');
            } else {
                throw new Error(datos.message);
            }
        } catch (error) {
            Swal.fire('Error', 'No se pudo asignar: ' + error.message, 'error');
        }
    });
});
</script>
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