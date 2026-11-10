<?php
require_once(__DIR__ . '/../../config.php');
require(CONEXION_PATH);
$pdo = conectar_db();



// PARTE PARA EL CORREO

// $selecionarID = selecionar_id_usuario($pdo, $correo);

// Esto es para insertar el token en la tabla de correos. 
function insertar_token($pdo, $idUsuario){

    // $idUsuario = $selecionarID['id'];
   // $token = bin2hex(random_bytes(10)); // Genera un token aleatorio
    $token = random_int(100000, 999999);
    $expiracion = date("Y-m-d H:i:s", strtotime('+15 minutes')); // Vence en 15 min


    $sql = "INSERT INTO usuarios.recuperacion_tokens (usuario_id, token, token_expiracion) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idUsuario, $token, $expiracion]);

  
}
// busca el token de recuperacion donde existe el id del usuario
function buscarToken($pdo, $idUsuario ){

    $sql_token = "SELECT token, token_expiracion FROM usuarios.recuperacion_tokens WHERE usuario_id = ? ORDER BY token_expiracion DESC LIMIT 1";
    $stmt= $pdo->prepare($sql_token);
    $stmt->execute([$idUsuario]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Si encontró algo, devuelve solo el string del token. Si no, devuelve false.
    return $resultado ? $resultado['token'] : false;
}


// agregar a la la tabla cron para que sea instantaneo
function agregar_cola_correos($pdo, $correo, $selecionarID){
    // inserta primero el token
    insertar_token($pdo, $selecionarID);
    $token_email= buscarToken($pdo, $selecionarID );

    // contenido del correo de recuperacion
    $destinatario = $correo;
    $asunto= 'Codigo de seguridad';
    $cuerpo= "Tu código de recuperación es: $token_email Expira en 15 minutos.";

    
    $sql = "INSERT INTO usuarios.cola_correos(destinatario, asunto, cuerpo) VALUES(?,?,?)";
    $stmt = $pdo->prepare($sql);
    $resultado = $stmt->execute([$destinatario, $asunto, $cuerpo]);
 
}

// seleciona todos los correos en pendiente y verifica que los intentos no sean 3 y tiene un limite de 10
function enviar_al_correo($pdo){
    $stmt = $pdo->prepare("SELECT cuerpo FROM usuarios.cola_correos WHERE estado = 'pendiente' AND intentos < 3 LIMIT 10");
    $stmt->execute();
    $correos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $correos;
}



function selecionar_id_usuario($pdo, $correo){
    $sql = "SELECT id FROM usuarios.usuario WHERE correo = ? ";
    $stmt= $pdo->prepare($sql);
    $stmt->execute([$correo]);
    return $stmt->fetchColumn();
}

// actualiza de procesado a enviado
function actualizar_estatus_correo($pdo, $selecionarID){
    $stmt = $pdo->prepare("UPDATE usuarios.cola_correos SET estado = 'enviado' WHERE id = ?");
    $stmt->execute([$selecionarID]);
}

// actualiza los intentos que se hacen con el correo hasta que funcione
function actualizar_intentos_correos($pdo, $selecionarID){
    $stmt = $pdo->prepare("UPDATE usuarios.cola_correos SET intentos = intentos + 1 WHERE id = ?");
    $stmt->execute([$selecionarID]);

}


// verificacion de token

// function selecionar_token($pdo, $selecionarID, $token_email){

// }



?>


