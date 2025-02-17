(function() {
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
    const usuario = JSON.parse(localStorage.getItem('usuario'));
    const data = {
        Email: usuario.Email,
    };
    // Se ejecuta cuando el contenido del documento ha sido completamente cargado
    document.addEventListener("DOMContentLoaded", function () {


        // Realiza una solicitud fetch para obtener el menú en formato JSON desde el servidor
        fetch(BASE_PATH + 'index.php?action=obtenerMenu', {
            method: 'POST',
            body: JSON.stringify(data),
            headers: {
                'Content-Type': 'application/json'
            },
            mode: 'cors'
        })
            .then(response => {
                // Verifica si el código de estado indica éxito
                if (!response.ok) {
                    throw new Error(`Error en la solicitud: ${response.status}`);
                }
                return response.json(); // Convierte la respuesta a JSON si es exitosa
            })
            .then(result => {
                const menuData = transformMenuData(result.data); // Transformar datos del menú
                renderMenu(menuData);
            })
            .catch(error => {
                // Manejo de errores en la solicitud
                console.error('Error al procesar la solicitud:', error.message);
                alert('Hubo un problema al procesar la solicitud. Por favor, inténtalo de nuevo.');
            });


    });
    // Función para renderizar el menú
    function renderMenu(menuData) {
        // Obtener los contenedores donde se insertarán los elementos del menú
        const menuContainer = document.getElementById("menu");
        const mobileMenuContainer = document.getElementById("mobileMenuList");
        // Verificamos que los contenedores existen
        if (!menuContainer || !mobileMenuContainer) {
            console.error("No se encontraron los contenedores del menú."); // Si no se encuentran, mostrar error
            return;
        }
        // Agregar clases CSS a los contenedores para aplicar estilos específicos
        menuContainer.classList.add("nav-menu");
        mobileMenuContainer.classList.add("mobile-nav-list");
        // Iterar sobre los datos del menú y generar los elementos correspondientes
        menuData.forEach(menu => {
            // Crear los elementos del menú principal y móvil
            const menuItem = createMenuItem(menu); // Crear un ítem del menú
            const mobileMenuItem = createMenuItem(menu); // Crear un ítem para el menú móvil

            // Añadir los ítems al contenedor correspondiente
            menuContainer.appendChild(menuItem);
            mobileMenuContainer.appendChild(mobileMenuItem);
        });
        // Agregar el botón de cerrar sesión
        addLogoutButton(menuContainer, mobileMenuContainer);
    }
    // Función para agregar el botón de cerrar sesión
    function addLogoutButton(menuContainer, mobileMenuContainer) {
        const logoutItem = document.createElement("li");
        logoutItem.classList.add("nav-item");
        const logoutLink = document.createElement("a");
        logoutLink.href = `${BASE_PATH}index.html`; // No importa el href porque controlamos el comportamiento con JS
        logoutLink.textContent = "Salir";
        logoutLink.classList.add("nav-link", "logout-btn");
        // Asignar el evento de clic
        logoutLink.onclick = function(event) {
            event.preventDefault(); // Prevenir la acción predeterminada (seguir el enlace)
            cerrarSesion(); // Llamamos a la función para cerrar sesión
            window.location.href = `${BASE_PATH}index.html`; // Redirigimos a la página de inicio después de cerrar sesión
        };
        logoutItem.appendChild(logoutLink);
        menuContainer.appendChild(logoutItem);
        // Agregar también al menú móvil
        const mobileLogoutItem = logoutItem.cloneNode(true);
        mobileMenuContainer.appendChild(mobileLogoutItem);
    }
    // Función para crear un ítem del menú
    function createMenuItem(menu) {
        // Crear un elemento <li> para el ítem del menú
        const menuItem = document.createElement("li");
        menuItem.classList.add("nav-item");
        // Crear un enlace <a> para el ítem del menú
        const menuLink = document.createElement("a");
        menuLink.href = `${BASE_PATH}${menu.url}`; // La URL del enlace
        menuLink.textContent = menu.nombre; // El nombre del menú
        menuLink.classList.add("nav-link"); // Añadir clase CSS
        menuItem.appendChild(menuLink); // Añadir el enlace al ítem del menú
        // Si el menú tiene submenús, generarlos también
        if (menu.submenus && menu.submenus.length > 0) {
            // Crear un contenedor <ul> para los submenús
            const subMenuList = document.createElement("ul");
            subMenuList.classList.add("submenu");
            // Iterar sobre los submenús y crear los elementos correspondientes
            menu.submenus.forEach(sub => {
                const subItem = document.createElement("li");
                subItem.classList.add("submenu-item");
                // Crear un enlace para el submenú
                const subLink = document.createElement("a");
                subLink.href = `${BASE_PATH}${sub.url}`; // URL del submenú
                subLink.textContent = sub.nombre; // Nombre del submenú
                subLink.classList.add("submenu-link"); // Añadir clase CSS
                subItem.appendChild(subLink); // Añadir el enlace al ítem del submenú
                subMenuList.appendChild(subItem); // Añadir el ítem al contenedor de submenús
            });
            menuItem.appendChild(subMenuList); // Añadir la lista de submenús al ítem principal
        }
        return menuItem; // Devolver el ítem del menú
    }
    /**
     * Función para alternar el menú móvil (abrir/cerrar)
     */
    function toggleMenu() {
        const mobileMenu = document.getElementById("mobileMenu"); // Obtener el contenedor del menú móvil
        mobileMenu.classList.toggle("active"); // Alternar la clase "active" para mostrar u ocultar el menú
    }
    /**
     * Transforma la estructura de `result.data` en un árbol de menús y submenús
     */
    function transformMenuData(menuItems) {
        const menuMap = {};

        menuItems.forEach(item => {
            if (!menuMap[item.menu_id]) {
                menuMap[item.menu_id] = {
                    id: item.menu_id,
                    nombre: item.menu,
                    url: item.menu_url,
                    submenus: []
                };
            }

            if (item.sub_id) {
                menuMap[item.menu_id].submenus.push({
                    id: item.sub_id,
                    nombre: item.sub_menu,
                    url: item.sub_url
                });
            }
        });

        return Object.values(menuMap); // Convertimos el objeto en un array de menús
    }

    function cerrarSesion() {
        // Elimina la información del usuario de localStorage
        localStorage.removeItem('usuario');
        window.location.href = `${BASE_PATH}index.html`;
    }
})();
function toggleMenu() {
    const mobileMenu = document.getElementById("mobileMenu"); // Obtener el contenedor del menú móvil
    mobileMenu.classList.toggle("active"); // Alternar la clase "active" para mostrar u ocultar el menú
}