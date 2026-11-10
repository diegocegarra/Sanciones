// Función para activar una pestaña específica
const activateTab = (tabId) => {
  // 1. Ocultar todos los paneles
  document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.add('d-none'));
  
  // 2. Mostrar el panel seleccionado
  const activePane = document.getElementById(tabId);
  if (activePane) {
    activePane.classList.remove('d-none');
  }
};

// Lógica de los botones
document.querySelectorAll('.tab-btn').forEach(button => {
  button.addEventListener('click', () => {
    // Cambiar estilos de los botones
    document.querySelectorAll('.tab-btn').forEach(btn => {
      btn.classList.remove('text-primary', 'border-bottom', 'border-primary', 'border-3');
      btn.classList.add('text-muted');
    });

    button.classList.add('text-primary', 'border-bottom', 'border-primary', 'border-3');
    button.classList.remove('text-muted');

    // Cambiar visibilidad
    const tabId = button.getAttribute('data-tab');
    activateTab(tabId);
  });
});

// --- VALIDACIÓN AL RECARGAR ---
document.addEventListener('DOMContentLoaded', () => {
  // Busca el botón que tiene la clase de color azul (el activo por defecto en tu HTML)
  const defaultActiveBtn = document.querySelector('.tab-btn.text-primary');
  
  if (defaultActiveBtn) {
    const defaultTabId = defaultActiveBtn.getAttribute('data-tab');
    activateTab(defaultTabId);
  }
});




/// FATURACION


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

function concurrirSancion(){


}