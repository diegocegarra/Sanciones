<?php

ini_set('log_errors', 1);


// 3. No mostrar errores al usuario final (seguridad)
ini_set('display_errors', 0);
require_once(__DIR__ . '/../config.php');
require_once(CONEXION_PATH);
require_once (ERRORES_PATH);

session_start(); // Es vital para leer $_SESSION

if (isset($_SESSION['usuario_id'])) {
    $pdo = conectar_db();
    $idUsuario = $_SESSION['usuario_id'];

    try {
        // 1. Cambiamos el estado en la base de datos
        $pdo->exec("USE usuarios");
        $stmt_update = $pdo->prepare("UPDATE usuario SET esta_logeado = 0 WHERE id = ?");
        $stmt_update->execute([$idUsuario]);
        
    } catch (PDOException $e) {
        // Si hay error de DB, igual queremos cerrar la sesión local
    }

    // 2. Limpiar todas las variables de sesión
    $_SESSION = array();

    // 3. Destruir la cookie de sesión si existe
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // 4. Destruir la sesión
    session_destroy();
}

// 5. Redirigir siempre al login
header("Location: /IVSS/front_end/login/login.php");
exit();

?>