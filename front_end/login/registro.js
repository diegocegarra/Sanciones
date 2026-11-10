const regexCorreo = /^[\w.-]+@[\w-]+\.[a-z]{2,}(\.[a-z]{2,})?$/i;
const regexNombre = /^[A-ZÁÉÍÓÚÑ][a-záéíóúñ]+ [A-ZÁÉÍÓÚÑ][a-záéíóúñ]+$/;
const regexClave = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{6,}$/;
const regexCedula = /^\d{6,10}$/;



//Validacion de los nombres, si esta bien se coloca valido y si esta mal se coloca invalido
const nombreInput = document.getElementById("nombre");

nombreInput.addEventListener("input", function () {
  if (regexNombre.test(nombreInput.value.trim())) {
    nombreInput.classList.add("is-valid");
    nombreInput.classList.remove("is-invalid");

  } else {
    nombreInput.classList.add("is-invalid");
    nombreInput.classList.remove("is-valid")
    

  }
});


//Validacion para la cedula

const cedulaInput = document.getElementById("cedula");
cedulaInput.addEventListener("input", function () {
  if (regexCedula.test(cedulaInput.value.trim())) {
    cedulaInput.classList.add("is-valid");
    cedulaInput.classList.remove("is-invalid"); 
  } else {
    cedulaInput.classList.add("is-invalid");
    cedulaInput.classList.remove("is-valid");
  }
});

//Validacion de los correos, si esta bien se coloca valido y si esta mal se coloca invalido

const correoInput = document.getElementById("correo");
correoInput.addEventListener("input", function () {
  if (regexCorreo.test(correoInput.value.trim())) {
    correoInput.classList.add("is-valid");
    correoInput.classList.remove("is-invalid");
  } else {
    correoInput.classList.add("is-invalid");
    correoInput.classList.remove("is-valid");
  }
});

//Validacion para la contraseña
const claveInput = document.getElementById("clave");

claveInput.addEventListener("input", () => {
    const esValida = regexClave.test(claveInput.value.trim());
    claveInput.classList.toggle("is-valid", esValida);
    claveInput.classList.toggle("is-invalid", !esValida);
});

//Validacion para la confirmar contraseña
const confirmarInput = document.getElementById("confirmar");
confirmarInput.addEventListener("input", function () {
  if (confirmarInput.value.trim() === claveInput.value.trim() && regexClave.test(confirmarInput.value.trim())) {
    confirmarInput.classList.add("is-valid");
    confirmarInput.classList.remove("is-invalid");
  } else {
    confirmarInput.classList.add("is-invalid");
    confirmarInput.classList.remove("is-valid");
  }
});




document.getElementById("btnRegistrar").addEventListener("click", async function(e){
          e.preventDefault(); // Detenemos el envío automático

    // ... (Tus capturas de variables nombre, correo, etc. se mantienen igual) ...
    const nombre = document.getElementById("nombre").value.trim();
    const correo = document.getElementById("correo").value.trim();
    const usuario = document.getElementById("usuario").value.trim();
    const cedula = document.getElementById("cedula").value.trim();
    const clave = document.getElementById("clave").value.trim();
    const confirmar = document.getElementById("confirmar").value.trim();
    const inputAdmin = document.getElementById("codigoAdmin");
    const codigoAdminValue = inputAdmin ? inputAdmin.value.trim() : "";
    
    const errorMsg = document.getElementById("errorMsg");

    // VALIDACIONES (Regex)
    if (clave !== confirmar) {
        errorMsg.textContent = "Las contraseñas no coinciden.";
        errorMsg.style.display = "block";
        return;
    }

          // Si coinciden, ocultamos errores previos y procedemos
        // 2. Envío de datos (La Promesa)
            errorMsg.style.display = "none";
            // const codigoAdmin_nombre = document.getElementById("codigoAdmin") ? document.getElementById("codigoAdmin").value.trim() : "";
            
            try {
                const respuesta = await fetch("/IVSS/back_end/login/iniciar_session.php", {

                  
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        nombre, cedula, correo,
                        codigoAdmin: codigoAdminValue, // Enviamos el valor capturado arriba
                        nombre_usuario: usuario,
                        usuarios_roles: 3,
                        password: clave
                        
                    })
                });

                //linea de prueba de respuesta verdadera del servidor
//                 const textoDePrueba = await respuesta.text(); 
// console.log("LO QUE EL SERVIDOR REALMENTE DICE:", textoDePrueba);

                // if (!respuesta.ok) throw new Error("Error en el servidor");

                const data = await respuesta.json();
        // 1. Ver qué mandó el servidor realmente
        // const textoBruto = await respuesta.text();
        // console.log("RESPUESTA BRUTA DEL SERVIDOR:", textoBruto);

        // // 2. Intentar limpiar si vienen dos JSON pegados (el parche)
        // // Buscamos el último '{' por si acaso hay basura al inicio
        // const posicionUltimoJSON = textoBruto.lastIndexOf('{');
        // const jsonLimpio = textoBruto.substring(posicionUltimoJSON);

        // const data = JSON.parse(jsonLimpio);

        if (data.status === "success") {
            // USAMOS ALERT PARA DETENER LA REDIRECCIÓN Y QUE PUEDAS LEER
            alert("REGISTRO EXITOSO: " + data.mensaje);
            window.location.href = "login.php";
        } else {
            errorMsg.textContent = data.mensaje;
            errorMsg.style.display = "block";
        }
                    // window.location.href = "login.php"; // Comentado para que no se mueva

    // console.log("Respuesta bruta del servidor:", textoRespuesta);

        // Intentar convertir ese texto a JSON
        // const resultado = JSON.parse(textoRespuesta);

                // if (data.status === "success" || data.mensaje === "Registro completado") {
                //     alert("¡Registro exitoso!");
                //     // window.location.href = "login.php";
                // } else {
                //     mostrarError(data.mensaje);
                // }

            } catch (err) {
                console.error(err);
                alert("Error de conexión con el servidor.");
            }

            // Función auxiliar para mostrar errores
            // function mostrarError(txt) {
            //     errorMsg.textContent = txt;
            //     errorMsg.style.display = "block";
            // }



});