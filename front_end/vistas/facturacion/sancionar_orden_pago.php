<?php
require_once(__DIR__ . '/../../../back_end/config.php');
require_once(FUNCIONES_FACTURACION);
require_once(FUNCIONES_TAREAS);



// verifica si hubo un post
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $monto_total = $_POST['montoBsUT'];

    $nombre_empresa_sancion =$_POST['empresa_sancion'];
   
//    Si tiene un valor en el monto bsUT hay que mejorarlo para las sanciones extra

    if($monto_total >0){

    // INGRESOS Y EGRESOS
    $montoSancion = mostrar_monto_sancion($pdo, $nombre_empresa_sancion);

    $data = $montoSancion[0]; // Extraemos el primer nivel

    $ut_ingresos = $data['monto_ut_ingresos'];
    $ut_egresos  = $data['monto_ut_egresos'];
    
    $bs_ingresos = $data['monto_bs_ingresos'];
    $bs_egresos  = $data['monto_bs_egresos'];


    $totalBsIngresos = array_sum(array_column($montoSancion, 'monto_bs_ingresos'));
    $totalBsEgresos = array_sum(array_column($montoSancion, 'monto_bs_egresos'));
    
    $totalExtemporaneos= $totalBsEgresos +$totalBsIngresos;




    
    
    // >>>>>>>>>>>>>>>>>>>>>>>>>>                  sanciones extra
    $montoSancionesExtra = mostrar_otras_sanciones($pdo, $nombre_empresa_sancion);
    
    $extra_sancion = $montoSancionesExtra[0]?? 0; // Extraemos el primer nivel

    $tachaduras = $extra_sancion['tachaduras']?? 0;
    $obstaculizacion  = $extra_sancion['obstaculizacion'] ?? 0;
    $inscripcion = $extra_sancion['inscripcion'] ?? 0;
    $novedades  = $extra_sancion['novedades'] ?? 0;
    $noEnterados  = $extra_sancion['intereses_no_enterados']?? 0;




    // concurrir.php para calcular el valor de las sancinoes
    $sancionesExtra = new SancionesCalcular();
    
    [$bs_tachaduras, $bs_obstaculizacion, $bs_inscripcion, $bs_novedades, $bs_noEnterados] = 
    
    $sumaTotales = $sancionesExtra->CalcularSancionesExtraBs($tachaduras,$obstaculizacion, $inscripcion, $novedades, $noEnterados);

    $totalFinalExtras = 0; // Variable para acumular la suma

    foreach ($sumaTotales as $monto) {
        $totalFinalExtras += $monto; // Sumamos cada valor al total
    }    


    // SUMA TOTAL DE LA SANCION

    $totalSancion = $totalFinalExtras + $totalExtemporaneos;


    // EMPRESAS 
    $empresas_valores = empresasDeSancion($pdo, $nombre_empresa_sancion);





    // concurrecia
    // $sancionMayor = max($tachaduras, $noEnterados, $novedades,$obstaculizacion, $inscripcion, $ut_ingresos, $ut_egresos);

    // echo $sancionMayor;

$montos = [
    'Ingresos'      => $ut_ingresos,
    'Egresos'      => $ut_egresos,
    'Tachaduras'      => $tachaduras,
    'Obstaculización' => $obstaculizacion,
    'Inscripción'     => $inscripcion,
    'Deudas no enteradas'     => $noEnterados,
    'Novedades'       => $novedades
];

// // Ordena el array de mayor a menor manteniendo las llaves
// arsort($montos);

// // 2. Saltamos el primer elemento (índice 0) y tomamos todo lo demás
// // El 'true' es vital para no perder los nombres (Ingresos, Tachaduras, etc.)
// $elResto = array_slice($montos, 1, null, true);

// // Obtenemos el nombre del primero (el mayor) y su valor
// $nombreMayor = array_key_first($montos);
// $valorMayor  = current($montos);

// // echo "La mayor sanción es $nombreMayor con un monto de $valorMayor Bs.";

// $sancionMontoResto = 0;
// // Ahora puedes recorrer los menores:
// foreach ($elResto as $nombre => $valor) {
//     $sancionMontoResto += $valor ;
// }

// $sancionDesglosada = $sancionMontoResto /2;
// // echo $sancionDesglosada;
$concurrecia = $sancionesExtra->concurrirSancion($montos);



//MONTO FINAL CONCURRIDO

$montoFinal = $sancionesExtra->calcularFinalDesglosado($concurrecia['mayor']['valor'], $concurrecia['desglose_final']);

?>

    <!--  si tiene monto bs-->
    
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- <title>Document</title> -->
        <link href="../../css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="icon" href="../ivss.png" type="image"> 

    </head>
    <body>

    <!-- <div style="margin-left: 250px; padding: 20px; width: calc(100% - 250px);">

        
        <div class="container-fluid mt-5">

                <div class="row">
                    
                     <div class="col-12 col-lg-8">
                        <p>pago 1</p>
                    </div>

                    <div class="col-12 col-lg-4">
                        <p>pago 2</p>
                    </div>
                </div>
        </div>

    </div>
    </div> -->
    <div class="mt-4 px-5">
            <a href="facturacion.php?empresa=<?=  $nombre_empresa_sancion?> " class="btn btn-outline-primary ">volver</a>

    </div>

            <div class="container py-5">
                  <form action="../../../back_end/facturacion/src/orden_pago.php" method="POST">
            <div class="row g-4">
                <!-- divide la pantalla para que sea la mas grande -->
                <div class="col-lg-8">


                    
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4">Detalle de sancion extra</h5>


                            
                            <!-- descripcion sancione extra  DECRIPCIONES-->
                            <div class="row g-3 mb-5">
                                <div class="col-12">
                                    <label class="form-label text-muted small fw-bold">Novedades</label>
                                    <input type="text" class="form-control bg-light border-0" name="novedades_descripcion" placeholder="Si existe coloca una novedad">
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted small fw-bold">Obstaculizacion</label>
                                    <input type="text" class="form-control bg-light border-0" name="obstaculizacion_descripcion" placeholder="Coloca el tipo de obstaculizacion">
                                </div>

                                <div class="col-12">
                                    <label class="form-label text-muted small fw-bold">Tachaduras</label>
                                    <input type="text" class="form-control bg-light border-0" name="tachaduras_descripcion" placeholder="Describe.....">
                                </div>

                               <h5 class="fw-bold mb-4">Intereses</h5>

                                <div class="col-md-3">
                                    <label class="form-label text-muted small fw-bold">Cantidad de meses</label>
                                    <input type="number" class="form-control bg-light border-0" name="cantidad_periodo" placeholder="1" >
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label text-muted small fw-bold">Interes no enterados</label>
                                    <select class="form-select bg-light border-0" name="formato_tiempo">
                                        
                                        <option value="mes" selected>Mes</option>
                                        <option value="anual">Anual</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold">Cantidad deuda bs</label>
                                    <input type="number" class="form-control bg-light border-0" name="intereses" placeholder="00000"  >
                                                                            

                                </div>
                            </div>

                            <!-- fin de descripcion -->

                        <h5 class="fw-bold mb-5">Detalle de sancion concurrida</h5>


                            
                            <!-- header de las sanciones -->
                            <div class="row">
                                    <div class="col-6">
                                            <label for="form-label text-muted">Articulo</label>
                                    </div>
                                    
                                    <div class="col-2 text-end">
                                            <label for="form-label text-muted text-end">No desglosada</label>
                                    </div>

                                    <div class="col-4 text-end">
                                            <label for="form-label text-muted text-end">Total UT</label>
                                    </div>
                            </div>
                            
                            <!-- finheader -->
                            <hr class="text-muted">   


                            <div class="row mb-5">

                                <div class="col-6">
                                    <!-- ingresos concurrida mayor -->
                                    <h6 class="text-muted  fw-bold mb-2"><?= $concurrecia['mayor']['nombre'] ?></h6>
                                    <small class="text-muted ">No desglosada</small>
                                </div>
                               
                                <div class="col-6 text-end">
                                    <h6 class="justify-text-end"><?= $concurrecia['mayor']['valor'] ?></h6>
                                </div>

                                <!-- fin de la fila -->
                            </div>

                              
                            <div class="row mb-5">

                                <div class="col-6">
                                    <!-- resto de sanciones -->
                                    <h6 class="text-muted  fw-bold mb-2">Resto de sanciones</h6>
                                    <small class="text-muted ">Desglosada</small>
                                </div>

                                <div class="col-2 text-end">
                                    <h6 class="justify-text-end"><?= $concurrecia['total_resto'] ?></h6>
                                </div>

                                <div class="col-4 text-end">
                                    <h6 class="justify-text-end"><?= $concurrecia['desglose_final']?></h6>
                                </div>

                                <!-- fin de la fila -->
                            </div>

 





                        </div>
                    </div>



                    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                    
                    <div class="card-body p-4">
                            
                        <!-- div cabezera de tabla -->
                        <div class="row">
                            <div class="col-6">
                                    <h5 class="fw-bold mb-4">Detalle Sanciones</h5>
                            </div>
                            <div class="col-4 text-end">
                                    <button 
                                    class="btn btn-outline-primary btn-sm rounded-circle" 
                                    type="button" 
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#infoAdicional" 
                                    aria-expanded="false" 
                                    aria-controls="infoAdicional">
                                    <img src="recursos/plus.svg" alt="">
                                    </button>
                            </div>
                            
                            </div>


                            <!-- div de colapso de tabla -->
                            <div class="collapse mt-3" id="infoAdicional">


                            
                            <!-- <div class="row g-3 mb-4">
                                    
                                    <div class="col-4">
                                        <div class="border rounded p-3 text-center border-primary bg-primary-subtle shadow-sm" style="cursor: pointer;">
                                            <i class="bi bi-credit-card-2-front fs-4 text-primary"></i>
                                            <div class="small fw-bold mt-1">Tarjeta</div>
                                        </div>
                                    </div>

                                    <div class="col-4">
                                        <div class="border rounded p-3 text-center text-muted" style="cursor: pointer;">
                                            <i class="bi bi-wallet2 fs-4"></i>
                                            <div class="small fw-bold mt-1">Wallet</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="border rounded p-3 text-center text-muted" style="cursor: pointer;">
                                            <i class="bi bi-cash-stack fs-4"></i>
                                            <div class="small fw-bold mt-1">COD</div>
                                        </div>
                                    </div>
                            </div> -->
                            







                            <!-- header de las sanciones -->
                            <div class="row">
                                    <div class="col-6">
                                            <label for="form-label text-muted">Articulo</label>
                                    </div>
                                        
                                    <div class="col-4 text-end">
                                            <label for="form-label text-muted text-end">Total UT</label>
                                    </div>
                            </div>


                            <!-- finheader -->
                            <hr class="text-muted">
                            
                            <!-- inicio del cuerpo de la tabla -->
                            <div class="row mb-5">

                                <div class="col-6">
                                    <!-- ingresos extemporaneos -->
                                    <h6 class="text-muted  fw-bold mb-2">Literal B del artículo 86</h6>
                                    <small class="text-muted ">Numeral 3 </small>
                                    
                                </div>
                               
                                <div class="col-4 text-end">
                                    
                                    <h6 class="justify-text-end"><?= $ut_ingresos ?></h6>
                                    <small class="justify-text-end text-muted"> Ingresos</small>
                                </div>

                                <!-- fin de la fila -->
                            </div>


                            
                            <div class="row mb-5">

                                <div class="col-6">
                                    <!-- egresos extemporaneos -->
                                    <h6 class="text-muted  fw-bold mb-2">Literal A del artículo 86</h6>
                                    <small class="text-muted ">Numeral 1</small>
                                </div>
                               
                                <div class="col-4 text-end">
                                    <h6 class="justify-text-end"><?= $ut_egresos ?></h6>
                                    <small class="justify-text-end text-muted"> Egresos</small>
                                    
                                </div>

                                <!-- fin de la fila -->
                            </div>



                            
                            <div class="row mb-5">

                                <div class="col-6">
                                    <!-- falta de inscripcion -->
                                    <h6 class="text-muted  fw-bold mb-2">Literal B del artículo 86</h6>
                                    <small class="text-muted ">Numeral 1 </small>
                                </div>
                               
                                <div class="col-4 text-end">
                                    <h6 class="justify-text-end"><?= $inscripcion ?></h6>
                                    <small class="justify-text-end text-muted"> Inscripcion</small>
                                </div>

                                <!-- fin de la fila -->
                            </div>



                            
                            <div class="row mb-5">

                                <div class="col-6">
                                    <!-- Novedades -->
                                    <h6 class="text-muted  fw-bold mb-2">Literal B del artículo 86</h6>
                                    <small class="text-muted ">Numeral 2</small>
                                </div>
                               
                                <div class="col-4 text-end">
                                    <h6 class="justify-text-end"><?= $novedades ?></h6>
                                    <small class="justify-text-end text-muted">Novedades</small>
                                </div>

                                <!-- fin de la fila -->
                            </div>



                              <div class="row mb-5">

                                <div class="col-6">
                                    <!-- Obstaculizacion-->
                                    <h6 class="text-muted  fw-bold mb-2">Literal C del artículo 86</h6>
                                    <small class="text-muted ">Numeral 2</small>
                                </div>
                               
                                <div class="col-4 text-end">
                                    <h6 class="justify-text-end"><?= $obstaculizacion ?></h6>
                                    <small class="justify-text-end text-muted">Obstaculizacion</small>
                                </div>

                                <!-- fin de la fila -->
                            </div>






                              <div class="row mb-5">

                                <div class="col-6">
                                    <!-- Tachaduras -->
                                    <h6 class="text-muted  fw-bold mb-2"> Literal C del artículo 86</h6>
                                    <small class="text-muted ">Numeral 3</small>
                                </div>
                               
                                <div class="col-4 text-end">
                                    <h6 class="justify-text-end"><?= $tachaduras ?></h6>
                                    <small class="justify-text-end text-muted">Tachaduras</small>
                                </div>

                                <!-- fin de la fila -->
                            </div>

                            
                            
                              <div class="row mb-5">

                                <div class="col-6">
                                    <!-- Intereses -->
                                    <h6 class="text-muted  fw-bold mb-2">Literal C del artículo 86 </h6>
                                    <small class="text-muted ">Numeral 4</small>
                                </div>
                               
                                <div class="col-4 text-end">
                                    <h6 class="justify-text-end"><?= $noEnterados ?></h6>
                                    <small class="justify-text-end text-muted">No enterados</small>
                                </div>

                                <!-- fin de la fila -->
                            </div>



                            
                              <div class="row mb-5">

                                <div class="col-6">
                                    <!-- Retenciones mayores a la ley -->
                                    <h6 class="text-muted  fw-bold mb-2">Literal C del artículo 86 </h6>
                                    <small class="text-muted ">Numeral 1</small>
                                </div>
                               
                                <div class="col-4 text-end">
                                    <h6 class="justify-text-end">sin datos</h6>
                                    <small class="justify-text-end text-muted">Retenciones</small>
                                </div>

                                <!-- fin de la fila -->
                            </div>

                            <!-- fin del cuerpo de la tabla -->

                        </div>
                            <!-- div desplegable -->
                        </div>
                    </div>
                </div>



                <!--                                                    SANCIONES EN BS                           -->
                <!-- pequeno para poner un resumen donde van a ir los valores -->
                <div class="col-lg-4">

                    <div class="card border-0 shadow-sm sticky-top" style="border-radius: 15px; top: 20px;">

                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4">Resumen de Sancion</h5>
                          
                                    <?php  foreach($empresas_valores as $empresa):?>
                                    <div class="d-flex align-items-center mb-4">

                                        <!-- <img src="recursos/coffee.jpg" class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;"> -->
                                    

                                        <div>
                                            <h6 class="mb-0 fw-bold"><?= $empresa['nombre_empresa'] ?></h6>
                                            <small class="text-muted">Numero patronal: <?= $empresa['numero_patronal'] ?></small><br>
                                            <small class="text-muted"><?= $empresa['direccion'] ?></small>
                                            <small class="text-muted">, <?= $empresa['estado'] ?></small><br>
                                            <small class="text-muted"><?= $empresa['industria'] ?></small>
                                            

                                            
                                        </div>
                                    
                                    </div>
                                    <?php endforeach;?>
                                    

                                    <hr class="text-muted">

                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Subtotal Bs</span>
                                        <span class="fw-bold"></span>
                                    </div>
                                    <hr class="text-muted">


                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Ingresos</span>
                                        <span class="fw-bold"><?= number_format($bs_ingresos, 2, ',', '.') ?></span>
                                    </div>

                                    <div class="d-flex justify-content-between mb-4">
                                        <span class="text-muted">Egresos</span>
                                        <span class="fw-bold"><?= number_format($bs_egresos, 2, ',', '.') ?></span>
                                    </div>





                                                                                <!-- Sanciones extra  en BS-->
                                    <div class="d-flex justify-content-between mb-4">
                                        <span class="text-muted">Tachaduras</span>
                                        <span class="fw-bold"><?= number_format($bs_tachaduras, 2, ',', '.') ?></span>
                                    </div>

                                    <div class="d-flex justify-content-between mb-4">
                                        <span class="text-muted">Obstaculizacion</span>
                                        <span class="fw-bold"><?= number_format($bs_obstaculizacion, 2, ',', '.') ?></span>
                                    </div>

                                    <div class="d-flex justify-content-between mb-4">
                                        <span class="text-muted">Inscripcion</span>
                                        <span class="fw-bold"><?= number_format($bs_inscripcion, 2, ',', '.') ?></span>
                                    </div>

                                    <div class="d-flex justify-content-between mb-4">
                                        <span class="text-muted">Novedades</span>
                                        <span class="fw-bold"><?= number_format($bs_novedades, 2, ',', '.') ?></span>
                                    </div>

                                    <div class="d-flex justify-content-between mb-4">
                                        <span class="text-muted">Intereses no enterados</span>
                                        <span class="fw-bold"><?= number_format($bs_noEnterados, 2, ',', '.') ?></span>
                                    </div>
                                    
                                    <!-- hr es la linea -->
                                    <hr class="text-muted">
                                    <div class="d-flex justify-content-between mb-4">
                                        <span class="text-muted">Total otras Multas</span>
                                        <span class="fw-bold"><?= number_format($totalFinalExtras, 2, ',', '.') ?></span>
                                    </div>

                                    <!-- hr es la linea -->
                                    <hr class="text-muted">
                                    <div class="d-flex justify-content-between mb-4">
                                        <span class="text-muted">Total extemporaneos</span>
                                        <span class="fw-bold"><?= number_format($totalExtemporaneos, 2, ',', '.') ?></span>
                                    </div>
                                    
                                    <hr class="text-muted">
                                    <div class="d-flex justify-content-between mb-4">
                                        <span class="text-muted">Total no desglosado</span>
                                        <span class="fw-bold"><?= number_format($totalSancion, 2, ',', '.') ?></span>
                                    </div>
                                    
                                
                                    
                                    <div class="d-flex justify-content-between border-top pt-3 mb-4">
                                        <span class="h5 fw-bold">Total</span>
                                        <span class="h5 fw-bold "><small class="text-muted">Bs. </small><?= number_format($montoFinal, 2, ',', '.') ?></span>
                                    </div>
                                        
                                    <div class="d-grid">
                                        <input type="hidden" name="valor_concurrencia" value="<?= $concurrecia['total_resto']  ?>">
                                        <input type="hidden" name="sancion_mayor" value ="<?=$concurrecia['mayor']['valor']  ?>">
                                        <input type="hidden" name="nombre_sancion_mayor" value="<?= $concurrecia['mayor']['nombre'] ?>">
                                        <input type="hidden" name="otras_sanciones" value="<?=  $totalFinalExtras ?>">
                                        <!-- esto va arriba en el input de intereses -->
                                        <!-- <input type="hidden" name="intereses" value="<=  ?>"> -->
                                        <input type="hidden" name="nombre_empresa" value="<?=  $nombre_empresa_sancion ?>">
                                        

                                        <input type="hidden" name="accion_pago" value="orden_pago">
                                        
                                        <button type="submit" class="btn btn-primary btn-lg py-3 fw-bold shadow"  style="border-radius: 12px;">
                                            CONCURRIR
                                        </button>
                                    </div>

                            </form>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        <script src="../../js/bootstrap.bundle.min.js"></script>
        <script src="../../js/bootstrap.bundle.js"></script>
    </body>
    </html>


<?php
 

    echo $nombre_empresa_sancion;
    }else{
        
    header("location: facturacion.php?error=valor_cero&empresa=" . urlencode($nombre_empresa_sancion));
    }
}


?>