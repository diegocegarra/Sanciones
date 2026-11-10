<?php 
require_once(__DIR__ . '/../../../config.php');
require_once(FUNCIONES_TAREAS);
require_once(ROLES_PATH);



$pdo = conectar_db();
$usuarios = obtener_usuarios($pdo); // aqui esta variable va a optener de el archivo function, para cargar los usuarios disponibles en la base de datos. 

//$usuario_selecionado = $_POST['selecion_usuario'];
// 2. Capturar el ID del formulario POST

$usuario_selecionado = $_POST['selecion_usuario'];

// 3. Buscar el nombre de usuario
$nombre_usuario = 'No encontrado'; // Valor por defecto


class asignacion extends RolesFactory{
    //valores que van a ser usados para el constructor
    protected $rol_usuario;
    private $id_usuario;
    public $usuario;
    public $creador_tarea;
    public DateTime $fecha_inicio_tarea;
    protected $nombre_usuario;
    private $usuarios_buscar;

    //valores de reasignar tarea
    public $usuario_reasignador = 'marco_antonio';
    public $trabajador_reasignado = 'guerrero chambas';
   // public DateTime $fecha_reasignacion_tarea;



    public function __construct( $id_usuario, $usuario_asignados= null , $creador_tarea= null, $usuario_admin= null, 
                                $usuario_supervisor = null ){
                                    
            //evalua si el usuario es admin o supervisor, si tiene los permisos para asignar
            if($this->rol_usuario == $usuario_admin || $this->rol_usuario == $usuario_supervisor ){
        
                // en id_usuario se coloca el usuario que se selecciona para asignar. 
                $this->id_usuario = $id_usuario; 

                // aqui se cargan los usuarios en la base de dato y se pasa como constructor
                $this->usuarios_buscar = $usuario_asignados; 

                // aqui se coloca la persona que esta asignando la tarea actualmente, la persona que tiene la seccion abierta
                $this->creador_tarea = $creador_tarea; 
               
                // va a la funcion privada de buscar nombre usuario, para asignar el nombre a la tarea
                $this-> buscarNombreUsuario();
           

                // asigna la zona horia al momento de asignar la tarea
                $timezone = new DateTimeZone('America/Caracas');

                // fecha de creacion de la tarea
                $this->fecha_inicio_tarea= new DateTime('now', $timezone); 

                
                

       
        }else{
            echo "<br >No tienes los permisos para asignar esta tarea<br>";
            return false; 
        }
         
    }

    // esta funcion se encarga de buscar el nombre del usuario en la base de datos, ya que al capturar el post proveniente de vista_roles
    // se asigna en $escoger como $escoger = id del usuario, y muestra el id del usuaro en asignado a: en vez vez del nombre
    private function buscarNombreUsuario() {
      
        // se crea una nueva variable para buscar la tarea
        foreach ($this->usuarios_buscar as $asignar) {
            if ($asignar['id'] == $this->id_usuario) {
                $this->nombre_usuario = $asignar['nombre_usuario'];
                return; // Sal del bucle una vez encontrado
            }
        }
        $this->nombre_usuario = 'Usuario no encontrado'; // Si no se encuentra
    }

    // esta funcion evalua si el nombre de usuario coincide con el nombre que el usuario ingreso, y basado en esto, busca
    public function buscarIdUsuario() {
      
        // se crea una nueva variable para buscar la tarea
        foreach ($this->usuarios_buscar as $asignar) {
            if ($asignar['nombre_usuario'] == $this->nombre_usuario) {
                $this->id_usuario = $asignar['id'];
                return; // Sal del bucle una vez encontrado
            }
        }
        $this->nombre_usuario = 'Usuario no encontrado'; // Si no se encuentra
    }


/// aqui se encarga de mostrar todos los valores de los usuarios en al pantalla
    public function mostrar_asignacion(){
        echo "<br>id de asignacion: {$this->id_usuario } <br>";
        echo "Asignado a: {$this->nombre_usuario } <br>";
        echo "Asignado por: {$this->creador_tarea}<br>";
        echo "fecha de asignacion: {$this->fecha_inicio_tarea -> format('d-m-Y')} <br>";
        echo "Hora de asignacion: {$this->fecha_inicio_tarea -> format('H:i:s')} <br>";
    }

}
// asignar valores y imprimir en pantalla

// $usuario_selecionado ='marco_antonio';
$creador_asignado= "don brasas";
$admin_selecionado= 'marco_antonio';
$supervisor_selecionado= null;



//           new asignacion($id_usuario, $usuario_asignados, $creador_tarea, $usuario_admin, $usuario_supervisor )
$asignacion= new asignacion($usuario_selecionado, $usuarios, $creador_asignado, $admin_selecionado, 
                            $supervisor_selecionado);



$asignacion->mostrar_asignacion();

// asignar roles
echo "<br> rol selecionado ".$rol_seleccionado."<br>"; // muestra la id del rol

$rol_seleccionado= $_POST['selecion_de_rol'];
$usuario_seleccionado_rol= $_POST['selecion_usuario_rol'];

echo "<br> usuario_seleccionado ".$usuario_seleccionado_rol."<br>";


// asigna el rol al usuario, primero agarra el id del rol pasado en el $_POST['']  usa el () use() para usar el $_POST['']  luego llama 
// luego llama a la clase usuarioRol para construir el rol,  y verifica el rol con $rol-> para pasarselo a funciones.php y usuar la variable de $asignar rol
$asignar_rol = function() use ($rol_seleccionado){
    $asignacion_roles = new usuarioRol($rol_seleccionado);
    return $asignacion_roles->rol_id;
};

//() use = permite a la funcion agarrar variable externas
// Asigna los usuarios y le pasa la variable $asignar_usuarios a functiones.php es lo mismo que $asignar_roles, pero al final le pasa la funcion de la clase asignacion, para
// verificar que el nombre de usuario que se le paso es el mismo que esta en la base de datos. 
$asignar_usuarios = function () use ($usuario_seleccionado_rol, $usuarios) {
    $asignacion_usuario_rol= new asignacion($usuario_seleccionado_rol, $usuarios);
    return $asignacion_usuario_rol->buscarIdUsuario();
};
//  $asignado_por= usuario_actual($pdo);
  $asignado_por= "senor pedro";

 insertar_roles($pdo, $rol_seleccionado, $usuario_seleccionado_rol, $asignado_por );
    //$asignacion_roles->permisos->mostrar_rol();

     header("Location: /IVSS/front_end/vistas/perfil_usuario/configuracion.php");

?>