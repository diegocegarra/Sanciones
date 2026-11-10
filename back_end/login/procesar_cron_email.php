<?php
// 1. Configuración de ejecución en segundo plano
ignore_user_abort(true); 
set_time_limit(120);
ini_set('display_errors', 0); // Evita que basura de texto rompa el JSON

register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== NULL && ($error['type'] === E_ERROR || $error['type'] === E_PARSE)) {
        if (ob_get_length()) ob_clean();
        echo json_encode(["status" => "error", "message" => "Error fatal: " . $error['message']]);
    }
});

ob_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

try {
    require_once(__DIR__ . '/../config.php');
    require(COMPOSER);
    require(FUNCION_USUARIO_CORREO);

    $correos = enviar_al_correo($pdo); // Obtiene los pendientes
    $enviadosCount = 0;

    if (!empty($correos)) {
        $mail = new PHPMailer(true);
        // Configuración fija del servidor (fuera del loop para ser más rápido)
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'legodie009@gmail.com';
        $mail->Password   = 'cjpfkpbeirckimif';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;
        $mail->SMTPKeepAlive = true; 
        $mail->SMTPOptions = [
            'ssl' => ['verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true]
        ];

        foreach($correos as $correo) {
            try {
                $mail->setFrom('no-reply@tuweb.com', 'Tu Sistema');
                $mail->addAddress($correo['destinatario']);
                $mail->isHTML(true);
                $mail->Subject = $correo['asunto'];
                $mail->Body    = $correo['cuerpo'];

                if ($mail->send()) {
                    // IMPORTANTE: Usa el ID de la tabla COLA, no el del usuario
                    actualizar_estatus_correo($pdo, $correo['id']); 
                    $enviadosCount++;
                }
            } catch (Exception $e) {
                // Si falla este correo específico, registramos el intento y seguimos con el próximo
                actualizar_intentos_correos($pdo, $correo['id']);
            }
            $mail->clearAddresses(); // Limpiar destinatario para el siguiente ciclo
        }
        $mail->smtpClose();
    }

    // RESPUESTA ÚNICA AL FINAL
    if (ob_get_length()) ob_end_clean();
    echo json_encode([
        "status" => "success", 
        "message" => "Proceso terminado", 
        "enviados" => $enviadosCount
    ]);

} catch (Exception $e) {
    if (ob_get_length()) ob_end_clean();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
exit;




// // 1. Bloquear cualquier salida de errores al navegador (esto evita el <br /> <b>)
// // FUERZA LA VISIBILIDAD DE ERRORES
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
// // 2. Preparar el encabezado JSON
// // header('Content-Type: application/json');

// // USANDO RESEND API > ESTO TIENE INSTALADO RESEND API LA CUAL ES UNA API DE PAGO DE CORREOS QUE PERMITE ENVIAR 100 Correos gratis al dia y mas de 3000 al mes gratis. 
// // RESEND API NO SE USA POR AHORA PERO PARA ENVIAR HEAVY CORREOS PUEDE SER
// // $resend = Resend::client('re_xxxxxxxxx');

// // $resend->emails->send([
// //   'from' => 'Acme <onboarding@resend.dev>',
// //   'to' => ['cegarradie@gmail.com'],
// //   'subject' => 'hello world',
// //   'html' => '<strong>it works!</strong>',
// // ]);

// // funcion para ver los errores que no aparecen con el json
// register_shutdown_function(function() {
//     $error = error_get_last();
//     if ($error !== NULL && ($error['type'] === E_ERROR || $error['type'] === E_PARSE)) {
//         ob_clean(); // Borra cualquier HTML que se haya colado
//         echo json_encode([
//             "status" => "error",
//             "message" => "Error fatal en PHP: " . $error['message'] . " en " . $error['file'] . " línea " . $error['line']
//         ]);
//     }
// });

// // Iniciar un buffer para atrapar cualquier eco accidental
// ob_start();

// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\SMTP;
// use PHPMailer\PHPMailer\Exception;

// try {
//       // 1. Permite que el script siga corriendo aunque el navegador se desconecte
//     ignore_user_abort(true); 

//     // 2. Evita que el script se corte por tiempo (útil si hay muchos correos)
//     set_time_limit(120);

//     // Verificar que los archivos existan
//     if (!file_exists(__DIR__ . '/../config.php')) throw new Exception("No se encuentra config.php");
//     require_once(__DIR__ . '/../config.php');
    
//     if (!defined('COMPOSER') || !file_exists(COMPOSER)) throw new Exception("No se encuentra el autoloader de Composer");
//     require(COMPOSER);
    
//     if (!defined('FUNCION_USUARIO_CORREO') || !file_exists(FUNCION_USUARIO_CORREO)) throw new Exception("No se encuentra el archivo de funciones");
//     require(FUNCION_USUARIO_CORREO);

 

//     // if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     //     $email = isset($_POST['email']) ? $_POST['email'] : null;

//     //     if (!$email) {
//     //         throw new Exception("Email no recibido");
//     //     }

//         //  $mail->Port       = 465;

//         // funciones de funcion_usuario.php para enviar el correo y agregarlo a un cron
//         // $idUsuario= selecionar_id_usuario($pdo, $email);
//         // $email_cola = agregar_cola_correos($pdo, $email, $idUsuario);
//         $selecionarID = selecionar_id_usuario($pdo, $email);
//         $correos = enviar_al_correo($pdo);

//         // correos es retornada por enviar_al_correo($pdo) en funcion_usuario.php        
//         foreach($correos as $correo){
            
//                 // Lógica de PHPMailer
//                 $mail = new PHPMailer(true);

//                 try {
//                 $mail->SMTPDebug = SMTP::DEBUG_OFF; // CRITICO: Debe estar en OFF
//                 $mail->isSMTP();
//                 $mail->Host       = 'smtp.gmail.com';
//                 $mail->SMTPAuth   = true;
//                 $mail->Username   = 'legodie009@gmail.com';
//                 $mail->Password   = 'cjpfkpbeirckimif';
//                 $mail->SMTPSecure = 'tls'; // Habilitar encriptación TLS

//                 // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
//                 // $mail->Port       = 587;
//                 // $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
//                 // $mail->Port       = 587;
//                 // $mail->Timeout = 70; // Aumenta a 60 segundos
                
//                 // EL PUERTO 465 va con ENCRYPTION_SMTPS NO ENCRYPTION_STARTTLS
//                 $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
//                 $mail->Port       = 465;
//                 $mail->Timeout    = 20; 
//                                         // No esperar más de 20 seg
//                 $mail->SMTPKeepAlive = true;                     // Mantiene la conexión abierta
                

//                 // para el SSL el certificado
//                 $mail->SMTPOptions = array(
//                     'ssl' => array(
//                         'verify_peer' => false,
//                         'verify_peer_name' => false,
//                         'allow_self_signed' => true
//                     )
//                 );

//                 // para enviar el email al usuario

//                 // Contenido del correo
//                 $mail->setFrom('no-reply@tuweb.com', 'Tu Sistema');
//                 $mail->addAddress($correo['destinatario']);
//                 $mail->isHTML(true);
//                 $mail->Subject = $correo['asunto'];
//                 $mail->Body    = $correo['cuerpo'];

//                 if ($mail->send()) {
//                     // 3. Marcar como enviado
//                     actualizar_estatus_correo($pdo, $selecionarID);
//                 }
//                 // Limpiar buffer y enviar éxito
//                 ob_end_clean();
//                 // echo json_encode(["status" => "success", "message" => "Correo enviado"]);
            
//             }catch (Exception $e) {

//                 actualizar_intentos_correos($pdo, $selecionarID);

//                 // RESPUESTA ÚNICA AL FINAL
//                 if (ob_get_length()) ob_end_clean();
//                 echo json_encode([
//                     "status" => "success", 
//                     "message" => "Proceso terminado", 
//                     "enviados" => $enviadosCount
//                 ]);
//             }

//          }//fin del foreach


//         // }//fin del if para ver si es un posts
//     }catch (Exception $e) {

//         ob_end_clean(); // Limpiar cualquier cosa que se haya impreso
//         echo json_encode(["status" => "error", "message" => $e->getMessage()]);
//     }

// exit;






// // LOCALMENTE SIN API CON PHP MAILER
// // 3. Capturar errores fatales para que respondan en JSON y no en HTML
// register_shutdown_function(function() {
//     $error = error_get_last();
//     if ($error !== NULL && ($error['type'] === E_ERROR || $error['type'] === E_PARSE)) {
//         ob_clean(); // Borra cualquier HTML que se haya colado
//         echo json_encode([
//             "status" => "error",
//             "message" => "Error fatal en PHP: " . $error['message'] . " en " . $error['file'] . " línea " . $error['line']
//         ]);
//     }
// });

// // Iniciar un buffer para atrapar cualquier eco accidental
// ob_start();

// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\SMTP;
// use PHPMailer\PHPMailer\Exception;

// try {
//     // Verificar que los archivos existan
//     if (!file_exists(__DIR__ . '/../config.php')) throw new Exception("No se encuentra config.php");
//     require_once(__DIR__ . '/../config.php');
    
//     if (!defined('COMPOSER') || !file_exists(COMPOSER)) throw new Exception("No se encuentra el autoloader de Composer");
//     require(COMPOSER);
    
//     if (!defined('FUNCION_USUARIO') || !file_exists(FUNCION_USUARIO)) throw new Exception("No se encuentra el archivo de funciones");
//     require(FUNCION_USUARIO);

//     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//         $email = isset($_POST['email']) ? $_POST['email'] : null;

//         if (!$email) {
//             throw new Exception("Email no recibido");
//         }
        
//         $idUsuario= selecionar_id_usuario($pdo, $email);

//         // Lógica de PHPMailer
//         $mail = new PHPMailer(true);
//         $mail->SMTPDebug = SMTP::DEBUG_OFF; // CRITICO: Debe estar en OFF
//         $mail->isSMTP();
//         $mail->Host       = 'smtp.gmail.com';
//         $mail->SMTPAuth   = true;
//         $mail->Username   = 'legodie009@gmail.com';
//         $mail->Password   = 'cjpfkpbeirckimif';
//         $mail->SMTPSecure = 'tls'; // Habilitar encriptación TLS

//         // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
//         // $mail->Port       = 587;
//         // $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
//         // $mail->Port       = 587;
//         // $mail->Timeout = 70; // Aumenta a 60 segundos
        
//         // EL PUERTO 465 va con ENCRYPTION_SMTPS NO ENCRYPTION_STARTTLS
//         $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
//         $mail->Port       = 465;
//        $mail->Timeout    = 20; 
//                                 // No esperar más de 20 seg
// $mail->SMTPKeepAlive = true;                     // Mantiene la conexión abierta
        

//         // para el SSL el certificado
//         $mail->SMTPOptions = array(
//             'ssl' => array(
//                 'verify_peer' => false,
//                 'verify_peer_name' => false,
//                 'allow_self_signed' => true
//             )
//         );

//         $mail->setFrom('legodie009@gmail.com', 'Jorge luis');
//         $mail->addAddress($email);

//         // Aquí asegúrate que $pdo esté definido en config.php o funciones
//         $token_data = insertar_token($pdo, $email, $idUsuario);
//         $token_seguro = $token_data['token'];

//         $mail->isHTML(true);
//         $mail->Subject = 'Codigo de seguridad';
//         $mail->Body    = "Tu código de recuperación es: <b>$token_seguro</b>. Expira en 15 minutos.";
//         $mail->AltBody = "Tu código de recuperación es: $token_seguro";

//         // $mail->send();


//             //si se cae el internet
//         // En enviar_correo.php
//         $intentos = 0;
//         $enviado = false;

//         while (!$enviado && $intentos < 2) { // Intenta máximo 2 veces
//             try {
//                 $mail->send();
//                 $enviado = true;
//             } catch (Exception $e) {
//                 $intentos++;
//                 if ($intentos >= 2) throw new Exception($mail->ErrorInfo);
//                 sleep(2); // Espera 2 segundos antes de reintentar
//             }
//         }

//         // Limpiar buffer y enviar éxito
//         ob_end_clean();
//         echo json_encode(["status" => "success", "message" => "Correo enviado"]);
//     }

// } catch (Exception $e) {
//     ob_end_clean(); // Limpiar cualquier cosa que se haya impreso
//     echo json_encode(["status" => "error", "message" => $e->getMessage()]);
// }
// exit;


?>
