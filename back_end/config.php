<?php
#ESTE ARCHIVO VA A SER EL DIRECTORIO DONDE TODOS LOS ACHIVOS VAN LLAMARSE ENTRE SI PARA USAR OTROS ARCHIVOS INCLUYENDO LA CONEXION. 
# Ejemplo necesito una funcion de hacer arepas() pero esta en funciones_tareas.php y estoy en tareas.php
# llamo entonces a este archivo de config primero:
//                              require_once(__DIR__ . '/../../../back_end/config.php');

# Y luego llamo el alias como lo coloque aqui
//                              require_once(FUNCIONES_TAREAS);




//::::::::::::::GENERALES TODOS VAMOS A USAR ESTO JEAN CHRISTOPHER DIEGO

// Directorio base del back_end
    define('BASE_PATH', __DIR__);
    // Rutas de módulos
    define('CONEXION_PATH', BASE_PATH . '/conexion.php');
    define('ERRORES_PATH', BASE_PATH . '/errores.php');


    // EL COMPOSER ES DONDE SE CARGA PARA INSERTAR EL EXCEL Y MANEJAR EL EXCEL EN LA BASE DE DATOS,
    // christopher creo que lo vas a necesitar porque ellos cargan el excel de las facturas, de todas maneras me avisas 
    //      puedes ver tambien el archivo en la carpeta:
    //                   tareas/ controladores/ importar_datos.php         
    //      para que veas como se usa el excel con composer
    
    // autoLoad composer
    define('COMPOSER', BASE_PATH . '/../vendor/autoload.php');
        

//:::::::::::: TAREAS DIEGO
    // rutas funciones
    define('FUNCIONES_TAREAS', BASE_PATH . '/tareas/src/funciones.php');
    define('FUNCIONES_ROLES', BASE_PATH . '/tareas/src/funciones_roles.php');


    define('ROLES_PATH', BASE_PATH . '/tareas/src/roles/roles.php');
    // define('PERMISOS_PATH', BASE_PATH . '/tareas/src/roles/permisos.php');

    // controladores
    define('EVALUAR_TRABAJADORES', BASE_PATH . '/tareas/controladores/evaluar_trabajadores.php');

    // VISTAS
    define('TAREAS', BASE_PATH . '/IVSS/front_end/vistas/tareas/tareas.php');


// ::::::::::::::::::::: EMPRESAS JEAN
// AQUI VAN LOS LLAMADOS DE TUS ARCHIVOS JEAN


//:::::::::::::::::::::: FACTURACION 
//CONTROLADORES
    define('CONCURRENCIA', BASE_PATH . '/facturacion/controladores/concurrir.php');
        define('FUNCIONES_FACTURACION', BASE_PATH . '/facturacion/src/funciones_facturacion.php');

                define('ORDEN_PAGO', BASE_PATH . '/facturacion/src/orden_pago.php');

// SESSION USUARIO

    define('SESSION', BASE_PATH . '/login/function_usuario.php');
// FPDF

// LOGS O ACTIVIDADES EN EL SISTEMA
  define('LOGS', BASE_PATH . '/logs/actividades.php');
  define('FUNCIONES_LOGS', BASE_PATH . '/logs/funcion_log.php');


  // ANALITICAS
      define('ANALITICAS', BASE_PATH . '/usuarios/analiticas.php');


//::::::::::::VISTAS

    define('BARRA_LATERAL', BASE_PATH . '/../front_end/vistas/barra_lateral/barraLateral.php');


// RECUPERAR CONTRASENIA

        // funcion para enviar los correos
    define('FUNCION_USUARIO_CORREO', BASE_PATH . '/usuarios/src/funcion_usuario_correo.php');

      

?>


