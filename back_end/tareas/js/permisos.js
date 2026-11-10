// AQUI PARA EDITAR VAN A COPIAR Y PEGAR ESTO EN SU CARPETA

// AQUI VAN A IR TODOS LOS PERMISOS EN TIEMPO REAL COMO ESCRIBIR, EDITAR O CUALQUIER OTRO PERMISO 
// PARA ESCRIBIR, PLIS COLOQUEN EN LA CLASE en html php: table a la tabla
// PARA QUE NO SE PUEDA EDITAR CUALQUIER COLUMNA ASI HAGAS CLICK COLOQUEN: .selectNull
// EL ARCHIVO EDITAR LO MANDA A funciones_jason.php en mi modulo tareas, les recomiendo que se creen uno similar para manejar todas las respuestas JASON

// PARA EL SWITCH ESTE ES EL VALOR QUE LE VAN A PASAR EN TIEMPOR REAL AL archivo php para hacer la consulta en el mysql.
//   Ejemplo como esto esta declarado en el html php
                  //  <th data-id="id_tarea"scope="row"><?=$tarea['id'] ?></th>
                  //  <td data-columna="codigo_tarea"><?= $tarea['codigo_tarea']?></td>

// Como agarra el id para saber en que columna esta el archivo selecionado en la tabla de la base de datos para editarlo.
//                datosEnviar.append('id_selecionado', id_real);

// Como lo agarra el switch
                // switch(valor_celda){
                
                //   case 'codigo_tarea':
                //       datosEnviar.append('codigo_selecionado', 'codigo');
                //       break;
                // }

// Como se lo pasa al php para ejecutar la consulta que se lo pasa con un $_POST['codigo_selecionado']

              // function editarTarea($pdo, $valorTarea){
                          
              //         error_reporting(0);
              //         header('Content-Type: application/json');


                      
              //             $id_tareas = $_POST['id_selecionado'];
                    

              //             $codigo_selecionado = $_POST['codigo_selecionado'];
              //             }

// MI recomendacion es que coloquen un switch al inicio de su archivo php donde le van a pasar el jason para que llamen la funcion que necesitas
//EJEMPLO

            // switch ($_POST['accion']) {

            //      // funcion para eliminar fecha  tareas de evaluar_trabajadores.js en tareas en el back end
          
            //     case 'eliminar_fecha':

            //          // Verifica que el ID llegue antes de llamar a la función
        
            //         if(isset($_POST['id'])) {
            //                 fechasFestivasEliminar($pdo, $_POST['id']);
            //         } else {
            //                 echo json_encode(['success' => false, 'message' => 'Falta el ID']);
            //         }
            //         break;


// ESTO USA PROMESAS SI VAN A COLOCAR EL AWAIT NO PUEDEN COLOCARLO SIN EL ASYNC 

// esta funcion va a ocultar las columnas de las tablas
function ocultar() {
    // 1. Selecciona todos los elementos que tienen la clase 'columna-ocultable'
    const celdas = document.querySelectorAll('.columna-ocultable');

    // 2. Itera sobre la lista y aplica 'display: none' a cada celda
    celdas.forEach(celda => {
        celda.style.display = 'none';
    });
    // console.log('hola');
}







// // ESTA funcion se encarga de al hacer click en la tabla escribir en la misma
// // Esta funcion edita toda la tabla en tiempo real, al momento de hacerle click y crea un jason. 
// function editar_tabla(){
//   // se seleciona el valor de la tabla no importa cual
//   let tabla = document.querySelector('table');


//       // al hacer click en el campo de la tabla
//       tabla.addEventListener('click', async (event) => {
//           const celda = event.target;
//        //   const valor_celda = event.target.id;
         

//           // si el nombre de la tabla no es td y es un input
//           if (celda.tagName !== 'TD' || celda.querySelector('input') || celda.closest('.selectNull')) {
//               return;
//           }

//           // el target se pasa a la variable td
//           let td = celda;
//           // se le eliminan los espacios el contenido de la tabla y se guarda en una variable como valor originar sin modificar
//           let valorOriginal = td.textContent.trim();
//           // se calcula la cantidad de px en la celda
//           const anchoCelda = td.clientWidth + 'px';

//           // Crear y configurar el input al momento de hacer click para editar la tabla
//           let input = document.createElement('input');
//           input.type = 'text';
//           input.value = valorOriginal;
//           input.style.width = anchoCelda;
//           input.style.boxSizing = 'border-box';
//           // ... (demás estilos que ya tenías)

//           //qui esto se formatea en ningun valor, y se crea el input al 
//           td.textContent = ''; 
//           td.appendChild(input);
//           // Forzamos el foco en ese elemento
//           input.focus();

//           // --- MANEJO DE EVENTOS DEL INPUT ---

//           // 1. Tecla Enter (solo quita el foco, el blur hará el resto)
//           input.addEventListener('keydown', (e) => {
//               if (e.key === 'Enter') {
//                 // deseleciona el cuadro de texto al hacer click en cualquier otra parte
//                   input.blur(); 
//               }
//           });

//           //:::::::::::::PROMESAS PARA ENVIARLE LOS DATOS AL PHP

//           //  Evento Blur (Aquí ocurre la magia)
//           // Al momento de selecionar en cualquier otro campo o hacer un enter o selecionar afuera o algo/ 
//             input.addEventListener('blur', async () => {
//                 const nuevoValor = input.value.trim();

//                 // Si el valor no cambió, solo restauramos el texto y salimos
//                 if (nuevoValor === valorOriginal) {
//                     td.textContent = valorOriginal;
//                     return;
//                 }
                
//                 // valores para selecionar data-columna en htlm php y selecionar la id
//                 const valor_celda = td.dataset.columna; 
//                 //.closest('tr'): Este método busca hacia arriba en el árbol de HTML (hacia los padres) hasta encontrar la etiqueta <tr> (la fila) más cercana
//                 const id_real = td.closest('tr').dataset.id;
                
//                 // Preparamos los datos para PHP
//                 let datosEnviar = new FormData();
//                 datosEnviar.append('valor', nuevoValor); // El dato editado
//                 //datosEnviar.append('id_selecionado', 'id_tarea');
//                 // Nota: Aquí podrías añadir un ID de fila si lo tienes en un data-attribute
//                 datosEnviar.append('id_selecionado', id_real);

//                 //switch para determinar el valor del campo seleccionado para saber cual valor se le pasa, para editarlo en el mysql. 
//                 // estos cases estan en el archivo tareas.php del front-end en vistas
//                 switch(valor_celda){
                
//                   case 'codigo_tarea':
//                       datosEnviar.append('codigo_selecionado', 'codigo');
//                     break;
//                   case 'nombre_tarea':
//                       datosEnviar.append('nombre_selecionado', 'nombre');
//                     break;
//                   case 'tarea_extemporaneos':
//                       datosEnviar.append('extemporaneo_selecionado', 'extemporaneo');

//                     break;    
//                   case 'incosistencias':
//                       datosEnviar.append('inconsistencia_selecionada', 'incosistencia');
//                     break;    
                  
//                   case 'tareas_trabajadores_activos':
//                       datosEnviar.append('trabajador_selecionado', 'trabajador');

//                     break;    
//                   case 'estatus_nombre_tarea':
//                       datosEnviar.append('estatus_selecionado', 'estatus');

//                     break;    

//                   default:
//                     'ningun valor selecccionado';
//                      console.log('ningun valor selecionado ');   

//                     break;
//                 }

//                 // accion tiene que ir despues que se le pasen los datos, porque va a pedir la peticion
//                 // y se manda a funciones jason.php como accion para que en el php valide en funciones_jason.php el switch y llame a la funcion para el mysql
//                     datosEnviar.append('accion', 'editar_tarea');
//                 try {
//                     td.textContent = "Guardando..."; // Feedback visual rápido para que se ponga al momento 

//                     const respuesta = await fetch('../../../back_end/tareas/src/funciones_jason.php', {
//                         method: "POST",
//                         body: datosEnviar
//                     });

//                     if (respuesta.ok) {
//                         const resultado = await respuesta.json();
//                         console.log("Servidor dice:", resultado);
                        
//                         td.textContent = nuevoValor; // Actualizamos la celda con el éxito
//                         td.classList.remove('editando');
//                     } else {
//                         throw new Error('Error en la respuesta del servidor al editar el valor');
//                     }

//                 } catch (error) {
//                     alert(`Error al procesar: ${error.message}`);
//                     td.textContent = valorOriginal; // Revertimos al valor original por error
//                     console.error(error);
//                 }
//             });
//       });
//     }



    
// // LLAMADOS DE LA FUNCIONES SEGUN SUS PERMISOS
//     editar_tabla();




// EDITAR GENERAL CUALQUIER VALOR EN LA BASE DE DATOS
/**
 * Script Universal de Edición de Tablas
 * Maneja cualquier tabla que tenga los atributos data-schema y data-tabla
 */

/**
 * Edición Universal - Versión Blindada
 */
const inicializarEdicionUniversal = () => {
    // Escuchamos el clic en el DOCUMENTO completo
    document.addEventListener('click', async (event) => {
        // Buscamos si el clic fue en un TD que tenga data-columna dentro de una tabla con data-tabla
        const td = event.target.closest('table[data-tabla] td[data-columna]');

        // Si no existe el TD o ya tiene un input o es clase no-editar, ignoramos
        if (!td || td.querySelector('input') || td.closest('.no-editar')) return;

        const valorOriginal = td.textContent.trim();
        const anchoCelda = td.clientWidth + 'px';
        
        // Crear el input dinámico
        const input = document.createElement('input');
        input.type = 'text';
        input.value = valorOriginal;
        input.className = "form-control form-control-sm"; // Opcional: clase de Bootstrap
        input.style.width = anchoCelda;

        td.textContent = '';
        td.appendChild(input);
        input.focus();

        // Al perder el foco (Guardar)
        input.addEventListener('blur', async () => {
            const nuevoValor = input.value.trim();

            if (nuevoValor === valorOriginal) {
                td.textContent = valorOriginal;
                return;
            }

            // Obtener metadatos subiendo por el árbol DOM
            const tabla = td.closest('table');
            const fila = td.closest('tr');

            const datosEnviar = new FormData();
            datosEnviar.append('accion', 'editar_universal');
            datosEnviar.append('schema', tabla.dataset.schema);
            datosEnviar.append('tabla', tabla.dataset.tabla);
            datosEnviar.append('id', fila.dataset.id);
            datosEnviar.append('columna', td.dataset.columna);
            datosEnviar.append('valor', nuevoValor);

            

            try {
                td.textContent = "Guardando...";
                
                const respuesta = await fetch('../../../back_end/tareas/src/funciones_roles.php', {
                    method: "POST",
                    body: datosEnviar
                });

                // Capturamos el texto crudo para depurar si hay error de JSON
                
                const rawText = await respuesta.text();
                // const resultado = JSON.parse(rawText);
                // let resultado;
                
                try {
                    resultado = JSON.parse(rawText);
                } catch (e) {
                    console.error("Respuesta no válida del servidor:", rawText);
                    throw new Error("El servidor respondió con un formato incorrecto (revisa la consola).");
                }

                if (resultado.status === 'success') {
                    td.textContent = nuevoValor;
                    td.classList.add('bg-success-subtle');
                    setTimeout(() => td.classList.remove('bg-success-subtle'), 1000);
                } else {
                    throw new Error(resultado.message);
                }
            } catch (error) {
                alert("Error: " + error.message);
                td.textContent = valorOriginal;
            }
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') input.blur();
            if (e.key === 'Escape') { input.value = valorOriginal; input.blur(); }
        });
    });
};

// Se ejecuta una sola vez al cargar la página
document.addEventListener('DOMContentLoaded', inicializarEdicionUniversal);














function cambiarEstatusValor(){


    // CAMBIAR ESTATUS: 
    const estatus = document.querySelectorAll('.estatus_selecionado');

    estatus.forEach(selectIndividual => { 

    selectIndividual.addEventListener( 'change', async(event)=>{
      
        const valorEstatus = event.target.value;
        const fila = event.target.closest('tr'); // Buscamos la fila
        const idReal = fila ? fila.dataset.id : null; // Obtenemos el ID del data-id

        const estatusCambio = new FormData();
        estatusCambio.append('estatus', valorEstatus); // Error corregido: valorEstatus
        estatusCambio.append('id_estatus', idReal); // ¡PHP necesita saber qué ID actualizar!
        estatusCambio.append('accion', 'cambio_estatus');

      try{
        // estatus.textContent = 'Actualizando...';
        const respuestaEstatus = await fetch('../../../back_end/tareas/src/funciones_jason.php', {
          method: 'POST',
          body: estatusCambio
        });

//         const textoFalso = await respuestaEstatus.text(); // Lee el error como texto plano
// console.log("CONTENIDO REAL DEL SERVIDOR:", textoFalso); // <--- ESTO te dirá el error de PHP

        if(respuestaEstatus.ok){
          const resultadoEstatus = await respuestaEstatus.json();
          console.log("respuesta del servidor",resultadoEstatus)
        }else{
          throw new Error('Error en la respuesta del servidor al cambiar el estatus');
          
        }
      }catch(error){
          alert(`Error al cambiar el estatus: ${error.message}`);
          console.error(error);
      }
    }); 

    });// fin foreach
}



cambiarEstatusValor();

// import('../src/funciones_jason.php');