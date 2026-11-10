<?php
$conn = new mysqli("localhost", "usuario", "clave", "validacion_db");

$nombre = $_POST['nombre'];
$iva = $_POST['iva'];
$deuda = $_POST['deuda'];
$total = $_POST['total'];

$sql = "INSERT INTO validacion (nombre, iva, deuda, total)
        VALUES ('$nombre', '$iva', $deuda, '$total')";

$conn->query($sql);
$conn->close();

header("Location: validacion.html"); // Redirige al formulario
?>
s