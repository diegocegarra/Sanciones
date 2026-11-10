<?php
session_start();
require_once(__DIR__ . '/../config.php');
require_once(CONEXION_PATH);

$pdo = conectar_db();
// $pdo->exec("USE usuarios"); 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto'])) {
    $id_usuario = $_SESSION['usuario_id'];
    $archivo = $_FILES['foto'];
    
    // Validar extensión
    $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
    $nombre_foto = "user_" . $id_usuario . "_" . time() . "." . $extension;
    
    // Ruta donde se guardará físicamente la imagen (Sube 2 niveles desde back_end/usuarios/)
    $directorio_destino = "../../front_end/publico/uploads/perfiles/";
    
    if (!file_exists($directorio_destino)) {
        mkdir($directorio_destino, 0777, true);
    }
    
    $ruta_destino = $directorio_destino . $nombre_foto;

    if (move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {
        // Actualizar BD
        $stmt = $pdo->prepare("UPDATE usuarios.usuario SET foto_perfil = ? WHERE id = ?");
        $stmt->execute([$nombre_foto, $id_usuario]);
        
        // Redirección exitosa (Sube 2 niveles y entra a front_end/vistas/perfil_usuario/)
        header("Location: ../../front_end/vistas/perfil_usuario/perfil.php?status=foto_ok");
        exit();
    } else {
        // Redirección con error
        header("Location: ../../front_end/vistas/perfil_usuario/perfil.php?status=error_subida");
        exit();
    }
}