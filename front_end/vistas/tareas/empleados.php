<?php
require_once(__DIR__ . '/../../../back_end/config.php');
  require_once (FUNCIONES_TAREAS);
  require_once (EVALUAR_TRABAJADORES);

// $resultado=  mostrar_trabajadores_ingresos($conn);

// valor que viene de tareas.php
//$nombre_empresa =$_GET['empleado_empresa'];

$nombre_empresa= 'Empresa 1';
// echo "<h1>Empleados de la empresa: " . htmlspecialchars($nombre_empresa) . "</h1>";


/// el array de destructura para ser usado ambos ya que ambos se pasaron como return
['datos_completos' => $resultado, 'fecha_formateada' => $fecha] = mostrar_trabajadores_ingresos($pdo, $nombre_empresa);

// $egresos = mostrar_trabajadores_egresos($pdo, $nombre_empresa);

$evaluar_trabajadores = verificarExtemporaneos($resultado, $fechasFestivas);



   //""""""""""""""""""""""""""""TRABAJAR EN QUE SE VEA SOLAMENTE LA FECHA DE ACTUALIZACION DE UNA DETERMINADA EMPRESA EN FUNCIONES
    // 1. Crear el objeto DateTime a partir de la cadena de texto
    $fecha_actualizacion = new DateTime($fecha);

    //aqui esta es la fecha formateada de la ultimo excel subido por el usuario de esta empresa
    $fecha_formateada = $fecha_actualizacion->format('d-m-Y ');
    $hora_formateada = $fecha_actualizacion->format('h:i A');

    // ESTO muestra la ultima fecha de actualizacion desde que se subio el ingreso de esta empresa en particular

    echo "<h4>Ultima actualizacion: ". htmlspecialchars($fecha_formateada). "</h4>";
    echo "<h5>". htmlspecialchars($hora_formateada). "</h5>";



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
  

    <!--  ESTO ES PARA CAMBIAR el color y los botones para cambiar de pestana dentro de la misma pagina -->
<style>

    .tab-content { display: none; }
    .tab-content.active { display: block; }
    .tab-button {
      padding: 8px 16px;
      margin-right: 5px;
      border: 1px solid #ccc;
      background: #f1f1f1;
      cursor: pointer;

    }
    /* hoover al hacer click en la pestana */
    .tab-button.active {
      background: #000000ff;
      color: white;
    }
  </style>



    <!-- query -->

    <!-- bootstrap -->
    <link href="../../css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

  </head>

<body>





<br>


 
  
<!-- MODAL para cargar empleados a la empresa -->
    
      <center> 
      <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#exampleModal">
        Cargar empleados
      </button>
     </center> 
     <!--  ESTE ES EL MODAL PARA CARGAR ARCHIVOS EXCEL -->

      <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Cargar Archivos excel</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> 
            </div>
            <div class="modal-body">
              <!-- cuerpo del modal  -->


                    <!-- cargar archivos excel ingresos -->
                  <div class="contain " style="width: 60%; margin: 0 auto;">

                    <div class="row row-cols-auto">
                
                        <form action="../../../back_end/tareas/pruebas_tareas/automatizaciones.php" method="POST" enctype="multipart/form-data">
                            <div class="col">
                              <label for="" class="form-label">
                                  Selecione un archivo excel de INGRESOS (*)
                              </label>
                              <input type="file" name="excel_ingresos" id="excel" class="form-control w-55">
                            </div>

                            <div class="col">
                                <label for="" class="form-label">
                                Seleciona un archivo excel de EGRESOS (*)
                                </label>
                                <input type="file" name="excel_egresos" id="excel_egresos" class="form-control w-55">
                            </div>
                            
                    </div><!-- row -->
                          
                        
                  </div>
            
            </div><!-- final cuerpo modal -->
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> 
                  <button class="btn btn-primary mt-2" name="enviar_ingresos"> importar Ingresos</button>
              </div>
          </div>
        </div>
      </div>
      </form>
<!-- FIN MODAL para cargar empleados a la empresa -->


<br>


<br>
<!-- botones para cambiar de pestana a otra tabla -->
 <div class="tab-links">
    <button class="tab-button active" data-tab="tab1">sanciones</button>
    <button class="tab-button" data-tab="tab2">trabajadores</button>
    <button class="tab-button" data-tab="tab3">extranjeros</button>


</div>


<div id="tab1" class="tab-content active">   
<!-- tabla  -->
<center> <h3>Sancionables</h3></center>
   
        <table class="table" >
        <thead>
            <tr>
            <th scope="col">#</th>
            <th scope="col">cedula</th>
            <th scope="col">nombre y apellido</th>
            <th scope="col">fecha de Ingreso</th>
            <th scope="col">movimiento de Ingreso</th>
            <th scope="col">tipo sancion</th>
            <th scope="col">articulo</th>
            <th scope="col">monto</th>
            </tr>
        </thead>
        <tbody >
         
          <!--ESTOS SON LOS TRABAJADORES ACTIVOS QUE AUN TRABAJAN EN LA EMPRESA -->
              <?php foreach ($evaluar_trabajadores as $empleado): ?>

                
                <?php //foreach ($evaluar_trabajadores as $evaluar): ?>
                <?php if(empty($empleado['fecha_egreso'])):

                      //formatea la cedula y le quita el VE de la consulta. para que muestre solamente el numero
                      $prefijos_a_eliminar = ['VE', 'EXT']; 
                      $cedula = $empleado['cedula'];
                      //elimina los prefijos VE y EXT de la cedula
                      $cedula_formateada= str_replace($prefijos_a_eliminar, '', $cedula);

                  ?>
                    
            <tr>
                <th scope="row"><?= htmlspecialchars($empleado['id']) ?></th>
                  <td><?= htmlspecialchars($cedula_formateada) ?></td>
                  <td><?= htmlspecialchars($empleado['nombre'].'  '.$empleado['apellido']) ?></td>
                 
                  <td><?php if($empleado['fecha_ingreso'] != null){
                      echo htmlspecialchars($empleado['fecha_ingreso']);
                    }else {
                      echo "trabajador activo";
                    }
                 
                    ?></td>
                  <td><?= htmlspecialchars($empleado['movimiento_ingreso']) ?></td>
                  
                
                    <!-- sancion de 3 dias o no aplica de evaluar_trabajadores.php -->
                    <td><?=  htmlspecialchars($empleado['sancion'])  ?></td>
                    
               
<!-- 
                <td class="selectNull">
                  <select >
                    <option value="">-- Elige una fruta --</option>
                    <option value="manzana">Manzana</option>
                    <option value="banana">Banana</option>
                    <option value="pera">Pera</option>
                  </select>
                </td>  -->
                <?php endif;?>
             </tr>

                    
             <?php endforeach; ?>
           
             <tr>
                
             
            </tr>
            
        </tbody>
        </table>

<!-- div de la pestana -->
</div> 


<!-- pestana egresos -->
<div id="tab2" class="tab-content ">   
    <!-- tabla  -->
      <center><h3>Egresos Totales</h3></center>
        <table class="table">
        <thead>
            <tr>
            <th scope="col">#</th>
            <th scope="col">cedula</th>
            <th scope="col">nombre y apellido</th>
            <th scope="col">fecha de egreso</th>
            <th scope="col">movimiento de egreso</th>
            </tr>
        </thead>
        <tbody >
      

          <!-- hace un bucle por todo el sistema para mostrar todos los datos de la base de datos -->
           <!-- muestra los egresos por ahora, pero la idea seria mostrar todos los ingresos y egresos  -->
           <?php foreach ($egresos as $egresos_empleados): ?>
            <tr>
              <!-- <th scope="row"><= htmlspecialchars($empleado['id']) ?></th>
                <td><= htmlspecialchars($empleado['cedula']) ?></td>
                <td><= htmlspecialchars($empleado['nombre'].'  '.$empleado['apellido']) ?></td>        
                -->
                <!-- verifica si el trabajador esta activo y no ha tenido egresos aun -->
                <th scope="row"><?= htmlspecialchars($egresos_empleados['id']) ?></th>
                <td><?= htmlspecialchars($egresos_empleados['cedula']) ?></td>
                <td><?= htmlspecialchars($egresos_empleados['nombre'].'  '.$egresos_empleados['apellido']) ?></td>
                <td><?= htmlspecialchars($egresos_empleados['fecha_egreso']) ?></td>
                <td><?= htmlspecialchars($egresos_empleados['movimiento_egreso']) ?></td> 
                        
                

                   <!-- verifica si el trabajador esta activo y no ha tenido movimiento egresos aun -->
                <!-- <td><php if($empleado['movimiento_egreso'] != null){
                      echo htmlspecialchars($empleado['movimiento_egreso']);
                    }else {
                      echo "sin egresos";
                    }
                 
                    ?></td> -->

                  <!-- <td class="selectNull">
                  <select >
                    <option value="">-- Elige una fruta --</option>
                    <option value="manzana">Manzana</option>
                    <option value="banana">Banana</option>
                    <option value="pera">Pera</option>
                  </select>
                </td>  -->
             </tr>
             
              <?php endforeach; ?>
     
            
        </tbody>
        </table>
<!-- div de la pestana -->
</div> 



<!-- pestana egresos -->
<div id="tab3" class="tab-content ">   
    <!-- tabla  -->
      <center><h3>Egresos Totales</h3></center>
        <table class="table">
        <thead>
            <tr>
            <th scope="col">#</th>
            <th scope="col">cedula</th>
            <th scope="col">nombre y apellido</th>
            <th scope="col">fecha de egreso</th>
            <th scope="col">movimiento de egreso</th>
            </tr>
        </thead>
        <tbody >
          <!-- hace un bucle por todo el sistema para mostrar todos los datos de la base de datos -->
           <!-- muestra los egresos por ahora, pero la idea seria mostrar todos los ingresos y egresos  -->
          <?php foreach ($resultado as $empleado): 
            
                      $cedula = $empleado['cedula'];
               if (strpos($cedula, 'EXT') === 0) {
                    // Elimina el prefijo "EXT" para mostrar solo el número
                     $cedula_formateada = str_replace('EXT', '', $cedula);
        ?>

                      <tr>
                        <th scope="row"><?= htmlspecialchars($empleado['id']) ?></th>
                          <td><?= htmlspecialchars($cedula_formateada) ?></td>
                          <td><?= htmlspecialchars($empleado['nombre'].'  '.$empleado['apellido']) ?></td>        
                        
                          <!-- verifica si el trabajador esta activo y no ha tenido egresos aun -->
                          <td><?php if($empleado['fecha_egreso'] != null){
                                echo htmlspecialchars($empleado['fecha_egreso']);
                              }else {
                                echo "trabajador activo";
                              }
                          
                              ?></td>

                            <!-- verifica si el trabajador esta activo y no ha tenido movimiento egresos aun -->
                          <td><?php if($empleado['movimiento_egreso'] != null){
                                echo htmlspecialchars($empleado['movimiento_egreso']);
                              }else {
                                echo "sin egresos";
                              }
                          
                              ?></td>
                            <!-- 
                            <td class="selectNull">
                             se le coloca la clase select, para que los valores de la tabla no se puedan seleccionar 
                            <select >
                              <option value="">-- Elige una fruta --</option>
                              <option value="manzana">Manzana</option>
                              <option value="banana">Banana</option>
                              <option value="pera">Pera</option>
                            </select>
                          </td>  -->
                      </tr>

            <?php } ?>
             <?php endforeach; ?>
           
          
            
        </tbody>
        </table>
<!-- div de la pestana -->
</div> 



  
  <!-- carga las pestanas -->
  <script src="../../../back_end/tareas/js/empleados.js" >
  </script>

  <!-- esto es para el jquery -->

  <script src="../../js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
  </script>

</body>
</html>