<?php

// ini_set('log_errors', 1);


// // 3. No mostrar errores al usuario final (seguridad)
// ini_set('display_errors', 0);
require_once(__DIR__ . '/../config.php');
require_once(CONEXION_PATH);
    // require_once (ERRORES_PATH);
$pdo = conectar_db(); 

if(isset($_POST['salirSessionUsuario'])){
    session_start();
    
    // 1. Obtener el ID del usuario antes de borrar nada
    // Asumimos que el ID viene por POST o ya está en la sesión
    $idUsuario = $_POST['salirSessionUsuario'];

    try {
        // 2. Conexión y Update
        $pdo->exec("USE usuarios");
        
        // No necesitas el SELECT si ya tienes el ID en el $_POST
        // Actualizamos directamente el estado
        $stmt_update = $pdo->prepare("UPDATE usuario SET esta_logeado = 0 WHERE id = ?");
        $stmt_update->execute([$idUsuario]);
        
    } catch (PDOException $e) {
        // Opcional: manejar errores de BD
    }

    // 3. Limpiar y destruir la sesión de PHP
    $_SESSION = array(); // Limpia el array de sesión
    session_destroy();
    
    // 4. Redirigir
    header("Location: /IVSS/front_end/login/login.php");
    exit();
}
?>