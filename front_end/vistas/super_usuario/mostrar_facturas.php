<?php
$conexion = new mysqli("localhost", "usuario", "contraseña", "ejemplo");

if ($conexion->connect_error) {
  die("Conexión fallida: " . $conexion->connect_error);
}

$sql = "SELECT id, nombre, correo FROM usuarios";
$resultado = $conexion->query($sql);

if ($resultado->num_rows > 0) {
  while($fila = $resultado->fetch_assoc()) {
    echo "<tr><td>" . $fila["id"] . "</td><td>" . $fila["nombre"] . "</td><td>" . $fila["correo"] . "</td></tr>";
  }
} else {
  echo "<tr><td colspan='3'>No hay datos</td></tr>";
}

$conexion->close();
?>
