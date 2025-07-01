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
        const ticketID = urlParams.get('id');
        //Mostrar recompensaID en el input RecompensaID
        const inputTicketID = document.getElementById("TicketID");
        if (inputTicketID) {
            inputTicketID.value = ticketID;
        }
        //Cargar los clientes si existe la tabla clientesTable
        const ticketsTable = document.getElementById("ticketsTable");
        // Verificar si el formulario existe en la página antes de agregar el evento
        if (ticketsTable) {
            listarTickets();
        }   
    });
    //Funcion para listarTickets
    function listarTickets() {
        fetch(URL_BASE + 'listarVentas', {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        })
        .then(response => {
            if (!response.ok) throw new Error(`Error en la solicitud: ${response.status}`);
            return response.json();
        })
        .then(result => {
            console.log("Datos originales:", result.data);
    
            // 🔄 Filtrar solo una fila por VentaID (la primera ocurrencia)
            const ventasUnicas = [];
            const idsAgregados = new Set();
    
            for (const venta of result.data) {
                if (!idsAgregados.has(venta.VentaID)) {
                    ventasUnicas.push(venta);
                    idsAgregados.add(venta.VentaID);
                }
            }
    
            console.log("Ventas únicas:", ventasUnicas);
    
            if ($.fn.DataTable.isDataTable('#ticketsTable')) {
                $('#ticketsTable').DataTable().destroy();
            }
    
            $('#ticketsTable').DataTable({
                data: ventasUnicas,
                columns: [
                    { data: 'VentaID' },
                    { data: 'Cliente' },
                    { data: 'Fecha' },
                    { data: 'Total' },
                    { data: 'EstadoVenta' },
                    { data: 'MetodoPago' },
                    { data: 'Servicio' },
                    { data: 'Cantidad' },
                    { data: 'PrecioUnitario' },
                    { data: 'Subtotal' },
                    {
                        data: null,
                        orderable: false,
                        render: function(data, type, row) {
                            // Lógica para mostrar el botón de Activar o Desactivar según el Estatus
                            let activarODesactivar = row.Estado === 'Activo' 
                                ? `<button class="btn Desactivar" data-id="${row.TicketID}">
                                        <i class="fas fa-user-slash"></i> Desactivar
                                    </button>`
                                : `<button class="btn Activar" data-id="${row.TicketID}">
                                        <i class="fas fa-user-check"></i> Activar
                                    </button>`;
                            return `
                                <div class="action-buttons">
                                    <a href="updateDescuento.html?id=${row.DescuentoID}" class="sale"><i class="fas fa-cart-plus"></i> Venta</a>
                                    <a href="updateTicket.html?id=${row.TicketID}" class="edit"><i class="fas fa-edit"></i> Editar</a>
                                    <button class="btn delete" data-id="${row.TicketID}"><i class="fas fa-trash-alt"></i> Eliminar</button>
                                    <a href="detailsTicket.html?id=${row.TicketID}" class="Ver"><i class="fas fa-eye"></i> Ver</a>
                                    <button class="btn CambiarClave" data-id="${row.TicketID}"><i class="fas fa-key"></i> Cambiar clave</button>
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
            console.error('Error al obtener lista de tickets:', error);
        });
    }
    
})();