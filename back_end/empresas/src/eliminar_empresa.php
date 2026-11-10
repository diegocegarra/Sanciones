<?php


// Intentar cargar el archivo de configuración usando una ruta absoluta basada en este archivo
require_once(__DIR__ . '/../../config.php');
require_once(CONEXION_PATH);

require_once (LOGS);


$pdo=  conectar_db();

// Si llegamos aquí, ya tenemos conexión. Procedemos a eliminar:
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM empresas.empresas WHERE id = ?");
        $stmt->execute([$id]);

        header("Location: ../../../front_end/vistas/empresas/empresas.php?status=deleted");
        exit();
    } catch (PDOException $e) {
        die("Error de base de datos: " . $e->getMessage());
    }
} else {
    header("Location: ../../../front_end/vistas/empresas/empresas.php");
    exit();
}