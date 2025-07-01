(function () {
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
    document.addEventListener("DOMContentLoaded", function () {
        //Obtener el ID del cliente de la URL
        const urlParams = new URLSearchParams(window.location.search);
        const clienteID = urlParams.get('id');
        //Mostrar clienteID en el input ClienteID
        const inputClienteID = document.getElementById("ClienteID");
        if (inputClienteID) {
            inputClienteID.value = clienteID;
        }
        //Cargar los clientes si existe la tabla clientesTable
        const clientesTable = document.getElementById("clientesTable");
        // Verificar si el formulario existe en la página antes de agregar el evento
        if (clientesTable) {
            obtenerListaClientes();

        }
        //Cargar el formulario de creación de cliente
        const formCrearCliente = document.getElementById("formCrearCliente");
        if (formCrearCliente) {
            formCrearCliente.addEventListener("submit", function (e) {
                e.preventDefault(); // Prevenir el envío por defecto del formulario 
                const formData = new FormData(formCrearCliente);
                const data = {};
                formData.forEach((value, key) => {
                    data[key] = value;
                });
                registrarCliente(data);
            });
        }
        //Cargar el formulario de modificar cliente
        const formModificarCliente = document.getElementById("formModificarCliente");
        if (formModificarCliente) {
            obtenerClientePorID(clienteID);
            formModificarCliente.addEventListener("submit", function (e) {
                e.preventDefault(); // Prevenir el envío por defecto del formulario 
                const formData = new FormData(formModificarCliente);
                const data = {};
                formData.forEach((value, key) => {
                    data[key] = value;
                });
                actualizarCliente(data);
            });
        }

        $(document).on('click', '.delete', function () {
            const clienteID = $(this).data('id');
            eliminarCliente(clienteID);
        });
        $(document).on('click', '.Desactivar', function () {
            const clienteID = $(this).data('id');
            desactivarCliente(clienteID);
        });
        $(document).on('click', '.Activar', function () {
            const clienteID = $(this).data('id');
            activarCliente(clienteID);
        });
        $(document).on('click', '.CambiarClave', function () {
            const clienteID = $(this).data('id');
            cambiarClave(clienteID);
        });
    });
    //Funcion para obtenerListaClientes
    function obtenerListaClientes() {
        fetch(URL_BASE + 'obtenerListaClientes', {
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
                if ($.fn.DataTable.isDataTable('#clientesTable')) {
                    $('#clientesTable').DataTable().destroy();
                }

                $('#clientesTable').DataTable({
                    data: result.data,
                    columns: [
                        { data: 'ClienteID' },
                        { data: 'Nombre' },
                        {
                            data: 'Email',
                            render: function (data) {
                                // Validar si el dato existe y no está vacío
                                if (!data) {
                                    return ''; // Si no hay correo, devuelve una cadena vacía
                                }

                                // Crear el enlace de correo electrónico
                                return `<a href="mailto:${data}" target="_blank">${data}</a>`;
                            }
                        },
                        {
                            data: 'Telefono',
                            render: function (data) {
                                // Validar si el dato existe y no está vacío
                                if (!data) {
                                    return ''; // Si no hay teléfono, devuelve una cadena vacía
                                }

                                // Asegúrate de que el número de teléfono esté en el formato correcto
                                const telefono = data.replace(/\D/g, ''); // Elimina caracteres no numéricos

                                // Crear el enlace de WhatsApp
                                return `<a href="https://api.whatsapp.com/send/?phone=521${telefono}&text&type=phone_number&app_absent=0" target="_blank">${telefono}</a>`;
                            }
                        },
                        { data: 'PuntosRecompensa' },
                        {
                            data: 'Estado',
                            render: function (data) {
                                return `<span class="estado-badge ${data.toLowerCase()}">${data}</span>`;
                            }
                        },
                        {
                            data: 'FechaCreacion',
                            render: function (data) {
                                if (!data) return ''; // Evita el error si es null o undefined

                                try {
                                    const fecha = data.split(' ')[0];
                                    const [anio, mes, dia] = fecha.split('-');
                                    return `${dia}-${mes}-${anio}`;
                                } catch (error) {
                                    console.warn('Formato de fecha no válido:', data);
                                    return data; // Muestra la fecha original si no se puede procesar
                                }
                            }
                        },
                        {
                            data: null,
                            orderable: false,
                            render: function (data, type, row) {
                                // Lógica para mostrar el botón de Activar o Desactivar según el Estatus
                                let activarODesactivar = row.Estado === 'Activo'
                                    ? `<button class="btn Desactivar" data-id="${row.ClienteID}">
                                        <i class="fas fa-user-slash"></i> Desactivar
                                    </button>`
                                    : `<button class="btn Activar" data-id="${row.ClienteID}">
                                        <i class="fas fa-user-check"></i> Activar
                                    </button>`;

                                return `
                                <div class="action-buttons">
                                    <a href="updateSale.html?id=${row.ClienteID}" class="sale"><i class="fas fa-cart-plus"></i> Venta</a>
                                    <a href="updateCustomer.html?id=${row.ClienteID}" class="edit"><i class="fas fa-edit"></i> Editar</a>
                                    <button class="btn delete" data-id="${row.ClienteID}">
                                        <i class="fas fa-trash-alt"></i> Eliminar
                                    </button>
                                    ${activarODesactivar}  <!-- Aquí se inserta el botón Activar/Desactivar -->
                                    <a href="detailsCustomer.html?id=${row.ClienteID}" class="Ver"><i class="fas fa-eye"></i> Ver</a>
                                    <button class="btn CambiarClave" data-id="${row.ClienteID}">
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
    //Funcion para registrar un cliente
    function registrarCliente(data) {
        fetch(URL_BASE + 'registrarCliente', {
            method: 'POST', // Método HTTP para registrar un cliente
            headers: {
                'Content-Type': 'application/json' // Indicar que los datos se envían en formato JSON
            },
            body: JSON.stringify(data)
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error en la solicitud: ${response.status}`); // Manejo de errores HTTP
                }
                return response.json(); // Convertir la respuesta a JSON
            })
            .then(result => {
                console.log(result);
            })
            .catch(error => {
                console.error('Error al registrar cliente:', error);
            });
    }
    //Funcion para obtener un cliente por su ID
    async function obtenerClientePorID(clienteID) {
        return fetch(URL_BASE + 'obtenerListaClientes', {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error en la solicitud: ${response.status}`);
                }
                return response.json();
            })
            .then(result => {
                console.log('idCliente: ', clienteID);
                try {
                    console.log('result obtenerClientePorID: ', result.data);

                    const cliente = result.data.find(cliente => cliente.ClienteID == clienteID);
                    console.log('Cliente obtenido:', cliente);

                    if (cliente) {
                        document.getElementById("ClienteID").value = cliente.ClienteID;
                        document.getElementById("Nombre").value = cliente.Nombre;
                        document.getElementById("Email").value = cliente.Email;
                        document.getElementById("Telefono").value = cliente.Telefono;
                        document.getElementById("Direccion").value = cliente.Direccion;
                        document.getElementById("Ciudad").value = cliente.Ciudad;
                        document.getElementById("Pais").value = cliente.Pais;
                        document.getElementById("Password").value = cliente.Password;
                        document.getElementById("PuntosRecompensa").value = cliente.PuntosRecompensa;
                        document.getElementById("Estado").value = cliente.Estado;

                    } else {
                        console.error("Cliente no encontrado");
                    }
                } catch (error) {
                    console.error("Error en obtenerClientePorID:", error);
                }
            })
            .catch(error => {
                console.error('Error al obtener lista de clientes:', error);
                throw error; // Importante para que el error se propague y pueda ser capturado
            });
    }
    //Funcion para actualizar un cliente
    function actualizarCliente(data) {
        fetch(URL_BASE + 'actualizarCliente', {
            method: 'PATCH', // Método HTTP para actualizar un cliente
            headers: {
                'Content-Type': 'application/json' // Indicar que los datos se envían en formato JSON
            },
            body: JSON.stringify(data)
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error en la solicitud: ${response.status}`);
                }
                return response.json();
            })
            .then(result => {
                console.log('result actualizarCliente: ', result);
            })
            .catch(error => {
                console.error('Error al actualizar cliente:', error);
            });
    }
    //Funcion para eliminarCliente
    function eliminarCliente(ClienteID) {
        // Confirmación del usuario antes de eliminar
        const confirmDelete = confirm("¿Estás seguro de que deseas eliminar este registro?");
        if (!confirmDelete) {
            return; // Si el usuario cancela, salimos de la función
        }
        const data = {
            ClienteID: ClienteID
        };
        fetch(URL_BASE + 'eliminarCliente', {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error en la solicitud: ${response.status}`);
                }
                return response.json();
            })
            .then(result => {
                console.log(result);
            })
            .catch(error => {
                console.error('Error al eliminar cliente:', error);
            });
    }
    //Funcion para activarCliente
    function activarCliente(ClienteID) {
        // Confirmación del usuario antes de eliminar
        const confirmDelete = confirm("¿Estás seguro de Activar el estado de la cuenta?");
        if (!confirmDelete) {
            return; // Si el usuario cancela, salimos de la función
        }
        const data = {
            ClienteID: ClienteID,
            Estado: 'Activo'
        };
        actualizarCliente(data)
    }
    //Funcion para desactivarCliente
    function desactivarCliente(ClienteID) {
        // Confirmación del usuario antes de eliminar
        const confirmDelete = confirm("¿Estás seguro de Desactivar el estado de la cuenta?");
        if (!confirmDelete) {
            return; // Si el usuario cancela, salimos de la función
        }
        const data = {
            ClienteID: ClienteID,
            Estado: 'Inactivo'
        };
        actualizarCliente(data)
    }
    //Funcion para cambiarClave
    function cambiarClave(ClienteID) {
        const nuevaClave = prompt("Por favor, ingrese la nueva clave de la cuenta:");
        if (nuevaClave === null) {
            // El usuario hizo clic en "Cancelar"
            alert("Operación cancelada. No se cambió la clave.");
            return;
        } else if (nuevaClave.trim() === "") {
            // El usuario no ingresó nada
            alert("Debe ingresar una clave válida.");
            return;
        } else {
            // El usuario ingresó una clave

            const data = {
                ClienteID: ClienteID,
                Password: nuevaClave
            };
            actualizarCliente(data)
        }
    }
})();
