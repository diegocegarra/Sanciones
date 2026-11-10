// ESTE ES EL ACHIVO DE BUSQUEDA ES IMPORTANTE PORQUE NO SE SI VAN A COLOCARLE BUSQUEDA O NO PERO EN EMPRESASA JEAN HAY QUE COLOCARSELA

// basicamente como funciona es que agarra el id que se le pase el nombre de la columna y crea una tabla de 0 nueva una replica
// y descarta dependiendo a la busqueda

// Asi que si le agregan cualquier funcionalidad a su tabla tambien tienen que agregarsela aqui. 

// ES DECIR QUE SI VAN A AGREGARSELO, TIENEN QUE HACER UNA TABLA DE 0 

// los document.querySelectors van a ser diferentes dependiendo de las clases y lo que selecionen, pero tiene que tener un error de la busqueda resultados
// y busquedas.

//                              para los resultados en el html con php crean 

// <!-- BARRA DE BUSQUEDA -->

//       <div class="input-container">
//          <input type="search" id="busqueda" placeholder="busca una tarea">
//     </div> 
//     <div class="error_busqueda "  style="display:none;">
//         <p></p>
//     </div>


// <div id="resultados" >
            // AQUI ADENTRO LE METEN TODA SU TABLA  =  fuera de chinazo
//</div >

 
 
 import './automatizaciones.js';
   
   window.addEventListener('DOMContentLoaded', () =>{
        const busqueda = document.querySelector('#busqueda');
        const tableContainer = document.querySelector('#results tbody');
        const resultsContainer = document.querySelector('#resultados');
        const errorsContainer =  document.querySelector('.error_busqueda');
        let search_criteria = '';
        
        //enviar peticion usando fetch
        // funcion para buscar los datos usando promesas
        const searchData = async() =>{
            let formData  = new FormData();

            // el nombre que va en el $_POST en php
           formData.append('busqueda_tarea', search_criteria);
           formData.append('accion', 'buscar');
            try{
                // redirije a nuestro archivo .php
                const response = await fetch('../../../back_end/tareas/src/funciones_jason.php', {
                        method: 'POST',
                        body: formData
                    
                });

                //respuesta de peticion
                return response.json();

            }catch(error){
                alert(`${'hubo un error para procesar la solicitud'} ${error.message}`);
                console.log(error);
            }
        }


        const cambiarEstatus =async () =>{
            let estadoEstatus = new FormData();

            estadoEstatus.append('estatus', 'completado');
            estadoEstatus.append('accion', 'cambiar_estatus');

            try{
                const responseEstatus = await fetch('../../../back_end/tareas/src/funciones_jason.php',{
                    method:'POST',
                    body: estadoEstatus
                });

                return responseEstatus.json();

            }catch(e){
                alert(`${'hubo un error al procesar la solicitud de cambio de estatus'} ${e.message}`);
                console.log(e);
            }


        }
        
// Esta funcion elimina cualquier letra que el usuario borre o elimine
        const limpiarInterfaz = () => {
            // search_criteria = '';
            // tableContainer.innerHTML = '';
            // resultsContainer.style.display = 'none';
            // errorsContainer.style.display = 'none';
        };

        // esto es para que elimine la busqueda al hacer click en la x
      //  busqueda.addEventListener('search', limpiarInterfaz);

        // eventlistener de control para mostrar resultados de la busqueda o no
        busqueda.addEventListener('input', event => {
            search_criteria = event.target.value.toUpperCase();
         
            // LOS if and else son sin queremos limpiar todo de la pantalla
        //  if (search_criteria === '') {
          //      limpiarInterfaz();
           // } else {
                showResults();
           // }
        });



    // esta funcion muestra los resultados
    const showResults = () => {

        if (busqueda.value.trim() === "") {
            // Redirige a la página principal de tareas
            window.location.href = '/IVSS/front_end/vistas/tareas/tareas.php'; 
            return; // Detiene la ejecución
        }

            searchData().then(dataResults => {
                       
                // Limpiamos siempre al recibir datos para evitar duplicados
                tableContainer.innerHTML = '';

                if (Array.isArray(dataResults) && dataResults.length > 0) {
                    resultsContainer.style.display = 'block';
                    errorsContainer.style.display = 'none';

                    dataResults.forEach(author => {
                        const row = document.createElement('tr');

                        // opciones para cambiar el estatus de la tarea

                        let optionsHtml = `<option selected>${author.estatus_nombre}</option>`;
                        let optionProcesando;
                        let optionFinalizada;
                        let optionEsperando;

                     //   const showResults = () => {

                    //cambiarEstatus().then(EstatusResultado => {
                                if(author.estatus_nombre !== 'procesando'){
                                    console.log('procesando');
                                    optionProcesando= ` <option value="procesando" >procesando</option>`
                                }
                                    
                                if(author.estatus_nombre !== 'finalizada'){
                                        optionFinalizada =`<option value='finalizada' >finalizada</option> `
                                }
                                    
                                if(author.estatus_nombre !== 'esperando'){
                                        optionEsperando=` <option value='esperando' >esperando</option>`
                                }
                               // })
                    //}
                         // if para extemporaneos o inconsistencias o trabajadores activos cuyos valores esten el blanco, para que los muestre o no pero 
                         //que no muestre undefined
                            // verifica si el falsy en javascript    
                        let incosistencias = author.incosistencias ? `<td>${author.incosistencias}</td>` : `<td></td>`; 
                        let trabajadores_activos = author.trabajadores_activos ? `<td>${author.trabajadores_activos}</td>` : `<td></td>`;
                        let extemporaneos = author.extemporaneos ? `<td>${author.extemporaneos}</td>` : `<td></td>`;
                                


                        // TABLA DE TAREAS REPLICADA
                        row.innerHTML = `
                          <!-- checkbox para selecionar las filas -->
                            <td><input type="checkbox" class="task-checkbox"></td>        
                            <td> <strong>${author.id}</strong></td>
                            <td>${author.codigo_tarea}</td>
                            <td>${author.nombre_empresa} ${author.cantidad_tarea}</td>
                           <td></td>
                            ${trabajadores_activos}
                            <td>${author.estado}</td>
                             <td>${author.creado_en}</td>
                           

                    <!--  seccion de estatus de la tarea -->
                    <!-- select null para que no se pueda editar -->

                    <td class="selectNull">
                        <!-- se le coloca la clase select, para que los valores de la tabla no se puedan seleccionar  al momento de mandarla a escribir-->
                    <form action="../../../back_end/tareas/pruebas_tareas/automatizaciones.php" method="get">
                        <input type="hidden" name="accion" value="cambiar_status">
                        
                        <input type="hidden" name="id_tarea" value="${author.id}">
                        <select name="estatus" class="form-select form-select-sm bg-primary text-white"  
                         style="font-weight: bold; width: auto; min-width: 120px; font-size: 0.8rem;" id="service-select" onchange="this.form.submit()">
                            

                         
                        <!--ESTAS SON LAS OPCIONES PARA EL ESTATUS -->
                             ${optionsHtml}
                             ${optionProcesando}
                             ${optionFinalizada}
                             ${optionEsperando}

                            
                            
                         </select>
                    </form>
                    </td>
                      
                        
                   
                        <form action="empleados.php" method="get">
                                <input type="hidden" name='empleado_empresa' value="${author.nombre_empresa} ">
                                <td> <a href="empleados.php"><button type="submit" class="btn btn-dark"  ><i class="fa-solid fa-user"></i></button></a></td>
                        </form>
                     

                        <td>
                      
                           <button type="button" class="btn badge bg-dark " data-bs-toggle="modal" data-bs-target="#subirEmpleadosModal${author.id}">
                            subir empleados
                            </button>
                        </td>
                           
                     

                        <td>
                           <a href=""><i class="fa-solid fa-file" style="color: #ff0000; font-size: 34px;"></i></a>
                      </td>

                


                            `;
                        tableContainer.appendChild(row);
                    });
                } else {
                    // Si llegamos aquí y el input ya está vacío (porque el usuario borró rápido)
                    // esto esta comentado porque oculta la tabla si el usuario no ha escrito nada
                    // if (busqueda.value.length === 0) {
                    //     limpiarInterfaz();
                    //     return;
                    // }
                    // Resultado de la busqueda si hubo un error, y no encontro resultados
                    resultsContainer.style.display = 'none';
                    errorsContainer.style.display = 'block';
                    const pError = errorsContainer.querySelector('p');
                    if (pError) {
                        pError.style.display = 'block';
                        pError.innerHTML = `No hay resultados para: <strong>${search_criteria}</strong>`;
                    }
                }
            }).catch(err => console.error("Error:", err));

            
        };


  //  }


    }) ; //fin del doom



