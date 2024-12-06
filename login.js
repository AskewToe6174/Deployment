function nuevaMinuta(event) {
    // Prevenir el comportamiento por defecto
    event.preventDefault();
  
    // Capturar el formulario usando el ID
    const form = document.getElementById('nuevaMinuta');
  
    // Crear un objeto con los datos del formulario
    const formData = new FormData(form);
    
    // Convertir los datos a un objeto JSON
    const data = Object.fromEntries(formData.entries());
    
    // Mostrar los datos en consola (o hacer otra cosa con ellos)
    console.log(data);
    form.reset()
  }
  
  
document.addEventListener("DOMContentLoaded", function() {
    document.querySelector("form").addEventListener('submit', e => {
        e.preventDefault();

        // Convertir los datos del formulario a un objeto
        const data = Object.fromEntries(new FormData(e.target));

        // Verificar si existe el campo 'psw' (contraseña) y encriptarlo con SHA-256
        if (data.psw) {
            data.psw = CryptoJS.SHA256(data.psw).toString(); // Encriptar la contraseña
        }
        

        // Enviar los datos al servidor a través de AJAX
        $.ajax({
            url: 'http://localhost/api/public/login.php',  // Archivo PHP en el servidor
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data), // Convertir los datos a JSON
            statusCode: {
                200: function(response) {
                    var responseData = JSON.parse(response);
                    Swal.fire({
                        icon: 'success',
                        title: responseData.message,
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    }).then((result) => {
                        // Redireccionar a otra página después de que la alerta se cierre
                        if (result.dismiss === Swal.DismissReason.timer) {
                            let url = responseData.url;
                            window.location.href = `${url}`; // Cambia la URL al destino deseado
                        }
                    });
                },
                400: function(jqXHR) {
                    handleError('Solicitud incorrecta', jqXHR.responseText);
                },
                401: function(jqXHR) {
                    handleError('No autorizado', jqXHR.responseText);
                },
                500: function(jqXHR) {
                    handleError('Error en el servidor', jqXHR.responseText);
                }
                // Manejar otros códigos de error si es necesario
            },
            error: function(jqXHR, status, error) {
                console.error('Error:', status, error);
                handleError('Error desconocido', jqXHR.responseText);
            }
        });
        
        function handleError(message, responseText) {
            let responseData;
            try {
                responseData = JSON.parse(responseText);
            } catch (e) {
                responseData = { message: 'Error al procesar la respuesta' };
            }
            
            Swal.fire({
                icon: 'error',
                title: message,
                text: responseData.details || 'Detalles no disponibles',
                timer: 2000,
                timerProgressBar: true
            });
        }
   
    });
});
