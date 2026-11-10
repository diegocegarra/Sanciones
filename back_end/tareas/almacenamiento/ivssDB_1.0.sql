create database usuarios;
-- drop database ivss;
-- 											SISTEMA DE ROLES Y USUARIO
select * from usuarios.usuario;
usuario-- Estos son el sistema de creacion de roles 
CREATE TABLE IF NOT EXISTS `ivss`.`roles` (
  `id_roles` INT NOT NULL auto_increment,
  `nombre_rol` VARCHAR(15) NOT NULL UNIQUE,
  `descripcion` VARCHAR(45),
  PRIMARY KEY (`id_roles`)
  );
CREATE TABLE IF NOT EXISTS `ivss`.`permisos` (
  `id_permisos` INT NOT NULL auto_increment,
  `nombre_permiso`  VARCHAR(16) NOT NULL,
  `modulo`  VARCHAR(45) ,
  PRIMARY KEY (`id_permisos`)

 ) ENGINE=InnoDB  -- Especifica el motor InnoDB (el predeterminado y necesario)
-- aqui en nombre_permiso me estaba dando error al colocar VARCHAR(16) y me salia VARCHAR(15) con esto se soluciona eso y no limita el espacio del varchar
ROW_FORMAT=DYNAMIC;
 
  select * from usuario;
 CREATE TABLE IF NOT EXISTS `ivss`.`usuario` (
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
ALTER TABLE usuario ADD COLUMN esta_logeado TINYINT(1) DEFAULT 0;
-- SELECT * FROM asignar_rol; 

-- esta tabla se llena una vez se cree un usuario y se le asigne un rol. 
asignar_tarea  CREATE TABLE IF NOT EXISTS `usuarios`.`asignar_rol` (
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

 
-- aqui en esta tabla se asigna los permiso a un usuario una vez que la tabla roles_usuarios, le asigne un rol al usuario.   
CREATE TABLE IF NOT EXISTS `usuarios`.`asignar_permisos` (
  `id_asignar_permisos` INT NOT NULL auto_increment, -- llave primaria

  -- aqui verifica el usuario asignado previamente en la tabla asignar_rol, ejemplo si el id_roles_permisos= 1, entonces usuario_asignado= 1
  `usuario_asignado` INT NOT NULL,
 
 -- aqui ve los tipos de permisos que un usuario puede tener, ejemplo si lector = la id 1, editor= la id 2, entonces se colocarian en usuario_permisos= 1  y usuario_permisos= 2
  -- ya que multiple usuarios pueden tener multiples permisos, en usuario asignado no cambia nada y se colocaria 
  -- usuario_asignado= 1 permisos_asignado = 1    lector
  -- usuario_asignado= 1 permisos_asignado = 2    editor
  `permisos_asignado` INT NOT NULL,
  PRIMARY KEY (`id_asignar_permisos`),
 
 -- llaves foraneas, el delete on cascade es para que se elimine la tabla hija, o si la tabla padre se elimina, se elimina la tabla padre y la hija
  constraint fk_roles_asignado FOREIGN KEY (usuario_asignado) REFERENCES asignar_rol(id_asignar_rol) ON DELETE CASCADE,
  constraint fk_usuario_rol FOREIGN KEY (permisos_asignado) REFERENCES permisos(id_permisos) 
  );

-- insertar permisos en la base de datos, los permisos van a ser
-- id_permisos= 1 leer
-- id_permisos= 2 editar
-- id_permisos= 3 eliminar
-- id_permisos= 4 escribir
-- id_permisos= 5 eliminar usuario
	INSERT INTO permisos(nombre_permiso)
    values('leer'),
		  ('editar'),
		 ('eliminar'),
         ('escribir'),
         ('eliminar usuario');

-- SELECT * FROM asignar_rol;
-- DELETE FROM asignar_rol WHERE asignar_usuario= 2 ;


-- insertar roles en la base de daos	
-- id_roles= 1  admin
-- id_roles= 2 supervisor
-- id_roles= 3 analista
-- id_roles= 4 root
	INSERT INTO roles(nombre_rol)
    values('Admin'),
		 ('supervisor'),
         ('analista'),
         ('root');

-- 								PRUEBA PARA COLOCAR USUARIOS en la tabla de asignaciones
	INSERT INTO usuario(nombre,cedula, correo, nombre_usuario, password)
    values('el papu',33345,'papu@gmail.com', 'papu rodriguez', 1);
    
    INSERT INTO asignar_rol(asignar_usuario, asignar_roles,fecha_asignacion, asignado_roles_por)
    values(1, 1,'2026-03-15 10:00:00', 'juan64');
    
	INSERT INTO roles_permisos(roles_permisos, usuario_permisos)
    values(1, 1);

create database tareas;
use tareas;

 CREATE TABLE IF NOT EXISTS `ivss`.`empleados_ingresos` (
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

Insert into empleados_ingresos(nombre_empresa, cedula, nombre, apellido, fecha_ingreso, movimiento_ingreso, fecha_egreso)
values('fritolay', '222222', 'papu', 'reyes', 07-11-2024, 12-12-2024, 12-12-2024);
--  CREATE TABLE IF NOT EXISTS `ivss`.`empleados_egresos` (
--   `id` INT NOT NULL auto_increment,
--    `Nombre_empresa`VARCHAR(45) NOT NULL,
--   `cedula`INT NOT NULL,
--   `nombre` VARCHAR(45) NOT NULL,
--   `apellido`  VARCHAR(45) NOT NULL,
--    `fecha_egreso`  VARCHAR(45),
--      `movimiento_egreso` VARCHAR(45),
--   PRIMARY KEY (`id`)
-- );


  

CREATE TABLE IF NOT EXISTS `tareas`.`fechas_festivas`(
	`id` INT NOT NULL auto_increment,
    `fecha` DATE unique,    
    PRIMARY KEY(`id`)
);
-- use usuarios;
-- ESTO ES PARA CONSULTAS TIPO PARA TESTEAR NO EJECUTEN ESTO
-- DROP TABLE fechas_festivas; 
-- SELECT * FROM usuario; 
-- SELECT * FROM empleados_ingresos; 
-- SELECT * FROM egresos_totales; 
-- SELECT * FROM tareas; 
-- SELECT * FROM empleados_ingresos;
-- DESCRIBE fechas_festivas; 
-- DROP TABLE trabajadores_duplicados; 
-- DROP TABLE egresos_totales; 
-- DROP TABLE empleados_ingresos; 

-- DROP TABLE trabajadores_activos; 
-- DROP TABLE trabajadores_duplicados; 


  CREATE TABLE IF NOT EXISTS `tareas`.`empresas` (
    `id` INT NOT NULL auto_increment,
    `numero_patronal` INT NOT NULL UNIQUE,
    `nombre_empresa` VARCHAR(45) ,
    `direccion`  VARCHAR(45),
    `estado_deuda`  VARCHAR(45),
	`industria` VARCHAR(45) UNIQUE,
	`estado`  VARCHAR(45) UNIQUE,
	 PRIMARY KEY (`id`)
);

    START TRANSACTION;
    INSERT INTO empresas(numero_patronal, nombre_empresa, 
						direccion, estado_deuda, industria, estado)
    values(10101, 'fritolay','cumana 123','solvente', 'farmaceutica', 'sucre');
    
    COMMIT;
    use tareas;
    SELECT * FROM egresos_totales;
    
    
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
ALTER TABLE tareas MODIFY cantidad_tarea INT;
#DROP TABLE tareas; 

-- esto es para optizar las consulta de tareas
-- PARTITION BY RANGE (YEAR(creado_en)) (
--     PARTITION p2024 VALUES LESS THAN (2025),
--     PARTITION p2025 VALUES LESS THAN (2026),
--     PARTITION pmax VALUES LESS THAN MAXVALUE
-- );



    START TRANSACTION;
    INSERT INTO tareas(codigo_tarea, nombre_empresa, 
						cantidad_tarea, extemporaneos, incosistencias, trabajadores_activos,
                        estado)
    values(334, 'fritolay', 1, 23, 34,34, 'sucre');
    
    COMMIT;
    
    
    CREATE TABLE IF NOT EXISTS `tareas`.`estatus_tarea` (
    `id_estatus` INT NOT NULL auto_increment,
    `estatus_nombre` VARCHAR(45),
    `tarea_id` INT UNIQUE,
    `estatus_cambiado` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`id_estatus`)
);

INSERT INTO estatus_tarea(estatus_nombre, tarea_id) values('procesando', 1);
 -- SELECT * FROM estatus_tarea; 
#DROP TABLE tareas

create database facturacion;

-- FACTURACION
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


select * from facturacion.monto_sanciones_extra;


ALTER TABLE usuarios.usuario ADD COLUMN foto_perfil VARCHAR(255) DEFAULT NULL;
ALTER TABLE usuarios.usuario ADD COLUMN apellido VARCHAR(45) DEFAULT NULL;
ALTER TABLE usuarios.usuario ADD COLUMN rol_id INT DEFAULT 3;

select * from usuarios.usuario;

-- intereses
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

SELECT * FROM facturacion.registrar_pago;

CREATE TABLE IF NOT EXISTS `facturacion`.`orden_pago` (
  `id_pago` INT NOT NULL auto_increment,
  -- valor concurrencia se va a concurrir luego
  `valor` VARCHAR(45),
  `monto_pago` VARCHAR(45),
  `numero_referencia` INT,
  `banco`VARCHAR(45),
  `fecha_pago`INT,
   `nombre_empresa`  VARCHAR(45),
   `fecha_pago` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`id_pago`)
);

