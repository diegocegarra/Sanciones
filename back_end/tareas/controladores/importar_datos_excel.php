<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap JS (necesario para modales) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</head>
<body>
    
</body>
</html>
<?php
error_reporting(E_ALL & ~E_DEPRECATED);
// ESTE ARCHIVO ES PARA IMPORTAR DESDE EL EXCEL HACIA LA BASE DE DATOS, 

# recuperar el achivo selecionado
require_once(__DIR__ . '/../../config.php');
require_once(FUNCIONES_TAREAS);
// cargar el composer
require_once(COMPOSER);

#SUPER DUPER UPER IMPORTANTE LO MAS IMPORTANTE DE TODODO EL SISTEMAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA

#   OPTIMIZADOOOOOOOOOOOOOOOOOOOOOOOOOOOOO EN TRABAJO

// function ImportarDatos_ingresos($pdo, $archivoExcelIngreso, $archivoExcelEgreso, $nombre_tarea) {
//     $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($archivoExcelIngreso);
//     $reader->setReadDataOnly(true);

//     // 1. Cargar datos de ambos Excels a arrays
//     $docIng = $reader->load($archivoExcelIngreso);
//     $datosIngresos = $docIng->getActiveSheet()->rangeToArray('C3:' . $docIng->getActiveSheet()->getHighestDataColumn() . $docIng->getActiveSheet()->getHighestDataRow(), NULL, FALSE, TRUE);
//     $docIng->disconnectWorksheets();

//     $docEgr = $reader->load($archivoExcelEgreso);
//     $datosEgresos = $docEgr->getActiveSheet()->rangeToArray('C3:' . $docEgr->getActiveSheet()->getHighestDataColumn() . $docEgr->getActiveSheet()->getHighestDataRow(), NULL, FALSE, TRUE);
//     $docEgr->disconnectWorksheets();

//     // 2. Crear archivo CSV temporal para la carga masiva
//     $nombreArchivo = 'import_masivo_' . time() . '.csv';
//     $rutaCsv = __DIR__ . '/temp/' . $nombreArchivo;
//     if (!file_exists(__DIR__ . '/temp')) mkdir(__DIR__ . '/temp', 0777, true);

//     $punteroCsv = fopen($rutaCsv, 'w');

//     // --- INTEGRACIÓN DE TU LÓGICA DE COMBINACIÓN ---
//     $total = min(count($datosIngresos), count($datosEgresos)); 
    
//     for ($i = 0; $i < $total; $i++) {
//         $filaIngreso = $datosIngresos[$i];
//         $filaEgreso = $datosEgresos[$i];

//         // Validar si la fila de empresa está vacía (Tu lógica)
//         if (empty($filaIngreso[0])) continue;

//         // Limpiar datos de INGRESOS usando tus funciones
//         $parametros_ingresos = [
//             $filaIngreso[0], // empresa
//             limpiarCedula($filaIngreso[1]),
//             $filaIngreso[2], // nombre
//             $filaIngreso[3], // apellido
//             convertirFecha($filaIngreso[4]),
//             convertirFecha($filaIngreso[5]),
//             isset($filaIngreso[6]) ? convertirFecha($filaIngreso[6]) : null,
//             isset($filaIngreso[7]) ? (string)$filaIngreso[7] : null, // Movimiento (texto)
//         ];

//         // Limpiar datos de EGRESOS usando tus funciones
//         $parametros_egresos = [
//             $filaEgreso[0], // empresa
//             limpiarCedula($filaEgreso[1]),
//             $filaEgreso[2], // nombre
//             $filaEgreso[3], // apellido
//             convertirFecha($filaEgreso[4]),
//             convertirFecha($filaEgreso[5]),
//             isset($filaEgreso[6]) ? convertirFecha($filaEgreso[6]) : null,
//             isset($filaEgreso[7]) ? (string)$filaEgreso[7] : null,
//         ];

//         // Combinamos ambos bloques + nombre_tarea al final (9 campos para la tabla)
//         // Nota: Si vas a insertar en una tabla que tiene 17 columnas (8 ing + 8 egr + tarea), usa array_merge.
//         // Si vas a insertar en tablas separadas, aquí decides qué guardar.
//         $filaFinalCsv = array_merge($parametros_ingresos, $parametros_egresos, [$nombre_tarea]);

//         // Escribir fila procesada al CSV
//         fputcsv($punteroCsv, $filaFinalCsv);
//     }
//     fclose($punteroCsv);

//     // 3. Previsualización y botón para el Archivo 2
//     $muestraPrevia = array_slice($datosIngresos, 0, 10); // Solo una muestra pequeña
//     mostrarExcel($muestraPrevia, $nombre_tarea);

//     echo "
//     <div style='padding:20px; background:#e3f2fd; border-radius:10px;'>
//         <p>Se han procesado " . count($datosIngresos) . " registros. ¿Deseas confirmar la carga?</p>
//         <form action='procesar_sql.php' method='POST'>
//             <input type='hidden' name='csv_temp' value='$nombreArchivo'>
//             <button type='submit' style='background:#1b5e20; color:white; padding:10px 20px; border:none; cursor:pointer;'>
//                 SÍ, CARGAR REGISTROS A MYSQL
//             </button>
//         </form>
//     </div>";
// }






#MYSQL

// <?php
// // Conexión PDO con MYSQL_ATTR_LOCAL_INFILE habilitado
// require_once 'conexion.php'; 

// if (isset($_POST['csv_temp'])) {
//     $nombreCsv = $_POST['csv_temp'];
//     $rutaCompleta = __DIR__ . '/temp/' . $nombreCsv;
    
//     // Convertir ruta para MySQL (Windows usa /)
//     $rutaMysql = str_replace('\\', '/', realpath($rutaCompleta));

//     try {
//         $pdo->exec("SET GLOBAL local_infile = 1");
        
//         // Optimizamos para 20 millones de registros
//         $pdo->exec("SET UNIQUE_CHECKS = 0;");
//         $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");

//         // El comando LOAD DATA debe coincidir con el número de columnas del CSV (17 columnas en este ejemplo)
//         $sql = "LOAD DATA LOCAL INFILE '$rutaMysql'
//                 INTO TABLE tareas.tu_tabla_combinada
//                 FIELDS TERMINATED BY ',' 
//                 ENCLOSED BY '\"'
//                 LINES TERMINATED BY '\n'
//                 (col1, col2, col3, col4, col5, col6, col7, col8, col9, col10, col11, col12, col13, col14, col15, col16, nombre_tarea)";

//         $pdo->exec($sql);

//         // Reactivar checks
//         $pdo->exec("SET UNIQUE_CHECKS = 1;");
//         $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

//         // Borrar el CSV para no ocupar espacio
//         unlink($rutaCompleta);

//         echo "<h1>Importación Exitosa</h1><p>Los datos han sido cargados.</p>";

//     } catch (Exception $e) {
//         echo "Error crítico: " . $e->getMessage();
//     }
// }

















#SUPER DUPER UPER IMPORTANTE LO MAS IMPORTANTE DE TODODO EL SISTEMAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA

// PHP SPREED SHEETS SE ROMPE AL CARGAR LOS 1002 registros, HAY QUE OPTIMIZARLO 



// VARIABLES UNICAS DE COMPOSER
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
// esto es para obtener el formato en datetime para la base de datos
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;


// CREAR UN VALOR EN LA BASE DE DATOS QUE SEA LA CANTIDAD DE TAREA Y AGREGARSELO
$nombre_tarea = $_POST['nombre_tarea'];


# IMPORTAR DATOS DE INGRESOS

if(isset($_POST['enviar_ingresos'])){
    #verificar que si existe el archivo selecionado
    if($_FILES['excel_ingresos']['size']> 0):

        #EGRESOS
        #validar que es un excel 
        if($_FILES['excel_ingresos']['type'] == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'){
            #realizar el proceso de importar datos de excel a la base de datos
           //['tmp_name'] nos devuelve el contenido del archivo selecionado
            $ArchivoContenido_egresos= $_FILES['excel_egresos']['tmp_name'];
            $ArchivoContenido_ingresos= $_FILES['excel_ingresos']['tmp_name'];

            ImportarDatos_ingresos($pdo, $ArchivoContenido_ingresos, $ArchivoContenido_egresos, $nombre_tarea);
          //  ImportarDatos_ingresos($conn, $ArchivoContenido_egresos);

            #INGRESOS
        //    if($_FILES['excel_egresos']['type'] == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'):

          //  endif;
        }else{
            echo "error, el archivo selecionado no es un excel";
        }
    else:
        echo"no existe el archivo selecionado";   
        header('location: ../../../front_end/vistas/tareas/tareas.php');     
    endif;
}

function ImportarDatos_ingresos($pdo, $archivoExcelIngreso, $archivoExcelEgreso, $nombre_tarea ){
       $DocumentoIngreso = IOFactory::load($archivoExcelIngreso); // libreria phpspreedshet cargamos el archivo excel


      #vamos a hacer uso de la hoja 1 en el archivo excel

        // :::::::::::::::::::::::INGRESOS
        // esto carga toda la hoja que estamos trabajando par funcionar dinamicamente
        $HojaExcel = $DocumentoIngreso->getActiveSheet();

        // variables agarrar la primera fila a la ultima que estaremos leyendo y guardando luego
        $ultimaFila = $HojaExcel->getHighestDataRow();
        $ultimaColumnaLetra = $HojaExcel->getHighestColumn();
        Coordinate::columnIndexFromString($ultimaColumnaLetra);

        // Si quieres empezar desde la columna C (índice 3), conviértelo a letra:
        $columnaInicioLetra = Coordinate::stringFromColumnIndex(3);
        $rangoCompleto = $columnaInicioLetra . '3:' . $ultimaColumnaLetra . $ultimaFila;

        // ahora $datos completes tiene todas las filas y columnas
        $datosIngresos = $HojaExcel ->rangeToArray(
            $rangoCompleto,
            NULL, // valor para celdas vacias
            FALSE, // calcular formulas
            TRUE // formatear datos
        );




          // :::::::::::::::::::::::EGRESOS
        $DocumentoEgreso = IOFactory::load($archivoExcelEgreso); // libreria phpspreedshet cargamos el archivo excel

        // esto carga toda la hoja que estamos trabajando par funcionar dinamicamente
        $HojaExcelEgresos = $DocumentoEgreso->getActiveSheet();

                // variables agarrar la primera fila a la ultima que estaremos leyendo y guardando luego
        $ultimaFilaEgresos = $HojaExcelEgresos->getHighestDataRow();
        $ultimaColumnaLetraEgresos = $HojaExcelEgresos->getHighestColumn();
        Coordinate::columnIndexFromString($ultimaColumnaLetraEgresos);

        // Si quieres empezar desde la columna C (índice 3), conviértelo a letra:
        $columnaInicioLetraEgresos = Coordinate::stringFromColumnIndex(3);
        $rangoCompletoEgresos = $columnaInicioLetraEgresos . '3:' . $ultimaColumnaLetraEgresos . $ultimaFilaEgresos;

        // ahora $datos completes tiene todas las filas y columnas
        $datosEgresos = $HojaExcelEgresos ->rangeToArray(
            $rangoCompletoEgresos,
            NULL, // valor para celdas vacias
            FALSE, // calcular formulas
            TRUE // formatear datos
        );







        //  asigna ambos archivos a un array para ser usados luego 
        $datosCombinados = [
            'ingresos' => $datosIngresos,
            'egresos' => $datosEgresos
        ];

        // aqui combinamos la hoja de excel de ingresos con la egresos. en 1 sola, llamando a la funcion combinar datos
         $datosFinales = combinarDatos($datosCombinados['ingresos'], $datosCombinados['egresos']);

            // print_r($datosFinales);
            // muestra una previsualizacion de la tabla a insertarse antes de insertarla
            mostrarExcel($datosFinales, $nombre_tarea);

            foreach ($datosFinales as $registro) {
                //ESTO LLAMA PARA INSERTAR LOS REGISTROS DE 
                //::::::::::::::::::: IMPORTANTISIMO >>>>>>>>>>>>>>>>>>>>>>>>>
                // insertarTrabajadores($pdo, $registro);
            }
              

}


function combinarDatos($datosIngresos, $datosEgresos) {
    $combinados = [];

    $total = min(count($datosIngresos), count($datosEgresos)); // sincroniza por índice
    
    for ($i = 0; $i < $total; $i++) {
        $filaIngreso = $datosIngresos[$i];
        $filaEgreso = $datosEgresos[$i];
        


        // Si el primer elemento (Empresa) es NULL o está vacío, saltamos la fila.
        if (is_null($filaIngreso[0]) || $filaIngreso[0] === '') {
             continue; // Saltar a la siguiente iteración
        }

        


        // valores del excel DEL EXCEL

        $parametros_ingresos = [
            $filaIngreso[0], // empresa
            limpiarCedula($filaIngreso[1]),// cedula
            $filaIngreso[2], // nombre
            $filaIngreso[3], // apellido
            convertirFecha($filaIngreso[4]),// fecha ingreso
            convertirFecha($filaIngreso[5]),// movimiento igreso
            isset($filaIngreso[6]) ? convertirFecha($filaIngreso[6]) : null, // fecha egreso
            isset($filaIngreso[7]) ? convertirFecha($filaIngreso[7]) : null, // fecha egreso

        ];
    
      
        $parametros_egresos = [
            $filaEgreso[0], // empresa
            limpiarCedula($filaEgreso[1]),// cedula
            $filaEgreso[2], // nombre
            $filaEgreso[3], // apellido
            convertirFecha($filaEgreso[4]),// fecha engreso
            convertirFecha($filaEgreso[5]),// movimiento 
            isset($filaEgreso[6]) ? convertirFecha($filaEgreso[6]) : null, // fecha egreso
            isset($filaEgreso[7]) ? convertirFecha($filaEgreso[7]) : null, // fecha egreso
        ];

       // print_r($parametros_ingresos);
  
        // aqui se combina los datos de ingresos y los datos de egresos
        $combinados[] = array_merge($parametros_ingresos, $parametros_egresos);
    }

    return $combinados;
}

function mostrarExcel($datosFinales, $nombre_tarea){

    // Estructura del modal Bootstrap
    echo "


<body> 
    <!-- Modal -->
    <div class='modal fade' id='tablaModal' tabindex='-1' aria-labelledby='tablaModalLabel' aria-hidden='true'>
      <div class='modal-dialog modal-xl'>
        <div class='modal-content'>
          <div class='modal-header'>
            <h5 class='modal-title fs-3' id='tablaModalLabel'>Sancion  # $nombre_tarea </h5>
            
            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Cerrar'></button>
          
        </div>
          <div class='modal-body' >

         
            <table class='table '  style='border-collapse: separate; border-spacing: 0 ;'>
              <thead>
                <tr >
                  <th class= 'text-muted'>Empresa</th>
                  <th class= 'text-muted'>Cédula</th>
                  <th class= 'text-muted'>Nombre y Apellido</th>
                  <th class= 'text-muted'>Fecha Ingreso</th>
                  <th class= 'text-muted'>Movimiento Ingreso</th>
                  <th class= 'text-muted'>Fecha Egreso</th>
                  <th class= 'text-muted'>Movimiento Egreso</th>
                  <th class= 'text-muted'>Tipo</th>


                </tr>
              </thead>
              <tbody>";
    
    // Generar filas
    foreach ($datosFinales as $registro) {
// Desestructuramos las 12 columnas en variables descriptivas
    [
        $empresaIng, $cedulaIng, $nombreIng, $apellidoIng, $fechaIng, $movimientoIng, $fechaEgr, $movimientoEgr,// Datos del primer array
        $empresaEgr, $cedulaEgr, $nombreEgr, $apellidoEgr, $fechaIng, $movimientoIng, $fechaEgr, $movimientoEgr  // Datos del segundo array
    ] = $registro;

    echo "<tr class='shadow-sm'>
            <td>$empresaIng</td>
            <td>$cedulaIng</td>
            <td><strong>$nombreIng $apellidoIng</strong></td>
            <td class=''>$fechaIng</td>
            <td class=''>$movimientoIng</td>
            <td class=''>$fechaEgr</td>
            <td class=''>$movimientoEgr</td>
            <td class= 'badge bg-primary-subtle text-primary border-0 '> Ingresos</td>


    </tr>";



    }

     foreach ($datosFinales as $registro) {
// Desestructuramos las 12 columnas en variables descriptivas
    [
          $empresaIng, $cedulaIng, $nombreIng, $apellidoIng, $fechaIng, $movimientoIng, $fechaEgr, $movimientoEgr,// Datos del primer array
        $empresaEgr, $cedulaEgr, $nombreEgr, $apellidoEgr, $fechaIng, $movimientoIng, $fechaEgr, $movimientoEgr  // Datos del segundo array
    ] = $registro;

        echo "<tr class='shadow-sm'>
            <td>$empresaEgr</td>
            <td>$cedulaEgr</td>
            <td><strong>$nombreEgr $apellidoEgr</strong></td>
            <td class=''>$fechaIng</td>
            <td class=''>$movimientoIng</td>
            <td class=''>$fechaEgr</td>
            <td class=''>$movimientoEgr</td>
            <td class= 'badge bg-primary-subtle text-primary border-0 '> Egresos</td>

    </tr>";

    }

    echo "
  </tbody>
            </table>
            
            <form action= '../src/funciones.php' method='post'>
                <input type='hidden' name='importar_excel' value='" . htmlspecialchars(json_encode($datosFinales)) . "'>
                <input type='hidden' name ='cantidad_tarea' value=' $nombre_tarea '>
                <div class='d-flex justify-content-end'>
                    <button type='submit' class='btn btn-success me-2' name='insertar'>Insertar Empleados</button>
                </div>
      
             </form>

          </div>
        </div>
      </div>
    </div>



    <script>
        
        var myModalEl = document.getElementById('tablaModal');
        var myModal = new bootstrap.Modal(myModalEl);

        // Mostrar modal automáticamente al abrir la pantalla
        myModal.show();

        // Escuchar el evento en el elemento DOM
        myModalEl.addEventListener('hidden.bs.modal', function () {
            // Usa ruta absoluta desde la raíz del servidor
            window.location.href = '/IVSS/front_end/vistas/tareas/tareas.php';
        });

    </script>
 </body>   ";
    
}


// ESTA FUNCION SE ENCARGA DE CONVERTIR EL VALOR STRING DE LA Fecha  en un valor feca
function convertirFecha($valor) {
    if (empty($valor)) return null;

    // 1. Normalizamos: convertimos guiones en barras para que el formato sea consistente
    $valor = str_replace('-', '/', trim($valor));

    // 2. Intentamos primero con año de 2 dígitos (ej: 27/06/22)
    // El formato 'y' minúscula detecta que 22 es 2022
    $fecha = DateTime::createFromFormat('d/m/y', $valor);

    // 3. Si falla, intentamos con año de 4 dígitos (ej: 27/06/2022)
    if (!$fecha) {
        $fecha = DateTime::createFromFormat('d/m/Y', $valor);
    }

    // 4. Si sigue fallando, dejamos que PHP intente procesarlo libremente
    if (!$fecha) {
        try {
            $fecha = new DateTime($valor);
        } catch (Exception $e) {
            return null;
        }
    }

    // 5. VALIDACIÓN CRÍTICA: Si el año resultó ser 0022, lo subimos a 2022
    // Esto corrige el error de interpretación de fechas antiguas
    if ($fecha && (int)$fecha->format('Y') < 100) {
        $fecha->modify('+2000 years');
    }

    // Retornamos en formato SQL estándar (Año-Mes-Día)
    return $fecha ? $fecha->format('Y-m-d') : null;
}



// esta funcion limpia la cedula de los valores por defecto que vienen el el excel
function limpiarCedula($valor) {
        // 1. Limpieza del valor de la cédula para obtener solo el número.
        $cedula_limpia = str_replace(['V-', 'E-'], '', trim((string)$valor));
        
        // 2. Variable para almacenar el ID Único.
        $id_unico = '';

        // 3. Evaluar el prefijo para añadir el identificador de país
        if (substr($valor, 0, 2) === 'V-') {
            // Asignar el código ISO de Venezuela
            $id_unico = 'VE' . $cedula_limpia;

        } elseif (substr($valor, 0, 2) === 'E-') {
            
            // Identificador para Extranjero. Es ideal tener el país real (p. ej., 'CO' para Colombia), 
            // pero si solo tienes 'E', usa un prefijo global.
            // aqui asigna EXT + cedula + 2  el numero 2 es para que si existe una cedula extranjera igual que una venezolana, no la elimine pensando que es un duplicado
            $id_unico = 'EXT' . $cedula_limpia. 2; 
            
            // El código de 'E' ya implica que se trata de un extranjero, por lo que podrías
            // simplemente usar:
            // $id_unico = 'E' . $cedula_limpia;
            
        } else {
            // En caso de que no tenga prefijo o sea NULL (manejo de errores)
            $id_unico = 'NA' . $cedula_limpia; 
        }
        
        // Opcional: imprimir el resultado para verificar
       // echo "Cédula: " . $valor . " -> ID Único: " . $id_unico . "<br>";
        
        // Si necesitas que la función devuelva el ID Único:
        return $id_unico;
      
        
         // 2. LIMPIEZA DE DATOS: Usar str_replace para eliminar la 'V' o E-
        // Los argumentos son: (lo que quieres reemplazar, por qué lo reemplazas, dónde lo buscas)
        //$fila[1] = intval(str_replace('V-', '', $fila[1]));
}

?>


