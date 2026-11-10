// AQUI VA IR TODA LA LOGICA DE JS PARA MOSTRAR LOS EMPLEADOS EN TAREAS.

      

    // esta funcion va a cambiar la pestana para ver tipo los ingresos y los ingresos al hacer click

function activarTabs() {
  const tabButtons = document.querySelectorAll('.tab-button');
  const tabContents = document.querySelectorAll('.tab-content');

  tabButtons.forEach(button => {
    button.addEventListener('click', () => {
      const targetId = button.getAttribute('data-tab');

      // Ocultar todos
      tabContents.forEach(content => content.classList.remove('active'));
      tabButtons.forEach(btn => btn.classList.remove('active'));

      // Mostrar el seleccionado
      document.getElementById(targetId).classList.add('active');
      button.classList.add('active');
    });
  });
}

  // Llamamos a la función cuando el DOM esté listo
activarTabs();



// ESTAS FUNCIONES PARA EL MENU AL HACER CLICK IZQUIERDO EN CADA VALOR DE LA TABLA DE TAREAS.PHP


const menu = document.getElementById("contextMenu");
let filaSeleccionada = null;

// Mostrar menú al hacer click derecho en una fila
document.querySelectorAll("table tbody tr").forEach(fila => {
  fila.addEventListener("contextmenu", function(e) {
    e.preventDefault();
    filaSeleccionada = this;

    // Posicionar el menú en el lugar del click
    menu.style.display = "block";
    menu.style.left = e.pageX + "px";
    menu.style.top = e.pageY + "px";
  });
});

// Ocultar menú al hacer click fuera
document.addEventListener("click", function(e) {
  if (!menu.contains(e.target)) {
    menu.style.display = "none";
  }
});

// Delegar acciones del menú
menu.addEventListener("click", function(e) {
  const action = e.target.dataset.action;
  if (!action) return;




  const id = filaSeleccionada.dataset.id;
  const nombre = filaSeleccionada.dataset.nombre;

  if (action === "editar") {
    //alert(`Editar fila: ID ${id}, Nombre ${nombre}`);
    editarFila(id, nombre);
  }
  if (action === "eliminar") {
    //alert(`Eliminar fila: ID ${id}, Nombre ${nombre}`);
    filaEliminar = filaSeleccionada;   // <— aquí guardas la fila
  eliminarFila();
  }
    if (action === "archivar") {
    alert(`Eliminar fila: ID ${id}, Nombre ${nombre}`);
  }

  menu.style.display = "none"; // cerrar menú después de la acción
});



// ELIMINAR FILAS

// Inicializar modal
const confirmarEliminarModal = new bootstrap.Modal(document.getElementById("confirmarEliminarModal"));

const mensajeEliminar = document.getElementById("mensajeEliminar");

const btnConfirmarEliminar = document.getElementById("btnConfirmarEliminar");

let filaEliminar = null; // se asigna en tu menú contextual

// Función que abre el modal de confirmación
function eliminarFila() {
    if (!filaEliminar) return;

    const id_eliminar_valor = filaEliminar.dataset.id;
    const nombre = filaEliminar.dataset.nombre;

    // Mostrar mensaje dinámico
    mensajeEliminar.textContent = `¿Seguro que deseas eliminar la fila con ID ${id_eliminar_valor} y Nombre ${nombre}?`;

    // Mostrar modal
    confirmarEliminarModal.show();

    // IMPORTANTE: Usamos .onclick para sobrescribir cualquier evento previo y evitar duplicados
    btnConfirmarEliminar.onclick = async function() {
        try {
            const response = await fetch("../../../back_end/tareas/src/funciones_jason.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
            body: `accion=eliminar_tarea&id_tarea=${encodeURIComponent(id_eliminar_valor)}`
            });

            if (!response.ok) throw new Error("Error en la red");

            const data = await response.json();

            if (data.success) {
                alert(`Fila eliminada: ${data.message}`);
                filaEliminar.remove();
                filaEliminar = null; // Limpiamos la referencia
            } else {
                alert("Error del servidor: " + data.message);
            }

        } catch (error) {
            console.error("Hubo un error:", error);
            alert("No se pudo eliminar el registro.");
        } finally {
            confirmarEliminarModal.hide();
        }
    };
} // <-- Aquí debe cerrar la función eliminarFila




// POP OUT DE CONFIRMACION

// Inicializar modal de Bootstrap
const editarModal = new bootstrap.Modal(document.getElementById("editarModal"));

function editarFila(id, nombre) {
  // Rellenar campos del modal con los datos de la fila
  document.getElementById("campoId").value = id;
  document.getElementById("campoNombre").value = nombre;

  // Mostrar modal
  editarModal.show();
}



// file uploader

