(function() {
    const CONFIG = {
        // Definición de la configuración para los entornos de desarrollo y producción
        development: {
            BASE_PATH: "http://localhost/DigitalOcean/RapiClean/" // Ruta base en entorno local
        },
        production: {
            BASE_PATH: "https://app.rapicleanpuebla.com/" // Ruta base en entorno en línea
        }
    };

    // Determinar el entorno en el que se ejecuta la aplicación
    const ENV = window.location.hostname.includes("localhost") ? "development" : "production";

    // Definir la ruta base según el entorno
    const BASE_PATH = CONFIG[ENV].BASE_PATH;

    // Esperamos que el documento se haya cargado completamente antes de ejecutar la carga de los componentes
    document.addEventListener('DOMContentLoaded', () => {
        loadComponent('header', 'components/head.html'); // Cargar el encabezado
        loadComponent('menul', 'components/menu.html'); // Cargar el menú
        loadComponent('footer', 'components/footer.html'); // Cargar el pie de página
    });

    // Función para cargar componentes HTML dinámicamente
    async function loadComponent(placeholderId, componentUrl) {
        try {
            // Construcción de la URL absoluta para obtener el componente
            const absoluteUrl = new URL(`${BASE_PATH}${componentUrl}`, window.location.origin);
            
            // Realizamos la solicitud fetch para obtener el archivo HTML
            const response = await fetch(absoluteUrl.href);
            
            // Verificamos si la respuesta fue exitosa
            if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
            
            // Obtenemos el contenido HTML del componente
            const html = await response.text();
            
            // Buscamos el contenedor en el DOM donde se insertará el componente
            const placeholder = document.getElementById(placeholderId);
            
            // Si encontramos el contenedor, insertamos el HTML y ejecutamos acciones posteriores
            if (placeholder) {
                placeholder.innerHTML = html;
                postLoadActions(placeholderId); // Acciones a ejecutar después de cargar el componente
            }
        } catch (error) {
            // En caso de error, mostramos un mensaje en la consola y un mensaje de error en el placeholder
            console.error(`Error cargando ${componentUrl}:`, error);
            showErrorPlaceholder(placeholderId); // Mostrar mensaje de error en el placeholder
        }
    }
    // Función para ejecutar acciones específicas después de cargar un componente
    function postLoadActions(componentId) {
        switch(componentId) {
            case 'header': // Si es el componente de cabecera
                loadPageSpecificCSS(); // Cargar el CSS específico de la página
                break;
        }
    }
    // Función para cargar un archivo CSS específico para la página actual
    function loadPageSpecificCSS() {
        const pageName = document.body.dataset.pageName; // Obtenemos el nombre de la página desde un atributo del body
        if (pageName) {
            // Creamos un enlace de estilo para la página
            const cssLink = document.createElement('link');
            cssLink.rel = 'stylesheet';
            cssLink.href = `${BASE_PATH}assets/css/${pageName}.css`; // Ruta del archivo CSS
            document.head.appendChild(cssLink); // Añadimos el enlace al head del documento
        }
    }
})();
