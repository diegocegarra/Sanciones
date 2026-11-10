<?php
require_once(__DIR__ . '/../../config.php');
require_once(CONEXION_PATH);
require_once(FUNCIONES_TAREAS);


//$mostrar_ingresos=  mostrar_trabajadores_ingresos($conn);

// funcion mysql para obtener todas las fechas festivas
$pdo=  conectar_db();



$festividades = mostrarFestividades($pdo);

$fechasFestivas =array_map(function($fecha) {
    return (string) trim($fecha); // Asegura que es string y elimina espacios
}, $festividades);
//var_dump($fechasFestivas);


// verifica los trabajaodres extemporaneos y las siguientes condiciones
# 1. tiene minimo de 5 anos
# 2. tiene maximo de 3 dias de registro
# 3. callo en fecha festiva?

function verificarMaxSancion(){
 date_default_timezone_set("America/Caracas");
   

    // aqui va a calcular 5 anios atras basado en la fecha actual, es decir que si es 2025 va a evaluar desde 2020

    /// aqui se evalua el tiempo actual con new dateTime() y luego agarra la misma fecha actual pero hace 5 anos atras
   return $sancionMaxima= (new DateTime())->modify("-5 years");

//   echo "Fecha límite de 5 anos atras: " . $fecha_max_sancion->format("d-m-Y") . "<br>". "<br>";
    
}

function Sanciones($fechaSancion, $movimientoSancion, $fechasFestivas){

           $fecha_max_sancion= verificarMaxSancion();

  
          // conteo de los dias laborables
          $diasLaborales = 0;
          if (!($movimientoSancion instanceof DateTime)) {
                // Si no hay fecha de movimiento, devolvemos 0 o lo que consideres lógico
                return 0; 
            }
          
            if ($fechaSancion > $fecha_max_sancion ) {// evalua si pasaron 5 anios 
            
                // 2. Calcula la diferencia total de días que pasan de egresos a los ingresos
            $ingreso = $fechaSancion->diff($movimientoSancion);
          
            $ingresos_por_dia = $ingreso->days + 1; // +1 para incluir el día inicial es decir el dia el cual empieza a contar
            
          
                    
                  // aqui clona los valores de fechaEgreso, y verifica si es menor y se mueve 1 a la vez para contarla.  
                    for ($fecha = clone $fechaSancion; $fecha <= $movimientoSancion; $fecha->modify('+1 day')) {
                        // aqui esto agarra la funcion de esDiaLaboral, para verificar si cae sabado, domingo, o festivo para no contarlo
                        if (esDiaLaboral($fecha, $fechasFestivas)) {

                         
                            $diasLaborales++;
                          
                        }else {
                      //      echo "no laborable: " . $fecha->format('d-m-Y') . "<br>";
                        }
                    }
                     
                     
                    // almacena el contador de dias laborables en ingresos por dia
                    $ingresos_por_dia =  $diasLaborales;
                    return $ingresos_por_dia;

                   
                   

            }
}



function verificarExtemporaneos($resultado, $fechasFestivas){
   //$sanciones = []; 
   $trabajadoresExtemporaneos = [];
   
          $fecha_max_sancion= verificarMaxSancion();

  
    // bucle para agarrar la fechas de la base de datos
    foreach ($resultado as $fechas) {
        // el string varchar que viene del excel aqui se transforma en formato DATE TIME
        $fechaIngreso = DateTime::createFromFormat("d-m-Y", $fechas['fecha_ingreso'] ?? '');
        $movimientoIngreso = DateTime::createFromFormat("d-m-Y", $fechas['movimiento_ingreso'] ?? '');

        $fechaEgreso = DateTime::createFromFormat("d-m-Y", $fechas['fecha_egreso'] ?? '');
        $movimientoEgreso = DateTime::createFromFormat("d-m-Y", $fechas['movimiento_egreso']?? '');

        // $fechaIngreso = $fechas['fecha_ingreso'];
        // $movimientoIngreso = $fechas['movimiento_ingreso'];

        // $fechaEgreso = $fechas['fecha_egreso'];
        // $movimientoEgreso = $fechas['movimiento_egreso'];

        
        $ingresos_por_dia= Sanciones($fechaIngreso, $movimientoIngreso, $fechasFestivas);
        $egresos_por_dia = Sanciones($fechaEgreso, $movimientoEgreso, $fechasFestivas);
          
                      //  Copiar todos los datos del empleado y agregar la sanción
                      // Copia todos los campos (id, cedula, nombre, etc.)
                      //Copia todo el array original ($fechas) que contiene los datos de la base de datos (id, cedula, nombre, apellido, fecha_ingreso, etc.). Si no hicieras esta copia, esos datos se perderían.
                      $datos_empleado_sancion = $fechas; 
                      // al cuarto dia ya es extemporaneo

                      $sancionIngreso = ($ingresos_por_dia <= 2) ? "3 días Ingreso" : "no aplica";

                      $sancionEgreso = ($egresos_por_dia <= 2) ? "3 días Egreso" : "no aplica";
                       
                      // Agrega los resultados del cálculo
                      $datos_empleado_sancion['dias_contados'] = $ingresos_por_dia;
                      $datos_empleado_sancion['sancion_ingreso'] = $sancionIngreso;
                      $datos_empleado_sancion['sancion_egreso'] = $sancionEgreso;
                      
                      // 4. Almacenar el registro COMPLETO en el array de resultados
                      $trabajadoresExtemporaneos[] = $datos_empleado_sancion;

                  
                
             

            
          
 
        // }// fin egresos null
    
  }// // fin foreach
return $trabajadoresExtemporaneos;
        
                    

    
}

// funcion para evaluar si es un dia laboral y si ese dia no fue sabado domingo o festivo
function esDiaLaboral(DateTime $fecha, array $fechasFestivas): bool {
    $diaSemana = (int) $fecha->format('w'); // 0 = domingo, 6 = sábado
    $formato = $fecha->format('d-m-Y');

    
    // Si es sábado, domingo o festivo, no es laborable
    if ($diaSemana === 0 || $diaSemana === 6 || in_array($formato, $fechasFestivas)) {
       return false;
    }

      return true;
}




?>