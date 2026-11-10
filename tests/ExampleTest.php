<?php
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\Attributes\PreserveGlobalState;

// 1. Solo parches de funciones (para que no den error de "undefined")
if (!function_exists('registrarLog')) {
    function registrarLog($pdo, $id, $accion, $msj) { return true; }
}

if (!function_exists('conectar_db')) {
    function conectar_db() { return $GLOBALS['pdo_mock'] ?? null; }
}
class ExampleTest extends TestCase {
    
//facturacion
    // public function test_calculo_ingreso_extemporaneo() {
    //     // 1. Preparación (Instanciamos la clase)
    //     $calculadora = new SancionesCalcular();
    //     $trabajadores = 3;

    //     // 2. Ejecución
    //     $resultado = $calculadora->calcularUTIngresos($trabajadores);

    //     // 3. Verificación (3 trab. * 5 UT * 43 Bs = 645)
    //     $this->assertEquals(645, $resultado);
    // }

    // public function test_valor_ut_por_defecto() {
    //     $calculadora = new SancionesCalcular();
    //     // Verificamos que la propiedad pública sea 43
    //     $this->assertEquals(43, $calculadora->ut);
    // }

    // public function test_importacion_retorna_estructura_correcta() {
    //     // Mock del PDO para que no intente conectar a base de datos real
    //     $pdoMock = $this->createMock(PDO::class);
        
    //     $archivoIngreso = __DIR__ . '/../fixtures/ingresos_test.xlsx';
    //     $archivoEgreso = __DIR__ . '/../fixtures/egresos_test.xlsx';
    //     $nombreTarea = "Tarea de Prueba";

    //     // Ejecutamos la función
    //     // NOTA: Asegúrate de que el archivo donde vive la función esté cargado (vía composer files o require)
    //     $resultado = ImportarDatos_ingresos($pdoMock, $archivoIngreso, $archivoEgreso, $nombreTarea);

    //     // Aquí puedes hacer asserts según lo que devuelva tu función
    //     // Si tu función llama a 'mostrarExcel', quizás necesites capturar el buffer o refactorizar
    //     $this->assertIsArray($resultado); 
    // }

/**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */


//  #[RunInSeparateProcess]
// #[PreserveGlobalState(false)]
// public function test_cambio_estatus_exitoso() {
//     // 1. Limpieza de salida
//     $this->expectOutputRegex('/.*/');

//     // 2. Simulamos la Sesión y los datos POST
//     // IMPORTANTE: Revisa que estos nombres coincidan con tu código real
//     if (session_status() === PHP_SESSION_NONE) { session_start(); }
//     $_SESSION['usuario_id'] = 1;
    
//     $_POST['estatus'] = 'Completado'; 
//     $_POST['id_estatus'] = 10;
//     // Añadimos la acción si tu archivo funciones.php la requiere
//     $_POST['accion'] = 'cambioEstatus'; 

//     // 3. Mocks de la Base de Datos
//     $stmt = $this->createMock(PDOStatement::class);
//     $stmt->method('execute')->willReturn(true);

//     $pdo = $this->createMock(PDO::class);
//     $pdo->method('prepare')->willReturn($stmt);
//     $pdo->method('beginTransaction')->willReturn(true);
//     $pdo->method('commit')->willReturn(true);
//     $pdo->method('exec')->willReturn(0);

//     // 4. Ejecución
//     ob_start();
//     try {
//         cambioEstatus($pdo);
//     } catch (\Throwable $e) {
//         // Ignoramos el exit;
//     }
//     $output = ob_get_clean();

//     // 5. Verificación (Buscamos el JSON en la salida)
//     $jsonStart = strpos($output, '{');
//     $cleanJson = ($jsonStart !== false) ? substr($output, $jsonStart) : $output;
//     $data = json_decode($cleanJson, true);

//     // Si esto falla, te dirá exactamente qué imprimió tu código
//     $this->assertIsArray($data, "No se recibió un JSON. Salida: " . $output);
//     $this->assertTrue($data['success'], "El test falló. Mensaje recibido: " . ($data['message'] ?? 'Ninguno'));


//     }



//session
#[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_login_exitoso_completo() {
        // 1. Forzar que PHPUnit no muera con exit
        if (!defined('PHPUNIT_RUNNING')) define('PHPUNIT_RUNNING', true);

        // 2. Preparar el entorno ANTES de cargar nada
        @session_start(); // Iniciamos sesión de verdad en el proceso hijo
        $_POST['nombre_usuario'] = 'admin'; 
        $_POST['password'] = '12345';

        // 3. Mock de PDO (Igual que antes, pero asegúrate de que esté en el scope global)
        $passwordHasheada = password_hash('12345', PASSWORD_DEFAULT);
        $usuarioFake = [
            'id' => 1,
            'nombre' => 'Admin Test',
            'password' => $passwordHasheada
        ];

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('fetch')->willReturn($usuarioFake);

        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willReturn($stmt);
        $pdo->method('exec')->willReturn(0);

        // Definimos la función de conexión para que devuelva el Mock y NADA MÁS
        $GLOBALS['pdo'] = $pdo; // Forzamos la variable que usa tu script
        if (!function_exists('conectar_db')) {
            function conectar_db() { return $GLOBALS['pdo']; }
        }

        // 4. Ejecución
        ob_start();
        try {
            // USAMOS UN TRUCO: Definimos las variables que el script espera 
            // por si el require de conexión falla o se salta
            $pdo = $GLOBALS['pdo']; 
            
            require __DIR__ . '/../../back_end/login/verificar_session.php'; 
        } catch (\Throwable $t) {
            // Captura errores de sintaxis o de lógica
            echo "Error interno: " . $t->getMessage();
        }
        $output = ob_get_clean();

        // 5. Verificación con el nuevo reporte
        $this->assertArrayHasKey('usuario_id', $_SESSION, 
            "La sesión sigue vacía. \n" .
            "Salida capturada: $output \n" .
            "¿Llegó al IF de password_verify?: " . (isset($_SESSION['usuario_id']) ? 'SÍ' : 'NO')
        );
    }
}
// public function testCalculoSancionDosMesesMora()
//     {
//         // 1. Datos de Entrada (Precondición)
//         $mesesMora = 2;
//         $unidadTributaria = 50.00; // Valor de ejemplo
//         $sueldoBase = 130.00;
        
//         $controlador = new SancionController();

//         // 2. Ejecutar la función real de tu sistema
//         // Supongamos que tu función se llama calcularMulta($meses, $ut, $sueldo)
//         $resultadoObtenido = $controlador->calcularMulta($mesesMora, $unidadTributaria, $sueldoBase);

//         // 3. Resultado Esperado (El cálculo matemático que DEBERÍA dar)
//         // Ejemplo ficticio: (Sueldo * meses) + (UT * 5)
//         $resultadoEsperado = ($sueldoBase * $mesesMora) + ($unidadTributaria * 5); // 260 + 250 = 510

//         // 4. Validación (Assert)
//         $this->assertEquals(
//             $resultadoEsperado, 
//             $resultadoObtenido, 
//             "Fallo en CP-05: El cálculo de la sanción no coincide con la ley del COT."
//         );
//     }



// namespace App;

// class Router {
//     private array $routes = [];

//     // Register a GET route
//     public function get(string $path, callable $handler): void {
//         $this->routes['GET'][$path] = $handler;
//     }

//     // Register a POST route
//     public function post(string $path, callable $handler): void {
//         $this->routes['POST'][$path] = $handler;
//     }

//     // Match the current request to a registered route
//     public function resolve(): void {
//         $method = $_SERVER['REQUEST_METHOD'];
//         $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

//         if (isset($this->routes[$method][$path])) {
//             call_user_func($this->routes[$method][$path]);
//         } else {
//             http_response_code(404);
//             echo "404 - Page Not Found";
//         }
//     }
// }