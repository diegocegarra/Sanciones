<?php 
// Esto obtiene el nombre del archivo actual (ej: 'usuarios.php')
$pagina_actual = basename($_SERVER['PHP_SELF']); 

// Para saber que la sesión y la conexión estén disponibles
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}



require_once(__DIR__ . '/../../../back_end/config.php');
require_once(CONEXION_PATH);
require_once(FUNCIONES_ROLES);

$pdo_sidebar = conectar_db();

// $rol = obtener_roles($pdo, $_SESSION['usuario_id']);

$pagina_actual = basename($_SERVER['PHP_SELF']); 

// Extrae los datos del usuario para la barra lateral
$id_user_sesion = $_SESSION['usuario_id'] ?? 0;
$nombre_user_sesion = "Usuario";
$foto_sidebar = "https://ui-avatars.com/api/?name=U&background=00488b&color=fff";

if ($id_user_sesion > 0) {
    $pdo_sidebar->exec("USE usuarios");
    $stmt = $pdo_sidebar->prepare("SELECT nombre, apellido, foto_perfil FROM usuario WHERE id = ?");
    $stmt->execute([$id_user_sesion]);
    $user_sidebar = $stmt->fetch();

   if ($user_sidebar) {
        $nombre_user_sesion = $user_sidebar['nombre'] . ' ' . ($user_sidebar['apellido'] ?? '');
        
        // Verificamos si tiene foto en la BD, si no, usamos el avatar de letras
        if (!empty($user_sidebar['foto_perfil'])) {
            $foto_sidebar = "../../publico/uploads/perfiles/" . $user_sidebar['foto_perfil'];
        } else {
            $foto_sidebar = "https://ui-avatars.com/api/?name=" . urlencode($nombre_user_sesion) . "&background=00488b&color=fff";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../../css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../publico/style_barra_tareas.css">
    <!-- NO HACE FALTA COLOCAR EL REL ICON EN LOS DEMAS ARCHIVOS PHP LA BARRA LATERAL COLOCARA EL ICO NO BORRAR-->
    <link rel="icon" href="../ivss.png" type="image"> 
</head>
<body>



        
    <div class="sidebar">
    <div class="user-panel mt-3 d-flex flex-column align-items-center border-bottom border-secondary">
        <div class="image">
         <a href="../perfil_usuario/perfil.php">  <img src="<?php echo $foto_sidebar; ?>" class="rounded-circle shadow-sm" width="45" height="45" style="object-fit: cover;">
        </a>
        </div>
        <div class="info text-center">
            <a href="../perfil_usuario/perfil.php" class="d-block text-white text-decoration-none fw-bold" style="font-size: 0.9rem;">
                <i class="fa-solid fa-circle text-success me-1" style="font-size: 0.6rem;"></i>
                <?php echo htmlspecialchars($nombre_user_sesion); ?>
            </a>
        </div>
    </div>

      <!-- BOTON PARA PLEGAR BARRA -->
        <div class="toggle-sidebar p-2 text-center" style="cursor: pointer;">
            <i class="fa-solid fa-bars text-white fs-4" id="btnToggle"></i>
        </div>
        

    <nav class="nav flex-column ">
        <div class="<?php echo ($pagina_actual == 'index.php') ? 'active' : ''; ?>">
            <a class="nav-link" href="../inicio/index.php">
                <span class="icon">
                    <i><img src="../barra_lateral/grid.svg" alt="" style="filter: brightness(0) invert(1);"></i>
                </span>
                <span class="description"> Inicio </span>
            </a>
        </div>

            <div class="<?php echo ($pagina_actual == 'perfil.php') ? 'active' : ''; ?> mt-3">
                <a class="nav-link" href="../perfil_usuario/perfil.php">
                    <span class="icon">
                        <i class=""><img src="../barra_lateral/person.svg" alt="" style="filter: brightness(0) invert(1);"></i>
                    </span>
                    <span class="description"> Perfil </span>
                </a>
            </div>

            <div class="<?php echo ($pagina_actual == 'empresas.php') ? 'active' : ''; ?> mt-3">
                <a class="nav-link" href="../empresas/empresas.php">
                    <span class="icon">
                        <i class=""><img src="../barra_lateral/building.svg" alt="" style="filter: brightness(0) invert(1);"></i>
                    </span>
                    <span class="description"> Empresas </span>
                </a>
            </div>

            <div class="<?php echo ($pagina_actual == 'tareas.php') ? 'active' : ''; ?> mt-3">
                <a class="nav-link" href="../tareas/tareas.php">
                    <span class="icon">
                        <i><img src="../barra_lateral/clipboard.svg" alt="" style="filter: brightness(0) invert(1);"></i>
                    </span>
                    <span class="description"> Tareas </span>
                </a>
            </div>

<!-- SI QUIERES VER ESTO CAMBIALO A 3 QUE ES ANALISTA -->
 <!-- HAY PROBLEMAS REGISTRANDO EL CODIGO DEL ADMIN -->
            <!-- ROLES -->
<?php if ($rol->rol_id == 1 || $rol->rol_id == 2):?>

            <div class="<?php echo ($pagina_actual == 'analiticas.php') ? 'active' : ''; ?> mt-3">
                <a class="nav-link" href="../perfil_usuario/logs.php">
                    <span class="icon">
                        <i><img src="../barra_lateral/graph-up-arrow.svg" alt="" style="filter: brightness(0) invert(1);"></i>
                    </span>
                    <span class="description"> Actividades </span>
                </a>
            </div>
    


            <div class="<?php echo ($pagina_actual == 'analiticas.php') ? 'active' : ''; ?> mt-3">
                <a class="nav-link" href="../perfil_usuario/analiticas.php">
                    <span class="icon">
                        <i><img src="../barra_lateral/graph-up-arrow.svg" alt="" style="filter: brightness(0) invert(1);"></i>
                    </span>
                    <span class="description"> Analíticas </span>
                </a>
            </div>

            <a class="nav-link mt-3" href="#" data-bs-toggle="collapse" data-bs-target="#submenu" 
               aria-expanded="<?php echo ($pagina_actual == 'configuracion.php') ? 'true' : 'false'; ?>" aria-controls="submenu">
                <span class="icon">
                    <i><img src="../barra_lateral/gear.svg" alt="" style="filter: brightness(0) invert(1);"></i>
                </span>
                <span class="description"> Configuración <i class="bi bi-caret-down-fill"></i></span>
            </a>

            <div class="sub-menu collapse <?php echo ($pagina_actual == 'configuracion.php') ? 'show' : ''; ?>" id="submenu">
                <div class="<?php echo ($pagina_actual == 'configuracion.php') ? 'active' : ''; ?>">
                    <a class="nav-link" href="../perfil_usuario/configuracion.php">
                        <span class="icon">
                            <i><img src="../barra_lateral/toggle.svg" alt="" style="filter: brightness(0) invert(1);"></i>
                        </span>
                        <span class="description"> Generales </span>
                    </a>
                </div>
            </div>
         </nav>

<?php endif;?>
           <div class="logout-section mt-auto p-3">
             <a href="../../../back_end/login/terminar_session.php" class="btn btn-danger w-10 ">
                 <i class="fa-solid fa-right-from-bracket me-2"></i>
               <span class="description">Cerrar Sesión</span>
             </a>
           </div>
        </div>

    <script>
    document.getElementById('btnToggle').addEventListener('click', function() {
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.getElementById('resultados'); // El contenedor de tu tabla
    
    sidebar.classList.toggle('collapsed');
    
    if (sidebar.classList.contains('collapsed')) {
        mainContent.style.marginLeft = "80px"; // Espacio para solo iconos
        mainContent.style.width = "calc(100% - 80px)";
    } else {
        mainContent.style.marginLeft = "15rem"; // Vuelve a su tamaño normal
        mainContent.style.width = "calc(100% - 15rem)";
    }
});
    </script>

    <script src="../../js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>    

</body>
</html>