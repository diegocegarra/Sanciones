// El archivo JS ya puede ver "DATA_GRAFICO" porque fue declarada antes
// Esperamos a que el DOM esté cargado para evitar errores de "canvas no encontrado"
document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Buscamos el elemento canvas en tu HTML
    // const ctx = document.getElementById('graficoTareas'); 

    // if (!ctxBar_finalizada) {
    //     console.error("No se encontró el elemento con ID 'graficoTareas'");
    //     return;
    // }


// --- CONFIGURACIÓN GRÁFICO DE BARRAS ---
const ctxBar_finalizada = document.getElementById('graficoTareas').getContext('2d');
const barGradient_finalizada = ctxBar_finalizada.createLinearGradient(0, 0, 0, 400);
barGradient_finalizada.addColorStop(0, '#5e5ce6'); // Azul más claro arriba
barGradient_finalizada.addColorStop(1, '#7a78ff'); // Azul más suave abajo
// Creamos un degradado para las barras




// >>>>>>>>>>>>>>>>>BARRA DE ESTATUS CUANDO SE PONE EN FINALIZADO

new Chart(ctxBar_finalizada, {
    type: 'bar',
    data: {
        labels: DATA_GRAFICO.labels, 
        datasets: [{
            data: DATA_GRAFICO.values,
            backgroundColor: barGradient_finalizada,
            borderRadius: 10, // Bordes redondeados como en la imagen
            borderSkipped: false,
            barPercentage: 0.6 // Hace las barras más delgadas
        }]
    },
    options: {
        plugins: { legend: { display: false } }, // Ocultar leyenda
        scales: {
            y: { beginAtZero: true, grid: { display: false } },
            x: { grid: { display: false } }
        }
    }
});




//>>>>>>>>>>>>>>>> BARRA DE FACTURACION UT EGRESOS


// --- CONFIGURACIÓN GRÁFICO DE BARRAS ---
const ctxBar = document.getElementById('graficoFacturacion_egresos').getContext('2d');

// Creamos un degradado para las barras
const barGradient = ctxBar.createLinearGradient(0, 0, 0, 400);
barGradient.addColorStop(0, '#5e5ce6'); // Azul más claro arriba
barGradient.addColorStop(1, '#7a78ff'); // Azul más suave abajo





new Chart(ctxBar, {
    type: 'bar',
    data: {
        labels: DATA_GRAFICO_FACTURACION.labels, 
        datasets: [{
            data: DATA_GRAFICO_FACTURACION.values_egresos,
            backgroundColor: barGradient,
            borderRadius: 10, // Bordes redondeados como en la imagen
            borderSkipped: false,
            barPercentage: 0.6 // Hace las barras más delgadas
        }]
    },
    options: {
        plugins: { legend: { display: false } }, // Ocultar leyenda
        scales: {
            y: { beginAtZero: true, grid: { display: false } },
            x: { grid: { display: false } }
        }
    }
});

//>>>>>>>>>>>>>>>>>>>>BARRA DE FACTURACION UT INGRESOS




// // --- CONFIGURACIÓN GRÁFICO DE LÍNEA ---
const ctxLine = document.getElementById('graficoFacturacion').getContext('2d');

// Degradado para el área bajo la línea
const lineGradient = ctxLine.createLinearGradient(0, 0, 0, 200);
lineGradient.addColorStop(0, 'rgba(94, 92, 230, 0.4)');
lineGradient.addColorStop(1, 'rgba(94, 92, 230, 0)');

new Chart(ctxLine, {
    type: 'line',
    data: {
        labels: DATA_GRAFICO_FACTURACION.labels, 
        datasets: [{

            data: DATA_GRAFICO_FACTURACION.values,
            borderColor: '#5e5ce6',
            borderWidth: 3,
            fill: true,
            backgroundColor: lineGradient,
            tension: 0.4, // Esto hace la línea curva (suave)
            pointRadius: 4,
            pointBackgroundColor: '#fff',
            pointBorderColor: '#5e5ce6',
            pointBorderWidth: 2
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        scales: {
            y: { display: false }, // Ocultar eje Y para estilo minimalista
            x: { grid: { display: false } }
        }
    }
});



// fin del doom loader
   });