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
        //Obtener el ID del cliente de la URL
        const urlParams = new URLSearchParams(window.location.search);
        const recompensaID = urlParams.get('id');
        //Mostrar recompensaID en el input RecompensaID
        const inputRecompensaID = document.getElementById("RecompensaID");
        if (inputRecompensaID) {
            inputRecompensaID.value = recompensaID;
        }
        //Cargar los clientes si existe la tabla clientesTable
        const recompensasTable = document.getElementById("recompensasTable");
        // Verificar si el formulario existe en la página antes de agregar el evento
        if (recompensasTable) {
            listarRecompensas();
        }   
    });
    //Funcion para listarRecompensas
    function listarRecompensas() {
        fetch(URL_BASE + 'listarRecompensas', {
            method: 'GET', // Método HTTP para obtener la lista de clientes
            headers: {
                'Content-Type': 'application/json' // Indicar que los datos se envían en formato JSON
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error en la solicitud: ${response.status}`); // Manejo de errores HTTP
            }
            return response.json(); // Convertir la respuesta a JSON
        })
        .then(result => {
            console.log(result);

            // Si ya hay una instancia previa de DataTable, destrúyela antes de volver a inicializar
            if ($.fn.DataTable.isDataTable('#recompensasTable')) {
                $('#recompensasTable').DataTable().destroy();
            }

            $('#recompensasTable').DataTable({
                data: result.data,
                columns: [
                    { data: 'RecompensaID' },
                    { data: 'Descripcion' },
                    { data: 'PuntosNecesarios' },
                    { data: 'UsuarioID' },  
                    {
                        data: null,
                        orderable: false,
                        render: function(data, type, row) {
                            // Lógica para mostrar el botón de Activar o Desactivar según el Estatus
                            let activarODesactivar = row.Estado === 'Activo' 
                                ? `<button class="btn Desactivar" data-id="${row.RecompensaID}">
                                        <i class="fas fa-user-slash"></i> Desactivar
                                    </button>`
                                : `<button class="btn Activar" data-id="${row.RecompensaID}">
                                        <i class="fas fa-user-check"></i> Activar
                                    </button>`;
            
                            return `
                                <div class="action-buttons">
                                    <a href="updateRecompensa.html?id=${row.RecompensaID}" class="sale"><i class="fas fa-cart-plus"></i> Venta</a>
                                    <a href="updateRecompensa.html?id=${row.RecompensaID}" class="edit"><i class="fas fa-edit"></i> Editar</a>
                                    <button class="btn delete" data-id="${row.RecompensaID}">
                                        <i class="fas fa-trash-alt"></i> Eliminar
                                    </button>
                                    ${activarODesactivar}  <!-- Aquí se inserta el botón Activar/Desactivar -->
                                    <a href="detailsRecompensa.html?id=${row.RecompensaID}" class="Ver"><i class="fas fa-eye"></i> Ver</a>
                                    <button class="btn CambiarClave" data-id="${row.RecompensaID}">
                                        <i class="fas fa-key"></i> Cambiar clave
                                    </button>
                                </div>
                            `;
                        }
                    }
                ],
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                language: {
                    processing: "Procesando...",
                    search: "Buscar:",
                    lengthMenu: "Mostrar _MENU_ registros",
                    info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    infoEmpty: "Mostrando 0 a 0 de 0 registros",
                    infoFiltered: "(filtrado de _MAX_ registros totales)",
                    loadingRecords: "Cargando...",
                    zeroRecords: "No se encontraron resultados",
                    emptyTable: "No hay datos disponibles en la tabla",
                    paginate: {
                        first: "Primero",
                        previous: "Anterior",
                        next: "Siguiente",
                        last: "Último"
                    },
                    aria: {
                        sortAscending: ": Activar para ordenar la columna de manera ascendente",
                        sortDescending: ": Activar para ordenar la columna de manera descendente"
                    }
                },
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
                responsive: true,
                order: [[0, "desc"]]
            });
        })
        .catch(error => {
            console.error('Error al obtener lista de clientes:', error);
        });
    }
})();