<?php
# 1 ESTE ARCHIVO SE ENCARGARA DE TODA LA CONEXION DE LA BASE DE DATOS EN FORMATO JASON PASADO DE JAVASCRIPT, PARA LA BASE DE DATOS
# 2 todos los datos van a ser pasados con el valor accion, del javascript
# 3. a este archivo se le pasaron promesas de js, para hacerlo mas facil de mantener

// 1. Iniciar buffer para evitar que errores o espacios en blanco rompan el JSON
ob_start();

// 2. Configurar cabeceras de respuesta
header('Content-Type: application/json; charset=utf-8');


ini_set('log_errors', 1);


// 3. No mostrar errores al usuario final (seguridad)
ini_set('display_errors', 0);
// 3. Gestión de sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    // 4. Carga de dependencias
    require_once(__DIR__ . '/../../config.php');
    require_once(CONEXION_PATH);
    require_once (ERRORES_PATH);
    require_once(LOGS);
    require_once(FUNCIONES_ROLES);

    // 5. Conexión a la base de datos
    $pdo = conectar_db();

    // 6. Validación de la acción (Soporte para 'accion' o 'accion_fechas')
    $accion = $_POST['accion'] ?? $_POST['accion_fechas'] ?? null;

    if (!$accion) {
        ob_clean(); // Limpiar cualquier salida previa
        echo json_encode(['success' => false, 'message' => 'Acción no especificada']);
        exit();
    }

    // 7. Captura de variables comunes (con valores por defecto)
    $id = $_POST['id'] ?? $_POST['id_fechas'] ?? $_POST['id_tarea'] ?? null;
    $busqueda = $_POST['busqueda_tarea'] ?? '';
    $fecha = $_POST['fecha'] ?? null;

    // 8. Limpiar el buffer antes de cualquier salida de las funciones
    if (ob_get_length()) ob_clean();

    // 9. Switch Principal
    switch ($accion) {
        case 'eliminar_fecha':
            if ($id) {
                fechasFestivasEliminar($pdo, $id);
            } else {
                echo json_encode(['success' => false, 'message' => 'Falta el ID de la fecha']);
            }
            break;
            
        case 'buscar':
            buscarTarea($pdo, $busqueda);
            break;

        case 'guardar_fechas':
            if ($fecha) {
                fechasFestivasAgregar($pdo, $fecha);
            } else {
                echo json_encode(['success' => false, 'message' => 'Fecha no proporcionada']);
            }
            break;

        case 'obtener_fechas':
            fechasFestivasObtener($pdo);
            break;

        case 'cambio_estatus':
            cambioEstatus($pdo);
            break;

        case 'eliminar_tarea':
            // Asegúrate que tu función eliminarTarea acepte $pdo e $id
            eliminarTarea($pdo, $id);
            break;

        case 'asignar_tarea_usuario':
            asignar_tarea_usuario($pdo);
            break;

        default:
            echo json_encode(['success' => false, 'message' => "Acción '$accion' no reconocida"]);
            break;
    }

} catch (Exception $e) {
    // Captura cualquier error fatal de los require o de lógica
    if (ob_get_length()) ob_clean();
    echo json_encode([
        'success' => false, 
        'message' => 'Error en el servidor: ' . $e->getMessage()
    ]);
}

// 10. Finalizar ejecución para evitar que se cuele cualquier otro texto
exit();


function asignar_tarea_usuario($pdo){
    header('Content-Type: application/json');
    try {
        $pdo = conectar_db(); // Tu función de conexión
        
        $usuario_asignado = $_POST['usuario_asignado'];
        $nombre_tarea = $_POST['nombre_tarea'];

        if (empty($usuario_asignado)) {
            echo json_encode(['success' => false, 'message' => 'Usuario no seleccionado']);
            exit;
        }

        $sql = "INSERT INTO tareas.asignar_tarea (usuario_asignado, nombre_tarea) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $resultado = $stmt->execute([$usuario_asignado, $nombre_tarea]);

        if ($resultado) {
            echo json_encode(['success' => true, 'message' => 'Asignado correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error en la base de datos']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit; // Importante para que no se imprima el resto del HTML
}










// Debe estar definida así:
function eliminarTarea($pdo, $id) { 
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');

    if ($id && is_numeric($id)) {
        try {
            $sql = "DELETE FROM tareas.tareas WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $resultado = $stmt->execute([$id]);

            if ($resultado) {
                echo json_encode([
                    "success" => true, 
                    "message" => "ID $id eliminado correctamente."
                ]);
            } else {
                echo json_encode(["success" => false, "message" => "No se encontró el registro."]);
            }
        } catch (PDOException $e) {
            echo json_encode(["success" => false, "message" => "Error de DB: " . $e->getMessage()]);
        }
    } else {
        // Este es el error que te está saliendo porque $id llega vacío
        echo json_encode(["success" => false, "message" => "ID no válido ($id)."]);
    }
    exit;
}
//>>>>>>>>>>>>>>>>>>>FUNCIONES PARA LAS FECHAS FESTIVAS EN EL PERFIL. 

// funcion para obtener las fechas festivas de la base de datos
function fechasFestivasObtener($pdo) {
      
   ob_clean(); 
header('Content-Type: application/json');
// $pdo->exec("USE usuarios");

    try {
       $stmt = $pdo->query("SELECT id, fecha FROM usuarios.fechas_festivas");
        $fechas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'fechas' => $fechas]);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}

// funcion para eliminar las fechas festivas de la base de datos
function fechasFestivasEliminar($pdo) { // Usamos el parámetro que viene de la función
header('Content-Type: application/json');
  $pdo->exec("USE usuarios");
    

    try {
        if (isset($_POST['id'])) {
            $id = $_POST['id'];

            $stmt = $pdo->prepare("DELETE FROM fechas_festivas WHERE id = :id");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $resultado =$stmt->execute();
            // guardar en el log
            if($resultado){
                 // registro de logs
                registrarLog($pdo, $_SESSION['usuario_id'], "ELIMINAR", "elimino la fecha festiva con el id: " . $id);
                }

            echo json_encode(['success' => true, 'message' => 'Fecha eliminada con éxito']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se proporcionó un ID válido']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    
}

// funcion para insertar las fechas festivas de la base de datos.
function fechasFestivasAgregar($pdo, $data){
      $pdo->exec("USE usuarios");

        header('Content-Type: application/json');
            ini_set('display_errors', 1); //Habilita la visualización de errores en la salida, útil para depuración.
            error_reporting(E_ALL);// Reporta todos los errores de PHP posibles.


                
             $fecha = $_POST['fecha'];
            try {
                //Inicia una transacción de base de datos. Esto significa que todas las 
                //operaciones SQL subsiguientes (los INSERT) no se harán permanentes 
                //hasta que se llame a $pdo->commit(). Si ocurre un error, se llamará a $pdo->rollBack(), deshaciendo todos los cambios.
                $pdo->beginTransaction();

                // Insertar nuevas fechas
                //:fecha: Es un marcador de posición (placeholder) para que el valor de la fecha se inserte de forma segura mediante bindValue().
                $stmt_insert = $pdo->prepare("INSERT IGNORE INTO fechas_festivas (fecha) VALUES ( :fecha)");
               
                $fecha_str = is_array($fecha) ? reset($fecha) : $fecha;

                if (is_string($fecha_str) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_str)) {
                    $stmt_insert->bindValue(':fecha', $fecha_str, PDO::PARAM_STR);
                    $resultado = $stmt_insert->execute();
                      if($resultado){
                         // registro de logs
                        registrarLog($pdo, $_SESSION['usuario_id'], "CREAR", "registro la fecha: " . $fecha_str);
                    }
                }
                //}

                $pdo->commit();
                echo json_encode(['success' => true, 'message' => 'fechas guardados /actualizados con éxito.']);
            } catch (Exception $e) {
                //: Deshace todos los cambios de la base de datos realizados desde $pdo->beginTransaction().
                // $pdo->rollBack();
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
}


//>>>>>>>>>>>>>>>>>>>>>FUNCIONES PARA LA TABLA TAREAS EN EL TAREAS.PHP. 

// 1. La función SÓLO necesita la conexión $pdo
// Definición de la función de búsqueda
// funcion para buscar una fecha festiva de la base de datos
function buscarTarea($pdo, $busqueda_tarea) {
      $pdo->exec("USE tareas");
    header('Content-Type: application/json');
    error_log("Buscando: " . $busqueda_tarea); 

    try { 
        $palabras = explode(" ", $busqueda_tarea); 
        
        $condiciones = []; 
        $params = []; 
        
        foreach ($palabras as $p) { 
            // aqui se usa una iteracion para buscar en todos los registros. 
            $condiciones[] = "(codigo_tarea LIKE ? OR nombre_empresa LIKE ? OR cantidad_tarea LIKE ?)"; 
            $params[] = "%$p%"; 
            $params[] = "%$p%"; 
            $params[] = "%$p%"; 
        } 
        
    
        $sql = "SELECT DISTINCT t.id, t.codigo_tarea, t.nombre_empresa, t.cantidad_tarea, t.estado, t.creado_en, e.estatus_nombre FROM tareas t 
        INNER JOIN estatus_tarea e ON t.id = e.id_estatus WHERE " . implode(" AND ", $condiciones) . " LIMIT 100"; 
        
        $stmt = $pdo->prepare($sql); 
        $stmt->execute($params); 
        $authors = $stmt->fetchAll(PDO::FETCH_ASSOC); 
        
        echo json_encode($authors ?: []);
      

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error en la consulta: ' . $e->getMessage()]);
    }


    # ESTO ES PARA HACERLO CON BILLONES DE REGISTROS PARA HACER UNA CONSULTA POR INDICES, AUNQUE NO FUNCIONA MUY BIEN

    //     try {
    //     $sql = "SELECT t.id, t.codigo_tarea, t.nombre_empresa, t.cantidad_tarea, e.estatus_nombre
    //             FROM tareas t
    //             INNER JOIN estatus_tarea e ON t.id = e.tarea_id
    //             WHERE MATCH(t.codigo_tarea, t.nombre_empresa, t.cantidad_tarea)
    //                 AGAINST (? IN BOOLEAN MODE)
    //             LIMIT 100";

    //     $stmt = $pdo->prepare($sql);

    //     // Transformar la búsqueda en formato boolean (+palabra +palabra)
    //     $palabras = explode(" ", $busqueda_tarea);
    //     $search_param = '+' . implode(' +', $palabras);

    //     $stmt->execute([$search_param]);

    //     $authors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //     echo json_encode($authors ?: []);

    // } catch (PDOException $e) {
    //     http_response_code(500);
    //     echo json_encode(['error' => 'Error en la consulta: ' . $e->getMessage()]);
    // }

    
}


#>>>>>>>>>>>>>>>>>>>>>>>>>> FUNCIONES DE PERMISOS
# 1. Aqui van a estar todas las funciones que son permisos que se ejecutan en tiempo real. 
// esta funcion es para editar una tarea en la base de datos, 

//                                                                      EDITAR TAREAS

// function editarTarea($pdo, $valorTarea){
//         $pdo->exec("USE tareas");
//         error_reporting(0);
//         header('Content-Type: application/json');


//         try{
//             // id selecionada que se pasa en el js, importante
//             $id_tareas = $_POST['id_selecionado'];
            
//             // columna para determinar cual columna se va a editar de la base de datos si es el nombre o codigo etc. 
//             $columna = "";
//             // tabla para cambiar de tabla dependiendo el valor que pase el js para editarlo en la base de datos. 
//             $tabla = "tareas"; // Por defecto actualizamos la tabla tareas

//             $codigo_selecionado = $_POST['codigo_selecionado'];
//             $nombre_selecionado = $_POST['nombre_selecionado'];
//             $extemporaneo_selecionado = $_POST['extemporaneo_selecionado'];
//             $inconsistencia_selecionada =$_POST['inconsistencia_selecionada'];
//             $trabajador_selecionado = $_POST['trabajador_selecionado'];
//             $estatus_selecionado =  $_POST['estatus_selecionado'];

//             // estos if van a determinar los campos de la tabla tarea en el msyql y lo va a guardar en 
//             //la variable columna para insertarlo dependiendo de lo que se le paso en el switch de permisos.js en el apartado para pasarle el jason. 

//             if($codigo_selecionado){
//                 $columna = 'codigo_tarea';

//             } else if($nombre_selecionado){
//                 $columna = 'nombre_empresa';

//             } else if($extemporaneo_selecionado){
//                 $columna = 'extemporaneos';

//             } else if($inconsistencia_selecionada){
//                 $columna = 'incosistencias';

//             } else if($trabajador_selecionado){
//                 $columna = 'trabajadores_activos';
//             }else if($estatus_selecionado){
//                 $columna = 'estatus_nombre';
//                 $tabla = 'estatus_tarea'; // <--- Cambiamos de tabla aquí
//                 $campo_id = 'tarea_id';   // <--- El JOIN usa tarea_id, no id
//             }
        
//             // esta condicion sirve para cambiar el nombre del id, ya que la tabla tareas y estatus_tarea tienen un nombre de id id diferente
//             $cambiadorTabla = ($tabla === 'tareas') ? 'id' : 'tarea_id';

//             $sql = "UPDATE tareas SET $columna = :valor WHERE id = :id";
//                 $stmt = $pdo->prepare($sql);
                
//                 // Ejecutamos pasando los valores en un array
//                 $resultado = $stmt->execute([
//                     ':valor' => $valorTarea,
//                     ':id'    => $id_tareas
//                 ]);

                  
                    

//                 if ($resultado) {
//                      // registro de logs
//                      registrarLog($pdo, $_SESSION['usuario_id'], "EDITAR", "Se edito la tarea con el nombre: " . $nombre_selecionado. " A: ".$valorTarea);

//                     echo json_encode(['success' => true, 'message' => 'Editado con éxito']);
//                     exit;
//                 } else {
//                     echo json_encode(['success' => false, 'message' => 'No se pudo actualizar']);
//                     exit;
//                 }

//         }catch(Exception $e){
//             echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
//         }



// }




//                                                          EDITAR UNIVERSA CUALQUIER VALOR EN FUNCIONES_ROLES



// function editarUniversal($pdo, $rol){
//  $permisos_editar =$rol->permisos->editar;
// // PARA EL EJEMPLO (Simulación de permisos):
    
//     $ejecutor = new AccionesUsuario($permisos_editar, $pdo);

//     if ($_POST['accion'] === 'edicion_universal') {
//         $res = $ejecutor->ejecutar_edicion_general(
//             $_POST['schema'],
//             $_POST['tabla'],
//             $_POST['columna'],
//             $_POST['valor'],
//             $_POST['id']
//         );
//         echo json_encode($res);
//         exit;
//     }
// }







//Esta funcion viene de permisos.js
function cambioEstatus($pdo){
// Evita que cualquier error previo de PHP ensucie la respuesta
    ob_start(); 
    
    // IMPORTANTE: Si usas $_SESSION, esto debe estar aquí o al inicio del archivo
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    header('Content-Type: application/json');



    try{
         $pdo->exec("USE tareas");

             
        // FECHA ACTUAL PARA EL CAMBIO DE ESTATUS
        $fecha = new DateTime("now", new DateTimeZone("America/Caracas"));
        $fecha_estatus= $fecha->format("Y-m-d H:i:s");

        $valorEstatus = $_POST['estatus'] ?? null;
        $idEstatus = $_POST['id_estatus'] ?? null;

        if (!$valorEstatus || !$idEstatus) {
            throw new Exception("Datos incompletos enviados al servidor");
        }

        $pdo->beginTransaction();
        
        $sql_estatus = " INSERT INTO estatus_tarea (estatus_nombre, tarea_id, estatus_cambiado) values(?, ?, ?)
                                   
                                    ON DUPLICATE KEY UPDATE 
                                    estatus_nombre = VALUES(estatus_nombre),
                                    estatus_cambiado = VALUES(estatus_cambiado)";

        $stmt_estatus= $pdo->prepare($sql_estatus); 
        $resultado = $stmt_estatus->execute([$valorEstatus,  $idEstatus, $fecha_estatus]);
        // registro de logs
           if($resultado){
                        registrarLog($pdo, $_SESSION['usuario_id'], "ACTUALIZAR", "Actualizacion de estatus a: " . $valorEstatus);
                    }
        $pdo->commit();

    // El éxito se envía AL FINAL, después de confirmar todo
        echo json_encode(['success' => true, 'message' => 'Estatus cambiado con éxito']);
    } catch(Exception $e) {
  if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        // Respondemos el error en formato JSON
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit; // Detenemos la ejecución para evitar que se cuele otro texto

}?>