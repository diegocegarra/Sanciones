<?php
session_start();
require_once(__DIR__ . '/../config.php');
require_once(CONEXION_PATH);
$pdo = conectar_db();

// Verificación de seguridad básica
if (!isset($_SESSION['usuario_id'])) {
    die("Acceso denegado");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    try {
        // --- CASO 1: ACTUALIZAR PERMISOS DE ROLES ---
        if ($accion === 'actualizar_permisos') {
            $rol_id = $_POST['rol_id'];
            $permisos = $_POST['permisos'] ?? []; // Array de checkboxes

            // Primero borramos los permisos actuales de ese rol
            $stmt = $pdo->prepare("DELETE FROM permisos_roles WHERE rol_id = :rol_id");
            $stmt->execute([':rol_id' => $rol_id]);

            // Insertamos los nuevos permisos seleccionados
            $sql = "INSERT INTO permisos_roles (rol_id, permiso_id) VALUES (:rol_id, :perm_id)";
            $ins = $pdo->prepare($sql);
            foreach ($permisos as $permiso_id) {
                $ins->execute([':rol_id' => $rol_id, ':perm_id' => $permiso_id]);
            }
            
            header("Location: ../../front_end/vistas/perfil_usuario/configuracion.php?status=ok_permisos");
        }

        // --- CASO 2: AGREGAR FECHA FESTIVA ---
        if ($accion === 'agregar_fecha') {
            $fecha = $_POST['fecha_festiva'];
            $motivo = $_POST['motivo'] ?? 'Festivo';

            if (!empty($fecha)) {
                $stmt = $pdo->prepare("INSERT INTO fechas_festivas (fecha, motivo) VALUES (?, ?)");
                $stmt->execute([$fecha, $motivo]);
            }
            header("Location: ../../front_end/vistas/perfil_usuario/configuracion.php?status=ok_fecha");
        }

        // --- CASO 3: CAMBIAR AJUSTES DE NOTIFICACIONES ---
        if ($accion === 'ajustes_sistema') {
            $notificar = isset($_POST['notificar_email']) ? 1 : 0;
            
            // Suponiendo una tabla de 'ajustes' con clave/valor
            $stmt = $pdo->prepare("UPDATE ajustes_sistema SET valor = ? WHERE clave = 'notificar_email'");
            $stmt->execute([$notificar]);
            
            header("Location: ../../front_end/vistas/perfil_usuario/configuracion.php?status=ok_sistema");
        }

    } catch (PDOException $e) {
        die("Error al guardar: " . $e->getMessage());
    }
}