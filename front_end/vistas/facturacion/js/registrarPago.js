// MODAL SUBIR MONTO MODAL 

  // Tasa de ejemplo
const TASA_CAMBIO = 43; 
let monedaActual = 'Bs';

function changeCurrency(moneda) {
    monedaActual = moneda;
    
    // Actualizar textos en la UI
    document.getElementById('currentCurrency').innerText = moneda;
    document.getElementById('currencySymbol').innerText = moneda;
    
    // Actualizar placeholder o lógica de cálculo
    calcularEquivalencia();
}

function calcularEquivalencia() {
    // ESTO ES PARA EL INPUT DE LOS VALORES QUE ELLOS PONEN 
    const val = parseFloat(document.getElementById('mainInput').value) || 0;
    // la cantidad que se ve igual pero abajo
    const secondaryDisplay = document.getElementById('secondaryAmount');
    
    if (monedaActual === 'Bs') {
        let usd = val / TASA_CAMBIO;
        secondaryDisplay.innerText = usd.toFixed(2) + " UT";
    } else {
        let bs = val * TASA_CAMBIO;
        secondaryDisplay.innerText = bs.toFixed(2) + " Bs";
    }
}

// Escuchar cambios en el input
let valor_moneda= document.getElementById('mainInput').addEventListener('input', calcularEquivalencia);















// modal FUNCINOES PARA NAVEGAR DE ALANTE HACIA ATRAS YA QUE SI LE DABAS LANTE ANTES SE QUEDABA ALLI

// 1. FUNCIÓN PARA NAVEGAR (Adelante y Atrás)
function cambiarPaso(actual, siguiente, monedaActual) {
    const dialog = document.getElementById('modalSize');

    // Ocultamos el actual
    const divActual = document.getElementById('paso' + actual);
    if (divActual) {
        divActual.classList.add('d-none');
        divActual.classList.remove('d-block');
    }

    // Mostramos el siguiente
    const divSiguiente = document.getElementById('paso' + siguiente);
    if (divSiguiente) {
        divSiguiente.classList.remove('d-none');
        divSiguiente.classList.add('d-block');
    }

    // Control de tamaño: Solo el paso 1 es grande
    if (siguiente === 1) {
        dialog.classList.add('modal-lg');
    } else {
        dialog.classList.remove('modal-lg');
    }


    
    if (actual === 2 && siguiente === 3) {
        // 1. Capturar valores
        const empresa_pago = document.getElementById('empresa_pago').innerText;
        const monto = document.getElementById('mainInput').value;
        const banco = document.getElementById('selectMoneda').value;
        const referencia = document.getElementById('referencia').value;
        const fecha = document.querySelector('input[name="fechaPago"]').value;
        const monedaActual = document.getElementById('currentCurrency').innerText;
        

        

        // 2. Validar que no estén vacíos (Opcional pero recomendado)
        if(!monto || !referencia || !fecha) {
            alert("Por favor rellene todos los campos");
            return;
        }

        // 3. Escribir en el Paso 3 (Resumen)
        document.getElementById('resumenMontoPrincipal').innerText = `${monto} ${monedaActual}`;
        document.getElementById('resumenBanco').innerText = banco.toUpperCase();
        document.getElementById('resumenReferencia').innerText = referencia;
        document.getElementById('resumenFecha').innerText = fecha;
        document.getElementById('resumenEmpresa').innerText = empresa_pago;

    }


}



// FUNCION PARA ENVIAR LOS DATOS JSON
// Función para enviar a PHP
async function finalizarTodo() {
    // Recolectamos los datos finales
    const datos = {

        empresa:      document.getElementById('empresa_pago').innerText,
        monto:        document.getElementById('mainInput').value,
        banco:        document.getElementById('selectMoneda').value,
        referencia:   document.getElementById('referencia').value,
        fecha:        document.querySelector('input[name="fechaPago"]').value,
        moneda:       document.getElementById('currentCurrency').innerText
        

    };

    try {
        let response = await fetch('/IVSS/back_end/facturacion/src/orden_pago.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datos)
        });

        // const textoRaw = await response.text();
        // console.log("Respuesta del servidor:", textoRaw);

        // // 4. Convertir a objeto JSON
        // const resultado = JSON.parse(textoRaw);
        let resultado = await response.json();

        // debug para errores
        // const textoRespuesta = await response.text();
        // console.log("Respuesta bruta del servidor:", textoRespuesta);

        // // Intentar convertir ese texto a JSON
        // const resultado = JSON.parse(textoRespuesta);
        
        if (resultado.status === "success" || resultado.success === true) {
            // alert("¡Pago registrado con éxito!");
            // LANZAMOS SWEET ALERT
            Swal.fire({
                title: '¡Pago registrado!',
                text: resultado.message,
                icon: 'success',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#3085d6',
                timer: 3000, // Se cierra solo en 3 seg
                timerProgressBar: true
            }).then((result) => {
                // CUANDO EL USUARIO CIERRE EL ALERT O PASE EL TIEMPO
                window.location.href = ""; 
            });

        } else {
            // Sweet Alert para errores
            Swal.fire({
                title: 'Error',
                text: resultado.message,
                icon: 'error'
            });
        }
           // location.reload(); // Recargar para ver cambios
        // } else {
        //     alert("Error: " + resultado.message);
        // }
    } catch (error) {
        console.error("Error al enviar:", error);
    }
}





// // 2. FUNCIÓN PARA BOTÓN FINAL (ÉXITO)
// function finalizarTodo() {
//     alert("¡Pago enviado con éxito!");
//     const modalEl = document.getElementById('ordenPagoModal');
//     const modalInstancia = bootstrap.Modal.getInstance(modalEl);
//     if (modalInstancia) {
//         modalInstancia.hide(); // Esto cerrará el modal y activará el evento de abajo
//     }
// }




// 3. EL SECRETO: RESETEO AUTOMÁTICO AL CERRAR
// Este evento se dispara SIEMPRE que el modal se oculte (por botón, por clic fuera, por escape)
const miModalPagos = document.getElementById('ordenPagoModal');

// 'hidden.bs.modal' es el evento que lanza Bootstrap cuando el modal termina de cerrarse
miModalPagos.addEventListener('hidden.bs.modal', function () {
    
    // 1. Resetear el tamaño al original (Paso 1 es Grande)
    const dialog = document.getElementById('modalSize');
    dialog.classList.add('modal-lg');

    // 2. Resetear la visibilidad de los pasos
    // PASO 1: Se muestra
    const p1 = document.getElementById('paso1');
    p1.classList.remove('d-none');
    p1.classList.add('d-block');

    // PASO 2: Se oculta
    const p2 = document.getElementById('paso2');
    p2.classList.remove('d-block');
    p2.classList.add('d-none');

    // PASO 3: Se oculta
    const p3 = document.getElementById('paso3');
    p3.classList.remove('d-block');
    p3.classList.add('d-none');



    

    // // Lógica para ocultar/mostrar divs
    // document.getElementById('paso' + actual).classList.add('d-none');
    // document.getElementById('paso' + siguiente).classList.remove('d-none');

    



    // 3. OPCIONAL: Limpiar los inputs (para que no queden datos viejos)
    const inputs = miModalPagos.querySelectorAll('input, select');
    inputs.forEach(input => {
        if (input.type === 'number' || input.type === 'text' || input.type === 'date') {
            input.value = ''; 
        }
    });
    // Limpiar inputs si lo deseas (opcional)
    // document.getElementById('mainInput').value = "0.00";


    




});




















// PARA LOS INPUT Y LAS ENTRADAS DEL USURIO

function obtenerDatosPago() {
    // 1. Capturar el monto del input (se usa .value)
    const monto = document.getElementById('mainInput').value;

    // 2. Capturar la moneda del dropdown (se usa .innerText)
    const moneda = document.getElementById('currentCurrency').innerText;

    // 3. Mostrar en consola para verificar
    console.log("Monto digitado: " + monto);
    console.log("Moneda seleccionada: " + moneda);

    // Ejemplo: Usarlo para una lógica simple
    if (monto > 0) {
        alert("Procesando " + monto + " " + moneda);
    } else {
        alert("Por favor ingresa un monto válido");
    }
}



// Escuchamos cuando el usuario escribe en el input
document.getElementById('mainInput').addEventListener('input', function() {
    const monto = parseFloat(this.value) || 0;
    const monedaActual = document.getElementById('currentCurrency').innerText;
    const visorSecundario = document.getElementById('secondaryAmount');

    let conversion = 0;
    const valorUT = 9.00; // Valor de ejemplo de la UT

    if (monedaActual === 'Bs') {
        // Si escribe en Bs, calculamos cuántas UT son
        conversion = monto / valorUT;
        visorSecundario.innerText = conversion.toFixed(2) + " UT";
    } else {
        // Si escribe en UT, calculamos cuántos Bs son
        conversion = monto * valorUT;
        visorSecundario.innerText = conversion.toFixed(2) + " Bs";
    }
});

function changeCurrency(tipo) {
    // 1. Cambiar el texto del botón y el símbolo al lado del input
    document.getElementById('currentCurrency').innerText = tipo;
    document.getElementById('currencySymbol').innerText = tipo;

    // 2. Opcional: Limpiar el input al cambiar de moneda para evitar confusiones
    document.getElementById('mainInput').value = "0.00";
    document.getElementById('secondaryAmount').innerText = "0.00 " + (tipo === 'Bs' ? 'UT' : 'Bs');
}