<?php
session_start();
require_once(__DIR__ . '/../config.php');
require_once(CONEXION_PATH);
$pdo = conectar_db();

// Verificación de seguridad global
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../front_end/vistas/login/login.php");
    exit();
}

// $id_usuario = $_SESSION['usuario_id'];

// // LÓGICA DE CONTROL: Decidir qué función ejecutar según el formulario enviado
// if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    
//     if ($_POST['accion'] === 'actualizar_datos') {
//         actualizarDatosPersonales($pdo, $id_usuario, $_POST);
//     } 
//     elseif ($_POST['accion'] === 'actualizar_password') {
//         actualizarPassword($pdo, $id_usuario, $_POST);
//     }
// }

// /* FUNCIÓN 1: Actualiza Nombre, Apellido, Correo y Teléfono*/
// function actualizarDatosPersonales($pdo, $id, $datos) {
//     $nombre   = trim($datos['nombre']);
//     $apellido = trim($datos['apellido']);
//     $correo   = trim($datos['correo']);
//     $telefono = trim($datos['telefono']);

//     if (empty($nombre) || empty($correo)) {
//         header("Location: ../../front_end/vistas/perfil/perfil.php?error=campos_vacios");
//         exit();
//     }

//     try {
//         $sql = "UPDATE usuarios SET nombre = :nom, apellido = :ape, correo = :cor, telefono = :tel WHERE id = :id";
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute([
//             ':nom' => $nombre, ':ape' => $apellido, ':cor' => $correo, ':tel' => $telefono, ':id' => $id
//         ]);

//         $_SESSION['usuario_nombre'] = $nombre; // Actualizar nombre en la interfaz
//         header("Location: ../../front_end/vistas/perfil/perfil.php?status=perfil_actualizado");
//     } catch (PDOException $e) {
//         header("Location: ../../front_end/vistas/perfil/perfil.php?error=db_error");
//     }
// }

// /* FUNCIÓN 2: Valida y cambia la contraseña*/
// function actualizarPassword($pdo, $id, $datos) {
//     $actual    = $datos['pass_actual'];
//     $nueva     = $datos['pass_nueva'];
//     $confirmar = $datos['pass_confirmar'];

//     if ($nueva !== $confirmar) {
//         header("Location: ../../front_end/vistas/perfil/perfil.php?error=pass_no_coinciden#seguridad");
//         exit();
//     }

//     try {
//         $stmt = $pdo->prepare("SELECT password FROM usuarios WHERE id = :id");
//         $stmt->execute([':id' => $id]);
//         $user = $stmt->fetch();

//         if ($user && password_verify($actual, $user['password'])) {
//             $hash = password_hash($nueva, PASSWORD_BCRYPT);
//             $update = $pdo->prepare("UPDATE usuarios SET password = :pass WHERE id = :id");
//             $update->execute([':pass' => $hash, ':id' => $id]);

//             header("Location: ../../front_end/vistas/perfil/perfil.php?status=pass_actualizada#seguridad");
//         } else {
//             header("Location: ../../front_end/vistas/perfil/perfil.php?error=pass_incorrecta#seguridad");
//         }
//     } catch (PDOException $e) {
//         header("Location: ../../front_end/vistas/perfil/perfil.php?error=db_error#seguridad");
//     }

// /* FUNCIÓN 3: Obtiene todos los usuarios de la base de datos */
// function obtener_usuarios($pdo){
//     $pdo->exec("USE usuarios");
//     // conexion db para obtener el usuario
//     $sql ="SELECT id, nombre_usuario FROM usuario";
//     $stmt_usuario =$pdo->prepare($sql);
//     $stmt_usuario->execute();
//     $resultado= $stmt_usuario->fetchAll(PDO::FETCH_ASSOC);
//     return $resultado;
// }

// }


$id_usuario = $_SESSION['usuario_id'];

// 3. LÓGICA DE CONTROL: Aquí es donde se reciben los formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    
    if ($_POST['accion'] === 'actualizar_datos') {
        actualizarDatosPersonales($pdo, $id_usuario, $_POST);
    } 
    elseif ($_POST['accion'] === 'actualizar_password') {
        actualizarPassword($pdo, $id_usuario, $_POST);
    }
    elseif ($_POST['accion'] === 'subir_foto') {
        // Aquí es donde saltaba el error porque la función no existía abajo
        subirFoto($pdo, $id_usuario, $_FILES['foto']);
    }
}

/* FUNCIÓN PARA SUBIR LA FOTO */
function subirFoto($pdo, $id, $foto) {
    if ($foto['error'] === 0) {
        // Generar nombre único para la imagen
        $ext = pathinfo($foto['name'], PATHINFO_EXTENSION);
        $nombre_archivo = "perfil_" . $id . "_" . time() . "." . $ext;
        
        // Ruta donde se guardará físicamente
        $directorio_destino = "../../front_end/publico/uploads/perfiles/";
        $ruta_final = $directorio_destino . $nombre_archivo;

        // Crear la carpeta si no existe
        if (!is_dir($directorio_destino)) {
            mkdir($directorio_destino, 0777, true);
        }

        // Mover el archivo del temporal a la carpeta destino
        if (move_uploaded_file($foto['tmp_name'], $ruta_final)) {
            // Actualizar el nombre en la base de datos
            $stmt = $pdo->prepare("UPDATE usuarios.usuario SET foto_perfil = ? WHERE id = ?");
            $stmt->execute([$nombre_archivo, $id]);
            
            header("Location: ../../front_end/vistas/perfil_usuario/perfil.php");
            exit();
        }
    }
    header("Location: ../../front_end/vistas/perfil/perfil.php?error=upload_fail");
}

/* FUNCIÓN PARA DATOS PERSONALES */
function actualizarDatosPersonales($pdo, $id, $datos) {
    $nombre   = trim($datos['nombre']);
    $apellido = trim($datos['apellido']);
    $correo   = trim($datos['correo']);
    // $telefono = trim($datos['telefono']);

    try {
        $stmt = $pdo->prepare("UPDATE usuarios.usuario SET nombre = ?, apellido = ?, correo = ? WHERE id = ?");
        $stmt->execute([$nombre, $apellido, $correo, $id]);
        header("Location: ../../front_end/vistas/perfil_usuario/perfil.php?status=datos_ok");
        exit();
    } catch (PDOException $e) {
        header("Location: ../../front_end/vistas/perfil_usuario/perfil.php?error=db_error");
    }
}

/* FUNCIÓN PARA CAMBIAR CONTRASEÑA */
function actualizarPassword($pdo, $id, $datos) {
    $actual = $datos['password_actual'];
    $nueva  = $datos['nueva_password'];

    $stmt = $pdo->prepare("SELECT password FROM usuario WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();

    if (password_verify($actual, $user['password'])) {
        $hash = password_hash($nueva, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE usuarios.usuario SET password = ? WHERE id = ?");
        $stmt->execute([$hash, $id]);
        header("Location: ../../front_end/vistas/perfil/perfil.php?status=pass_ok");
    } else {
        header("Location: ../../front_end/vistas/perfil/perfil.php?error=pass_incorrecta");
    }
    exit();
}

/* FUNCIÓN PARA OBTENER USUARIOS (Para otros componentes) */
function obtener_usuarios($pdo){
    $sql ="SELECT id, nombre_usuario FROM usuario";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}