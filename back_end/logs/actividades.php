<?php
require_once(__DIR__ . '/../config.php');
require_once(CONEXION_PATH);


$pdo = conectar_db(); 
$usuario_id = $_SESSION['usuario_id'] ?? 0;
function registrarLog($pdo, $usuario_id, $accion, $detalle) {
    try {
        $pdo->exec("USE usuarios");

        $ip = $_SERVER['REMOTE_ADDR'];
        $sql = "INSERT INTO logs_sistema (usuario_id, accion, detalle, ip_address) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$usuario_id, $accion, $detalle, $ip]);

    } catch (Exception $e) {
        // Opcional: escribir en un archivo de texto si la DB falla
        error_log("Error al registrar log: " . $e->getMessage());
    }
}
?>