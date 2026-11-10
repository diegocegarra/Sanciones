<?php
// 1. Iniciar sesión para no perder el rastro del usuario
session_start();

require_once(__DIR__ . '/../../../back_end/config.php');
require_once(CONEXION_PATH);
$pdo = conectar_db();
$pdo->exec("USE usuarios");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = $_SESSION['usuario_id'];
    $pass_actual = $_POST['pass_actual'];
    $pass_nueva = $_POST['pass_nueva'];

    try {
        // 2. Verificar que la contraseña actual sea correcta
        $stmt = $pdo->prepare("SELECT password FROM usuario WHERE id = ?");
        $stmt->execute([$id_usuario]);
        $user = $stmt->fetch();

        if ($user && password_verify($pass_actual, $user['password'])) {
            
            // 3. ENCRIPTAR la nueva contraseña 
            $pass_encriptada = password_hash($pass_nueva, PASSWORD_DEFAULT);

            // 4. Actualizar en la base de datos
            $update = $pdo->prepare("UPDATE usuario SET password = ? WHERE id = ?");
            $update->execute([$pass_encriptada, $id_usuario]);

            // 5. REDIRECCIÓN DE ÉXITO (Sin destruir la sesión)
            // Agregamos un parámetro para mostrar una alerta de éxito
            header("Location: perfil.php?cambio=ok");
            exit();

        } else {
            // Contraseña actual incorrecta
            header("Location: perfil.php?error=pass_incorrecta");
            exit();
        }

    } catch (PDOException $e) {
        die("Error en la base de datos: " . $e->getMessage());
    }
}