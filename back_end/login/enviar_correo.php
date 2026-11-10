<?php
// 1. Configuración de errores
ini_set('display_errors', 0); // En producción mejor 0, para que no ensucie el JSON
error_reporting(E_ALL);

// Función de apagado para atrapar errores fatales
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== NULL && ($error['type'] === E_ERROR || $error['type'] === E_PARSE)) {
        if (ob_get_length()) ob_clean(); 
        header('Content-Type: application/json');
        echo json_encode([
            "status" => "error",
            "message" => "Error fatal en PHP: " . $error['message']
        ]);
    }
});

// Iniciar buffer
ob_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

try {
    // Verificaciones de archivos
    if (!file_exists(__DIR__ . '/../config.php')) throw new Exception("No se encuentra config.php");
    require_once(__DIR__ . '/../config.php');
    
    if (!defined('COMPOSER') || !file_exists(COMPOSER)) throw new Exception("No se encuentra el autoloader de Composer");
    require(COMPOSER);
    
    if (!defined('FUNCION_USUARIO_CORREO') || !file_exists(FUNCION_USUARIO_CORREO)) throw new Exception("No se encuentra el archivo de funciones");
    require(FUNCION_USUARIO_CORREO);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = isset($_POST['email']) ? $_POST['email'] : null;

        if (!$email) {
            throw new Exception("Email no recibido");
        }

        // Lógica de base de datos
        $idUsuario = selecionar_id_usuario($pdo, $email);
        if (!$idUsuario) throw new Exception("El correo no está registrado");

        $email_cola = agregar_cola_correos($pdo, $email, $idUsuario);

        // --- ESTA ES LA PARTE QUE FALTABA ---
        if (ob_get_length()) ob_end_clean(); // Limpiamos cualquier warning accidental
        header('Content-Type: application/json');
        echo json_encode([
            "status" => "success", 
            "message" => "Correo encolado correctamente"
        ]);
        exit;
        // ------------------------------------

    } else {
        throw new Exception("Método no permitido");
    }

} catch (Exception $e) {
    if (ob_get_length()) ob_end_clean(); 
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}

exit;
?>