<?php

ini_set('log_errors', 1);


// 3. No mostrar errores al usuario final (seguridad)
ini_set('display_errors', 0);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once(__DIR__ . '/../../config.php');
require_once(CONEXION_PATH);
require_once (ERRORES_PATH);
require_once(ROLES_PATH);


$pdo= conectar_db();


// 1. Asegúrate de tener la sesión iniciada
$id_usuario_actual = $_SESSION['usuario_id'] ?? null; 

/**
 * Función corregida
 */
function obtenerRoles($pdo, $id_usuario_actual) {
    if (!$id_usuario_actual) return null;

    $sql = "SELECT rol_id FROM usuarios.usuario WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id_usuario_actual]); 
    $datos = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificamos si encontramos al usuario
    if ($datos) {
        // Creamos el objeto y LO RETORNAMOS al final
        return new UsuarioRol($datos['rol_id']);
    }

    return null;
}

// 2. Llamada a la función (usa el mismo nombre que definiste)
$rol = obtenerRoles($pdo, $id_usuario_actual);


// if ($rol->permisos->editar) {
//     echo "Tengo permiso para editar";
// }











// ACCIONES PARA ELIMINAR EDITAR ETC
class AccionesUsuario {
    private $permisos;
    private $pdo;



    public function __construct(permisos_generales $permisos, $pdo) {
        $this->permisos = $permisos;
        $this->pdo = $pdo;
    }










    /**
     * MÉTODO UNIVERSAL PARA EDITAR CUALQUIER TABLA
     */
    public function ejecutar_edicion_general($schema, $tabla, $columna, $valor, $id_fila) {
    // 1. Verificación de permisos
    if (!$this->permisos->editar) {
        return ["status" => "error", "message" => "No tienes permiso de edición."];
    }

    // schemas de la base de datos  con sus tablas individuales
    // 2. Lista Blanca por Esquema (Seguridad)
    // Definimos qué tablas están permitidas en cada esquema
    $permisos_db = [
        //schema            tablas
        'tareas' => ['tareas', 'egresos_totales'],
        'empresas'   => ['empresas']
        // 'facturacion' => ['productos', 'stock']
    ];


    if (!isset($permisos_db[$schema]) || !in_array($tabla, $permisos_db[$schema])) {
        return ["status" => "error", "message" => "Tabla o Esquema no autorizado."];
    }


    // salen como proteguidas si se piden editar
    // 3. Lista Negra de Columnas (Nunca editar estas)
        $prohibidas = ['password', 'rol_id', 'token'];
        if (in_array($columna, $prohibidas)) {
            return ["status" => "error", "message" => "Columna protegida."];
        }



    try {
        // 3. Construcción del SQL: schema.tabla
        // Es vital limpiar las variables para evitar inyección SQL en identificadores
        $identificador_valido = "`$schema`.`$tabla`"; 
        // Agregamos backticks a la columna para evitar errores de sintaxis
        $sql = "UPDATE $identificador_valido SET `$columna` = :valor WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'valor' => $valor,
            'id'    => $id_fila
        ]);

        return ["status" => "success", "message" => "Actualizado en $schema.$tabla"];

    } catch (Exception $e) {
        return ["status" => "error", "message" => $e->getMessage()];
    }
}





public function ejecutar_eliminacion_tarea($id) {
        // 1. Verificación de permisos (ajusta según tu objeto de permisos)
        if (!$this->permisos->eliminar) { 
            return ["success" => false, "message" => "No tienes permiso para eliminar."];
        }

        if (!$id || !is_numeric($id)) {
            return ["success" => false, "message" => "ID inválido."];
        }

        try {
            $stmt = $this->pdo->prepare("DELETE FROM tareas.tareas WHERE id = ?");
            $resultado = $stmt->execute([$id]);

            if ($resultado && $stmt->rowCount() > 0) {
                return ["success" => true, "message" => "Registro eliminado correctamente."];
            } else {
                return ["success" => false, "message" => "No se encontró el registro o ya fue eliminado."];
            }
        } catch (Exception $e) {
            return ["success" => false, "message" => "Error: " . $e->getMessage()];
        }
    }










}




if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    
    // Si la acción es editar_universal, la manejamos aquí
    if ($_POST['accion'] === 'editar_universal') {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');
        
        $ejecutor = new AccionesUsuario($rol->permisos, $pdo);
        $res = $ejecutor->ejecutar_edicion_general(
            $_POST['schema'] ?? '',
            $_POST['tabla'] ?? '',
            $_POST['columna'] ?? '',
            $_POST['valor'] ?? '',
            $_POST['id'] ?? ''
        );
        echo json_encode($res);
        exit;
    }
    
    // IMPORTANTE: Si la acción es 'eliminar_tarea', NO HAGAS EXIT AQUÍ.
    // Deja que el flujo continúe hacia funciones_jason.php
}
// if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
//     // 1. Limpiar cualquier buffer de salida previo (evita espacios en blanco o warnings)
//     if (ob_get_length()) ob_clean();
    
//     // 2. Cabecera JSON
//     header('Content-Type: application/json; charset=utf-8');

//     // Validación de seguridad para el objeto $rol
//     if (!isset($rol->permisos) || $rol->permisos === false) {
//         echo json_encode(["status" => "error", "message" => "El usuario no tiene permisos definidos."]);
//         exit;
//     }

//     // Validar que el rol se cargó correctamente
//     if (!$rol || !isset($rol->permisos)) {
//             throw new Exception("Sesión inválida o permisos no encontrados.");
//         }

//     try {
//         // Validación de existencia de variables necesarias
//         if (!isset($rol->permisos) || !isset($pdo)) {
//             throw new Exception("Error de configuración: Conexión o Permisos no encontrados.");
//         }

//         $ejecutor = new AccionesUsuario($rol->permisos, $pdo);

//         if ($_POST['accion'] === 'editar_universal' || $_POST['accion'] === 'editar_universal') {
//             $res = $ejecutor->ejecutar_edicion_general(
//                 $_POST['schema'] ?? '',
//                 $_POST['tabla'] ?? '',
//                 $_POST['columna'] ?? '',
//                 $_POST['valor'] ?? '',
//                 $_POST['id'] ?? ''
//             );
//             echo json_encode($res);
//         } else {
//             echo json_encode(["status" => "error", "message" => "Acción no reconocida."]);
//         }
//     } catch (Exception $e) {
//         echo json_encode(["status" => "error", "message" => "Fallo crítico: " . $e->getMessage()]);
//     }
    
//     // 3. Importante: terminar la ejecución para que no se pegue código HTML después
//     exit; 
// }






?>