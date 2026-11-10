<?php 
require_once(__DIR__ . '/../config.php');
require_once(CONEXION_PATH);

$pdo= conectar_db();
class Analiticas{

public $etiquetas = [];
public $valores = [];

public function analiticaFacturacion($pdo){
    $pdo->exec("USE facturacion");

    $sql = "SELECT 
            DATE_FORMAT(concurrenciaSubida, '%Y-%m') as periodo, 
            SUM(monto_ut_ingresos) as total_ut_ingresos, 

            SUM(monto_ut_egresos) as total_ut_egresos, 
            
            SUM(CASE WHEN monto_ut_ingresos > 0 THEN 1 ELSE 0 END) as cantidad_facturas_ingresos,
            SUM(CASE WHEN monto_ut_egresos > 0 THEN 1 ELSE 0 END) as cantidad_facturas_egresos
 
        FROM facturacion 
        GROUP BY periodo 
        ORDER BY periodo ASC";

    $stmt =$pdo->prepare($sql);
    $stmt->execute();
    $datos_ut = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $datos_ut;

// Preparamos dos arreglos para JS

}
public function analiticaTareas($pdo){
    $pdo->exec("USE tareas");
    // $sql_activas  = "SELECT estatus_nombre, COUNT(*) as activas FROM estatus_tareas GROUP BY estausActivos WHERE estatus_nombre != 'finalizada' ";
$sql = "SELECT DATE_FORMAT(estatus_cambiado, '%Y-%m') as fechaFinalizado, 
            
            COUNT(*) as completadas 
            FROM estatus_tarea 

            WHERE estatus_nombre = 'finalizada' 
            
            GROUP BY fechaFinalizado
            ORDER BY fechaFinalizado ASC";   
            
            $stmt =$pdo->prepare($sql);

    $stmt->execute();
    $datos_estatus = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $datos_estatus;
}


public function mostrarDatosAnaliticas($pdo) {
    $fact = $this->analiticaFacturacion($pdo);
    $tareas = $this->analiticaTareas($pdo);

    return [
        'facturacion' => [
            'labels' => array_column($fact, 'periodo'),
            // Cambia 'total_dinero' por 'cantidad' si prefieres contar facturas
            'values' => array_column($fact, 'total_ut_ingresos'),
            'values_egresos' => array_column($fact, 'total_ut_egresos')
        ],
        'tareas_estatus' => [
            'labels' => array_column($tareas, 'fechaFinalizado'),
            'values' =>array_values( array_column($tareas, 'completadas')),
            'fecha'  =>array_column($tareas, 'fechaFinalizado')
        ]
    ];
}

}

// $analiticas = new Analiticas();
// $valores = $analiticas->mostrarDatosAnaliticas($pdo);

// // Para ver los montos (etiquetas)
// print_r($valores['tareas_estatus']['labels']);

// // Para ver las cantidades (valores)
// print_r($valores['tareas_estatus']['values']);

// // Para ver las cantidades (valores)
// print_r($valores['tareas_estatus']['fecha']);
?>