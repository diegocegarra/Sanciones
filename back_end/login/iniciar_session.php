<?php

ini_set('log_errors', 1);


// 3. No mostrar errores al usuario final (seguridad)
ini_set('display_errors', 0);
require_once(__DIR__ . '/../config.php');
require_once(CONEXION_PATH);
require_once (ERRORES_PATH);

error_reporting(0); 
header('Content-Type: application/json');

try {
    $pdo = conectar_db();
    
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    error_log(print_r($data, true)); // Esto enviará el contenido real al log de errores de XAMPP

    if (!$data) {
        throw new Exception("No se recibieron datos válidos.");
    }



    // Extraer con valores por defecto para evitar errores de undefined index
    // $nombre      = $data['nombre'] ?? '';
    // $cedula      = $data['cedula'] ?? '';
    // $correo      = $data['correo'] ?? '';
    // $usuario     = $data['nombre_usuario'] ?? '';

        // 3. Limpieza trim() extra: quitar espacios vacíos al inicio y final
    $nombre = filter_var($data['nombre'], FILTER_SANITIZE_SPECIAL_CHARS);
    $correo = filter_var($data['correo'], FILTER_SANITIZE_EMAIL);
    $cedula = filter_var($data['cedula'], FILTER_SANITIZE_NUMBER_INT);
    $usuario = $data['nombre_usuario'];
    $passwordRaw = filter_var($data['password'], FILTER_SANITIZE_SPECIAL_CHARS);

    $codigoAdmin = isset($data['codigoAdmin']) ? trim($data['codigoAdmin']) : '';
    // $passwordRaw = $data['password'] ?? '';


    
    $entradas_nombre = preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/", $nombre);


    // nombre
    // VALOR DE LONGITUD
    $max_nombre = 30;
    $min_nombre = 6;

    // Usamos mb_strlen para nombres con tildes o eñes
    $longitud_actual = mb_strlen($nombre);

    if (!empty($nombre)) {

        if ($longitud_actual < $min_nombre) {
            throw new Exception("El nombre debe tener al menos $min_nombre caracteres.");
        }

        if ($longitud_actual > $max_nombre) {
            throw new Exception("El nombre no puede exceder los $max_nombre caracteres.");
        }
        // 2. Usamos una expresión regular para permitir solo letras y espacios
        if (!is_string($nombre) && ! $entradas_nombre) {
        
            throw new Exception("El nombre solo debe contener letras y espacios.");
        }


    }else{
        throw new Exception("El nombre no puede estar vacío.");

    }
   
    

    // cedula
    // si es int y tiene numeros
    
    if (!empty($cedula)) {

        if(!is_int($cedula) && !preg_match("/^[0-9]+$/", $cedula)){

            throw new Exception("la cedula tiene que ser solamente numeros.");

        }
        $min_cedula = 6;
        $max_cedula = 8;
        if(strlen($cedula) < $min_cedula || strlen($cedula) > $max_cedula  ){
            throw new Exception("La cedula no es valida");
        }

    }

 

    // usuario
    $min_usuario = 4;
    $max_usuario= 10;
    if(strlen($usuario) < $min_usuario || strlen($usuario) > $max_usuario ){
         throw new Exception("El nombre de usuario debe ser almenos ".$min_usuario." caracteres 
         y maximo ".$max_usuario." caracteres de largo .");
    }
    

    // correo
    // Si es valido
    // esto es para evitar que sea injecciones sql
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    throw new Exception("El formato del correo electrónico no es válido.");
    }
    if(empty($correo)){    
        throw new Exception("El correo no puede estar vacio.");
    }
    
    if (empty($usuario) || empty($passwordRaw)) {
        throw new Exception("Usuario y contraseña son obligatorios.");
    }

    
        $password = password_hash($passwordRaw, PASSWORD_DEFAULT);



        
//  DB CODE
    
// VERIFICAR SI EXISTE EL USUARIO

// 1. VERIFICAR SI EL USUARIO YA EXISTE

       // Buscamos coincidencia por cédula, correo o usuario
        $sql_check = "SELECT cedula, correo, nombre_usuario FROM usuarios.usuario WHERE cedula = ? OR correo = ? OR nombre_usuario = ? LIMIT 1";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->execute([$cedula, $correo, $usuario]);

   if ($resultado_comparar) {
    $correo_comparar = $resultado_comparar['correo'] === $correo;
    $cedula_comparar =$resultado_comparar['cedula'] === $cedula;
    $usuario_comparar =$resultado_comparar['usuario'] === $usuario;

    
    // 2. Identificamos cuál de los campos causó el conflicto
    if ($correo_comparar) {
        throw new Exception("El correo electrónico ya está en uso.");
    }
    if ($cedula_comparar) {
        throw new Exception("La cédula ya se encuentra registrada.");
    }
    if ($usuario_comparar) {
        throw new Exception("El nombre de usuario ya no está disponible.");
    }
    
      // Fallback genérico por seguridad
        throw new Exception("cedula, correo o usuarios datos ya están registrados.");
    }

 

    // LÓGICA DE ROL
    $CODIGO_SECRETO_ADMIN = "ADMIN"; 
    // Convertimos a mayúsculas y limpiamos espacios por seguridad
    $codigoLimpio = strtoupper(trim($codigoAdmin));

    // Si coincide es 1, si no, es 3 automáticamente
    $rol_id = ($codigoLimpio == $CODIGO_SECRETO_ADMIN) ? 1 : 3;

    $pdo->beginTransaction();

    // Especificamos la base de datos en el nombre de la tabla para evitar fallos de "No database selected"
    $sql = "INSERT INTO usuarios.usuario (nombre, cedula, correo, nombre_usuario, password, rol_id) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt_insert = $pdo->prepare($sql);
    $resultado = $stmt_insert->execute([
        $nombre, 
        $cedula, 
        $correo, 
        $usuario, 
        $password, 
        $rol_id
    ]);

    if ($resultado) {
        $pdo->commit();
        echo json_encode(["status" => "success", "mensaje" => "Registro completado con Rol ID: $rol_id"]);
        // header("Location: /IVSS/front_end/login/login.php");


    } else {
        throw new Exception("Error en la ejecución de la consulta.");
    }

} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        // $pdo->rollBack();
    }
    echo json_encode(["status" => "error", "mensaje" => $e->getMessage()]);
}















exit;