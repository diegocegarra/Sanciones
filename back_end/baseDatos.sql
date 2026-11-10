-- PLIS LEAN ESTO PRIMERO
-- ESTA BASE DE DATOS LE FATA HOY ME VOY A PONER FUERTE CON ESTO. PERO ESTO SERIA EL SCHEMA TAREAS AL MOMENTO DE CREAR LA BASE DE DATOS
-- ESTO NO ES SU SCHEMA ES SOLO DE TAREAS

-- LA BASE DE DATOS VA A TENER LOS SIGUIENTES SCHEMAS O BASES DE DATOS

-- TAREAS       = create database tareas;

-- EMPRESAS     = create database empresas;

-- FACTURACION  = create database facturacion;

-- PERFIL       = create database perfil;

-- AUDITORIA    = create database auditoria;
 
-- ANALITICAS   = create database analiticas;
-- ESTAS SON TODAS LOS SCHEMAS QUE VA A TENER LA BASE DE DATOS SOLO COPIEN PEGUEN Y EJECUTEN
create database tareas; 
create database empresas; 
create database facturacion; 
create database usuarios; 


-- SCHEMA USUARIO

-- Estos son el sistema de creacion de roles 
use usuarios;
CREATE TABLE IF NOT EXISTS `usuarios`.`roles` (
  `id_roles` INT NOT NULL auto_increment,
  `nombre_rol` VARCHAR(15) NOT NULL UNIQUE,
  `descripcion` VARCHAR(45),
  PRIMARY KEY (`id_roles`)
  ); 
  
 CREATE TABLE IF NOT EXISTS `usuarios`.`usuario` (
  `id` INT NOT NULL auto_increment,
  `nombre` VARCHAR(45) NOT NULL,
  `cedula` INT NOT NULL UNIQUE,
  `correo` VARCHAR(45) NOT NULL,
  `nombre_usuario` VARCHAR(15) NOT NULL,
 `password` CHAR(60) NOT NULL,  -- aqui la contrasena va a estar cifrada ya que CHAR(60) cifra todas las contrasenas. 
  PRIMARY KEY (`id`),
  
  	-- esto, registra la fecha de actualizacion de actualizacino y creacion de usuario 
  `Creado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  -- aqui ve la ultima actualizacion del usuario
  `actualizado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
  );
  -- LINEA QUE FALTA PARA INICIAR SECCION

  ALTER TABLE usuario ADD COLUMN esta_logeado TINYINT(1) DEFAULT 0;


-- INSERT INTO usuario(nombre,cedula, correo, nombre_usuario, password)
   --  values('el papu',33345,'papu@gmail.com', 'papu rodriguez', 1);

CREATE TABLE IF NOT EXISTS `usuarios`.`fechas_festivas`(
	`id` INT NOT NULL auto_increment,
    `fecha` DATE unique,    
    PRIMARY KEY(`id`)
);

    
INSERT INTO roles(nombre_rol)
    values('Admin'),
		 ('supervisor'),
         ('analista'),
         ('root');

-- SCHEMA EMPRESAS
use empresas;
  CREATE TABLE IF NOT EXISTS `empresas`.`empresas` (
    `id` INT NOT NULL auto_increment,
    `numero_patronal` INT NOT NULL UNIQUE,
    `nombre_empresa` VARCHAR(45) ,
    `direccion`  VARCHAR(45),
    `estado_deuda`  VARCHAR(45),
	`industria` VARCHAR(45) ,
	`estado`  VARCHAR(45) ,
	 PRIMARY KEY (`id`)
);

   START TRANSACTION;
    INSERT INTO empresas(numero_patronal, nombre_empresa, 
						direccion, estado_deuda, industria, estado)
    values(10101, 'fritolay','cumana 123','solvente', 'farmaceutica', 'sucre');
    
    COMMIT;

    
-- SCHEMA TAREAS
use tareas;
CREATE TABLE IF NOT EXISTS `tareas`.`tareas` (
    `id` INT NOT NULL auto_increment,
    `codigo_tarea` VARCHAR(45)  DEFAULT '' UNIQUE,
    `nombre_empresa` VARCHAR(45),
    `cantidad_tarea`  INT,
    `extemporaneos`  INT,
	`incosistencias` INT,
	`trabajadores_activos`  INT,
    `estado`  VARCHAR(45),
    `finalizado_en` DATE,
    `creado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	 PRIMARY KEY (`id`)
);
  CREATE TABLE IF NOT EXISTS `usuarios`.`asignar_rol` (
  `id_asignar_rol` INT NOT NULL auto_increment, 
  
  -- asignar_usuario: aqui va agarrar el id del rol correspondiente, ejermplo si roles_usuario = 1   y previamente se asigno en roles id_roles = 1  nombre_rol=  admin,  entonces tiene asignado el rol de admin
  `asignar_usuario` INT NOT NULL,
    
    -- asignar_roles: aqui va agarrar el id del usuario correspondiente, ejermplo si usuario_roles = 1   y previamente se asigno en usuarios id_usuario = 1   entonces se sabe que es el usuario en el id 1
  `asignar_roles` INT NOT NULL,
   `asignado_roles_por`VARCHAR(15) NOT NULL,
	
    -- esto, registra la fecha de actualizacion de asignacion y creacion de rol 
   `asignado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `actualizacion_rol` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 
  PRIMARY KEY (`id_asignar_rol`),
  constraint fk_roles_permisos FOREIGN KEY (asignar_roles) REFERENCES roles(id_roles) ON DELETE CASCADE,
  -- aqui si se elimina el usuario se va eliminar esto tambien de la base de datos para no tener nada que ver 
  constraint fk_permisos_roles FOREIGN KEY (asignar_usuario) REFERENCES usuario(id) ON DELETE CASCADE
  );


  CREATE TABLE IF NOT EXISTS `tareas`.`asignar_tarea` (
  `id_asignar_permisos` INT NOT NULL auto_increment,
  `usuario_asignado` VARCHAR(45),
  `nombre_tarea`VARCHAR(45),
  PRIMARY KEY (`id_asignar_permisos`)

  );
    
    CREATE TABLE IF NOT EXISTS `tareas`.`estatus_tarea` (
    `id_estatus` INT NOT NULL auto_increment,
    `estatus_nombre` VARCHAR(45),
    `tarea_id` INT UNIQUE,
    `estatus_cambiado` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`id_estatus`)
);

 CREATE TABLE IF NOT EXISTS `tareas`.`empleados_ingresos` (
  `id` INT NOT NULL auto_increment,
   `Nombre_empresa`VARCHAR(45),
  `cedula` VARCHAR(15),
  `nombre` VARCHAR(45) ,
  `apellido`  VARCHAR(45),
   `fecha_ingreso`  VARCHAR(45),
	`movimiento_ingreso` VARCHAR(45),
   `fecha_egreso`  VARCHAR(45),
     `movimiento_egreso` VARCHAR(45),
     `actualizado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);



  CREATE TABLE IF NOT EXISTS `tareas`.`egresos_totales` (
  `id` INT NOT NULL auto_increment,
   `Nombre_empresa`VARCHAR(45),
  `cedula` VARCHAR(15),
  `nombre` VARCHAR(45) ,
  `apellido`  VARCHAR(45),
   `fecha_ingreso`  VARCHAR(45),
     `movimiento_ingreso` VARCHAR(45),
     `fecha_egreso`  VARCHAR(45),
     `movimiento_egreso` VARCHAR(45),
  PRIMARY KEY (`id`)
);

use facturacion;


  CREATE TABLE IF NOT EXISTS `facturacion`.`facturacion` (
    `id_facturacion` INT NOT NULL auto_increment,
    `monto_ut_ingresos` INT,
    `monto_bs_ingresos` INT,
    `monto_ut_egresos` INT,
    `monto_bs_egresos` INT,
    `nombre_empresa` VARCHAR(45),
    `concurrenciaSubida` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`id_facturacion`)
);
 
    
	START TRANSACTION;
    INSERT INTO tareas(codigo_tarea, nombre_empresa, 
						cantidad_tarea, extemporaneos, incosistencias, trabajadores_activos,
                        estado)
    values(334, 'fritolay', 1, 23, 34,34, 'sucre');
    COMMIT;
    

    
	
--                       AGREGADO Nuevo 20/02/2026

-- LOGS o actividades del usuario en el sistema
CREATE TABLE IF NOT EXISTS `usuarios`.`logs_sistema` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    accion VARCHAR(255) NOT NULL, -- Ej: "Inicio de Sesión", "Cierre de Sesión"
    detalle TEXT,                -- Ej: "El usuario cerró sesión correctamente"
    ip_address VARCHAR(45),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuario(id) ON DELETE SET NULL
);


-- VALORES QUE VAN EN ESTAS TABLAS 
-- SE COLOCARON PORQUE NOMBRE TAREAS es el sombre de la sancion y como es sancion 1 sancion 2
-- necesitamos buscarlo para que habra la sacion correspondiente
ALTER TABLE empleados_ingresos ADD nombre_tarea varchar(45);
ALTER TABLE egresos_totales ADD nombre_tarea varchar(45);
ALTER TABLE egresos_totales ADD `creado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Valores agregados para las sanciones extra

-- Facturacion
-- Descripcion de las sanciones extra

CREATE TABLE IF NOT EXISTS `facturacion`.`descripcion_sanciones_extra` (
  `id_descripcion_sanciones_extra` INT NOT NULL auto_increment,
   `tachaduras`VARCHAR(100),
  `obstaculizacion` VARCHAR(100),
  `novedades`  VARCHAR(100),
    `nombre_empresa`  VARCHAR(45),


  PRIMARY KEY (`id_descripcion_sanciones_extra`)
);
-- PARA PORNER LOS VALORES
CREATE TABLE IF NOT EXISTS `facturacion`.`monto_sanciones_extra` (
  `id_monto_sanciones_extra` INT NOT NULL auto_increment,
   `tachaduras`INT,
  `obstaculizacion` INT,
  `inscripcion` INT,
  `novedades`  INT,
  `intereses_no_enterados` INT,
   `nombre_empresa`  VARCHAR(45),

  PRIMARY KEY (`id_monto_sanciones_extra`)
);

-- UNICO INDEX para que no se repitan sacionex extra 
ALTER TABLE facturacion.monto_sanciones_extra ADD UNIQUE (nombre_empresa);


ALTER TABLE tareas.tareas DROP COLUMN extemporaneos;
ALTER TABLE tareas.tareas DROP COLUMN incosistencias;


-- Agrega un campo a tareas, y un FK para que cuando se elimine se elimine la facturacion tambien

-- Tabla padre = tareas
-- Tabla hijo = facturacion
ALTER TABLE facturacion.facturacion 
ADD COLUMN tarea_id INT,  
ADD CONSTRAINT fk_tarea_id      
FOREIGN KEY (tarea_id)             
REFERENCES tareas.tareas(id)              
ON DELETE CASCADE;



-- Tabla padre = facturacion
-- Tabla hijo = monto_sanciones_extra
ALTER TABLE facturacion.monto_sanciones_extra 
ADD COLUMN monto_extra_id INT,  
ADD CONSTRAINT fk_monto_extra_id      
FOREIGN KEY (monto_extra_id)             
REFERENCES facturacion.facturacion(id_facturacion)              
ON DELETE CASCADE;

-- Tabla padre = monto_sanciones_extra
-- Tabla hijo = descripcion_sanciones_extra
ALTER TABLE facturacion.descripcion_sanciones_extra 
ADD COLUMN descripcion_extra_id INT,  
ADD CONSTRAINT fk_descripcion_extra_id      
FOREIGN KEY (descripcion_extra_id)             
REFERENCES facturacion.monto_sanciones_extra(id_monto_sanciones_extra)              
ON DELETE CASCADE;                      



ALTER TABLE usuarios.usuario ADD COLUMN foto_perfil VARCHAR(255) DEFAULT NULL;
ALTER TABLE usuarios.usuario ADD COLUMN apellido VARCHAR(45) DEFAULT NULL;


--                                                            Actualizacion  25/02/2026
CREATE TABLE IF NOT EXISTS `facturacion`.`concurrida` (
  `id_concurrida` INT NOT NULL auto_increment,
  -- valor concurrencia se va a concurrir luego
  `valor_concurrencia` INT,
  `sancion_mayor`INT,
  -- esta es la mayor entre todas las individuales
  `nombre_sancion_mayor`  VARCHAR(45),
  `otras_sanciones`INT,
   `nombre_empresa`  VARCHAR(45),
   `concurrenciaAplicada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`id_concurrida`)
);


-- intereses
CREATE TABLE IF NOT EXISTS `facturacion`.`intereses` (
  `id_intereses` INT NOT NULL auto_increment,
  -- valor concurrencia se va a concurrir luego
  `cantidad_periodo` INT,
  `formato`VARCHAR(45),
  `monto_intereses_bs`INT,
   `nombre_empresa`  VARCHAR(45),
   `intereses_subidos` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`id_intereses`)
);


ALTER TABLE usuarios.usuario ADD COLUMN cargo VARCHAR(43) ;
ALTER TABLE usuarios.usuario ADD COLUMN telefono VARCHAR(43) ;

-- AGREGADO             27/02/2026
ALTER TABLE usuarios.usuario ADD COLUMN rol_id INT DEFAULT 3;






-- ultimo
CREATE TABLE IF NOT EXISTS `facturacion`.`registrar_pago` (
  `id_pago` INT NOT NULL auto_increment,
  -- valor en bs o ut
   `valor` VARCHAR(45),
  `monto_pago` VARCHAR(45),
  `numero_referencia` INT,
  `banco`VARCHAR(45),
   `nombre_empresa`  VARCHAR(45),
  `fecha_pago`  DATETIME,
   `registrado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`id_pago`)
);

-- AGREGADA el 8/3/2026

CREATE TABLE IF NOT EXISTS usuarios.recuperacion_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    token VARCHAR(64) NOT NULL,
    token_expiracion DATETIME NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    -- Esto asegura que si borras al usuario, sus tokens también se borren
    CONSTRAINT fk_usuario_token FOREIGN KEY (usuario_id) 
    REFERENCES usuarios.usuario(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;    


-- tabla para correos agregada 21/3/2026
CREATE TABLE usuarios.cola_correos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    destinatario VARCHAR(255) NOT NULL,
    asunto VARCHAR(255) NOT NULL,
    cuerpo TEXT NOT NULL,
    estado ENUM('pendiente', 'enviado', 'fallido') DEFAULT 'pendiente',
    intentos INT DEFAULT 0,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultima_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);