<?php 

ini_set('log_errors', 1);


// 3. No mostrar errores al usuario final (seguridad)
ini_set('display_errors', 0);
require_once(__DIR__ . '/../../config.php');
require_once(CONEXION_PATH);
require_once (ERRORES_PATH);
require_once(CONCURRENCIA); // Asegúrate de que aquí estén las funciones de consulta

$pdo = conectar_db();



// Obtener datos del JSON enviado por Fetch
$json = file_get_contents('php://input');
$data = json_decode($json, true);


$accion = isset($data['accion_pago']) ? $data['accion_pago'] : 'default';

$respuesta = [];
// PARA LAS FUNCIONES DE JSON

// Evaluamos una expresión que devuelva true o false
// switch (!empty($data)) { 
//     case true:
//          // La función debe retornar un array, NO imprimir nada
//         $resultadoAccion = registrarPago($pdo, $data);
        
//         // Si la función ya devuelve el formato correcto:
//         $respuesta = $resultadoAccion;
//         break;

//     case false:
//         // Aquí entra si $data está vacío o es null
//         $respuesta = [
//             "success" => false,
//             "status" => "error", 
//             "message" => "No se recibió información válida"
//         ];        break;
// }

if (!empty($data)) {
    // Si entró aquí, es porque enviaste datos vía Fetch
    $resultadoAccion = registrarPago($pdo, $data);
    
    ob_end_clean(); // Limpiamos cualquier "Warning" o eco previo
    echo json_encode($resultadoAccion);
    exit; // DETENEMOS el script aquí para que no ejecute lo de abajo
}

// PARA LAS FUNCIONES DE FORMULARIO
// si existe un post de snacionar_orden_pago.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $nombre_empresa = $_POST['nombre_empresa'] ?? 0;


        switch($_POST['accion_pago']){

            case 'orden_pago':
                registrarSacionFinal($pdo, $nombre_empresa);
                registrarDescripcion($pdo, $nombre_empresa);
                registrarIntereses($pdo, $nombre_empresa);
                header('Location: /IVSS/front_end/vistas/facturacion/facturacion.php?empleado_empresa=' . urlencode($nombre_empresa));

                break;

            default:
                // echo "no se pudo cargar la sancion"; 
            break;  
        }

}

function registrarPago($pdo,$data){
    ob_start();
    header('Content-Type: application/json');
    ob_end_clean();

    try{

              // Si pasa las validaciones, procedemos al registro
        $monto      = $data['monto'];
        $banco      = $data['banco'];
        $referencia = $data['referencia'];
        $fecha      = $data['fecha'];
        $moneda     = $data['moneda'];
        $empresa    = $data['empresa']; // El campo que capturaste con JS  

        $sql = "INSERT INTO facturacion.registrar_pago(valor, monto_pago, numero_referencia, banco, nombre_empresa, fecha_pago) VALUES (?,?,?,?,?,?)";
        $stmt = $pdo->prepare($sql);
        $resultado = $stmt->execute([ $moneda, $monto,  $referencia, $banco, $empresa, $fecha]);

        if($resultado){
                registrarLog($pdo, $_SESSION['usuario_id'], "CREAR", "Subido Registrar pago : " . $empresa);
                return [
                "success" => true,
                "status" => "success",
                "message" => "¡Pago registrado correctamente!",
                "redirect" => "/IVSS/front_end/vistas/facturacion/facturacion.php?empresa=.$empresa."
            ];


        }
    }catch(PDOException $e){
            return [
                        "success" => false,
                        "status" => "error",
                        "message" => "Error en base de datos: " . $e->getMessage()
                    ];
    }

}

// Funciones sancionar
function registrarSacionFinal($pdo, $nombre_empresa){
    // valores del input
    $otras_sanciones = $_POST['otras_sanciones'] ?? 0;
    $nombre_sancion_mayor = $_POST['nombre_sancion_mayor']?? '';
    $valor_sancion_mayor = $_POST['sancion_mayor']?? 0;
    $valor_concurrencia = $_POST['valor_concurrencia']?? 0;
    
    try{
        $sql  = "INSERT INTO facturacion.concurrida(valor_concurrencia, sancion_mayor, 
                                                nombre_sancion_mayor, otras_sanciones, nombre_empresa) 
            VALUES (?,?,?,?,?)";
        $stmt = $pdo->prepare($sql);
        $resultado = $stmt->execute([$valor_concurrencia, $valor_sancion_mayor, $nombre_sancion_mayor, 
                                    $otras_sanciones, $nombre_empresa]);

        if($resultado){
                registrarLog($pdo, $_SESSION['usuario_id'], "CREAR", "Subida orden de pago : " . $nombre_empresa);

        }
    }catch(Exception $e){
        echo "Error registrar sancion: " . $e->getMessage();

    }

    
}

// Registra la descripcion en el input
function registrarDescripcion($pdo, $nombre_empresa){
    
    $novedades = $_POST['novedades_descripcion']?? '';
    $obstaculizacion = $_POST['obstaculizacion_descripcion']?? '';
    $tachaduras = $_POST['tachaduras_descripcion']?? '';

    try{

        
            $sql  = "INSERT INTO facturacion.descripcion_sanciones_extra(tachaduras, obstaculizacion, 
                                                                        novedades, 
                                                                        nombre_empresa) 
                    VALUES (?,?,?,?)";
            $stmt = $pdo->prepare($sql);
            $resultado = $stmt->execute([$tachaduras, $obstaculizacion, $novedades , $nombre_empresa]);

            if($resultado){
                    registrarLog($pdo, $_SESSION['usuario_id'], "CREAR", "decripcion para la sancion para: " . $nombre_empresa);

            }
    }catch(Exception $e){
        echo "Error registrar descripcion: " . $e->getMessage();

    }
    

}

function registrarIntereses($pdo, $nombre_empresa){


    if (!empty($_POST['intereses'])){
            
            $intereses = $_POST['intereses'];

            $cantidad_periodo = $_POST['cantidad_periodo'];
            $formato_tiempo = $_POST['formato_tiempo'];

            try{
                $sql  = "INSERT INTO facturacion.intereses(cantidad_periodo, formato, 
                                                        monto_intereses_bs, nombre_empresa) 
                    VALUES (?,?,?,?)";

                $stmt = $pdo->prepare($sql);
                $resultado = $stmt->execute([$cantidad_periodo, $formato_tiempo, $intereses, $nombre_empresa]);

                if($resultado){
                        registrarLog($pdo, $_SESSION['usuario_id'], "CREAR", "Subido Intereses: " . $nombre_empresa);
                        header("Location: /IVSS/front_end/vistas/empresas/empresas.php?status=success");

                }   

            }catch(Exception $e){
                echo "Error registrar descripcion: " . $e->getMessage();

            }

            

    
    }
}

#funciones para mostrar valores de la orden de pago  

function mostrarOrdenPago($pdo, $nombre_empresa){
    
    
            // $sql  = "SELECT c.id, c.monto, c.nombre     
            //         FROM concurrida c
            //         INNER JOIN intereses i ON i.cliente_id = i.id";
            
            $sql  = "SELECT valor_concurrencia, sancion_mayor, 
                            nombre_sancion_mayor, otras_sanciones  
                            FROM facturacion.concurrida 
                            WHERE nombre_empresa = ?";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nombre_empresa]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);


     
    

}



#Funciones para el pago del beta



?>