<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../login/login.php");
    exit();
}

require_once(__DIR__ . '/../../../back_end/config.php');
require_once (FUNCIONES_TAREAS);
require_once (EVALUAR_TRABAJADORES);
require_once (CONCURRENCIA);
require_once(FUNCIONES_FACTURACION);
require_once(BARRA_LATERAL);
require_once(ORDEN_PAGO);


// require_once('../Ivss/front_end/vistas/barra_lateral/barraLateral.php');

// $resultado=  mostrar_trabajadores_ingresos($conn);

// valor que viene de tareas.php
//$nombre_empresa =$_GET['empleado_empresa'];

$nombre_empresa_sancion= $_GET['empleado_empresa'] ?? $_GET['empresa'];



echo $nombre_empresa_sancion;
// esto es 1 por un problema en lA concurrencia que me pone 1
// $nombre_empresa_sancion= 1;

// echo "<h1>Empleados de la empresa: " . htmlspecialchars($nombre_empresa) . "</h1>";


/// el array de destructura para ser usado ambos ya que ambos se pasaron como return
['datos_completos' => $resultado_unificado, 'fecha_formateada' => $fecha] = mostrar_trabajadores_ingresos($pdo, $nombre_empresa_sancion);

// $resultadoEgresos = mostrar_trabajadores_egresos($pdo, $nombre_empresa_sancion);
// $egresos = mostrar_trabajadores_egresos($pdo, $nombre_empresa);
$evaluar_trabajadores_ingresos = verificarExtemporaneos($resultado_unificado, $fechasFestivas);
// $evaluar_trabajadores_egresos = verificarExtemporaneos($resultadoEgresos, $fechasFestivas);





$otrasSanciones = mostrar_otras_sanciones($pdo, $nombre_empresa_sancion);

$totalSancionesExtra = 0;

$totalSancionesExtra = array_sum(array_column($otrasSanciones, 'total_sanciones'));




   //""""""""""""""""""""""""""""TRABAJAR EN QUE SE VEA SOLAMENTE LA FECHA DE ACTUALIZACION DE UNA DETERMINADA EMPRESA EN FUNCIONES
    // 1. Crear el objeto DateTime a partir de la cadena de texto
    $fecha_actualizacion = new DateTime($fecha ?? 'now');;

    //aqui esta es la fecha formateada de la ultimo excel subido por el usuario de esta empresa
    $fecha_formateada = $fecha_actualizacion->format('d-m-Y ');
    $hora_formateada = $fecha_actualizacion->format('h:i A');

    // ESTO muestra la ultima fecha de actualizacion desde que se subio el ingreso de esta empresa en particular

    // echo "<h4>Ultima actualizacion: ". htmlspecialchars($fecha_formateada). "</h4>";
    // echo "<h5>". htmlspecialchars($hora_formateada). "</h5>";




  $montoSancion = mostrar_monto_sancion($pdo, $nombre_empresa_sancion);








  //            ORDEN PAGO

  $orden_pago_sancion= mostrarOrdenPago($pdo, $nombre_empresa_sancion);


  //  REGISTRAR SANCIONES EN ORDEN DE PAGOS
  $orden_pago_registrado = mostrarPagoRegistrado ($pdo, $nombre_empresa_sancion);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
         		
  <link rel="stylesheet" href="facturacion.css">

<!-- datatables -->
<link rel="stylesheet" href="../../../node_modules/datatables.net-dt/css/dataTables.dataTables.min.css">
<script src="../../../node_modules/jquery/dist/jquery.min.js"></script>
<script src="../../../node_modules/datatables.net/js/dataTables.min.js"></script>
<!-- fin datables -->


    <!-- bootstrap -->
    <link href="../../css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
<!-- !-- Latest Font Awesome CDN -->
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="../../publico/style_barra_tareas.css">

  </head>

<body>


<!-- IMPORTANTE DIV DE TODA LA PAGINA -->
<div style="margin-left: 13rem; padding: 10px; width: calc(100% - 200px);">









    <h2 class="  display-6 text-primary"> Facturación</h2>
    <br>


    <!-- titulo de la pagina -->
     <h2 class="fw-bold fw-bold "> <?= htmlspecialchars($nombre_empresa_sancion?? '') ?></h2>

    <br>


<!-- IMPORTANTE div de el container de toda la pagina -->
<div class="container-fluid mt-5">
  <!-- div de ordenamiento de toda la pagina -->
    <div class="row">
        
    <!-- div de la mitad izquierda de la pagina -->
        <div class="col-12 col-lg-9">

                      

              <!-- error de orden de pago -->
              <!-- esto es para las ordenes de pago si no pasa el valor en bs devueve esto. y lo muesta -->
              <?php if (isset($_GET['error']) && $_GET['error'] == 'valor_cero'): ?>
                  <div id="alerta-autohide" class="alert alert-danger alert-dismissible fade show" role="alert">
                      No se puede si el monto en bs es zero
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>


                  <script>
                    // esto es para que desaparezca despues de un tiempo.
                      var alertNode = document.getElementById('alerta-autohide');
                      var alertInstance = new bootstrap.Alert(alertNode);
                      
                      setTimeout(function() {
                          alertInstance.close();
                      }, 5000); // 5 segundos
                  </script>

              <?php endif; ?>
              <!-- fin de error de orden de pago -->



          <!-- MODALES  -->
          <!-- MODAL BARRA LATERAL  de otras sanciones-->

          <div class="modal fade  modal-dialog-scrollable" id="modalConcurrir" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content" > 
                
                <div class="modal-header">
                  <h5 class="modal-title">Otras sanciones</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                

                <!-- cuerpo del modal lateral de otra sanciones -->
                <div class="modal-body d-flex flex-column" > 
                  

          <!-- EL SELECT DEL Modal lateral de otra sanciones -->
                  <div class="mb-3">
                    <label class="form-label text-muted small fw-bold">Sanciones extra</label>
                    
                    <div class="dropdown custom-multiselect" id="industryDropdown">
                      <button class="form-select d-flex justify-content-between align-items-center border-success w-100"
                              type="button" 
                              id="dropdownSelect" 
                              data-bs-toggle="dropdown" 
                              data-bs-auto-close="outside"
                              aria-expanded="false">
                          <span id="selectedCountText">Seleciona las opciones</span>
                      </button>

                      <ul class="dropdown-menu w-100 shadow-sm" aria-labelledby="dropdownSelect">
                      <!-- Valores de los checkbox -->
                      <li>
                          <div class="dropdown-item py-2">
                            <div class="form-check">
                              <input class="form-check-input ind-checkbox" type="checkbox" value="inscripcion" id="inscripcion">
                              <label class="form-check-label w-100" for="Inscripcion">Inscripcion</label>
                            </div>
                          </div>
                        </li>
                      
                        <li>
                          <div class="dropdown-item py-2">
                            <div class="form-check">
                              <input class="form-check-input ind-checkbox" type="checkbox" value="tachaduras" id="tachaduras">
                              <label class="form-check-label w-100" for="Tachaduras">Tachaduras</label>
                            </div>
                          </div>
                        </li>
                      
                        <li>
                          <div class="dropdown-item py-2">
                            <div class="form-check">
                              <input class="form-check-input ind-checkbox" type="checkbox" value="obstaculizacion" id="obstaculizacion">
                              <label class="form-check-label w-100" for="obstaculizacion">obstaculizacion</label>
                            </div>
                          </div>
                        </li>


                            <!-- novedades -->
                        <li>
                          <div class="dropdown-item py-2">
                            <div class="form-check">
                              <input class="form-check-input ind-checkbox" type="checkbox" value="novedades" id="novedades">
                              <label class="form-check-label w-100" for="novedades">novedades</label>
                            </div>
                          </div>
                        </li>

                              <!-- novedades -->
                        <li>
                          <div class="dropdown-item py-2">
                            <div class="form-check">
                              <input class="form-check-input ind-checkbox" type="checkbox" value="noEnterados" id="noEnterados">
                              <label class="form-check-label w-100" for="noEnterados">Intereses no enterados</label>
                            </div>
                          </div>
                        </li>


                      </ul>
                      <!-- div dropdown -->
                    </div>     
              
                </div>
                <!-- div para novedades para usar en el futuro -->
                      <!-- <div class=" row align-items-end" style="margin-top: 10rem;">
                
                          <div class="checkBoxNovedades col  justify-content-end  ">
                              
                                
                          </div>
                      </div> -->


                <div class=" labelTachaduras row align-items-end" style="margin-top: 30rem;">
                
                    <div class="tachaduras_descripcion col  justify-content-end  ">     
                          
                    </div>
                </div>
            

                <!-- AQUI VAN LAS OPCIONES SELECIONADAS en la barra desplegable que se hace click en sanciones extra-->
                        <div class="footer_extras  mt-auto pt-3 border-top ">

                        
                          <div class="row mb-3">
                              <div class="tachaduras col d-flex justify-content-end  align-items-center gap-2">
                                <!-- TACHADURAS -->
                              </div>
                            </div>
                            <div class="row mb-3">
                                  <div class="obstaculizacion col d-flex justify-content-end align-items-center gap-2">
                                <!-- obstaculizaciones -->
                                  </div>
                            </div>
                            <!-- inscrpbcion -->
                            <div class="row mb-3">    
                                <div class="inscripcion col d-flex justify-content-end align-items-center gap-2">
                                      <!-- falta de inscripcion -->
                                </div>
                            </div>
                            <!-- novedades  -->
                            <div class="row mb-3">    
                                <div class="novedades col d-flex justify-content-end align-items-center gap-2">
                                      <!-- novedades -->
                                </div>
                            </div>

                              <div class="row mb-3">    
                                <div class="noEnterados_footer col d-flex justify-content-end align-items-center gap-2">
                                      <!-- intereses no enterados -->
                                </div>
                            </div>
                          
                                <!-- boton para subir sancion extras -->
                                <input type="hidden" name="empresa_sanciones_extra" value="<?= $nombre_empresa_sancion ?>">

                                <button class="btn btn btn-primary w-100 py-2 rounded-3 shadow-sm" id="btnSubirSanciones" type="submit">Subir</button>


                        </div>


                    <!-- div cuerpo body modal -->
                </div>
              </div>
              </div>
            </div>








            <!-- <div class="d-flex justify-content-end w-100 border-bottom pb-2">
              <button class="btn tab-btn text-muted border-0 rounded-0 px-2" data-tab="tab4">
                Ordenes de pago
              </button>
            
          </div> -->

          <!-- sanciones EXTRA boton mostrar i agregar-->

            <!-- seccion de otras sanciones  -->
          <div class="row  g3 ">
          <!-- otras sacciones boton -->
                 <div class="col"> 
                      <!-- <form action="../../../back_end/facturacion/src/funciones_facturacion.php" method="Post"> -->
                          <input type="hidden" name="accionFacturacion" value="concurrirSancion" >
                          <input type="hidden" name="concurrir" value="<?= htmlspecialchars($nombre_empresa_sancion) ?>">
                          <button type="button" class="btn btn-outline-primary   bg-gradient btn-hover-shadow" data-bs-toggle="modal" data-bs-target="#modalConcurrir">Sanciones Extra</button>
                      <!-- </form> -->
                  </div>    

                  <div class="col-md-5 ">
                    <div class="d-flex justify-content-end">
                    <small class=" text-muted">Fecha de sancion</small>
                    </div>
                    <div class="d-flex justify-content-end">
                        <p class="h6 fw-bold "><?=  htmlspecialchars($fecha_formateada ?? 'Sin fecha'); ?></p>
                      </div>
                  </div>
            </div>

            <!-- TARJETAS DE OTRAS SANCIONES -->
         
          <div class="container">

                <div class="row g3 row-cols-auto">

                

                  
                  <?php 
                  // AQUI SI EL ARRAY ESTA EN ZERO
                                
                        if (empty($montoSancion)) {
                            $montoSancion = [
                                [
                                    'monto_ut_ingresos' => 0,
                                    'monto_ut_egresos' => 0,
                                    'monto_bs_ingresos' => 0,
                                    'monto_bs_egresos' => 0
                                ]
                            ];
                        }
                              
                              $totalFilaBs = 0;
                              ?> 

                              
                              <?php  foreach($montoSancion as $montos):
                                          $ingresos_sancion = $montos['monto_bs_ingresos'] ?? 0;
                                          $egresos_sancion = $montos['monto_bs_egresos'] ?? 0;
                                          // total en bs de los ingresos y egresos
                                          $totalFilaBs = $ingresos_sancion + $egresos_sancion;
                                          
                            
                            ?>



                

                <!-- PARTE DONDE VAN LOS MONTOS O TARJETAS CON LA INFO DE CADA SANCION -->
                  

                  
                  <div class="col-md-4">
                    <div class="card border-0 custom-card card-pink p-3 shadow-sm">
                      <div class="d-flex justify-content-between align-items-center">
                        <div>
                          <p class="text-muted mb-1 small">UT Ingresos</p>
                          <h3 class="fw-bold mb-1"><?= htmlspecialchars($montos['monto_ut_ingresos'] ?? '0') ?> </h3>
                          <p class="text-muted mb-0 extra-small">Sanciones ingresos</p>

                        </div>
                        <div class="icon-circle bg-pink-icon">
                          <img src="../barra_lateral/person.svg" alt="Icono">
                        </div>
                      </div>
                    </div>
                  </div>

                  
                  <div class="col-md-4">
                    <div class="card border-0 custom-card card-pink p-3 shadow-sm">
                      <div class="d-flex justify-content-between align-items-center">
                        <div>
                          <p class="text-muted mb-1 small">UT Egresos</p>
                          <h3 class="fw-bold mb-1"><?= htmlspecialchars($montos['monto_ut_egresos']?? '0') ?> </h3>
                          <p class="text-muted mb-0 extra-small">Sanciones egresos</p>

                        </div>
                        <div class="icon-circle bg-pink-icon">
                          <img src="../barra_lateral/person.svg" alt="Icono">
                        </div>
                      </div>
                    </div>
                  </div>


              <div class="col-md-4">
                  <button class="btn p-0 border-0 w-100 text-start" 
                          data-bs-toggle="modal" 
                          data-bs-target="#SancionesExtraModal"
                          onmouseover="hoverPlus(this)" 
                          onmouseout="unhoverPlus(this)">
                      
                      <div class="card border-0 custom-card card-pink p-3 shadow-sm position-relative">
                          
                          <div class="plus-container d-flex align-items-center justify-content-center" 
                              style="position: absolute; top: 10px; right: 10px; width: 30px; height: 30px; border-radius: 50%; transition: 0.3s; background-color: rgba(0,0,0,0.05);">
                              
                              <img src="recursos/plus.svg" alt="Agregar" class="plus-icon"
                                  style="width: 16px; transition: 0.3s;">
                          </div>

                          <div class="d-flex justify-content-between align-items-center">
                              <div>
                                  <p class="text-muted mb-1 small">UT Otros</p>
                                  <h3 class="fw-bold mb-1"><?= htmlspecialchars($totalSancionesExtra ?? '0') ?></h3>
                                  <p class="text-muted mb-0 extra-small">Sanciones extra</p>
                              </div>
                              
                              <div class="icon-circle bg-pink-icon">
                                  <img src="../barra_lateral/card-list.svg" alt="Icono">
                              </div>
                          </div>
                      </div>
                  </button>
              </div>


                <?php endforeach;?>

                  
                  <div class="col-md-4">
                    <div class="card border-0 custom-card card-pink p-3 shadow-sm">
                      <div class="d-flex justify-content-between align-items-center">
                        <div>
                          <p class="text-muted mb-1 small">Total Bs </p>
                          <h3 class="fw-bold mb-1"><?= number_format($totalFilaBs, 2, ',', '.') ?> </h3>
                          <p class="text-muted mb-0 extra-small">Antes de concurrencia</p>

                        </div>
                        <div class="icon-circle bg-pink-icon">
                          <i class="bi bi-currency-yen text-white"></i>
                        </div>
                      </div>
                    </div>
                  </div>




                
                  <?php
                   $pagado = mostrarMontoPagado($pdo, $nombre_empresa_sancion);
                   $concurrencia= mostrarConcurrencia($pdo, $nombre_empresa_sancion);


                    if (empty($pagado)) {
                            $pagado = [
                                [
                                    'valor' => 0,
                                    'monto_pago' => 0
                                ]
                            ];
                    }
                    
                    if (empty($concurrencia)) {
                            $concurrencia = [
                                [
                                    'valor_concurrencia' => 0,
                                    'sancion_mayor' => 0
                                ]
                            ];
                    }


                    $concurrenciaTotal = 0;
                   foreach($concurrencia as $concurrencia_valor){

                    $desglosada =  $concurrencia_valor['valor_concurrencia'] / 2;
                    $noDesglosada = $concurrencia_valor['sancion_mayor'];

                    $concurrenciaTotal = $desglosada + $noDesglosada;

                  
                  ?>



                   <?php }?>

                  <!-- AL FINAL DE SUBIR LA SANCION -->
                   <div class="col-md-4">
                    <div class="card border-0 custom-card card-pink p-3 shadow-sm">
                      <div class="d-flex justify-content-between align-items-center">
                        <div>
                          <p class="text-muted mb-1 small">Deuda total</p>
                          <div class="row">
                          <div class="col">
                          <p class="small mt-2"> UT</p>
                          </div><div class="col">
                          <h3 class="fw-bold mb-1"><?= number_format($concurrenciaTotal, 2, ',', '.')?? 0 ?> </h3>

                          </div>
                          </div>
                        
                          <p class="text-muted mb-0 extra-small">Sancion concurrida total</p>

                        </div>
                        <div class="icon-circle bg-pink-icon">
                                <img src="recursos/cash.svg" alt="">
                      </div>
                      </div>
                    </div>
                  </div>

                  


<?php                   

$pagadoTotal = 0;
            foreach($pagado as $pagado){

         $pagadoTotal = $pagado['monto_pago'] ;     

       }
      ?>
                  <!-- AL FINAL DE SUBIR LA SANCION -->
                   <div class="col-md-4">
                    <div class="card border-0 custom-card card-pink p-3 shadow-sm">
                      <div class="d-flex justify-content-between align-items-center">
                        <div>
                          <p class="text-muted mb-1 small">Monto pagado total</p>
                          <h3 class="fw-bold mb-1"><?= number_format( $pagadoTotal, 2, ',', '.')?? 0 ?> </h3>
                          <p class="text-muted mb-0 extra-small">Pagado por empleador</p>

                        </div>
                        <div class="icon-circle bg-pink-icon">
                                <img src="recursos/cash.svg" alt="">
                        </div>
                      </div>
                    </div>
                  </div>
                   
                      <div class=" p-4 d-flex justify-content-end gap-3">

                      </div>
                  

              </div>

            <!-- fin div  -->
          </div>


          <?php

                
          if (empty($otrasSanciones)) {
              $otrasSanciones = [
                  [
                      'tachaduras' => 0,
                      'obstaculizacion' => 0,
                      'inscripcion' => 0,
                      'novedades' => 0,
                      'intereses_no_enterados' => 0,
                  ]
              ];
          }
          ?>

            






          <!-- >>>>>>>>>  MODAL para ver las sanciones extra -->

<div class="modal fade" id="SancionesExtraModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            
            <div class="modal-header bg-light">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-octagon-fill text-danger fs-4 me-2"></i>
                    <h5 class="modal-title fw-bold" id="myLargeModalLabel">Detalle de Sanciones Extra</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <p class="text-muted mb-4">A continuación se detallan los valores de unidades tributarias (UT) aplicados para cada concepto extra:</p>
                
                <?php foreach($otrasSanciones as $extras): ?>
                <div class="list-group list-group-flush border rounded">
                    
                    <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary-subtle p-2 rounded me-3">
                                <i class="bi bi-eraser-fill text-primary"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-semibold">Tachaduras</h6>
                                <small class="text-muted">Infracciones por correcciones manuales</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-dark px-3 py-2 me-3">
                                <small class="text-light opacity-75 fw-normal">UT</small> <?= htmlspecialchars($extras['tachaduras']) ?>
                            </span>
                            <button class="btn btn-outline-secondary btn-sm border-0">
                                <img src="recursos/pencil-square (2).svg" width="18" alt="editar">
                            </button>
                        </div>
                    </div>

                    <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-warning-subtle p-2 rounded me-3">
                                <i class="bi bi-shield-lock-fill text-warning"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-semibold">Obstaculización</h6>
                                <small class="text-muted">Impedimento de fiscalización</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-dark px-3 py-2 me-3">
                                <small class="text-light opacity-75 fw-normal">UT</small> <?= htmlspecialchars($extras['obstaculizacion']) ?>
                            </span>
                            <button class="btn btn-outline-secondary btn-sm border-0">
                                <img src="recursos/pencil-square (2).svg" width="18" alt="editar">
                            </button>
                        </div>
                    </div>

                    <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-success-subtle p-2 rounded me-3">
                                <i class="bi bi-person-plus-fill text-success"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-semibold">Inscripción</h6>
                                <small class="text-muted">Registro fuera de plazo</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-dark px-3 py-2 me-3">
                                <small class="text-light opacity-75 fw-normal">UT</small> <?= htmlspecialchars($extras['inscripcion']) ?>
                            </span>
                            <button class="btn btn-outline-secondary btn-sm border-0">
                                <img src="recursos/pencil-square (2).svg" width="18" alt="editar">
                            </button>
                        </div>
                    </div>

                    <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-info-subtle p-2 rounded me-3">
                                <i class="bi bi-megaphone-fill text-info"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-semibold">Novedades</h6>
                                <small class="text-muted">Omisión de avisos obligatorios</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-dark px-3 py-2 me-3">
                                <small class="text-light opacity-75 fw-normal">UT</small> <?= htmlspecialchars($extras['novedades']) ?>
                            </span>
                            <button class="btn btn-outline-secondary btn-sm border-0">
                                <img src="recursos/pencil-square (2).svg" width="18" alt="editar">
                            </button>
                        </div>
                    </div>

                    <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-danger-subtle p-2 rounded me-3">
                                <i class="bi bi-cash-stack text-danger"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-semibold">Intereses No Enterados</h6>
                                <small class="text-muted">Retenciones no declaradas</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-dark px-3 py-2 me-3">
                                <small class="text-light opacity-75 fw-normal">UT</small> <?= htmlspecialchars($extras['intereses_no_enterados']) ?>
                            </span>
                            <button class="btn btn-outline-secondary btn-sm border-0">
                                <img src="recursos/pencil-square (2).svg" width="18" alt="editar">
                            </button>
                        </div>
                    </div>

                </div>
                <?php endforeach; ?>
            </div>

            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">Cerrar</button>
                <!-- <button type="button" class="btn btn-primary fw-bold">Guardar Cambios</button> -->
            </div>

        </div>
    </div>
</div>

          <!-- FIN MODAL para ver las sanciones extra  -->



















<!-- ORDEN DE PAGO -->






<!-- Importante  -->
                                               <!-- boton de concurrir  y orden de pago -->
          <div class="fixed-top p-4 d-flex justify-content-end gap-3">
              
              <div class="col-auto"> 
                  <form action="../../../back_end/facturacion/src/funciones_facturacion.php" method="Post">
                      <input type="hidden" name="accionFacturacion" value="concurrirSancion" >
                      <input type="hidden" name="concurrir" value="<?= htmlspecialchars($nombre_empresa_sancion) ?>">
                      <button type="submit" class="btn btn-primary text-nowrap shadow bg-gradient rounded-pill px-4" >Sumar sancion</button>
                  </form>
              </div>

              <div class="col-auto">
                      <button  
                          class="btn btn-outline-primary text-nowrap shadow-sm rounded-pill px-4"
                          data-bs-toggle="modal" 
                          data-bs-target="#ordenPagoModal">Orden de pago</button>
              </div>
          </div>



















  <!--                                                                MODAL ORDEN DE PAGO  -->

  <!--      modal de subir el monto pagado -->
  <div class="modal fade" id="ordenPagoModal" tabindex="-1" aria-hidden="true"  >
    <div class="modal-dialog modal-dialog-centered modal-lg" id="modalSize">
      <div class="modal-content  " style="border-radius: 20px;">
        


                    

                     <div id="paso1" class="d-block"> 
      

                                <div class=" border-0 overflow-hidden" style="border-radius: 20px;">
                                    <button type="button" class="btn-close position-absolute top-0 end-0 m-3 z-3" data-bs-dismiss="modal" aria-label="Close"></button>
                                    
                                    <div class="row g-0"> 

                                      <!-- primera tarjeta de abonar -->
                                      <div class="col-md-6 border-end"> 
                                        
                                        <div class="text-center bg-success-subtle py-5 position-relative">
                                                <img src="recursos/undraw_wallet_diag.svg" class="img-fluid" style="height: 150px;">
                                        </div>

                                        <!-- cuerpo de la tarjeta -->
                                            <div class="modal-body text-center p-4">
                                                
                                                <h4 class="fw-bold mb-3">Registrar Pago</h4>
                                                
                                                <p class="text-muted small mb-4">
                                                    Coloca el monto y el pago que el empleador realizo de su sancion.
                                                </p>

                                                <div class="d-grid gap-2">
                                              
                                                  <button type="button" class="btn btn-success py-2 fw-bold"  onclick="cambiarPaso(1, 2)" style="border-radius: 10px;">
                                                        REGISTRA EL PAGO
                                                    </button>
                                                </div>

                                              </div>
                                            <!-- fin de cuerpo de la tarjeta -->


                                      </div>
                                        
                                        <!-- segunda tarjeta -->
                                        <div class="col-md-6">
                                            <div class="text-center bg-danger-subtle py-5 position-relative">
                                                <img src="recursos/undraw_data-reports_l2u3.svg" class="img-fluid" style="height: 150px;">
                                            </div>

                                            <div class="modal-body text-center p-4">
                                                <h4 class="fw-bold mb-3">Aplicar Sanción</h4>
                                                <p class="text-muted small mb-4">
                                                    Se descontarán las UT correspondientes según el literal seleccionado.
                                                </p>

                                                <div class="d-grid gap-2">

                                                  <form action="sancionar_orden_pago.php" method="POST">
                                                    <input type="hidden" name="empresa_sancion" value="<?= $nombre_empresa_sancion ?>">
                                                    <input type="hidden" name="montoBsUT" value="<?= $totalFilaBs ?>">
                                                    <button type="submit" class="btn btn-danger w-100 py-2 fw-bold" style="border-radius: 10px;">
                                                        SANCIONAR
                                                    </button>
                                                  </form>

                                                </div>

                                            </div>

                                        </div>

                                    </div> 
                                    
                                    
                                    
                              </div>

                       <!-- div paso -->
                    </div>


                    

<!-- Modal 2 -->
        <div id="paso2" class="d-none">
             

                  <div class="modal-header border-0">
                      <button type="button" class="btn btn-outline-light btn-sm rounded-circle" data-bs-dismiss="modal">
                        <i class="bi bi-arrow-left"></i>
                      </button>
                      <h6 class="modal-title w-100 text-center text-secondary uppercase small">Registrar pago</h6>
                      <button type="button" class="btn-close btn-close-dark" data-bs-dismiss="modal"></button>
                  </div>


                  <div class="modal-body text-center px-4">
                    <div class="d-flex justify-content-end mb-2">
                      
                        <div class="dropdown">

                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle rounded-3 px-3" type="button" 
                            id="currencySelector" data-bs-toggle="dropdown" aria-expanded="false">
                              Valor <span class="ms-1 text-white fw-bold" id="currentCurrency">Bs</span>
                            </button>
                            
                            <ul class="dropdown-menu dropdown-menu-dark shadow" aria-labelledby="currencySelector">

                              <li name="bolivares">
                                <a class="dropdown-item active"  href="#" onclick="changeCurrency('Bs')">Bolívares (Bs)</a>
                              </li>

                              <li name="unidades_tributarias">
                                <a class="dropdown-item" href="#"  name="UT" onclick="changeCurrency('UT')">Unidades tributarias (UT)</a>
                              </li>

                            </ul>

                          </div>

                    </div>
                    

                    <small class="text-secondary d-block mb-1">Monto a cancelar</small>
                    
                    <div class="my-4">
                      <span class="text-secondary h4" id="currencySymbol">Bs</span>

                      <!-- donde el usuario coloca el monto -->
                      <input type="number" id="mainInput" name="montoPagado" class="display-5 fw-bold bg-transparent border-0  text-center w-75" 
                      placeholder="0.00" value="0.00" step="0.01" style="outline: none;">

                    </div>

                    <div class="opacity-50 mb-4">
                      <small id="secondaryAmount">0.00 UT</small>
                    </div>
                </div>



                  
                    <!-- div del cuerpo de los valores En la Base de datos -->
                  <div class="bg-opacity-25 rounded-pill p-2 px-3 mb-4">

                      <div class="row">
                        <div class="col">
                          <small>deuda restante</small><br>
                          <h5>23334</h5>
                        </div>
                        
                        <div class="col">
                          <small>total en bs</small><br>
                          <h5><?= number_format($totalFilaBs, 2, ',', '.') ?></h5>
                        </div>

                      </div>

                      <div class="row">
                        <div class="col">
                          <small>Ultimo pago</small>
                          <h5>23/34/55</h5>
                        </div>
                      </div>


                      <!-- CUERPO PARA COLOCAR EL INPUT -->
                      <hr>
                      <div class="row mt-4">
                        <div class="col">
                          <h5 class="text-muted fw-bold">Colocar pago</h5>
                        </div>
                      </div>

                      
                          <div class="row">

                              <div class="col">
                                <label class="">Banco</label> <br>
                                <select class="form-select form-select-sm border-secondary border-opacity-25 shadow-none rounded-0" 
                                              id="selectMoneda" 
                                              style=" cursor: pointer;" name="banco">


                                      <!-- <div class="bg-warning rounded-circle me-2" style="width: 30px; height: 30px; display: grid; place-items: center;">
                                          <i class="bi bi-person-fill text-dark"></i>
                                          
                                      </div> -->

                                        
                                          <option value="venezuela" selected>Venezuela</option>
                                          <option value="banesco">Banesco</option>
                                          <option value="mercantil">Mercantil</option>
                                        
                                      
                                    <!-- <span>Shared Wallet</span> -->
                                    
                                  <!-- <button class="btn btn-dark btn-sm rounded-pill px-3">Adjust <i class="bi bi-chevron-right ms-1"></i></button> -->
                                </select>
                                </div>
                          </div>
                            

                          <div class="row mt-4">
                                
                                <div class="col px-3">
                                  <label class="">Numero de referencia</label>
                                  <br>
                                  <input type="number" id="referencia" name="referencia" class="form-bg border-secondary border-opacity-25 shadow-none form-select-sm rounded-0" placeholder="coloque el numero de referencia">
                                </div>
                          </div>
                          
                          <div class="row mt-4">      
                                <div class="col px-3">
                                  <label class="">Fecha de pago</label>
                                  <br>
                                  <input type="date" name="fechaPago" class="form-control rounded-0" placeholder="coloque el numero de referencia">
                                </div>
                          </div>

                          <div class="row mt-4">      
                                <div class="col px-3">
                                  <label class="">Nombre sancion</label>
                                  <br>
                                  <span id="empresa_pago"><?= htmlspecialchars($nombre_empresa_sancion) ?? '' ?></span>
                                </div>
                          </div>

                        </div>

                      
                    
                        <div class="row g-2 px-3 mt-5">
                        <button class="btn btn-light w-60 py-3 fw-bold rounded-4 mb-3"  onclick="cambiarPaso(2, 3)" >Subir Monto</button>
                      </div>



          </div>





                    <!-- modal 3 -->

                    <div id="paso3" class="d-none">
                      <div class="modal-content border-0 shadow-lg rounded-5 overflow-hidden bg-white mx-auto" >
                         
                        <div class="modal-body text-center p-4 p-md-5">

                        <!-- boton bolver -->
                          <div class="row g-2 px-2 ">
                        <button class="btn btn-light w-20 py-2 fw-bold rounded-4 mb-3"  onclick="cambiarPaso(3, 2)" >volver</button>
                      </div>
                          
                          <!-- <div class="success-icon-container mb-4">
                            <div class="success-icon bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto shadow-sm" style="width: 80px; height: 80px;">
                              <img src="recursos/check2.svg" class="img-fluid w-75" alt="">
                            </div>
                          </div> -->

                          <p id="resumenValor" class="tex-muted small "></p>
                          <h2 id="resumenMontoPrincipal" class="fw-bold text-dark mb-2 display-4"></h2>

                          <p class="text-muted small mb-4 px-3">
                            Registro total a pagar
                          </p>

                          <div class="bg-light rounded-4 p-4 mb-4 text-start">


                           <!-- mostrar banco -->
                                <div class="d-flex justify-content-between mb-3">
                                  <span class="text-secondary small">Nombre sancion</span>
                                  <span id="resumenEmpresa" class="fw-bold text-dark"></span>

                                </div>
                          <!-- mostrar banco -->
                                <div class="d-flex justify-content-between mb-3">
                                  <span class="text-secondary small">Banco</span>
                                  <span id="resumenBanco" class="fw-bold text-dark"></span>

                                </div>

                                <!-- referencia -->
                                <div class="d-flex justify-content-between mb-3">
                                  <span class="text-secondary small"># Referencia</span>
                                  <span  id="resumenReferencia" class="fw-bold text-dark"></span>
                                </div>

                                <!-- fecha -->
                                <div class="d-flex justify-content-between mb-3">
                                  <span class="text-secondary small">Fecha de pago</span>
                                  <span id="resumenFecha" class="fw-bold text-dark text-end"></span>
                                </div>



                          </div>
                                                 


                          <div class="d-grid mt-4">
                            <button class="btn btn-dark py-3 fw-bold rounded-4 shadow" 
                                    onclick="finalizarTodo()" 
                                    data-bs-dismiss="modal">
                              Registrar pago
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>






      </div>
    </div>
</div>
 <!--   fin   modal de subir el monto pagado -->
<!-- fin modal -->


<style>
  /* CONFIRMACION DE PAGO MODAL */
  /* Estilo para el contenedor del ícono azulado */
.success-icon {
    background-color: #eef2ff !important; /* Azul muy pálido */
    color: #6366f1 !important; /* Azul moderno tipo Stripe/Indigo */
}

/* Redondear bordes de Bootstrap un poco más */
.rounded-4 { border-radius: 1.2rem !important; }
.rounded-5 { border-radius: 2rem !important; }

/* Efecto de elevación del botón */
.btn-dark {
    background-color: #0a0c1b !important;
    border: none;
    transition: transform 0.2s;
}

.btn-dark:hover {
    transform: translateY(-2px);
    background-color: #1a1d3a !important;
}

/* Tipografía */
.text-secondary {
    color: #8e94a3 !important;
}
</style>
<!-- POST PARA VERIFICAR EL MODAL LOS INPUT COLOCADOS  -->























          




                                                                              <!-- FIN ORDEN DE PAGO -->














                              <!-- >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>TABLASSSS DE LA FACTURACION -->


          <!-- botones para cambiar de pestana a otra tabla -->


                          <div class="tab-links d-flex gap-4 justify-content-center align-items-center vh-10 ">

                                  <button class="btn tab-btn text-primary border-0 border-bottom border-primary border-3 rounded-0 " data-tab="tab1">
                                    <p class="text-muted mb-0">Ingresos</p>  
                                  </button>


                                  <button class="btn tab-btn text-muted border-0 rounded-0 " data-tab="tab2">
                                        <p class="text-muted mb-0">Egresos</p>  

                                  </button>


                          </div>



                          <!-- INGRESOS TABLA -->

                          <!-- container tablas -->
                          <div class="container " >
                                <!-- tabs -->
                                

                                


                                <div class="row justify-content-center">
                                  
                                      <div class="md-3 ">
                                        <div id="tab1"class="tab-pane">

                                        <!-- cabezera de la tabla -->
                                            <div class="row">
                                              <div class="col fw-bold h4">Ingresos</div>
                                              
                                            </div>
                                            <div class="row">
                                              <div class="col text-muted small">Sanciones extemporaneas por trabajador</div>
                                            </div>
                                            <!-- fin cabezera de la tabla -->

                                        <!-- cartas datos -->
                                            <div class="card border-0 shadow-sm mb-3 py-2 px-3"  data-schema="tareas" data-tabla="empleados_ingresos" >
                                                  <div class="card-header mt-4">
                                                      <div class="row px-3 d-none d-md-flex text-muted small uppercase mb-2">
                                                        <div class="col-1">#</div>
                                                        <div class="col-2 ">Trabajador</div>
                                                        <div class="col-2">Movimiento Ingreso</div>
                                                        <div class="col-2">Sancion</div>
                                                        <div class="col-2">Fecha ingreso</div>
                                                        <div class="col-1 text-end">UT</div>
                                                        <div class="col-1 text-end">Bs</div>
                                                      </div>
                                                  </div>

                                                  <?php
                                                  if(empty($evaluar_trabajadores_ingresos)){?>
                                                  
                                                  <div class="text-center ">
                                                    <div class="text-center text-muted py-5 text-primary">  <h3> Sin ingresos</h3></div>
                                                    <div>
                                                      <img src="recursos/busqueda_no_encontrada.svg" alt="" class="img-fluid w-25 h-25 opacity-50">
                                                    </div>
                                                  </div>
                                                  <?php }
                                                  
                                                  $contadorIngresos = 1;
                                                  ?>
                                                  
                                                  <?php  foreach ($evaluar_trabajadores_ingresos as $empleado): ?>                
                                                  <?php //if(empty($empleado['fecha_egreso'])):
                                                  

                                                  
                                                                  //formatea la cedula y le quita el VE de la consulta. para que muestre solamente el numero
                                                                  $prefijos_a_eliminar = ['VE', 'EXT']; 
                                                                  $cedula = $empleado['cedula'];
                                                                  //elimina los prefijos VE y EXT de la cedula
                                                                  $cedula_formateada= str_replace($prefijos_a_eliminar, '', $cedula);
                                                    ?>

                                                  <ul class="list-group list-group-flush" data-id="<?php $empleado['id'] ?>">
                                                      <li class="list-group-item" >
                                                          <div class="row   border-0 shadow-sm mb-3 py-2 px-3" >
                                                          
                                                            <div class="col-1"> 
                                                               
                                                              <small  class="text-muted" > <?= $contadorIngresos++ ?></small>
                                                            </div>

                                                            <div class="col-2"><p class="mb-3"><?= htmlspecialchars($empleado['nombre'].'  '.$empleado['apellido']) ?></p>
                                                                <small class="text-muted"><?= htmlspecialchars($cedula_formateada) ?></small>
                                                            </div>
                                                            <div data-columna="movimiento_ingreso" class="col-2"><small class="text-muted"><?= htmlspecialchars($empleado['movimiento_ingreso']) ?></small></div>  
                                                            <div class="col-2"> <span class="badge rounded-pill bg-success-subtle text-success"><?= htmlspecialchars($empleado['sancion_ingreso']) ?></div></span>

                                                            <div class="col-2">
                                                            
                                                                
                                                                <?php if($empleado['fecha_ingreso'] != null){?>
                                                                
                                                                          <small class="text-muted"> <?= htmlspecialchars($empleado['fecha_ingreso'])?></small>
                                                                      
                                                                
                                                                          <?php
                                                                        }else {?>
                                                                          
                                                                        <div class="col-2"> <span class="badge small text-dark ">Activo</div></span>

                                                                       <?php }
                                                                  ?>
                                                            </div>
                                                            
                                                            <?php $valorIngresos = 50;
                                                            $valorSumaIngreso = $valorIngresos * 43;?>  
                                                            <div class="col-1 text-end text-muted"><?= !empty($empleado['fecha_ingreso'])?  $valorIngresos: '0' ; ?></div>
                                                            <div class="col-1 text-end text-muted"><?=  !empty($empleado['fecha_ingreso'])?  $valorSumaIngreso: '0' ;?></div>

                                                          
                                                          </div>
                                                      </li>
                                                  </ul>
                                                  
                                                  <?php endforeach; ?>

                                              </div>
                                          </div>



                                      <!-- row -->
                                      </div>
                            <!-- tab1 -->
                          </div>
                          </div>


                          <!-- FIN INGRESOS TABLA -->



                          <!-- EGRESOS TABLA -->


                                <!-- container tablas -->
                                <div class="container ">
                                <!-- tabs -->


                                <div class="row justify-content-center">
                                <div class="md-3">
                                <div id="tab2" class="tab-pane">


                                      
                                      <!-- cabezera de la tabla -->
                                      <div class="row">
                                        <div class="col fw-bold h4">Egresos</div>
                                        
                                      </div>
                                      <div class="row">
                                        <div class="col text-muted small">Sanciones extemporaneas por trabajador</div>
                                      </div>
                                      <!-- fin cabezera de la tabla -->

                                <!-- cartas datos -->
                                <div class="card border-0 shadow-sm mb-3 py-2 px-3">
                                      <div class="card-header mt-4">
                                          <div class="row px-3 d-none d-md-flex text-muted small uppercase mb-2">
                                            <div class="col-1">#</div>
                                            <div class="col-2">Trabajador</div>
                                            <div class="col-2">Movimiento egreso</div>
                                            <div class="col-2">Sancion</div>
                                            <div class="col-2">Fecha egreso</div>
                                            <div class="col-1 text-end">UT</div>
                                            <div class="col-1 text-end">Bs</div>
                                          </div>
                                      </div>

                                      
                                      <?php
                                      if(empty($evaluar_trabajadores_ingresos)){?>
                                      
                                      <div class="text-center ">
                                        <div class="text-center text-muted py-5 text-primary">  <h3> No hay egresos</h3></div>
                                        <div>
                                          <img src="recursos/busqueda_no_encontrada.svg" alt="" class="img-fluid w-25 h-50 opacity-50">
                                        </div>
                                      </div>
                                      <?php }
                                      $contadorEgresos = 1;
                                      ?>

                                      <?php  foreach ($evaluar_trabajadores_ingresos as $empleadoEgresos): ?>                
                                      <?php //if(empty($empleado['fecha_egreso'])):
                                      

                                      
                                                      //formatea la cedula y le quita el VE de la consulta. para que muestre solamente el numero
                                                      $prefijos_a_eliminar = ['VE', 'EXT']; 
                                                      $cedula = $empleadoEgresos['cedula'];
                                                      //elimina los prefijos VE y EXT de la cedula
                                                      $cedula_formateada= str_replace($prefijos_a_eliminar, '', $cedula);
                                        ?>

                                      <ul class="list-group list-group-flush">
                                          <li class="list-group-item">
                                              <div class="row   border-0 shadow-sm mb-3 py-2 px-3">
                                              
                                                <div class="col-1"> 
                                                  <!-- <small  class="text-muted" > <?= htmlspecialchars($empleadoEgresos['id']) ?></small> -->
                                                  <small  class="text-muted" > <?= $contadorEgresos++ ?></small>
                                                </div>
                                                <!-- <div class="col-1"><?= htmlspecialchars($cedula_formateada) ?></div> -->
                                                <div class="col-2"><?= htmlspecialchars($empleadoEgresos['nombre'].'  '.$empleadoEgresos['apellido']) ?>
                                                <small class="text-muted"><?= htmlspecialchars($cedula_formateada) ?></small>
                                                </div>
                                                
                                                <div class="col-2"><small class="text-muted"><?= htmlspecialchars($empleadoEgresos['movimiento_egreso']) ?></small></div>  
                                                <div class="col-2"> <span class="badge rounded-pill bg-success-subtle text-success"><?= htmlspecialchars($empleadoEgresos['sancion_egreso']) ?></div>
                                                
                                                <div class="col-2">

                                                    
                                                    <?php if($empleado['fecha_egreso'] != null){?>
                                                    
                                                              <small class="text-muted"> <?= htmlspecialchars($empleadoEgresos['fecha_egreso'])?></small>
                                                          
                                                    
                                                              <?php
                                                            }else {?>
                                                                        <div class="col-2"> <span class="badge small text-dark ">Activo</div></span>

                                                           <?php }
                                                      ?>
                                                </div>
                                                  
                                                  <?php $valorEgresos = 25;
                                                            $valorSumaEgreso = $valorEgresos * 43;?>  
                                                <div class="col-1 text-end text-muted"><?= !empty($empleado['fecha_egreso'])?  $valorEgresos: '0' ; ?></div>
                                                <div class="col-1 text-end text-muted"><?= !empty($empleado['fecha_egreso'])?  $valorSumaEgreso: '0' ;?></div>

                                              
                                              </div>
                                          </li>
                                      </ul>
                                      <?php // endif;?>
                                      <?php endforeach; ?>

                                  </div>
                                  </div>



                                <!-- row -->
                                </div>
                                  <!-- tab2 -->
                                </div>

                                <!-- FIN EGRESOS TABLA -->

                                <!-- main div -->
                                </div> 

                        <!-- div de todo el contenedor de la tabla -->
                        <!-- </div> -->



                        

                        <!--              ORDEN DE PAGO DETALLE MODAL -->


                        <div class="modal fade" id="detallesOrdenPago" tabindex="-1" aria-labelledby="detallesOrdenPago" aria-hidden="true">
                            <div class="modal-dialog modal-lg"> <div class="modal-content">
                                
                                <div class="modal-header">
                                  <h5 class="modal-title" id="exampleModalLabel">Orden de pago</h5>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <div class="modal-body ">
                                    <div class="d-flex align-items-center mb-4">

                                          <div class="bg-primary text-white rounded-circle p-2 me-3">
                                              <i class="bi bi-info-circle-fill"></i> </div>
                                          <div>


                                              <h6 class="mb-0 fw-bold">Sanciones concurridas</h6>
                                              <small class="text-muted">Aqui estan todas las sanciones pendientes procesadas</small>
                                          </div>
                                      </div>
                                      <h5>concurrencia</h5>
                                      <?php 
                                      $numeroConcurrencia = 1;
                                      foreach($orden_pago_sancion as $orden_mostrar): 
                                       $concurrida = $orden_mostrar['valor_concurrencia'] / 2;
                                      ?>
                                      <hr>
                                      <div class="">
                                            
                                              <span class="fw-bold display-6 text-dark">#<?= $numeroConcurrencia++ ?></span>
                                          </div>

                                      <div class="row g-3 mb-5">
                                          
                                          
                                          
                                          <div class="col-6">
                                              <label class="text-muted small d-block">Concurrida</label>
                                              <span class="fw-bold text-dark"><?= $concurrida ?> UT</span>
                                          </div>
                                          
                                          <div class="col-6">
                                              <label class="text-muted small d-block">Sancion mayor</label>
                                              <span class="fw-bold small text-dark"><?= $orden_mostrar['nombre_sancion_mayor'] ?> : <?= $orden_mostrar['sancion_mayor'] ?> UT</span>
                                          </div>

                                            <div class="col-6">
                                              <label class="text-muted small d-block">Otras</label>
                                              <span class="fw-bold small text-dark"><?= $orden_mostrar['otras_sanciones']?> Bs</span>
                                          </div>
                                        
                                          


                                          <div class="col-12">

                                          
                                              <div class="p-3 rounded-3 bg-light border-start border-primary border-4">
                                                  <div class="d-flex justify-content-between align-items-center">
                                                      <div>
                                                          <p class="mb-0 small text-muted">Total a pagar</p>
                                                          <h4 class="mb-0 fw-bold text-primary">UT. <?= $concurrida + $orden_mostrar['sancion_mayor'] ?></h4>
                                                      </div>
                                                      <i class="bi bi-cash-stack fs-2 text-primary opacity-50"></i>
                                                  </div>
                                              </div>
                                          </div>


                                      </div> <br><br>
                                        <?php endforeach;?>
                                      <!--  -->
                                </div>

                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                  <!-- <button type="button" class="btn btn-primary">Guardar Cambios</button> -->
                                </div>

                              </div>
                            </div>
                          </div>
                        <!--         FIN     ORDEN DE PAGO DETALLE MODAL -->


                <!--div para verlo en la mitad de la pantalla  -->

                  </div>


















                  <!--                                                BARRA IZQUIERDA ORDEN PAGO -->
<!-- div de la otra parte de la pagina -->
      <div class="col-12 col-lg-3 mb-4">


                <!-- orden de pagos -->
                <!-- container tablas -->
                    
                 
                      <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0" style="color: #333;">Ordenes de Pago</h5>
                        <!-- <span class="badge bg-light text-muted border">All <i class="bi bi-chevron-down small"></i></span> -->
                    </div>
                    

                    
                <div class="card border-0 shadow-sm sticky-top" style="top: 10rem; border-radius: 15px;">


  <!-- SANCIONES SUBIDAS -->
                
                <?php foreach($orden_pago_sancion as $orden): ?>
                    
                    <div class="card-body px-3">
                        <ul class="list-group list-group-flush">


                            <li class="list-group-item border-0 mb-3 p-3 shadow-sm hover-card" style="border-radius: 12px; transition: 0.3s;">

                                <div class="d-flex align-items-start">

                                        <div class="rounded-circle bg-danger-subtle d-flex align-items-center justify-content-center me-3" 
                                              style="width: 45px; height: 45px; flex-shrink: 0;">
                                            <span class="fw-bold text-danger">S</span>
                                        </div>
                                        

                                        <div class="flex-grow-1">

                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <!-- <h6 class="fw-bold mb-0 small">Sanción #<= htmlspecialchars($empleado['id'] ?? '1') ?></h6> -->
                                                <span class="badge bg-danger-subtle text-danger extra-small" style="font-size: 10px;">
                                                    <i class="bi bi-circle-fill me-1" style="font-size: 6px;"></i> <?php 

                                                    if(!empty($orden['valor_concurrencia'])){
                                                          ?>Sancion<?php
                                                    } ?>
                                                </span>
                                            </div>

                                            <p class="text-muted mb-2" style="font-size: 12px; line-height: 1.4;">
                                                El trabajador presenta una inconsistencia en el literal B...
                                           
                                              </p>

                                              <div class="row">
                                                <div class="col">
                                                      <button class="btn text-primary fw-bold" style="font-size: 11px;"
                                                       data-bs-toggle="modal" 
                                                        data-bs-target="#detallesOrdenPago"
                                                        >Ver detalles</button>
                                           
                                                </div>
                                                <div class="col-4">
                                                  <form action="../pdf/concurrenciaPdf.php" method="POST">
                                                    <input type="hidden" name="empresa" value="<?= $nombre_empresa_sancion ?>">
                                                    <input type="hidden" name="empresa" value="<?= $nombre_empresa_sancion ?>">
                                                      <button type="submit" class="btn btn-outline-primary btn-sm fw-bold" style="font-size: 10px;">
                                                        <i class="bi bi-file-pdf"></i> PDF
                                                      </button>
                                                  </form>
                                                </div>
                                              </div>
                                            
                                        </div>

                                         




                                  </div>

                                  
                            </li>

                            


                        </ul>
                    </div>

                    <?php endforeach;?>









<!--  PAGOS REGISTRADOS -->


                      <?php foreach($orden_pago_registrado as $ordenPago): ?>
                    
                    <div class="card-body px-3">
                        <ul class="list-group list-group-flush">


                            <li class="list-group-item border-0 mb-3 p-3 shadow-sm hover-card" style="border-radius: 12px; transition: 0.3s;">

                                <div class="d-flex align-items-start">

                                        <div class="rounded-circle bg-primary-subtle d-flex align-items-center justify-content-center me-3" 
                                              style="width: 45px; height: 45px; flex-shrink: 0;">
                                            <span class="fw-bold text-primary">P</span>
                                        </div>
                                        

                                        <div class="flex-grow-1">

                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <!-- <h6 class="fw-bold mb-0 small">Sanción #<= htmlspecialchars($empleado['id'] ?? '1') ?></h6> -->
                                                <span class="badge bg-primary-subtle text-primary extra-small" style="font-size: 10px;">
                                                    <i class="bi bi-circle-fill me-1" style="font-size: 6px;"></i> <?php 

                                                    if(!empty($ordenPago['monto_pago'])){
                                                          ?>Pago<?php
                                                    } ?>
                                                </span>
                                            </div>

                                            <p class="text-muted mb-2" style="font-size: 12px; line-height: 1.4;">
                                                El pago ha sido realizado por.....
                                           
                                              </p>

                                              <div class="row">
                                                <div class="col">
                                                      <button class="btn text-primary fw-bold" style="font-size: 11px;"
                                                       data-bs-toggle="modal" 
                                                        data-bs-target="#detallesOrdenPago"
                                                        >Ver detalles</button>
                                           
                                                </div>
                                                <div class="col-4">
                                                  <form action="../pdf/factura_pagada.php" method="POST">
                                                    <input type="hidden" name="empresa" value="<?= $nombre_empresa_sancion ?>">
                                                    <input type="hidden" name="empresa" value="<?= $nombre_empresa_sancion ?>">
                                                      <button type="submit" class="btn btn-outline-primary btn-sm fw-bold" style="font-size: 10px;">
                                                        <i class="bi bi-file-pdf"></i> PDF
                                                      </button>
                                                  </form>
                                                </div>
                                              </div>
                                            
                                        </div>

                                         




                                  </div>

                                  
                            </li>

                            


                        </ul>
                    </div>

                    <?php endforeach;?>




                    




                </div>


                      <!-- Div para verlo en el lado izquierdo-->
                  </div>
              




  </div>
</div>







<!-- DIV DE TODA LA PAGINA -->

 </div>

<style>

/* PARA ORDENES DE PAGO */


  /* Estilos adicionales para que se vea premium como la foto */
.extra-small {
    font-size: 0.7rem;
    padding: 4px 8px;
}

.hover-card:hover {
    transform: translateY(-3px);
    background-color: #f8f9fa;
    cursor: pointer;
}

/* Para que la tabla principal a la izquierda sea col-lg-8 */
.main-layout {
    margin-left: 250px; /* Ancho de tu sidebar azul */
    padding: 30px;
    width: calc(100% - 250px);
}

@media (max-width: 992px) {
    .main-layout {
        margin-left: 0;
        width: 100%;
    }
}


/* MODALES */
 /* Si hay más de un fondo oscuro, oculta los extras para que no se sumen */
.modal-backdrop + .modal-backdrop {
    display: none !important;
}

/* Asegura que el modal activo siempre esté por encima de la mancha negra */
.modal.show {
    background-color: rgba(0, 0, 0, 0.5); /* Esto crea un fondo oscuro propio si el oficial falla */
}
</style>



<script src="js/registrarPago.js">
  
</script>



<!-- para sweet alert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script src="../../../back_end/facturacion/js/facturacion.js" defer></script>

<script src="js/sancionesExtra.js">

</script>


 <script src="../../js/bootstrap.bundle.min.js"></script>
  <script src="../../js/bootstrap.bundle.js"></script>


<!-- Este archivo hace los estilos dinamicos para esto -->
<script src="js/facturacion.js">
</script>

 <script>
    //SCrip para los datatables
    $(document).ready(function() {
    $('#extemporaneos').DataTable({

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
        }
    });
});
 </script>


</body>
</html>