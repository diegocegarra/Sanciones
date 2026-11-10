function enviar_email() {
    const form = document.getElementById('formRecuperar');
    if (!form) return; // Seguridad por si el ID cambia

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const btn = this.querySelector('button');
        const originalText = btn.innerText;
        const data_form = new FormData(this);

        // Estado de carga
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Enviando...';

        fetch("../../back_end/login/enviar_correo.php", {
            method: "POST",
            body: data_form
        })
        .then(response => {
            if (!response.ok) throw new Error("Error en el servidor");
            return response.json();
        })
        .then(data => {
            if(data.status === "success") {
                // DISPARO DE FONDO (Fire and forget)
                fetch("../../back_end/login/procesar_cron_email.php", { 
                    method: "GET",
                    keepalive: true 
                }).catch(e => console.log("Cron disparado de fondo"));

                // REDIRECCIÓN INMEDIATA
                window.location.href = "verificar_codigo.php";
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            alert("Error: " + error.message);
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    });
}

// // ¡No olvides llamarla!
// document.addEventListener('DOMContentLoaded', enviar_email);

function verificacion_codigo(){
    
document.getElementById('formVerificarToken').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // 1. Referencias a elementos UI
            const btn = this.querySelector('button');
            const data_form = new FormData(this);
            const userToken = data_form.get('token'); // Guardamos el email para pasarlo

            //const data_form = new FormData(this);
           // console.log("Enviando correo a:", data_form.get('email'));

            fetch("../../back_end/login/verificar_codigo_email.php", {
                method: "POST",
                body: data_form
            })
            .then(response => {
                // Validación extra: ¿El archivo PHP existe y respondió bien?
                if (!response.ok) throw new Error("Error en el servidor para verificar");
                return response.json();
            })
            .then(data => {
                console.log("Respuesta del servidor:", data);

                if(data.status === "success") {
                    window.location.href = "../vistas/inicio/index.php";
                } else {
                            // Si el PHP devolvió un error (ej. SMTP falló)
                    alert("Atención: " + data.message);
                    btn.disabled = false;
                
                }
            })
            .catch(error => {
                console.error("Error detectado:", error);
                alert("Error en el codigo de verificacion");
                btn.disabled = false;
              
            });
        });
}
