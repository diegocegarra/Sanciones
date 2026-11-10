<?php

// require_once('permisos.php');
// require_once(__DIR__ . '/../../../config.php');
// require_once(CONEXION_PATH);

// echo "roles";
// $rol_seleccionado= $_POST['selecion_de_rol'];
// $usuario_seleccionado= $_POST['selecion_usuario_rol'];


//:::::::::::::::::::::::::::::::: FORMA NUEVA OPTIMIZADA
// OTRA FORMA DE HACERLO

// aqui van a estar todos los permisos que los usuarios pueden tener como eliminar editar por ejemplo
//aqui va para verificar que tiene la seccion activada if(isset($_SESSION['user'])){
    class permisos_generales{

        public $leer; 
        public $escribir; 
        public $editar; 
        public $eliminar;
        public $ocultar;
        public $asignar;
        public $eliminar_usuario;
        
        public function __construct($leer, $escribir, $editar, $eliminar,
                                    $ocultar, $asignar, $eliminar_usuario){
            $this->leer = $leer;
            $this->escribir = $escribir;
            $this->editar = $editar;
            $this->eliminar = $eliminar;
            $this->ocultar = $ocultar;
            $this->asignar = $asignar;
            $this->eliminar_usuario = $eliminar_usuario;
        }
     
        
        public function mostrar_rol(){
            echo "Leer: " . ($this->leer ? 'Sí' : 'No') . "<br>";
            echo "Escribir: " . ($this->escribir ? 'Sí' : 'No') . "<br>"; 
            echo "Eliminar: " . ($this->eliminar ? 'Sí' : 'No') . "<br>";
            echo "Editar: " . ($this->editar ? 'Sí' : 'No') . "<br>";
            echo "Ocultar: " . ($this->ocultar ? 'Sí' : 'No') . "<br>";
            echo "Asignar: " . ($this->asignar ? 'Sí' : 'No') . "<br>";
            echo "Eliminar_usuario: " . ($this->eliminar_usuario ? 'Sí' : 'No') . "<br>";
        }

       // ESTE ÚNICO MÉTODO SE ENCARGA DE LOS 7 PERMISOS NO HAY QUE DECLARAR MAS
        public function __get($propiedad) {
            if (property_exists($this, $propiedad)) {
                return $this->$propiedad;
            }
            return null; // O lanza una excepción si prefieres
        }


 }


    



// AQUI SE LE ASIGNAN LOS ROLES A LOS USUARIOS
// Clase para definir los niveles de acceso (Ya no hereda de permisos)
class RolesFactory {
    public static function obtenerPermisosPorRol($id_rol) {
        switch ($id_rol) {
            case "1": // Admin
                //                            leer  escribir editar eliminar ocultar asignar
                return new permisos_generales(true, true, true, true, true, true, true);
            case "2": // Supervisor
                return new permisos_generales(true, true, true, false, true, true, false);
            case "3": // Analista
                return new permisos_generales(true, true,false, false, false, false, false);
            case "4": // Root
                return new permisos_generales(true, true, true, true, true, true, true);
            default:
                return new permisos_generales(false, false, false, false, false, false, false);
        }
    }
}

// Clase de Usuario
class UsuarioRol {
    public $rol_id;
    public $permisos;

    public function __construct($id_rol) {
        $this->rol_id = $id_rol;
        // Llamamos al método estático sin necesidad de crear una instancia de "roles"
        $this->permisos = RolesFactory::obtenerPermisosPorRol($id_rol);
    }
}



// $pdo= conectar_db();
// // Supongamos que el ID del usuario logueado está en la sesión
// $id_usuario_actual = $_SESSION['user_id']; 

// $sql = "SELECT rol_id FROM usuarios.usuario WHERE id = :id";
// $stmt = $pdo->prepare($sql);
// $stmt->execute(['id' => $id_usuario_actual]); 
// $datos = $stmt->fetch(PDO::FETCH_ASSOC);

// // Ahora $rol tendrá los permisos de quien esté usando la página en ese momento
// return $rol = new UsuarioRol($datos['rol_id']);



// // $supervisor = new UsuarioRol("2"); // Crea un supervisor
// // $analista = new UsuarioRol("3"); // Crea un analista
// // $root = new UsuarioRol("4"); // Crea un root

// if ($rol->permisos->editar) {
//     echo "Tengo permiso para editar";
// }
