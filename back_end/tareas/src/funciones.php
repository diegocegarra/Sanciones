
<?php

ini_set('log_errors', 1);

// 3. No mostrar errores al usuario final (seguridad)
ini_set('display_errors', 0);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}#ARCHIVO PRINCIPAR CUIDADO CON ESTE ARCHIVO
#MODELO PRINCIPAL DE TODO EL SISTEMA
#ACTIVOS CHRISTOPHER Y JEAN, ESTE ES EL QUE SE VA A ENCARGAR DE INSERTAR LOS DATOS EN EL MYSQL Y VA A MANEJAR TODO DE TAREAS
# PUEDEN USAR ESTO DE EJEMPLO PARA QUE CONCENTREN TODAS SUS CONSULTAS AQUI




# ESTE ARCHIVO VA A ENCARGARSE DE LA BASE DE DATOS DE TAREAS
require_once(__DIR__ . '/../../config.php');
require_once(CONEXION_PATH);
require_once (ERRORES_PATH);
//logs del sistema
require_once (LOGS);
// require_once(ROLES_PATH);
// require_once(PERMISOS_PATH);



$pdo=  conectar_db();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'asignar_rol_directo') {

$usuario_id = $_POST['id_usuario'] ?? null;
    $rol_id = $_POST['nuevo_rol_id'] ?? null;

cambiarRol($pdo, $usuario_id, $rol_id);
}

function cambiarRol($pdo, $usuario_id, $rol_id){

// $usuario_id = $_POST['id_usuario'] ?? null;
//     $ = $_POST['nuevo_rol_id'] ?? null;
    if ($usuario_id && $rol_id) {
        try {
            // Preparamos el UPDATE
            $sql = "UPDATE usuarios.usuario SET rol_id = :rol_id WHERE id = :user_id";
            $stmt = $pdo->prepare($sql);
            
            // Ejecutamos la actualización
            $resultado = $stmt->execute([
                ':rol_id'  => $rol_id,
                ':user_id' => $usuario_id
            ]);

            if ($resultado) {
                // Opcional: registrar en el log
                registrarLog($pdo, $_SESSION['usuario_id'], "ACTUALIZAR", "Cambió rol a usuario $usuario_id");
                       header("Location: ../../../front_end/vistas/perfil_usuario/configuracion.php");

                $mensaje_exito = "Rol actualizado correctamente.";
            } else {
                $mensaje_error = "No se realizaron cambios en la base de datos.";
            }

        } catch (PDOException $e) {
            $mensaje_error = "Error crítico: " . $e->getMessage();
        }
    } else {
        $mensaje_error = "Por favor, complete todos los campos.";
    }

}


function mostrar_asignacion($pdo, $nombre_empresa){
$pdo->exec("USE tareas");
    // conexion db para obtener el usuario
    $sql ="SELECT usuario_asignado FROM asignar_tarea where nombre_tarea= ?";
    $stmt_usuario =$pdo->prepare($sql);
    $stmt_usuario->execute([$nombre_empresa]);
    $resultado= $stmt_usuario->fetchAll(PDO::FETCH_ASSOC);
    return $resultado;

}


function mostrar_logeado($pdo){
    // conexion db para obtener el usuario
    $sql ="SELECT nombre, nombre_usuario, apellido, esta_logeado FROM usuarios.usuario ";
    $stmt_usuario =$pdo->prepare($sql);
    $stmt_usuario->execute();
    $resultado= $stmt_usuario->fetchAll(PDO::FETCH_ASSOC);
    return $resultado;

}


#::::::::::::::::::::::::::: CONSULTAS SIN OPTIMIZAR
#ARCHIVOS QUE VIENEN DE importar_datos_excel.php

// funcion para selecionar usuarios de la base de datos, solamente para los roles
// NO vamos a usar esto, para hacer consultas de todos los usuarios en la base de datos
function obtener_usuarios($pdo){
    $pdo->exec("USE usuarios");
    // conexion db para obtener el usuario
    $sql ="SELECT id, nombre_usuario FROM usuario";
    $stmt_usuario =$pdo->prepare($sql);
    $stmt_usuario->execute();
    $resultado= $stmt_usuario->fetchAll(PDO::FETCH_ASSOC);
    return $resultado;
}


//  $pdo =  conectar_db();

// function SeQuemaMiCasa($pdo) {

// $pdo->exc("USE usuarios");
//    $sql ="SELECT id, nomre_casa FROM casa";
//     $stmt_usuario =$pdo->prepare($sql);
//      $stmt_usuario->execute();
//          $resultado= $stmt_usuario->fetchAll(PDO::FETCH_ASSOC);
//     return $resultado;


// }

//:::::::::::::::Seccion para asignar roles, a los usuarios, todas estas consultas, son hechas para insertar roles, dede asignar_usuario.php

// esta funcion selecciona todos los roles de las base de datos, lo coloque para seleccionar todo, ya que los roles son solo 4 y no se va a aggregar o eliminar roles
function obtener_roles($pdo){
       $pdo->exec("USE usuarios");
        $sql = ("SELECT *  FROM usuarios.roles");
        $stmt_usuario =$pdo->prepare($sql);
        $stmt_usuario->execute();
        // lo devuelve en forma de array asociativo= (PDO::FETCH_ASSOC)row_rol[0]['administrador']
        return $roles = $stmt_usuario->fetchAll(PDO::FETCH_ASSOC);
    
    }


function mostrar_roles_usuarios($pdo){
       $pdo->exec("USE usuarios");
        $sql = ("SELECT rol_id, nombre_usuario  FROM usuarios.usuario");
        $stmt_usuario =$pdo->prepare($sql);
        $stmt_usuario->execute();
        // lo devuelve en forma de array asociativo= (PDO::FETCH_ASSOC)row_rol[0]['administrador']
        return $roles = $stmt_usuario->fetchAll(PDO::FETCH_ASSOC);
    
    }

    


function insertar_roles($pdo, $asignar_rol, $asignar_usuarios, $asignado_por){
    
    $pdo->exec("USE usuarios");
    $sql_ver_asignacion = "SELECT COUNT(*) FROM asignar_rol  WHERE asignar_usuario = ?";
    $stmt_usuario_rol = $pdo->prepare($sql_ver_asignacion);
    $stmt_usuario_rol-> execute([$asignar_usuarios]);
    //$rol_comprobar =$stmt_usuario_rol->fetchAll(PDO::FETCH_ASSOC);
    $rol_existe = $stmt_usuario_rol->fetchColumn();


    // aqui se va a insertar en la base de datos si el rol todavia no existe. 
        if($rol_existe == 0){ 
            
            $sql = "INSERT INTO asignar_rol (asignar_usuario, asignar_roles, asignado_roles_por) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);        
        
        // Si tus variables son funciones, debes ejecutarlas primero, aqui agarra $asignar_rol de asignar_usuario.php y inicializa la funcion ya que
        // esta funcion es anonima para colocarle la funcion asignar_rol() a la variable asignar rol, para insertarlo , y que aparezca en usuarios
            if ($asignar_rol instanceof Closure) {

                $asignar_rol = $asignar_rol();
            }

            if ($asignar_usuarios instanceof Closure) {
                $asignar_usuarios = $asignar_usuarios();
            }

            $resultado = $stmt->execute([$asignar_usuarios, $asignar_rol, $asignado_por]);
            
            return $resultado->rowCount();
              if($resultado){
             registrarLog($pdo, $_SESSION['usuario_id'], "ASIGNAR", "ASigno el rol: " .$asignar_rol. " a ". $asignar_usuarios);
            }
            
            echo "insertado, Correctamente";

            // esto va a actualizar el rol, ya existente, primero verifica si el rol ya existe y luego lo actualiza. 
        } if($rol_existe != 0){
            $sql_actualizar = "UPDATE asignar_rol SET asignar_usuario = ?, asignar_roles= ? , asignado_roles_por= ?";
            $stmt_actualizar = $pdo->prepare($sql_actualizar);   
            
            
            // Si tus variables son funciones, debes ejecutarlas primero, aqui agarra $asignar_rol de asignar_usuario.php y inicializa la funcion ya que
        // esta funcion es anonima
            if ($asignar_rol instanceof Closure) {
                $asignar_rol = $asignar_rol();
            }

            if ($asignar_usuarios instanceof Closure) {
                $asignar_usuarios = $asignar_usuarios();
            }

             $resultadoActualizado =$stmt_actualizar->execute([$asignar_usuarios, $asignar_rol, $asignado_por]);

            
            return $resultadoActualizado->rowCount();

            if($resultadoActualizado){
             registrarLog($pdo, $_SESSION['usuario_id'], "ASIGNAR", "ASigno el rol: " .$asignar_rol. " a ". $asignar_usuarios);
            }
            echo "actualizado, Correctamente";

        }else{
            echo "error al asignar";
        }
            // lo devuelve en forma de array asociativo= (PDO::FETCH_ASSOC)row_rol[0]['administrador']
        // return $asignar_usuario_roles = $stmt_usuario->fetchAll(PDO::FETCH_ASSOC);
}



// MUESTRA LAS FESTIVIDADES 
function mostrarFestividades($pdo) {
       $pdo->exec("USE usuarios");
    $sql_fechas_festivas =" SELECT DATE_FORMAT(fecha, '%d-%m-%Y') AS fecha_festiva_formateada FROM fechas_festivas";
    $stmt_fechas =$pdo->prepare($sql_fechas_festivas);
    $stmt_fechas->execute();
    $festividades= $stmt_fechas->fetchAll(PDO::FETCH_COLUMN, 0);

    return $festividades;
      
}

// ESTA FUNCION NO VA AQUI ES SOLO DE PRUEBA
function mostrarEmpresas($pdo){
      $pdo->exec("USE empresas");
         $sql_empresas =" SELECT *  FROM empresas";
         $stmt_empresas =$pdo->prepare($sql_empresas);
         $stmt_empresas->execute();
         $empresas= $stmt_empresas->fetchAll(PDO::FETCH_ASSOC);

        return $empresas;
}

// ESTO VA EN EMPRESAS

function empresasDeSancion($pdo, $nombre_empresa){
    // 1. Limpiamos y preparamos el término en PHP
// 1. Limpiamos números y pasamos a mayúsculas
$limpio = preg_replace('/\d+/', '', $nombre_empresa);
$termino = "%" . mb_strtoupper(trim($limpio), 'UTF-8') . "%";

    try{

// 2. Consulta SQL
$sql = "SELECT * FROM empresas.empresas WHERE UPPER(nombre_empresa) LIKE UPPER(?)";

$stmt = $pdo->prepare($sql);
$stmt->execute([$termino]);
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $resultados;
    }catch(Exception $e){
        $e->getMessage();
    }
}



#::::::::::::::::::::::::::: CONSULTAS OPTIMIZADAS
// esta funcion muestra los ingresos de los trabajadores
 function mostrar_trabajadores_ingresos($pdo, $empresa_empleado){
    

    $pdo->exec("USE tareas");
    // $termino_busqueda = trim($empresa_empleado);
    $sql ="SELECT id, nombre_empresa, cedula, nombre, apellido, fecha_ingreso, movimiento_ingreso, 
    fecha_egreso, movimiento_egreso, nombre_tarea FROM empleados_ingresos  WHERE TRIM(nombre_tarea) = ?";

    $stmt_usuario =$pdo->prepare($sql);
    // $termino_busqueda = trim($empresa_empleado); 

    $stmt_usuario->execute([$empresa_empleado]);
    $resultado= $stmt_usuario->fetchAll(PDO::FETCH_ASSOC);

    $actualizacion_reciente = "SELECT actualizado_en 
                 FROM empleados_ingresos 
                 ORDER BY id DESC 
                 LIMIT 1";
   
   
    $stmt_actualizacion =$pdo->prepare($actualizacion_reciente);
    $stmt_actualizacion->execute();
    $actualizado= $stmt_actualizacion->fetch(PDO::FETCH_OBJ);
    
   // 1. Inicializamos la variable siempre
$fecha = null;

    if($actualizado){
        $fecha = $actualizado->actualizado_en;
    }

    return [ 
        'datos_completos' => $resultado, 
        'fecha_formateada' => $fecha
    ];

 
    

   
 }

//  // esta funcion muestra los ingresos de los trabajadores
//  function mostrar_trabajadores_egresos($pdo, $empresa_empleado){
  
//  // 1. Preparamos la consulta unificada
//             $sql_unificado = " SELECT id, nombre_empresa, cedula, nombre, apellido, fecha_ingreso, movimiento_ingreso, fecha_egreso, movimiento_egreso 
//                 FROM empleados_ingresos 
//                 WHERE nombre_empresa LIKE :empresa1
//                 UNION ALL
//                 SELECT id, nombre_empresa, cedula, nombre, apellido, fecha_ingreso, movimiento_ingreso, fecha_egreso, movimiento_egreso 
//                 FROM egresos_totales 
//                 WHERE nombre_empresa LIKE :empresa2";

//             $stmt = $pdo->prepare($sql_unificado);
//             $termino = "%" . trim($empresa_empleado) . "%";

//             // 2. Ejecutamos pasando el parámetro dos veces (una para cada parte del UNION)
//             $stmt->execute([
//                 ':empresa1' => $termino,
//                 ':empresa2' => $termino
//             ]);
//             $resultado_unificado = $stmt->fetchAll(PDO::FETCH_ASSOC);

//             // 3. Obtenemos la última fecha de actualización (puedes elegir una tabla o ambas)
//             $sql_actualizacion = "SELECT actualizado_en FROM empleados_ingresos ORDER BY id DESC LIMIT 1";
//             $stmt_act = $pdo->prepare($sql_actualizacion);
//             $stmt_act->execute();
//             $actualizado = $stmt_act->fetch(PDO::FETCH_OBJ);

//             $fecha = $actualizado ? $actualizado->actualizado_en : null;

//             // 4. Retorno con las llaves correctas para que tu desestructuración NO falle
//             return [ 
//                 'datos_completos'  => $resultado_unificado, 
//                 'fecha_formateada' => $fecha,
//             ];
     
//  }

 // muestra los egresos de los trabajadores


//::::: ACA FALTA COLOCAR LA VALIDACION DE LA CEDULA DE TRABAJADOR

// ESTO ES PARA IMPORTAR DEL EXCEL A LOS TRABAJADORES A SUS EMPRESAS
if (isset($_POST['importar_excel'])) {
    // Recuperar el JSON
    $json = $_POST['importar_excel'];
    // Convertirlo de nuevo a array PHP
    $registros = json_decode($json, true);





    

    function insertarTrabajadores($pdo, $registros) {
        $cantidadTarea = $_POST['cantidad_tarea'];

        try {
             $pdo->exec("USE tareas");
            $pdo->beginTransaction();

            
                # Lógica de activos vs egresos
                //if (empty($registro[6])) {
                    // Activos → empleados_ingresos
                    $sql = "INSERT INTO tareas.empleados_ingresos 
                            (Nombre_empresa, cedula, nombre, apellido,
                             fecha_ingreso, movimiento_ingreso, fecha_egreso, movimiento_egreso, nombre_tarea) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt_ingreso = $pdo->prepare($sql);



                                        $sql_egresos = "INSERT INTO tareas.egresos_totales 
                            (Nombre_empresa, cedula, nombre, apellido,
                             fecha_ingreso, movimiento_ingreso, fecha_egreso, movimiento_egreso, nombre_tarea) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt_egreso = $pdo->prepare($sql_egresos);


            // Recorremos todas las filas (cada registro es un array de 8 campos)
            foreach ($registros as $registro) {
                // Validación mínima
                // if (!is_array($registro) || count($registro) < 8) {
                //     continue; // saltar filas incompletas
                // }

                // Normalizar cada campo
                $registro = array_map(function($campo) {
                    if (is_array($campo)) {
                        return implode('', $campo); // ['E','m','p','r','e','s','a'] → "Empresa"
                    }
                    if (is_null($campo)) {
                        return '';
                    }
                    return trim($campo);
                }, $registro);



                // // 1. Verificamos que $registro no sea null y sea un array
                // if (!is_array($registro) || empty($registro)) {
                //     continue; // Saltar esta iteración si no hay datos
                // }

                // // Debug opcional: ver la empresa
                //  echo "Empresa: " . $registro[0] . "<br>";

            // INSERCIÓN 1: Datos de Ingreso (Índices 0 al 7)
                if (!empty($registro[1])) { // Validamos que al menos haya cédula
                $resultado = $stmt_ingreso->execute([
                        $registro[0], $registro[1], $registro[2], $registro[3],
                        $registro[4], $registro[5], $registro[6], $registro[7],
                        $cantidadTarea
                    ]);
                }

                // INSERCIÓN 2: Datos de Egreso (Índices 8 al 15)
                // Solo si el array es lo suficientemente largo y tiene datos de egreso
                if (count($registro) >= 16 && !empty($registro[9])) { 
                    $stmt_egreso->execute([
                        $registro[8], $registro[9], $registro[10], $registro[11],
                        $registro[12], $registro[13], $registro[14], $registro[15],
                        $cantidadTarea
                    ]);
                }
                // }

                
                if($resultado){
                    registrarLog($pdo, $_SESSION['usuario_id'], "CREAR", "subio los trabajadores: " );
                }

            }

            $pdo->commit();
        } catch (PDOException $e) {
                // Error específico de la Base de Datos
                echo "<h3>Error de Base de Datos</h3>";
                echo "<strong>Mensaje:</strong> " . $e->getMessage() . "<br>";
                echo "<strong>Código de error:</strong> " . $e->getCode();
            } catch (Exception $e) {
                // Cualquier otro error de lógica en PHP
                echo "<h3>Error de Sistema</h3>";
                echo "<strong>Mensaje:</strong> " . $e->getMessage();
            }
    }



    try {
        insertarTrabajadores($pdo, $registros);

       header("Location: /IVSS/front_end/vistas/tareas/tareas.php?status=success");
        exit;
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    header("Location: /IVSS/front_end/vistas/tareas/tareas.php?status=deleted");

    }
}
#TRABAJADORES
// FUNCION PARA UNIR EGRESOS CON INGRESOS Y DESCARTAR A LOS TRABAJADORES INACTIVOS 



#TABLA TAREAS.PHP EN EL FRONT END

// ESTA funcion sirve para mostar las tareas
function mostrarTareas($pdo){
     $pdo->exec("USE tareas");
        try{
                $sql_tareas ="SELECT DISTINCT t.id, t.codigo_tarea, t.nombre_empresa, t.cantidad_tarea, 
                                              t.trabajadores_activos, t.estado, t.creado_en,

                                                e.estatus_nombre, e.id_estatus, e.tarea_id


                            FROM tareas.tareas t 
                            
                            LEFT JOIN estatus_tarea e ON t.id = e.tarea_id 
                            
                            WHERE t.creado_en >= '2024-01-01' AND t.creado_en < '2027-01-01'
                            ORDER BY t.creado_en DESC;
                            ";
                
                $stmt_tareas =$pdo->prepare($sql_tareas);
                $stmt_tareas->execute();
                $tareas= $stmt_tareas->fetchAll(PDO::FETCH_ASSOC);

               

                return $tareas;
        }catch(Exception $e){
        //  $pdo->rollBack();
         echo "Error: " . $e->getMessage();
    }
}

// funcion para cambiar estatus de la tare en procesando, finalizada y esperando
// ESTO ESTA OBSOLETO YA QUE ESTA EN funciones_jason.php
// function cambiarEstatusDB($pdo, $estatus, $tarea_id, $fecha_estatus){

//         try{
//         $pdo->beginTransaction();
        
//         $sql_estatus = " INSERT INTO estatus_tarea (estatus_nombre, tarea_id, estatus_cambiado) values(?, ?, ?)
                                   
//                                     ON DUPLICATE KEY UPDATE 
//                                     estatus_nombre = VALUES(estatus_nombre),
//                                     estatus_cambiado = VALUES(estatus_cambiado)";

//         $stmt_estatus= $pdo->prepare($sql_estatus); 
//         $stmt_estatus->execute([$estatus,  $tarea_id, $fecha_estatus]);
        
//         $pdo->commit();

    
//     }catch(Exception $e){
//          $pdo->rollBack();
//          echo "Error: " . $e->getMessage();
//     }
// }


function mostrarCantidadTarea($pdo, $nombre_empresa){
     $pdo->exec("USE tareas");
    $sql_cantidadTarea = "SELECT COUNT(*) AS cantidad_tarea 
                            FROM tareas 
                            WHERE nombre_empresa = ?";
    $stmt_cantidadTarea = $pdo->prepare($sql_cantidadTarea);
    $stmt_cantidadTarea->execute([$nombre_empresa]);
    return $stmt_cantidadTarea->fetch(PDO::FETCH_ASSOC);

}



function insertarTarea($pdo, $nombre_empresa, $estado_empresa){
     $pdo->exec("USE tareas");
    try {
        $pdo->beginTransaction();

        $enumeracionTarea = mostrarCantidadTarea($pdo, $nombre_empresa);

        // Si existe la empresa, incrementamos cantidad_tarea
        if ($enumeracionTarea) {
            $cantidad = $enumeracionTarea['cantidad_tarea'] + 1;
        } else {
            $cantidad = 1; // primera tarea para esa empresa
        }
    
        // 1. Insertar sin el código
        $sql_tarea = "INSERT INTO tareas(nombre_empresa, cantidad_tarea, estado) VALUES (?, ?, ?)";
        $stmt_tarea = $pdo->prepare($sql_tarea);
        $stmt_tarea->execute([$nombre_empresa, $cantidad, $estado_empresa]);

        // 2. Obtener el ID autoincremental
       // lastInsertID funcion nativa de php
        $id_tarea = $pdo->lastInsertId();
        // 3. Generar el código único
        //crear codigo viene de automatizaciones.php
        $codigo_tarea = crearCodigo($id_tarea);

        // 4. Actualizar la tarea con su código
        $stmt = $pdo->prepare("UPDATE tareas SET codigo_tarea = ? WHERE id = ?");
        $resultado= $stmt->execute([$codigo_tarea, $id_tarea]);

          if($resultado){
            registrarLog($pdo, $_SESSION['usuario_id'], "CREAR", "Creó la tarea: " . $codigo_tarea);
        }

        $pdo->commit();


         $id_tarea; // devuelve el ID de la tarea creada
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}