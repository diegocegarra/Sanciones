<?php

require_once(__DIR__ . '/../config.php');
require_once(CONEXION_PATH);

$pdo=conectar_db();

function mostrarLogs($pdo){
    $pdo->exec("USE usuarios");
    // conexion db para obtener el usuario
    $sql ="SELECT usuario_id, accion, detalle, ip_address, fecha_registro FROM logs_sistema";
    $stmt_usuario =$pdo->prepare($sql);
    $stmt_usuario->execute();
    $resultado_log= $stmt_usuario->fetchAll(PDO::FETCH_ASSOC);
    return $resultado_log;
}
?>