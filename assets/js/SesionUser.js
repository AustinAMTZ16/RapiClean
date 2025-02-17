(function () {
    // Definición de la configuración para los entornos de desarrollo y producción  
    const CONFIG = {
        development: {
            BASE_PATH: "http://localhost/DigitalOcean/RapiClean/" // Ruta base en entorno local 
        },
        production: {
            BASE_PATH: "https://app.rapicleanpuebla.com/" // Ruta base en entorno en línea  
        }
    };  
    // Detectar entorno
    const ENV = window.location.hostname.includes("localhost") ? "development" : "production";
    const BASE_PATH = CONFIG[ENV].BASE_PATH;
    // Evento para cargar el contenido de la página
    document.addEventListener("DOMContentLoaded", function () {
        // Verificar si hay una sesión activa
        if (!sesionActiva()) {
            window.location.href = `${BASE_PATH}index.html`;
        }
    });
    // Función para comprobar si hay una sesión activa
    function sesionActiva() {
        // Obtiene la información del usuario desde localStorage
        const usuario = localStorage.getItem('usuario');
        // Verifica si usuario es una cadena no vacía y puede ser parseada a un objeto JSON
        if (usuario && usuario.trim() !== "") {
            try {
                const usuarioObj = JSON.parse(usuario);
                // Verifica si usuarioObj es un objeto
                return (typeof usuarioObj === 'object' && usuarioObj !== null);
            } catch (error) {
                // Error al parsear JSON, por lo tanto no es un objeto válido
                return false;
            }
        }
        // Si la información del usuario no existe o no es válida, la sesión no está activa
        return false;
    }
})();