<?php
// Asegúrate de que no haya espacios en blanco antes de <?php
session_start();
header('Content-Type: application/json'); // Indicamos que responderemos JSON

require_once(__DIR__ . '/../config.php');
require_once(CONEXION_PATH);

try {
    $pdo = conectar_db(); 
    $pdo->exec("USE usuarios");

    $usuario_input = trim($_POST['nombre_usuario'] ?? '');
    $password_input = trim($_POST['password'] ?? '');

    if (empty($usuario_input) || empty($password_input)) {
        echo json_encode(['success' => false, 'message' => 'Campos obligatorios vacíos']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT id, nombre, password FROM usuario WHERE nombre_usuario = ? LIMIT 1");
    $stmt->execute([$usuario_input]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password_input, $user['password'])) {
        // Logeo exitoso
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['nombre'] = $user['nombre'];
        
        echo json_encode(['success' => true]); // Avisamos a JS que todo OK
    } else {
        echo json_encode(['success' => false, 'message' => 'Usuario o contraseña incorrectos']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error de servidor: ' . $e->getMessage()]);
}
exit;