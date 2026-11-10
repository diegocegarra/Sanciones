// // ESTE ARCHIVO ES PARA LAS FECHAS FESTIVAS. 

// import 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js';

// const tagContainer = document.getElementById('btnAddDate');
// const tagInput = document.getElementById('tag-input');
// // aarray donde se guardaran las fechas
// let tags = [];

// //  esto va a incluir las fechas una vez el usuario haga enter

// tagInput.addEventListener('change', async function (e) {
//     if (e.key === 'Enter') {
//         const tagText = tagInput.value.trim();
//         if (tagText) {
//             // Aquí deberías llamar a guardar en servidor primero para obtener un ID real
//             // Por ahora, lo guardamos y recargamos
//             await guardarTagsEnServidor(tagText);
//             tagInput.value = '';
//         }
//     }
// });

// // renderiza la fecha y crea un nuevo elemento
// function renderTags() {
//     tagContainer.innerHTML = '';
//     // d-flex: horizontal, flex-wrap: permite saltar de línea
//     tagContainer.className = "d-flex flex-wrap gap-2"; 

//     tags.forEach(tag => {
//         const tagEl = document.createElement('div');
        
//         // No definimos ancho, Bootstrap ajustará según el contenido
//         tagEl.innerHTML = `
//             <button class='badge bg-primary border-0 p-2' onclick="removeTag('${tag.id}')">
//                 &times; <span>${tag.fecha}</span>
//             </button>`;
            
//         tagContainer.appendChild(tagEl);
//     });
// }

// function removeTag(id) {
//     // 1. Eliminación visual rápida (optimismo)
//     tags = tags.filter(t => t.id !== id);
//     renderTags();

//     // 2. Eliminación real en base de datos
//     eliminarTagsEnServidor(id);
// }

// // Hacerla global para el botón HTML
// window.removeTag = removeTag;


//   //FUNCIONES ASYNCRONAS

//     const cargarFechasDesdeServidor = async () => {
//       let fechasCargar = new FormData();
//       fechasCargar.append('accion', 'obtener_fechas');

//       try {
//           const response = await fetch('../../../back_end/tareas/src/funciones_jason.php', {
//               method: 'POST',
//               body: fechasCargar // <--- CORRECCIÓN: Envía el FormData
//           });

//           // Esperamos a que la respuesta se convierta en JSON
//           const datos = await response.json();

//           // Ahora que ya tenemos los datos, llamamos a renderTags
//           tags = datos.fechas; 
//           renderTags();

//           return datos;

//       } catch (error) {
//           alert(`Hubo un error para procesar la solicitud: ${error.message}`);
//           console.log(error);
//       }
//   }


//     const guardarTagsEnServidor = async (fechaTexto) => {
//       let guardarCargar = new FormData();
//       guardarCargar.append('accion', 'guardar_fechas');
//       guardarCargar.append('fecha', fechaTexto);
 

//       try {
//           const response = await fetch('../../../back_end/tareas/src/funciones_jason.php', {
//               method: 'POST',
//               body: guardarCargar // <--- CORRECCIÓN: Envía el FormData
//           });

//           // Esperamos a que la respuesta se convierta en JSON
//       // 1. Validar si la respuesta es OK (status 200)
     

//         // 2. Convertir a JSON correctamente
//         // const datos = await response.json(); 
        
//         // 3. Imprimir el objeto real para depurar
//         // console.log("Respuesta del servidor al guardar:", datos);

//         // 4. Actualizar la lista visual
//         await cargarFechasDesdeServidor();
         

//         //    console.log(`${datos} guardados correctamente`);

//       } catch (error) {
//           alert(`Hubo un error para procesar la solicitud: ${error.message}`);
//           console.log(error);
//       }
//   }
// //console.log("Fechas a eliminar:", arrayFechas);



// //:::::::::::::funcion ajax para eliminar fechas en el servidor

// //PROMESAS

//     const eliminarTagsEnServidor = async (id) => {
//       let eliminarCargar = new FormData();
//       eliminarCargar.append('accion', 'eliminar_fecha');
//       eliminarCargar.append('id', id);

//       try {
//           const response = await fetch('../../../back_end/tareas/src/funciones_jason.php', {
//               method: 'POST',
//               body: eliminarCargar // <--- CORRECCIÓN: Envía el FormData
//           });

//           // Esperamos a que la respuesta se convierta en JSON
//         const datos = await response.json();
//         console.log("Servidor dice:", datos.message);
        
//         // RECARGA la lista para que desaparezca de la pantalla oficialmente
//         await cargarFechasDesdeServidor();

//       } catch (error) {
//           alert(`Hubo un error para procesar la solicitud: ${error.message}`);
//           console.log(error);
//       }
//   }

// //}



// // carga el jason a el navegador
// window.addEventListener('DOMContentLoaded', cargarFechasDesdeServidor);


// // uploader

import 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js';

// CORRECCIÓN: Apuntar al contenedor real, no al botón
const tagContainer = document.getElementById('tag-container');
const tagInput = document.getElementById('tag-input');
const btnAddDate = document.getElementById('btnAddDate');

let tags = [];

// Función para procesar la entrada (compartida por Enter y Botón)
async function procesarNuevaFecha() {
    const tagText = tagInput.value.trim();
    if (tagText) {
        await guardarTagsEnServidor(tagText);
        tagInput.value = '';
    }
}

// Evento para la tecla Enter
tagInput.addEventListener('keydown', async (e) => {
    if (e.key === 'Enter') {
        e.preventDefault();
        await procesarNuevaFecha();
    }
});

// Evento para el botón Añadir
if (btnAddDate) {
    btnAddDate.addEventListener('click', procesarNuevaFecha);
}

function renderTags() {
    if (!tagContainer) return;
    tagContainer.innerHTML = '';

    // Si por algún error 'tags' no es un array, lo convertimos
    const listaTags = Array.isArray(tags) ? tags : [];

    listaTags.forEach(tag => {
        const tagEl = document.createElement('div');
        tagEl.innerHTML = `
            <span class="badge bg-primary p-2 d-flex align-items-center gap-2">
                ${tag.fecha}
                <i class="fa-solid fa-circle-xmark btn-borrar" 
                   style="cursor:pointer" 
                   data-id="${tag.id}"></i>
            </span>`;
        tagContainer.appendChild(tagEl);
    });
}

// CORRECCIÓN: Event Delegation para eliminar (más robusto que onclick)
tagContainer.addEventListener('click', (e) => {
    if (e.target.classList.contains('btn-borrar')) {
        const id = e.target.getAttribute('data-id');
        removeTag(id);
    }
});

function removeTag(id) {
    // 1. Eliminación visual rápida
    tags = tags.filter(t => t.id != id);
    renderTags();

    // 2. Eliminación real
    eliminarTagsEnServidor(id);
}

// FUNCIONES ASYNCRONAS (Sin cambios mayores, solo limpieza)
// const cargarFechasDesdeServidor = async () => {
//     let formData = new FormData();
//     formData.append('accion', 'obtener_fechas');

//     try {
//         const response = await fetch('../../../back_end/tareas/src/funciones_jason.php', {
//             method: 'POST',
//             body: formData
//         });
//         const datos = await response.json();

//         tags = Array.isArray(datos.fechas) ? datos.fechas : (Array.isArray(datos) ? datos : []);
//     renderTags();


//     } catch (error) {
//         console.error("Error al cargar:", error);
//     }
// }

// const cargarFechasDesdeServidor = async () => {
//     let formData = new FormData();
//     formData.append('accion', 'obtener_fechas');

//     try {
//         const response = await fetch('../../../back_end/tareas/src/funciones_jason.php', {
//             method: 'POST',
//             body: formData
//         });

//         // ESTA LÍNEA ES OBLIGATORIA
//         const datos = await response.json(); 
        
//         // Ahora sí puedes usar 'datos'
//         tags = Array.isArray(datos.fechas) ? datos.fechas : (Array.isArray(datos) ? datos : []);
//         renderTags();

        

//     } catch (error) {
//         console.error("Error al cargar:", error);
//     }
// }



const cargarFechasDesdeServidor = async () => {
    let formData = new FormData();
    formData.append('accion_fechas', 'obtener_fechas');

    try {
        const response = await fetch('../../../back_end/tareas/src/funciones_jason.php', {
            method: 'POST',
            body: formData
        });

        const texto = await response.text(); // Leemos como texto primero
        console.log("Respuesta bruta del servidor:", texto); // ESTO TE DIRÁ EL ERROR REAL

        const datos = JSON.parse(texto); // Luego intentamos convertir a JSON
        
        tags = Array.isArray(datos.fechas) ? datos.fechas : [];
        renderTags();

    } catch (error) {
        console.error("Error detallado:", error);
    }
}


const guardarTagsEnServidor = async (fechaTexto) => {
    let formData = new FormData();
    formData.append('accion_fechas', 'guardar_fechas');
    formData.append('fecha', fechaTexto);

    try {
        await fetch('../../../back_end/tareas/src/funciones_jason.php', {
            method: 'POST',
            body: formData
        });
        await cargarFechasDesdeServidor();
    } catch (error) {
        console.error("Error al guardar:", error);
    }
}

const eliminarTagsEnServidor = async (id) => {
    let formData = new FormData();
    formData.append('accion_fechas', 'eliminar_fecha');
    formData.append('id', id);

    try {
        const response = await fetch('../../../back_end/tareas/src/funciones_jason.php', {
            method: 'POST',
            body: formData
        });
        const datos = await response.json();
        console.log("Eliminado:", datos.message);
    } catch (error) {
        console.error("Error al eliminar:", error);
        // Si falla, recargamos para mostrar lo que hay en DB
        await cargarFechasDesdeServidor();
    }
}

window.addEventListener('DOMContentLoaded', cargarFechasDesdeServidor);