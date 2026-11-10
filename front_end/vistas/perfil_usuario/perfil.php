<?php
session_start();

// 1. CARGA DE CONFIGURACIÓN Y CONEXIÓN
require_once(__DIR__ . '/../../../back_end/conexion.php'); 
$pdo = conectar_db();
$pdo->exec("USE usuarios"); 

require_once(__DIR__ . '/../../../back_end/config.php');

// 2. VERIFICACIÓN DE SESIÓN
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../login/login.php");
    exit();
}

// 3. OBTENER DATOS DEL USUARIO LOGUEADO
$id_usuario = $_SESSION['usuario_id'];
try {
    $query = "SELECT nombre, apellido, cargo, cedula, correo, telefono, foto_perfil FROM usuario WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':id' => $id_usuario]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        die("Usuario no encontrado.");
    }
} catch (PDOException $e) {
    die("Error de base de datos: " . $e->getMessage());
}

// 4. OBTENER LISTA DE EQUIPO
$queryEquipo = "SELECT nombre, apellido, cargo, foto_perfil, esta_logeado FROM usuario ORDER BY nombre ASC";
$equipo = $pdo->query($queryEquipo)->fetchAll(PDO::FETCH_ASSOC);

function determinarEstado($fechaConexion) {
    if (!$fechaConexion) return false;
    $ultima = strtotime($fechaConexion);
    $ahora = time();
    return ($ahora - $ultima) < 300; 
}

$nombre_completo = $usuario['nombre'] . ' ' . $usuario['apellido'];

// Lógica de visualización de foto: si no tiene, usa avatar por defecto
$ruta_fotos = "../../publico/uploads/perfiles/";
$foto_perfil = !empty($usuario['foto_perfil']) ? $ruta_fotos . $usuario['foto_perfil'] : 'https://ui-avatars.com/api/?name=' . urlencode($nombre_completo);

// 5. CARGAR BARRA LATERAL
require_once(BARRA_LATERAL);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil</title>
    <link href="../../css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .perfil-header { background: linear-gradient(135deg, #0d6efd 0%, #003d99 100%); color: white; padding: 60px 0; border-radius: 0 0 20px 20px; }
        .foto-perfil { width: 150px; height: 150px; object-fit: cover; border: 5px solid white; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .cont-foto { position: relative; display: inline-block; margin-top: -75px; }
        .btn-edit-foto { position: absolute; bottom: 5px; right: 5px; width: 40px; height: 40px; border-radius: 50%; border: 3px solid white; display: flex; align-items: center; justify-content: center; }
        .main-content { margin-left: 250px; padding: 20px; transition: 0.3s; }
        @media (max-width: 992px) { .main-content { margin-left: 0; } }
    </style>
</head>
<body class="bg-light">

<main class="main-content">
    <div class="perfil-header text-center">
        <h1 class="fw-bold">Gestión de Perfil</h1>
    </div>

    <div class="container mt-4">
        <div class="row">
            <div class="col-lg-4 text-center">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="cont-foto">
                            <img src="<?php echo $foto_perfil; ?>" class="rounded-circle foto-perfil" alt="Perfil">
                            <button type="button" class="btn btn-primary btn-edit-foto shadow" onclick="document.getElementById('input_foto').click();">
                                <i class="fa-solid fa-camera"></i>
                            </button>
                        </div>

                        <form id="form-foto" action="../../../back_end/usuarios/subir_foto.php" method="POST" enctype="multipart/form-data" style="display: none;">
                            <input type="file" name="foto" id="input_foto" accept="image/*" onchange="subirFotoAuto()">
                        </form>

                        <h4 class="fw-bold mt-3"><?php echo htmlspecialchars($nombre_completo); ?></h4>
                        <p class="text-muted"><?php echo htmlspecialchars($usuario['cargo']); ?></p>
                        
                        <div class="nav flex-column nav-pills mt-4 text-start" id="v-pills-tab" role="tablist">
                            <button class="nav-link active mb-2" data-bs-toggle="pill" data-bs-target="#info-personal"><i class="fa-solid fa-user me-2"></i> Información</button>
                            <button class="nav-link mb-2" data-bs-toggle="pill" data-bs-target="#seguridad"><i class="fa-solid fa-lock me-2"></i> Seguridad</button>
                            <button class="nav-link mb-2" data-bs-toggle="pill" data-bs-target="#equipo"><i class="fa-solid fa-users me-2"></i> Mi Equipo</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="tab-content" id="v-pills-tabContent">
                    
                    <div class="tab-pane fade show active" id="info-personal">
                        <div class="card border-0 shadow-sm p-4">
                            <h5 class="fw-bold mb-4">Información Personal</h5>
                            <div class="row g-3">
                                <div class="col-md-6"><label class="small fw-bold">Nombre</label><input type="text" class="form-control" value="<?php echo $usuario['nombre']; ?>" readonly></div>
                                <div class="col-md-6"><label class="small fw-bold">Apellido</label><input type="text" class="form-control" value="<?php echo $usuario['apellido']; ?>" readonly></div>
                                <div class="col-md-6"><label class="small fw-bold">Cédula</label><input type="text" class="form-control" value="<?php echo $usuario['cedula']; ?>" readonly></div>
                                <div class="col-md-6"><label class="small fw-bold">Teléfono</label><input type="text" class="form-control" value="<?php echo $usuario['telefono']; ?>" readonly></div>
                                <div class="col-md-12"><label class="small fw-bold">Correo</label><input type="email" class="form-control" value="<?php echo $usuario['correo']; ?>" readonly></div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="seguridad">
                        <div class="card border-0 shadow-sm p-4">
                            <h5 class="fw-bold mb-4">Seguridad de la Cuenta</h5>
                            <form action="procesar_password.php" method="POST">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Contraseña Actual</label>
                                    <input type="password" name="pass_actual" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Nueva Contraseña</label>
                                    <input type="password" name="pass_nueva" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-primary px-4">Actualizar Contraseña</button>
                            </form>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="equipo">
                        <div class="card border-0 shadow-sm p-4">
                            <h5 class="fw-bold mb-4">Compañeros de Equipo</h5>
                            <div class="list-group list-group-flush">
                                <?php foreach ($equipo as $miembro): 
                                // ultima conexion
                                    $online = determinarEstado($miembro['esta_logeado']);
                                    $foto_m = !empty($miembro['foto_perfil']) ? $ruta_fotos . $miembro['foto_perfil'] : 'https://ui-avatars.com/api/?name='.urlencode($miembro['nombre']);
                                ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo $foto_m; ?>" class="rounded-circle me-3" width="45" height="45" style="object-fit: cover;">
                                            <div>
                                                <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($miembro['nombre'].' '.$miembro['apellido']); ?></h6>
                                                <small class="text-muted"><?php echo htmlspecialchars($miembro['cargo']); ?></small>
                                            </div>
                                        </div>
                                        <span class="badge rounded-pill <?php echo $online ? 'bg-success' : 'bg-secondary'; ?>">
                                            <?php echo $online ? 'En línea' : 'Desconectado'; ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</main>

<script src="../../js/bootstrap.bundle.min.js"></script>

<script>
// Función para enviar la foto apenas se selecciona
function subirFotoAuto() {
    const fileInput = document.getElementById('input_foto');
    if (fileInput.files.length > 0) {
        Swal.fire({
            title: 'Subiendo imagen...',
            text: 'Estamos actualizando tu foto de perfil',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });
        document.getElementById('form-foto').submit();
    }
}

// Lógica de Alertas desde la URL
const urlParams = new URLSearchParams(window.location.search);

// 1. Alertas de Contraseña
if (urlParams.get('cambio') === 'ok') {
    Swal.fire('¡Éxito!', 'Contraseña actualizada correctamente.', 'success');
}
if (urlParams.get('error') === 'pass') {
    Swal.fire('Error', 'La contraseña actual no coincide.', 'error');
}

// 2. Alertas de Foto 
if (urlParams.get('status') === 'foto_ok') {
    Swal.fire('¡Genial!', 'Foto de perfil actualizada con éxito.', 'success');
}
</script>

</body>
</html>