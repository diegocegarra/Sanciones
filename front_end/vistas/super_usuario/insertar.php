<?php
$conn = new mysqli("localhost", "usuario", "clave", "empresas_db");

$nombre = $_POST['nombre'];
$rif = $_POST['rif'];
$empleados = $_POST['empleados'];
$sector = $_POST['sector'];

$sql = "INSERT INTO empresas (nombre, rif, empleados, sector)
        VALUES ('$nombre', '$rif', $empleados, '$sector')";

$conn->query($sql);
$conn->close();

header("Location: empresa.html"); // Redirige al formulario
?>
s