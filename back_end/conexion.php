<?php 
#                                              NOTAS IMPORTANTES:
#ESTE ES EL ARCHIVO DE CONEXION, PLIS LEAN LOS COMENTARIOS ESTE ARCHIVO ES ESENCIAL. 
# PLIS PLIS NO USEN ARCHIVOS HTML, YA QUE NO SE PUEDEN INTEGRAR PARA MOSTRAR VALORES EN LA BASE DE DATOS
# USEN SOLO SI VAN A HACER EL INICIO, inicio.php Y HAY LE METEN EL HTML ANTES DEL php
# IMPORTANTE PLIS CREEN UN ARCHIVO DE funciones_empresas y funciones_facturacion en sus carpetas src para que manejen todas las consultas alli. 


    
# COMO USAR Y COMO USAR LAS CONSULTAS EN PHP: PLIS LEAN LA GUIA PASO A PASO ES IMPORTANTE QUE LO LEAN TODO.

        // :::::::::::::::::        COMO LLAMAR ESTE ARCHIVO PARA QUE LO LEA

        // lo vamos a llamar desde nuestro archivo config donde es el directorio de todos nuestros archivos 
        // Colocamos al inicio de nuestro php:
        //          require_once(__DIR__ . '/../../config.php');
        //          require_once(CONEXION_PATH);
        // esto llama a config y la ruta de este archivo


        //::::::::::::::::::    COMO USAR ESTA FUNCION DE CONEXION
        // 1. ESTA ES LA VARIABLE QUE VAN A USAR PARA EJECUTAR CUALQUIER COMANDO PARA LA BASE DE DATOS; 
        // 2. PARA USARLA llamamos la funcion y podemos guardarla en una variable. en el archivo que queramos
        //    o solo llamar esta funcion:   
        //                       conectar_db() en el archivo que vamos a usar

        
        //:::::::::::::::::     COMO CREAR UNA FUNCION QUE LE PASE LA CONEXION?

        # 3. EJEMPLO AL USARLA EN UNA FUNCION PARA HACER ALGO EN MYSQL
                // function obtener_usuarios($pdo){
                //     $sql ="SELECT id, nombre_usuario FROM usuario";
                //     $stmt_usuario =$pdo->prepare($sql);
                //     $stmt_usuario-e>excute();
                //     $resultado= $stmt_usuario->fetchAll(PDO::FETCH_ASSOC);
                //     return $resultado;
                // }
      
        
        //:::::::::::::::::::       PASOS PARA HACER ALGO EN LA BASE DE DATOS CONSULTAS INSERT ETC
      
        // 1. crear una variable con la consulta ejemplo: $sql ="SELECT id, nombre_usuario FROM usuario";
      


        // 2. hacer un $variableConexion->prepare() : esto prepara la consulta, acuerdense que necesitamos pasar la variable donde guardamos esta funcion de conexion
        //          ejemplo de prepare :   $stmt_usuario =$pdo->prepare($sql);  ATENTOS SE GUARDA EN ESTA VARIABLE STMT_USUARIO PARA HACER EL SIGUIENTE PASO
      


        // 3. ejecutar la consulta: aqui se usa la variable del paso 2.2 y se coloca : $stmt_usuario->execute();
        //          3.1 ATENTOS, SI ESTAN HACIENDO UN INSERT O LO QUE SEA, Y EL VALOR SE LO VAN A PASAR DE OTRA PARTE, SE LO PASAN EN EL EXECUTE Y COLOCAN EL SIMBOLO ? o :nombreRandom
        //                  EJEMPLO DE LO MECIONADO CON EL EXECUTE:   
        //                      $stmt = $pdo->prepare("UPDATE tareas SET codigo_tarea = ? WHERE id = ?");
        //                      $stmt->execute([$codigo_tarea, $id_tarea]);



        // 4. Para los select, necesitamos devolver los datos, como lo hacemos?
        //         4.1 primero necesitamos la variable del prepare() y hacemos lo siguiente para todos los resultados
        //                     $resultado= $stmt_usuario->fetchAll(PDO::FETCH_ASSOC);
        //                     HAY DIFERENTE MANERAS DE RETORNAR EL RESULTADO, ESTO LO RETORNA COMO ARRAY, PERO SE PUEDE COMO OBJETO SI SE LE CAMBIA la parte de PDO:FETCH_ASSOC
    
        //          4.2 luego retornamos el resultado para usarlo o dejarlo como esta y solo llamar a la funcion ejemplo:
        //                     return $resultado;
        //                              o
        //                      $tareas = obtener_usuarios($pdo);


        
        // 5. Ahora como usarlo para mostrar este resultado?
        //          5.1 Primero tenemos que llamar a la funcion de nuestra consulta y la guardamos en una variable.
        //                  EJEMPLO:    $tareas = obtener_usuarios($pdo);

        //          5.2 y este valor lo definimos como un array cuando dijimos: fetchAll(PDO::FETCH_ASSOC);
        //          ahora seria solamente colocar: $tareas['columna de la base de datos'] 
        //                  EJEMPLO:    $tareas['id'];

        //          5.3 Ah pero esto nos va a dar un error probablemente, porque no nos va a mostrar todo si queremos ver todo. 
        //          entonces para ver todo hacemos una iteracion foreach para que recorra toda la columna de la base de datos. 
        //                  EJEMPLO:     foreach($tareas as $columnas){
        //                                          $columnas['id'];

        //          5.4 Cosas para tener en cuenta: Le pasamos fetchAll(PDO::FETCH_ASSOC) por eso usamos $columnas['id']
        //          pero si cambiamos esto a fetchAll(PDO::FETCH_OBJ) seria un objeto entonces :  $columnas->id;
        //          tambien hay una opcion si queremos un valor en especific de la columna y se cambia esto de fetchAll() y se colocaria $columnas['id'][0]  esto me da todas las columnas en el id 0
     
        //          5.5 Acuerdense que esto se lo pasamos al otro archivo que va a ser de nuestra vista o que va a tener nuestro php y html
        
        //          5.6 IMPORTANTE: van a usar combinaciones de html y php por lo tanto, al momento de ejecutar cualquier comando php, PLIS
        //              por lo que mas quieran es sumamente importante por las cholas de jesucrito que dios sabe lo mucho que camino con esas cholas. 
        //                  usar <? php > no coloque la ? al final porque este archivo se rompe
        //                          ejemplo html y php combinados : 
        //                              <td name="codigo_tarea">  <?php $tarea['codigo_tarea'] > </td>

        //          5.7 FINAL: al incluir su archivo donde van a tener todas las consultas, coloquenlo al incio plis, del archivo donde van a mostrar todo
        //                      Sin esto no van a ver nada ni hacer nada
        //                      ejemplo:
        //                              require_once(__DIR__ . '/../../../back_end/config.php');
        //                              require_once(FUNCIONES_TAREAS);






        

function conectar_db(){
    
    #AQUI VAMOS A COLOCAR LOS DATOS DE NUESTRA BASE DE DATOS QUE ESTEMOS EJECUTANDO ESTO.
        $host = 'localhost';
        $usuario = 'root';
        $acceso ='123456'; //contrasena
        $charset = 'utf8mb4';
        $dsn ="mysql:host =$host; charset=$charset";
        
        //driver:atributo1=valor1;atributo2=valor2;... estructura para el dsn (Data Source Name

        

        $options = [
            PDO::ATTR_ERRMODE=> PDO::ERRMODE_EXCEPTION, // lanzar una exepcion cada vez que ocurra un error
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,//cada fila de resultados se devolverá como un arreglo asociativo. Esto significa que puedes acceder a los datos usando los nombres de las columnas, como $row['nombre_columna']
            PDO::ATTR_EMULATE_PREPARES   => false,//ndica a PDO que no emule las sentencias preparadas, sino que las use de forma nativa. Cuando se establece en false, PDO envía la consulta y los parámetros por separado al servidor de la base de datos, lo que lo protege contra la inyección SQL.
        ];

        try {
        

        $pdo = new PDO($dsn, $usuario, $acceso, $options);
        
            //echo '<br>conexion exitosa<br>';
             return $pdo;
        } catch (\PDOException $e) {
            //getMessage(), getCode() y getFile(). Esto te permite personalizar la respuesta para cada tipo de error. 
                // getMessage muestra el codigo
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
            }
  


    
    }
