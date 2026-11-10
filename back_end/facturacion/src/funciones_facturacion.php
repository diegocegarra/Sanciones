<?php

ini_set('log_errors', 1);


// 3. No mostrar errores al usuario final (seguridad)
ini_set('display_errors', 0);
require_once(__DIR__ . '/../../config.php');
require_once(CONEXION_PATH);
require_once (ERRORES_PATH);
require_once(CONCURRENCIA); // Asegúrate de que aquí estén las funciones de consulta
require_once (EVALUAR_TRABAJADORES);
require_once (LOGS);
require_once(FUNCIONES_TAREAS);



$pdo = conectar_db(); 
$nombre_empresa = trim($_POST['concurrir'] ?? '');

// para las sanciones extra
// $nombre_empresa_extras = trim($_POST['empresa_sanciones_extra'] ?? '');

// echo $nombre_empresa_extras;

$accion = $_POST['accionFacturacion'] ?? '';




//proveniente de sancionesExtra.js
// 1. Leer el cuerpo de la petición (el "input" crudo)
$json = file_get_contents('php://input');

// 2. Decodificar el JSON para convertirlo en un objeto o array de PHP
// El segundo parámetro 'true' lo convierte en un Array Asociativo
$decoded = json_decode($json, true);

if ($decoded) {
    // Debe coincidir con las claves de tu objeto 'payload' en JS
    $nombre_empresa_extras = $decoded['nombre_empresa'] ?? ''; 
    $data = $decoded['lista_sanciones'] ?? [];

// $nombre_empresa_extras = $data['empresa'] ?? ''; // <--- Ya no viene de $_POST



#CODIGO PARA DEBUGEAR Y VER ERRORES NO ELIMINAR
// $validación = [];


         $montoTachaduras = $montoObstaculizacion = $montoInscripcion = $montoNovedades = $montoNoEnterados = 0;

    // Aquí ya puedes acceder a tu payload
    // se rrecorre la variable payload que viene e sancionesExtra.php
    foreach ($data as $sancion) {
        // $tipo = $sancion['tipo'];
        // $monto = $sancion['monto'];
        // $descripcion = $sancion['descripcion'];

         
switch ($sancion['tipo']) {

        case 'novedades':
            // Lógica específica para novedades
            $montoNovedades = $sancion['monto'];
            break;
            
        case 'inscripcion':
            // Lógica para inscripción
            $montoInscripcion = $sancion['monto'];
            break;

        case 'tachaduras':
            // Lógica para tachaduras
            $montoTachaduras = $sancion['monto'];
            break;
         case 'obstaculizacion':
            // Lógica para tachaduras
            $montoObstaculizacion = $sancion['monto'];
            break;

         case 'noEnterados':
            // Lógica para tachaduras
            $montoNoEnterados = $sancion['monto'];
            break;

        default:
            // Lo que pase con cualquier otro tipo
            break;
    }






    #CODIGO PARA DEBUGEAR Y VER ERRORES NO ELIMINAR
        // $validación[] = [
        //     "recibido_tipo" => $sancion['tipo'],
        //     "recibido_monto" => $sancion['monto'],
        //     "recibido_desc" => $sancion['descripcion']
        // ];




        $pdo->beginTransaction();
        
        $sql_monto_extra= "INSERT INTO facturacion.monto_sanciones_extra (
            tachaduras, 
            obstaculizacion, 
            inscripcion, 
            novedades, 
            intereses_no_enterados, 
            nombre_empresa
        ) 
        VALUES (?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            tachaduras = VALUES(tachaduras),
            obstaculizacion = VALUES(obstaculizacion),
            inscripcion = VALUES(inscripcion),
            novedades = VALUES(novedades),
            intereses_no_enterados = VALUES(intereses_no_enterados)";

// -- UNICO INDEX para que no se repitan sacionex extra 
// ALTER TABLE facturacion.monto_sanciones_extra ADD UNIQUE (nombre_empresa);


        $stmt_monto_extra = $pdo->prepare($sql_monto_extra);
        $stmt_monto_extra->execute([$montoTachaduras, $montoObstaculizacion, 
                                    $montoInscripcion, $montoNovedades , $montoNoEnterados, $nombre_empresa_extras]);


        // // SI ESTO YA EXISTE
        // $sql_monto_actualizar = "UPDATE facturacion.monto_sanciones_extra SET tachaduras = ?, obstaculizacion = ?, 
        //                        inscripcion = ?, novedades = ?, intereses_no_enterados = ? 
                             
        //                       WHERE nombre_empresa = ?";

        // $stmt_monto_actualizar= $pdo->prepare($sql_monto_actualizar);

        // $stmt_monto_actualizar->execute([$montoTachaduras, $montoObstaculizacion, 
        //                             $montoInscripcion, $montoNovedades , $montoNoEnterados, $nombre_empresa_extras]);

                                    
        $pdo->commit();
       

      }
        echo json_encode(['success' => true, 'message' => 'sanciones extra subidas']);

    
    #CODIGO PARA DEBUGEAR Y VER ERRORES NO ELIMINAR

    // echo json_encode(["status" => "success",
    //     "datos_que_recibio_php" => $validación]);
} else {
    // Si el JSON está vacío o mal formado
    // http_response_code(400);
    // echo json_encode(['status' => 'error', 'message' => 'No se recibieron datos']);
}










switch($accion) {
    case 'concurrirSancion': 
        if (!empty($nombre_empresa)) {
            // 1. OBTENER LOS DATOS AQUÍ MISMO
            ['datos_completos' => $resultado, 'fecha_formateada' => $fecha] = mostrar_trabajadores_ingresos($pdo, $nombre_empresa);
            
            // 2. PROCESARLOS (esto genera las sanciones)
            $ingresos_concurrir = verificarExtemporaneos($resultado, $fechasFestivas);
            
            // 3. PASARLOS A LA FUNCIÓN DE GUARDADO
            guardarMontoSancion($pdo, $ingresos_concurrir, $nombre_empresa);
        }
        break;
}


function mostrarMontoPagado($pdo, $nombre_empresa) {
    try {
        // Verificamos que PDO sea válido
        if (!$pdo) { throw new Exception("Conexión PDO no establecida."); }

        $sql = "SELECT valor, monto_pago FROM facturacion.registrar_pago WHERE nombre_empresa = ?  ";
        $stmt_usuario = $pdo->prepare($sql);
        $stmt_usuario->execute([$nombre_empresa]);
        
        return $stmt_usuario->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        // Log del error (opcional) y retorno de array vacío para evitar pantalla blanca
        error_log("Error en mostrarMontoPagado: " . $e->getMessage());
        return []; 
    } catch (Exception $e) {
        error_log("Error general: " . $e->getMessage());
        return [];
    }
}

function mostrarConcurrencia($pdo, $nombre_empresa) {
    try {
        if (!$pdo) { throw new Exception("Conexión PDO no establecida."); }

        $sql = "SELECT valor_concurrencia, sancion_mayor FROM facturacion.concurrida WHERE nombre_empresa = ? ";
        $stmt_usuario = $pdo->prepare($sql);
        $stmt_usuario->execute([$nombre_empresa]);
        
        $resultado = $stmt_usuario->fetchAll(PDO::FETCH_ASSOC);
        
        // Si no hay resultados, devolvemos valores por defecto para que los cálculos no den error
        return $resultado;

    } catch (PDOException $e) {
        error_log("Error en mostrarConcurrencia: " . $e->getMessage());
        return [['valor_concurrencia' => 0, 'sancion_mayor' => 0]];
    } catch (Exception $e) {
        error_log("Error general: " . $e->getMessage());
        return [['valor_concurrencia' => 0, 'sancion_mayor' => 0]];
    }
}




function guardarMontoSancion($pdo, $ingresos_concurrir, $nombre_empresa) {
    // Protección extra contra null
    if (is_null($ingresos_concurrir)) $ingresos_concurrir = [];

    // concurrir.php
    $calculo = new SancionesCalcular();
    [$montoIngresosUT, $montobs, $montoEgresosUT, $montobsEgresos] = $calculo->calcularUTIngresos($ingresos_concurrir);

    try {
        $pdo->exec("USE facturacion");
        
        // Verificar existencia
        $stmt_verificar = $pdo->prepare("SELECT COUNT(*) FROM facturacion WHERE nombre_empresa = ?");
        $stmt_verificar->execute([$nombre_empresa]);
        $empresa_existe = $stmt_verificar->fetchColumn();
        
        $pdo->beginTransaction();

        if ($empresa_existe == 0) {
            $sql = "INSERT INTO facturacion(monto_ut_ingresos, monto_bs_ingresos, monto_ut_egresos, monto_bs_egresos, nombre_empresa) VALUES(?,?,?,?,?)";
            $stmt = $pdo->prepare($sql);
            $resultado_subir= $stmt->execute([$montoIngresosUT, $montobs, $montoEgresosUT, $montobsEgresos, $nombre_empresa]);
              // guardar en el log
            if($resultado_subir){
                registrarLog($pdo, $_SESSION['usuario_id'], "CREAR", "concurrencia del el monto de la sancion para: " . $nombre_empresa);
                }

        } else {
            // CORRECCIÓN: Se agregó WHERE nombre_empresa = ?
            $sql_existe = "UPDATE facturacion SET monto_ut_ingresos = ?, monto_bs_ingresos = ?, monto_ut_egresos = ?, monto_bs_egresos = ? WHERE nombre_empresa = ?";
            $stmt_existe = $pdo->prepare($sql_existe);
            $resultado_actualizar = $stmt_existe->execute([$montoIngresosUT, $montobs, $montoEgresosUT, $montobsEgresos, $nombre_empresa]);
            if($resultado_actualizar){
                registrarLog($pdo, $_SESSION['usuario_id'], "ACTUALIZAR", "se atualizo el monto de la sancion para: " . $nombre_empresa);
                }
        }

        $pdo->commit();
        
        header('Location: /IVSS/front_end/vistas/facturacion/facturacion.php?empleado_empresa=' . urlencode($nombre_empresa));
        exit;

    } catch(Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}







 function mostrar_monto_sancion($pdo, $nombre_empresa){
     $pdo->exec("USE facturacion");
    
    $sql ="SELECT monto_ut_ingresos, monto_bs_ingresos, monto_ut_egresos, monto_bs_egresos FROM facturacion.facturacion  WHERE nombre_empresa = ?";

    $stmt_usuario =$pdo->prepare($sql);
    $stmt_usuario->execute([$nombre_empresa]);
    return $stmt_usuario->fetchAll(PDO::FETCH_ASSOC);
 
}



function mostrar_otras_sanciones($pdo, $nombre_empresa){   
    // echo $nombre_empresa;
    $sql ="SELECT tachaduras, obstaculizacion, inscripcion, novedades, intereses_no_enterados,
  
    (COALESCE(tachaduras, 0) + 
     COALESCE(obstaculizacion, 0) + 
     COALESCE(inscripcion, 0) + 
     COALESCE(novedades, 0) + 
     COALESCE(intereses_no_enterados, 0)) AS total_sanciones

    FROM facturacion.monto_sanciones_extra  WHERE nombre_empresa = ?";

    $stmt_usuario =$pdo->prepare($sql);
    $stmt_usuario->execute([$nombre_empresa]);
    $resultados_extra= $stmt_usuario->fetchAll(PDO::FETCH_ASSOC);

    foreach ($resultados_extra as &$fila) {
    $fila['total_sanciones'] = array_sum($fila);
}

return $resultados_extra;
}




function mostrarPagoRegistrado($pdo, $nombre_empresa){
    
    $sql ="SELECT valor, monto_pago, numero_referencia, banco, fecha_pago FROM facturacion.registrar_pago  WHERE nombre_empresa = ?";
    $stmt_usuario =$pdo->prepare($sql);
    $stmt_usuario->execute([$nombre_empresa]);
    return $stmt_usuario->fetchAll(PDO::FETCH_ASSOC);
}
?>