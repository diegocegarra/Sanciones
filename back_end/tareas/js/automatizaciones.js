// 1. Referenciamos el checkbox maestro y los individuales
const selectAll = document.getElementById('selectAll');
const checkboxes = document.querySelectorAll('.task-checkbox');

// 2. Escuchamos el cambio en el checkbox maestro
selectAll.addEventListener('change', function() {
    checkboxes.forEach(cb => {
        // Hacemos que cada checkbox sea igual al estado del maestro
        cb.checked = selectAll.checked;
        
        // Opcional: Agregar una clase visual a la fila
        actualizarFila(cb);
    });
});

// 3. (Opcional) Si el usuario desmarca uno individual, desmarcar el maestro
checkboxes.forEach(cb => {
    cb.addEventListener('change', () => {
        if (!cb.checked) {
            selectAll.checked = false;
        }
        // Si todos están marcados manualmente, marcar el maestro
        const todosMarcados = Array.from(checkboxes).every(c => c.checked);
        selectAll.checked = todosMarcados;
        
        actualizarFila(cb);
    });
});

function actualizarFila(checkbox) {
    const fila = checkbox.closest('tr');
    if (checkbox.checked) {
        fila.style.backgroundColor = '#f0f7ff'; // Color de selección
    } else {
        fila.style.backgroundColor = '';
    }
}

// fechas desplegables en tareas.php

// 1. Función para mostrar/ocultar secciones
function toggleSeccion(fechaSeccion, filaClickeada) {
    // Buscamos todas las filas que tengan la clase de ese año
    const filas = document.querySelectorAll('.fechaDesplegable-' + fechaSeccion);
    const tdCabecera = filaClickeada.querySelector('.seccionFecha');
    const flechaDesplegable = filaClickeada.querySelector('.flecha-desplegable');

    // 3. Cambiamos el color del TD (Lógica de Toggle)
  let colorActual = tdCabecera.style.backgroundColor;

    // Comparamos contra el color original de tu HTML (rgba(216, 230, 255, 1))
    // Nota: El navegador a veces lo convierte a rgb(...) si la opacidad es 1
    if (colorActual === "rgba(254, 255, 255, 1)" || colorActual === "rgb(254, 255, 255)") {
        tdCabecera.style.backgroundColor = "rgba(225, 239, 253, 1)"; // Cambia a oscuro
        flechaDesplegable.innerHTML = `<strong><i class="fa-solid fa-angle-up"></i> ${fechaSeccion} </strong>`;
        
    } else {
        tdCabecera.style.backgroundColor = "rgba(254, 255, 255, 1)"; // Vuelve al original
         flechaDesplegable.innerHTML = `<span><i class="fa-solid fa-angle-down"></i> ${fechaSeccion} </span>`;
    }

    filas.forEach(fila => {
        // Si está oculta la muestra, si no la oculta
        fila.style.display = (fila.style.display === 'none') ? 'table-row' : 'none';
    });
}

// 2. Lógica para el Checkbox Maestro "Seleccionar Todos"
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.task-checkbox');
  
    checkboxes.forEach(cajaSelecionada => {
        // Solo marcar los que están visibles actualmente
        if(cajaSelecionada.closest('tr').style.display !== 'none') {
            cajaSelecionada.checked = this.checked;
          
        }
    });
});