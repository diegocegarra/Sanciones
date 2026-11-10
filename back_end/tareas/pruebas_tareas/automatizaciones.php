<?php
// ARCHIVO DE AUTOMATIZACIONES QUE SE COMUNICA CON FUNCIONES.php 
require_once(__DIR__ . '/../../config.php');
require_once(FUNCIONES_TAREAS);

//  $estatus =$_GET['estatus'];
//  $tarea_id =$_GET['id_tarea'];

 // valores que vienen de empresas
 $nombre_empresa =$_POST['nombre_empresa'];
 $id_empresa  =$_POST['id_empresa'];
 $estado_empresa =$_POST['estado_empresa'];

//  function tareas_estatus($pdo, $tarea_id, $estatus){
//         $fecha = new DateTime("now", new DateTimeZone("America/Caracas"));
//         $fecha_estatus= $fecha->format("Y-m-d H:i:s");
//         // cambiar en la tabla de estatus

//         if(isset($estatus)){// cambiar en la tabla de estatus
//             cambiarEstatusDB($pdo, $estatus, $tarea_id, $fecha_estatus );
//             header("Location: ../../../front_end/vistas/tareas/tareas.php");
//         }

// }

// esta funcion sirve para crear el codigo de la tarea una vez se inserta en la tabla tareas en el mysql
function crearCodigo($id_tarea){
    return "#TAR" . str_pad($id_tarea, 5, "0", STR_PAD_LEFT);
}

// crea tarea de empresas
function crearTarea($pdo, $nombre_empresa, $estado_empresa){
    return insertarTarea($pdo, $nombre_empresa, $estado_empresa);
}

// // redirige las funciones
// $action =$_GET['accion'];


  if(isset($_POST['accion_tarea_crear'])){
        crearTarea($pdo, $nombre_empresa, $estado_empresa);
        header('Location: /IVSS/front_end/vistas/empresas/empresas.php?status=tareaCreada');
  }
//   if($action == 'cambiar_status'){
//         //  tareas_estatus($pdo, $tarea_id, $estatus);
//   }

?>