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
        // Obtener el formulario de registro por su ID
        const formRegistro = document.getElementById("formRegistro");
        // Verificar si el formulario existe en la página antes de agregar el evento
        if (formRegistro) {
            formRegistro.addEventListener("submit", function(e) {
                e.preventDefault(); // Prevenir el envío por defecto del formulario 
                const formData = new FormData(formRegistro);
                const data = {};

                formData.forEach((value, key) => {
                    data[key] = value;
                });

                registrarUsuario(data);
            });
        }
        //Obtener el ID del usuario de la URL
        const urlParams = new URLSearchParams(window.location.search);
        const usuarioID = urlParams.get('id');
        //Mostrar el ID del usuario en el input
        const inputUsuarioID = document.getElementById("UsuarioID");
        if (inputUsuarioID) {
            inputUsuarioID.value = usuarioID;
        }   
        //Detalles de Usuario
        const UsuarioDiv = document.getElementById("UsuarioDiv");
        if (UsuarioDiv) {
            inputUsuarioID.value = usuarioID;
            obtenerUsuarioPorID(usuarioID)
        } 
        // Obtener el formulario de actualizarUsuario por su ID
        const formActualizarUsuario = document.getElementById("formActualizarUsuario");
        // Verificar si el formulario existe en la página antes de agregar el evento
        if (formActualizarUsuario) {
            obtenerUsuarioPorID(usuarioID);
            formActualizarUsuario.addEventListener("submit", function(e) {
                e.preventDefault(); // Prevenir el envío por defecto del formulario
                const formData = new FormData(formActualizarUsuario);
                const data = {};
                formData.forEach((value, key) => {
                    data[key] = value;
                }); 
                actualizarUsuario(data);
            });
        }
        //Obtener la tabla de usuarios
        const tablaUsuarios = document.getElementById("usuariosTable");
        if (tablaUsuarios) {
            obtenerListaUsuarios();
        }
        $(document).on('click', '.delete', function() {
            const usuarioID = $(this).data('id');
            eliminarUsuario(usuarioID);
        });
        $(document).on('click', '.Desactivar', function() {
            const usuarioID = $(this).data('id');
            desactivarUsuario(usuarioID);
        });
        $(document).on('click', '.Activar', function() {
            const usuarioID = $(this).data('id');
            activarUsuario(usuarioID);
        });
        $(document).on('click', '.CambiarClave', function() {
            const usuarioID = $(this).data('id');
            cambiarClave(usuarioID);
        });
    });
    //Funcion para registrarUsuario
    function registrarUsuario(data) {
        fetch(URL_BASE + 'registrarUsuario', {
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
            console.log(result);
        })
        .catch(error => {
            console.error('Error al registrar usuario:', error);
        });
    }
    //Funcion para actualizarUsuario
    function actualizarUsuario(data) {
        fetch(URL_BASE + 'actualizarUsuario', {
            method: 'PATCH', // Método HTTP para enviar la solicitud 
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
            console.log(result);
        })
        .catch(error => {
            console.error('Error al actualizar usuario:', error);
        }); 
    }
    // Función para obtener la lista de usuarios
    function obtenerListaUsuarios() {
        fetch(URL_BASE + 'obtenerListaUsuarios', {
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
            console.log(result);

            // Si ya hay una instancia previa de DataTable, destrúyela antes de volver a inicializar
            if ($.fn.DataTable.isDataTable('#usuariosTable')) {
                $('#usuariosTable').DataTable().destroy();
            }

            $('#usuariosTable').DataTable({
                data: result.data,
                columns: [
                    { data: 'UsuarioID' },
                    { data: 'Nombre' },
                    { data: 'Email' },
                    { 
                        data: null, 
                        render: function(data, type, row) {
                            return row.RolID + ' - ' + row.NombreRol;
                        }
                    },
                    { 
                        data: 'Estado',
                        render: function(data) {
                            return `<span class="estado-badge ${data.toLowerCase()}">${data}</span>`;
                        }
                    },
                    { data: 'FechaCreacion' },
                    { 
                        data: 'UltimoAcceso',
                        render: function(data) {
                            return data || 'Nueva Cuenta';
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        render: function(data, type, row) {
                            // Lógica para mostrar el botón de Activar o Desactivar según el Estatus
                            let activarODesactivar = row.Estado === 'Activo' 
                                ? `<button class="btn Desactivar" data-id="${row.UsuarioID}">
                                        <i class="fas fa-user-slash"></i> Desactivar
                                    </button>`
                                : `<button class="btn Activar" data-id="${row.UsuarioID}">
                                        <i class="fas fa-user-check"></i> Activar
                                    </button>`;
            
                            return `
                                <div class="action-buttons">
                                    <a href="updateUser.html?id=${row.UsuarioID}" class="edit"><i class="fas fa-edit"></i> Editar</a>

                                    <button class="btn delete" data-id="${row.UsuarioID}">
                                        <i class="fas fa-trash-alt"></i> Eliminar
                                    </button>

                                    ${activarODesactivar}  <!-- Aquí se inserta el botón Activar/Desactivar -->

                                    <a href="detailsUser.html?id=${row.UsuarioID}" class="Ver"><i class="fas fa-eye"></i> Ver</a>

                                    <button class="btn CambiarClave" data-id="${row.UsuarioID}">
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
            console.error('Error al obtener lista de usuarios:', error);
        });
    }
    //Funcion para obtenerUsuario por ID
    async function obtenerUsuarioPorID(UsuarioID) {
        return fetch(URL_BASE + 'obtenerListaUsuarios', {
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
            console.log('idUsuario: ', UsuarioID);
            try {
                console.log('result obtenerUsuarioPorID: ', result.data);
        
                const usuario = result.data.find(usuario => usuario.UsuarioID == UsuarioID);
                console.log('Usuario obtenido:', usuario);
        
                if (usuario) {
                    document.getElementById("UsuarioID").value = usuario.UsuarioID;
                    document.getElementById("Nombre").value = usuario.Nombre;
                    document.getElementById("Email").value = usuario.Email;
                    if (document.getElementById("RolName")) {
                        document.getElementById("RolName").value = usuario.RolID + ' - ' + usuario.NombreRol;
                    }
                    if (document.getElementById("RolID")) {
                        document.getElementById("RolID").value = usuario.RolID; 
                    }
                    document.getElementById("Estado").value = usuario.Estado;
                    if (document.getElementById("FechaCreacion")) {
                        document.getElementById("FechaCreacion").value = usuario.FechaCreacion;
                    }
                    if (document.getElementById("UltimoAcceso")) {
                        document.getElementById("UltimoAcceso").value = usuario.UltimoAcceso;
                    }
                    if (document.getElementById("Password")) {
                        document.getElementById("Password").value = 'hola';
                    }

                } else {
                    console.error("Usuario no encontrado");
                }
            } catch (error) {
                console.error("Error en obtenerUsuarioPorID:", error);
            }
        })
        .catch(error => {
            console.error('Error al obtener lista de usuarios:', error);
            throw error; // Importante para que el error se propague y pueda ser capturado
        });
    }
    //Funcion para eliminarUsuario
    function eliminarUsuario(UsuarioID) {
        // Confirmación del usuario antes de eliminar
        const confirmDelete = confirm("¿Estás seguro de que deseas eliminar este registro?");
        if (!confirmDelete) {
            return; // Si el usuario cancela, salimos de la función
        }
        const data = {
            UsuarioID: UsuarioID
        };
        fetch(URL_BASE + 'eliminarUsuario', {
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
            console.error('Error al eliminar usuario:', error);
        }); 
    }
    //Funcion para activarUsuario
    function activarUsuario(UsuarioID){
        // Confirmación del usuario antes de eliminar
        const confirmDelete = confirm("¿Estás seguro de Activar el estado de la cuenta?");
        if (!confirmDelete) {
            return; // Si el usuario cancela, salimos de la función
        }
        const data = {
            UsuarioID: UsuarioID,
            Estado : 'Activo'
        };
        actualizarUsuario(data)
    }
    //Funcion para desactivarUsuario
    function desactivarUsuario(UsuarioID){
        // Confirmación del usuario antes de eliminar
        const confirmDelete = confirm("¿Estás seguro de Desactivar el estado de la cuenta?");
        if (!confirmDelete) {
            return; // Si el usuario cancela, salimos de la función
        }
        const data = {
            UsuarioID: UsuarioID,
            Estado : 'Inactivo'
        };
        actualizarUsuario(data)
    }
    //Funcion para cambiarClave
    function cambiarClave(UsuarioID){
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
                UsuarioID: UsuarioID,
                Password : nuevaClave
            };
            actualizarUsuario(data)
        }
    } 

















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
        })
        .catch(error => {
            console.error('Error al obtener lista de clientes:', error);
        });
    }
    
})();
