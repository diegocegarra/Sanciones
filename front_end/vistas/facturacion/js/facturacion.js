
// AQUI VA A IR LOS VALORES QUE SON DE ESTILO O DISENO 

//para el boton de + de sanciones extra en la pagina de facturacion.php

function hoverPlus(btn) {
    // Buscamos el contenedor del círculo y el icono
    const container = btn.querySelector('.plus-container');
    const icon = btn.querySelector('.plus-icon');
    
    // Aplicamos clases de Bootstrap al contenedor
    container.classList.add('bg-primary', 'shadow-sm'); 
    
    // Si el SVG es negro, lo volvemos blanco con un filtro CSS
    icon.style.filter = 'brightness(0) invert(1)';
    // Pequeño efecto de escala solo al icono
    icon.style.transform = 'scale(1.2)';
}

function unhoverPlus(btn) {
    const container = btn.querySelector('.plus-container');
    const icon = btn.querySelector('.plus-icon');
    
    // Quitamos el azul y volvemos al estado original
    container.classList.remove('bg-primary', 'shadow-sm');
    
    // Quitamos los estilos directos
    icon.style.filter = 'none';
    icon.style.transform = 'scale(1)';
}