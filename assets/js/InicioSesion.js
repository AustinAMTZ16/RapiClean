(function() {
    // Definición de la configuración para los entornos de desarrollo y producción
    let CONFIG = {
        development: {
            BASE_PATH: "http://localhost/DigitalOcean/RapiClean/" // Ruta base en entorno local
        },
        production: {
            BASE_PATH: "https://app.rapicleanpuebla.com/" // Ruta base en entorno en línea
        }
    };

    // Determinar el entorno en el que se ejecuta la aplicación
    let ENV = window.location.hostname.includes("localhost") ? "development" : "production";

    // Definir la ruta base según el entorno
    let BASE_PATH = CONFIG[ENV].BASE_PATH;

    // URL base para realizar peticiones al backend
    let URL_BASE = `${BASE_PATH}index.php?action=`;

    // Evento que se ejecuta cuando el DOM ha sido completamente cargado
    document.addEventListener("DOMContentLoaded", function() {
        // Obtener el formulario de inicio de sesión por su ID
        const formInicioSesion = document.getElementById("formInicioSesion");

        // Verificar si el formulario existe en la página antes de agregar el evento
        if (formInicioSesion) {
            formInicioSesion.addEventListener("submit", function(e) {
                e.preventDefault(); // Prevenir el envío por defecto del formulario

                // Crear un objeto FormData con los datos del formulario
                const formData = new FormData(formInicioSesion);
                const data = {};

                // Convertir los datos del formulario en un objeto JavaScript
                formData.forEach((value, key) => {
                    data[key] = value;
                });

                // Llamar a la función de inicio de sesión enviando los datos
                inicioSesion(data);
            });
        }
    });
    /**
     * Función para realizar la petición de inicio de sesión
     * @param {Object} data - Datos del formulario de inicio de sesión
     */
    function inicioSesion(data) {
        fetch(URL_BASE + 'iniciarSesion', {
            method: 'POST', // Método HTTP para enviar la solicitud
            headers: {
                'Content-Type': 'application/json' // Indicar que los datos se envían en formato JSON
            },
            body: JSON.stringify(data) // Convertir el objeto a una cadena JSON
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error en la solicitud: ${response.status}`); // Manejo de errores HTTP
            }
            return response.json(); // Convertir la respuesta a JSON
        })
        .then(result => {
            // Almacenar los datos del usuario en localStorage
            localStorage.setItem('usuario', JSON.stringify(result.data));

            // Obtener el ID del rol del usuario y convertirlo a número
            let rolID = Number(result.data.RolID);

            // Redirigir al usuario según su rol
            switch(rolID) {
                case 1:
                    window.location.href = 'views/dashboard.html'; // Redirigir a dashboard
                    break;
                case 2:
                    window.location.href = 'views/usuarios/usuarios.html'; // Redirigir a gestión de usuarios
                    break;
                default:
                    alert('No se pudo determinar el rol del usuario.'); // Mensaje de error si el rol es desconocido
                    break;
            }
        })
        .catch(error => {
            // Manejo de errores en caso de fallo en la solicitud
            alert('Hubo un problema al procesar la solicitud. Por favor, inténtalo de nuevo.');
        });
    }
})();
