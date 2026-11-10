<?php
	//alertas_condicion
  //alertar_usuario
  //notificar_sistema
  //guardar_base_datos
  //tipos de alertas
  //crud_notificacion
  
  class alertas{
    public $notificar_usuario; 
    
    public function __construct($notificar){
       $this->notificar_usuario= $notificar;
    }
    public function guardar_alertas(){
    // guarda las alertas en la base de datos
    }
    public function alertas_especiales(){
     //aqui van a ir las alertas a los admins o a los usuarios con alertas especiales
    }
    public function subir_alertas(){
    // esto es para subir alertas manualmente como recordatorio
    }
    public function mostrar_alertas(){
    // muestra las alertas en pantallas
    }

    
  }
  
class notificador_eventos{
  
  public function sistema_notificacion(){
  // son notificaciones de errores, o del sistema
    
  }
  public function error_notificacion(){
  //notificaciones sobre errores o cosas requeridas
    
  }
  public function notificador_empresa(){
  // esta notificacion se dispara al momento de que te asignen una empresa
    
  }
  public function notificador_asignado(){
  // notificacion al asignarte una cuenta
    
  }
  public function notificador_porAsignar(){
  //notificacion especiales de admin o supervisores de empresas sin asignar
    
  }
  public function empresas_graves(){
  //asigna a las empresas que comenten faltas graves
  }
}
  
  $alertas = new alertas("Papu Notificado"); 
  echo $alertas->notificar_usuario; 
  
  
?>