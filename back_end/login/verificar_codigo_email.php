<?php
// funcion para ver los errores que no aparecen con el json
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== NULL && ($error['type'] === E_ERROR || $error['type'] === E_PARSE)) {
        ob_clean(); // Borra cualquier HTML que se haya colado
        echo json_encode([
            "status" => "error",
            "message" => "Error fatal en PHP: " . $error['message'] . " en " . $error['file'] . " línea " . $error['line']
        ]);
    }
});

try{

    if (!file_exists(__DIR__ . '/../config.php')) throw new Exception("No se encuentra config.php");
    require_once(__DIR__ . '/../config.php');

    if (!defined('FUNCION_USUARIO_CORREO') || !file_exists(FUNCION_USUARIO_CORREO)) throw new Exception("No se encuentra el archivo de funciones email");
        require(SESSION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        $token = isset($_POST['token']) ? $_POST['token'] : null;
        $email = isset($_POST['email']) ? $_POST['email'] : null;

        $selecionarID = selecionar_id_usuario($pdo, $email);


        
        $token_original =buscarToken($pdo, $selecionarID);

        if($token_original == $token_email){
            echo "exito";
        }

        if (!$email) {
            throw new Exception("Token no recibido");
        }


    }

}catch(Exception $e){
     ob_end_clean(); // Limpiar cualquier cosa que se haya impreso
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
} 


?>