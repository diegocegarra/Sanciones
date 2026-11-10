<?php
# ESTE ARCHIVO VA A ENCARGARSE DE LA BASE DE DATOS DE TAREAS
require_once(__DIR__ . '/../../config.php');
require_once(CONEXION_PATH);
// require_once(ROLES_PATH);
// require_once(PERMISOS_PATH);
require_once (LOGS);




$pdo=  conectar_db();

$nombre_empresa = $_POST['nombre_empresa'];
$patronal_empresa = $_POST['patronal_empresa'];
$direccion_empresa = $_POST['direccion_empresa'];
$industria_empresa = $_POST['industria_empresa'];
$estado_empresa = $_POST['estado_empresa'];


if(isset($_POST['accionEmpresa'])){
    // $pdo->exec("USE empresas");
    $sqlEmpresa= "INSERT INTO empresas.empresas(numero_patronal, nombre_empresa, direccion, industria, estado) 
    VALUES(?,?,?,?,?)";
    $stmt= $pdo->prepare($sqlEmpresa);
    $resultado = $stmt->execute([$patronal_empresa, $nombre_empresa, $direccion_empresa, $industria_empresa, $estado_empresa]);
     if($resultado){
        registrarLog($pdo, $_SESSION['usuario_id'], "CREAR", "Creó la Empresa: " . $nombre_empresa);
        
     }
        header("Location: /IVSS/front_end/vistas/empresas/empresas.php?status=success");

    }



?>