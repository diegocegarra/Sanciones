
  //DROPDOWN OTRAS SANCIONES
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.ind-checkbox');
    const labelText = document.getElementById('selectedCountText');
    

    // EVENT LISTENER PARA LOS CHECK BOX
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // 1. Obtener todos los seleccionados
            const checked = document.querySelectorAll('.ind-checkbox:checked');
            const count = checked.length;
        // 2. Actualizar el texto del botón
            labelText.textContent = count > 0 ? `${count} Selecionadas` : 'Seleciona una opcion';
            // 3. Aplicar fondo suave a la fila (como la imagen)
            const parentItem = this.closest('.dropdown-item');
            
            if (this.checked) {
                
                parentItem.classList.add('selected-bg');

                
            } else {
                parentItem.classList.remove('selected-bg');
            }
        });
    });
});

// variable globar que va a tener todos los datos,
const sancionesActivas = {}; // Central de referencias para evitar conflictos





    const tachaduras = document.getElementById('tachaduras'); 
    const inscripcion = document.getElementById('inscripcion'); 
    const obstaculizacion =  document.getElementById('obstaculizacion'); 
    const novedades =  document.getElementById('novedades'); 
    const noEnterados =  document.getElementById('noEnterados'); 


//configurarSancion(checkbox, key, titulo, selectorContenedor)

// Tachaduras (Tiene lógica especial de textarea dentro de la función)
configurarSancion(tachaduras, 'tachaduras', 'Tachaduras', '.tachaduras');

// Obstaculización
configurarSancion(obstaculizacion, 'obstaculizacion', 'Obstaculización', '.obstaculizacion');

// Inscripción
configurarSancion(inscripcion, 'inscripcion', 'Inscripción', '.inscripcion');

// Novedades
configurarSancion(novedades, 'novedades', 'Novedades', '.novedades');

// Intereses No Enterados
configurarSancion(noEnterados, 'noEnterados', 'Intereses no enterados', '.noEnterados_footer');





function configurarSancion(checkbox, key, titulo, selectorContenedor) {
    checkbox.addEventListener('change', () => {
        if (checkbox.checked) {
            // 1. Crear los elementos base (ut, texto, input)
            const sancion = colocarSancionExtra('', '', null);
            sancion.texto.textContent = titulo;

            // 2. Manejo especial para TACHADURAS (si necesitas el textarea)
            let labelTachadura = null;
            let tachaduraTextArea = null;
           
            if (key === 'tachaduras') {
                labelTachadura = document.createElement('label')
                labelTachadura.className = "form-label text-muted"
               
                labelTachadura.textContent = "Describe la tachadura";
                labelTachadura.htmlFor = "floatingInput";
                document.querySelector('.labelTachaduras').appendChild(labelTachadura);

                tachaduraTextArea = document.createElement('textarea');
                // tachaduraTextArea.placeholder = 'Describe la tachadura';
                tachaduraTextArea.className = 'form-control form-control-lg mt-2';
               
               
                document.querySelector('.tachaduras_descripcion').appendChild(tachaduraTextArea);
               
            }
            // if( key == 'novedades'){
            //     const checkboxNovedades = document.createElement('input');
            //     checkboxNovedades.type = 'checkbox';
            //     checkboxNovedades.id = 'mi-checkbox-id';
            //     checkboxNovedades.className = 'form-check-input'; // Clase de Bootstrap
                
            //   // 3. Crear el Label (donde va el texto)
            //     const label = document.createElement('label');
            //     label.className = 'form-check-label';
            //     label.htmlFor = 'check_id_unico'; // Vincula el texto al check
            //     label.textContent = ' CAMBIO DE REP LEGA'; // TU TEXTO AQUÍ

            //     // Para que aparezca marcado por defecto (opcional)
            //     // checkboxNovedades.checked = false;
            //        document.querySelector('.checkBoxNovedades').appendChild(checkboxNovedades);
            //                           document.querySelector('.checkBoxNovedades').appendChild(label);

            // }

            // 3. Crear botón remover
            const btnRemover = botonRemover(null, checkbox);

            // 4. GUARDAR REFERENCIAS: Así el 'else' sabrá qué borrar
            //... expread operator lo pone como un objeto.
            sancionesActivas[key] = { ...sancion, btnRemover, labelTachadura, tachaduraTextArea };

            // 5. INSERTAR EN EL HTML
            const contenedor = document.querySelector(selectorContenedor);
            if (contenedor) {
                contenedor.appendChild(btnRemover);
                contenedor.appendChild(sancion.texto);
                contenedor.appendChild(sancion.ut);
                contenedor.appendChild(sancion.input);
            }

        } else {
            // 6. ELIMINACIÓN AUTOMÁTICA
            if (sancionesActivas[key]) {
                // Borramos cada elemento guardado en el objeto (ut, texto, input, btn, tachaduraTextArea)
                // object.values, toma los valores del array se sancionesActivas
                Object.values(sancionesActivas[key]).forEach(el => {
                    if (el) el.remove();
                });
                delete sancionesActivas[key];
            }
        }
    });

    
}



 function botonRemover(valorBoton,valorEvento){
     
            // 4. Crear Botón de Cerrar (X)
            valorBoton = document.createElement('button');
            valorBoton.type = 'button';
            valorBoton.className = 'btn-close'; // Clase nativa de Bootstrap 5
            valorBoton.style.fontSize = '0.7rem'; // Hacerlo más pequeño
          
                // LÓGICA DEL BOTÓN X: Desmarcar el checkbox disparará el 'else' automáticamente
                        valorBoton.addEventListener('click', () => {
                            valorEvento.checked = false; // Desmarcamos
                            valorEvento.dispatchEvent(new Event('change')); // Forzamos el evento change para limpiar
                        });
            return valorBoton;
    }





function colocarSancionExtra(uTSancion,textoSancion, inputSancion){

            // ESTOS SON > los valores iniciales que se van a inicializar, pensemos en ellos como constructores. 
            textoSancion = document.createElement('h4'); 
            textoSancion.textContent = ".....";
            textoSancion.className = "text-primary fw-bold mt-2 small";
            
            //imput pequeno para poner los valores UT 
            inputSancion = document.createElement('input');
            inputSancion.defaultValue = 43;
            inputSancion.type = 'number';
            Object.assign(inputSancion.style, {
                    width: '7em',             // Ancho reducido
                    padding: '5px',
                    border: '2px solid #368ecc', // Verde como tu tema
                    borderRadius: '4px',
                    textAlign: 'center',       // Número centrado
                    fontSize: '0.9rem',
                    fontWeight: 'bold',
                    outline: 'none'
                });
            
            

            // montos
            uTSancion = document.createElement('h5');
            uTSancion.textContent = 'UT 43';
            uTSancion.className = 'text-muted fw-bold small ms-auto';

            return {
                ut: uTSancion,
                texto: textoSancion,
                input: inputSancion
            }
}




// ENVIAR TODO AL PHP CON PROMESAS JSON

function prepararDatosParaEnvio() {
    const datos = [];

    // Recorremos el objeto donde guardamos las referencias
    for (const key in sancionesActivas) {
        
        
        const sancion = sancionesActivas[key];
        
        datos.push({
            tipo: key,                              // 'tachaduras', 'novedades', etc.
            monto: sancion.input.value,             // El valor numérico
            descripcion: sancion.extra ? sancion.extra.value : '' // El textarea si existe
        });
    }
    return datos;
}


const enviarDatosSanciones = () => {
    // const payload = prepararDatosParaEnvio();

    // if (payload.length === 0) {
    //     alert("No hay sanciones seleccionadas para subir.");
    //     return;
    // }
    // 1. Obtienes el array de sanciones (sin tocarlo)
    const sanciones = prepararDatosParaEnvio(); 

    // 2. Obtienes el nombre de la empresa desde tu input
    const nombreEmpresa = document.querySelector('input[name="empresa_sanciones_extra"]').value;

    if (sanciones.length === 0) {
        alert("No hay sanciones seleccionadas para subir.");
        return;
    }

    // 3. Creas un NUEVO objeto que contiene AMBOS datos
    // Esto mantiene tu array 'sanciones' intacto
    const payload = {
        nombre_empresa: nombreEmpresa,
        lista_sanciones: sanciones
    };

    // Iniciamos la Promesa con fetch
    fetch('/IVSS/back_end/facturacion/src/funciones_facturacion.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload) // Convertimos el array a JSON
    })
    .then(response => {
        if (!response.ok) throw new Error('Error en la red');
        return response.json(); // Convertimos la respuesta de PHP a objeto JS
    })
    .then(data => {
        console.log('Éxito:', data);
        alert("Sanciones guardadas correctamente.");
    })
    .catch(error => {
        console.error('Error:', error);
        alert("Hubo un problema al guardar.");
    });
};


//A MANANA TRBAJAR BOTON SUBIR SANCION

// Seleccionamos tu botón
const btnSubir = document.querySelector('#btnSubirSanciones');

btnSubir.addEventListener('click', enviarDatosSanciones);

btnSubir.addEventListener('click', () => {
    // 1. Llamamos a la función que extrae los datos del almacén global
    const datosFinales = prepararDatosParaEnvio();
    // Conectar al botón de subir


    // 2. Validación básica: Si no hay nada seleccionado, no enviamos nada
    if (datosFinales.length === 0) {
        alert("Por favor, selecciona al menos una sanción antes de subir.");
        return;
    }
});








