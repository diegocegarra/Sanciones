<?php
// este archivo es una prueba para el componente de la vista de los roles
require_once('../src/funciones.php');
$pdo = conectar_db();
$usuarios =obtener_usuarios($pdo);
// consulta del array de usuarios para ver valores asignados a la base de datos print_r($usuarios);
$roles =obtener_roles($pdo);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body>

  

    <!-- asignar tareas -->
    <form action="../src/asignar_usuario.php" method="POST">
        <center>
        <select name="selecion_usuario" id="lista_usuarios" >
            <option value="">-- seleccione usuario --</option>
            <?php foreach ($usuarios as $escoger): ?>
                <option value="<?php echo htmlspecialchars($escoger['id']); ?>">
                    <?php echo htmlspecialchars($escoger['nombre_usuario']); ?>
                </option>
        <?php endforeach; ?>
        </select>

        <input type="submit" value="asignar" onclick="">
        </center>
    </form>

    <!-- asignar roles -->

       <form action="../src/asignar_usuario.php" method="POST">
        <center>
            <select name="selecion_usuario_rol" id="lista_usuarios" >
            <option value="">-- seleccione usuario --</option>
            <?php foreach ($usuarios as $escoger): ?>
                <option value="<?php echo htmlspecialchars($escoger['id']); ?>">
                    <?php echo htmlspecialchars($escoger['nombre_usuario']); ?>
                </option>
        <?php endforeach; ?>
        </select>

          <select name="selecion_de_rol" id="lista_rol" >
            <option value="">-- seleccione un rol --</option>
                <?php foreach ($roles as $escoger_rol): ?>
                    <option value="<?php echo htmlspecialchars($escoger_rol['id_roles']); ?>">
                        <?php echo htmlspecialchars($escoger_rol['nombre_rol']); ?>
                    </option>
            <?php endforeach; ?>
        </select>

        <input type="submit" value="asignar roles" onclick="">
        </center>
    </form>


<div class="container mt-4">
  <div class="row px-3 d-none d-md-flex text-muted small uppercase mb-2">
    <div class="col-md-4">Usuario</div>
    <div class="col-md-3">Correo</div>
    <div class="col-md-3 text-center">Estado</div>
    <div class="col-md-2 text-end">Acciones</div>
  </div>

  <div class="card border-0 shadow-sm mb-3 py-2 px-3">
    <div class="card-body p-0">
      <div class="row align-items-center">
        <div class="col-md-4">
          <div class="d-flex align-items-center">
            <div class="bg-primary text-white rounded-circle p-2 me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
              JD
            </div>
            <div>
              <h6 class="mb-0">John Doe</h6>
              <small class="text-muted">ID: #8821</small>
            </div>
          </div>
        </div>
        <div class="col-md-3 py-2 py-md-0">
          john.doe@example.com
        </div>
        <div class="col-md-3 text-md-center">
          <span class="badge rounded-pill bg-success-subtle text-success">Activo</span>
        </div>
        <div class="col-md-2 text-end">
          <button class="btn btn-light btn-sm"><i class="bi bi-three-dots"></i></button>
        </div>
      </div>
    </div>
  </div>
</div>


</body>
</html>

<?php
?>