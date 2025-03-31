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
    // Datos de la base de datos
    let clientes = [];
    let servicios = [];
    let negocio = [
        {
            "nombre": "Rapiclean",
            "direccion": "Av. Siempre Viva 123",
            "telefono": "2221234567",
            "email": "rapiclean@gmail.com",
            "logo": "../../assets/images/logoprincipal.png"
        }
    ];
    let carrito = [];
    let clienteSeleccionado = null;
    // Clases
    class Cliente {
        constructor(nombre, telefono, email) {
            this.id = clientes.length + 1;
            this.nombre = nombre;
            this.telefono = telefono;
            this.email = email;
        }
    }
    class Servicio {
        constructor(id, cantidad) {
            this.id = id;
            this.cantidad = cantidad;
            this.precio = servicios.find(s => s.id === id).precio;
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        iniciarApp();
    });
    // Inicializar la aplicación
    async function iniciarApp() {
        const btnFinalizarVenta = document.getElementById("btnFinalizarVenta");
    
        if (btnFinalizarVenta) {
            btnFinalizarVenta.addEventListener("click", function (e) {
                e.preventDefault();
            });
        } else {
            console.warn("Botón 'btnFinalizarVenta' no encontrado en el DOM.");
        }
    
        // ✅ Esperamos a que se carguen los datos primero
        await cargarDatosBase();
    
        // ✅ Luego sí pintamos la tabla con los servicios ya disponibles
        cargarServicios();
    }
    // Registrar venta
    function registrarVenta(data) {
        fetch(URL_BASE + 'registrarVenta', {
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
            // Manejo de errores en caso de fallo en la solicitud
            alert('Hubo un problema al procesar la solicitud. Por favor, inténtalo de nuevo.');
        });
    }    
    // Inicializar tabla de servicios
    function cargarServicios() {
        let tbody = document.querySelector('#tablaServicios tbody');
        servicios.forEach(servicio => {
            let tr = document.createElement('tr');
            tr.innerHTML = `
                <td><input type="checkbox" onchange="actualizarCarrito(${servicio.id}, this)"></td>
                <td>${servicio.nombre}</td>
                <td>$${servicio.precio.toFixed(2)}</td>
                <td><input type="number" min="1" value="1" id="cantidad-${servicio.id}"></td>
            `;
            tbody.appendChild(tr);
        });
    }
    // Funcionalidades de Clientes
    function buscarCliente() {
        let busqueda = document.getElementById('buscarCliente').value.toLowerCase();
        let resultados = clientes.filter(cliente =>
            cliente.nombre.toLowerCase().includes(busqueda) ||
            cliente.telefono.includes(busqueda) ||
            cliente.email.toLowerCase().includes(busqueda)
        );

        let html = resultados.map(cliente => `
            <div class="cliente-item" onclick="seleccionarCliente(${cliente.id})">
                ${cliente.nombre} - ${cliente.telefono} - ${cliente.email} 
            </div>
        `).join('');

        document.getElementById('resultadosClientes').innerHTML = html;
    }
    // Funcionalidades de Modal de Cliente
    function abrirModalCliente() {
        document.getElementById('modalCliente').style.display = 'block';
    }
    // Cerrar Modal de Cliente
    function cerrarModalCliente() {
        document.getElementById('modalCliente').style.display = 'none';
    }
    // Guardar Cliente
    function guardarCliente() {
        let nuevoCliente = new Cliente(
            document.getElementById('nombreCliente').value,
            document.getElementById('telefonoCliente').value,
            document.getElementById('emailCliente').value
        );
        clientes.push(nuevoCliente);
        registrarCliente(nuevoCliente);
        cerrarModalCliente();
        buscarCliente();
    }
    // Seleccionar Cliente
    function seleccionarCliente(id) {
        clienteSeleccionado = id;
        document.getElementById('buscarCliente').value = clientes.find(c => c.id === id).nombre + " - " + clientes.find(c => c.id === id).telefono + " - " + clientes.find(c => c.id === id).email;
        document.getElementById('resultadosClientes').innerHTML = '';
    }
    // Funcionalidades de Venta
    function actualizarCarrito(idServicio, checkbox) {
        let cantidad = parseInt(document.getElementById(`cantidad-${idServicio}`).value);

        if (checkbox.checked) {
            carrito.push(new Servicio(idServicio, cantidad));
        } else {
            carrito = carrito.filter(item => item.id !== idServicio);
        }

        calcularTotal();
    }
    // Calcular Total
    function calcularTotal() {
        let subtotal = carrito.reduce((acc, item) => acc + (item.precio * item.cantidad), 0);
        let descuento = aplicarDescuento(document.getElementById('codigoDescuento').value);

        document.getElementById('subtotal').textContent = subtotal.toFixed(2);
        document.getElementById('descuento').textContent = descuento.toFixed(2);
        document.getElementById('total').textContent = (subtotal - descuento).toFixed(2);
    }
    // Aplicar Descuento
    function aplicarDescuento(codigo) {
        // Lógica de descuentos (ejemplo)
        if (codigo === "RAPICLEAN10") return 10;
        if (codigo === "REDESSOCIALES") return 15;
        return 0;
    }
    // Finalizar Venta
    function finalizarVenta() {
        let venta = {
            usuarioID: 1, // ID del usuario loggeado
            clienteID: clienteSeleccionado,
            metodoPago: document.getElementById('metodoPago').value,
            servicios: carrito,
            codigoDescuento: document.getElementById('codigoDescuento').value,
            comentarios: document.getElementById('comentarios').value
        };

        // Validación básica (opcional)
        if (!clienteSeleccionado || carrito.length === 0) {
            alert("Debes seleccionar un cliente y al menos un servicio antes de finalizar la venta.");
            return;
        }

        // 👉 Aquí haces el envío al backend
        registrarVenta(venta);
        document.getElementById('btnImprimir').style.display = 'block';
    }
    // Imprimir Ticket
    function imprimirTicket() {
        const cliente = clientes.find(c => c.id === clienteSeleccionado);
        const negocioInfo = negocio[0];

        const ticketContent = `
                <div id="ticket" style="width: 80mm; font-family: Arial, sans-serif; font-size: 12px; padding: 10px; display: none;">
                    <div style="text-align: center; margin-bottom: 10px;">
                        <img src="${negocioInfo.logo}" alt="Logo" style="max-width: 50mm; height: auto;">
                        <h2 style="margin: 5px 0; font-size: 14px;">${negocioInfo.nombre}</h2>
                        <p style="margin: 2px 0;">${negocioInfo.direccion}</p>
                        <p style="margin: 2px 0;">Tel: ${negocioInfo.telefono}</p>
                        <p style="margin: 2px 0;">${negocioInfo.email}</p>
                        <hr style="border-top: 1px dashed #000; margin: 10px 0;">
                    </div>
                    
                    <div style="margin-bottom: 10px;">
                        <p style="margin: 3px 0;"><strong>Fecha:</strong> ${new Date().toLocaleString()}</p>
                        ${cliente ? `<p style="margin: 3px 0;"><strong>Cliente:</strong> ${cliente.nombre}</p>` : ''}
                        <p style="margin: 3px 0;"><strong>Método de pago:</strong> ${document.getElementById('metodoPago').value}</p>
                    </div>
                    
                    <table style="width: 100%; margin-bottom: 10px;">
                        <thead>
                            <tr>
                                <th style="text-align: left;">Servicio</th>
                                <th style="text-align: right;">Cant.</th>
                                <th style="text-align: right;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${carrito.map(item => `
                                <tr>
                                    <td>${servicios.find(s => s.id === item.id).nombre}</td>
                                    <td style="text-align: right;">${item.cantidad}</td>
                                    <td style="text-align: right;">$${(item.precio * item.cantidad).toFixed(2)}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                    
                    <hr style="border-top: 1px dashed #000; margin: 10px 0;">
                    
                    <div style="text-align: right;">
                        <p style="margin: 3px 0;">Subtotal: $${document.getElementById('subtotal').textContent}</p>
                        <p style="margin: 3px 0;">Descuento: $${document.getElementById('descuento').textContent}</p>
                        <p style="margin: 3px 0; font-weight: bold;">Total: $${document.getElementById('total').textContent}</p>
                    </div>
                    
                    ${document.getElementById('comentarios').value ? `
                        <div style="margin-top: 10px;">
                            <p style="margin: 3px 0;"><strong>Comentarios:</strong></p>
                            <p style="margin: 3px 0;">${document.getElementById('comentarios').value}</p>
                        </div>
                    ` : ''}
                    
                    <div style="text-align: center; margin-top: 15px;">
                        <p style="font-size: 10px;">¡Gracias por su preferencia!</p>
                        <p style="font-size: 10px;">${negocioInfo.nombre}</p>
                    </div>
                </div>
            `;

        const ventanaImpresion = window.open('', '_blank');
        ventanaImpresion.document.write(ticketContent);
        ventanaImpresion.document.close();

        ventanaImpresion.onload = function () {
            const ticket = ventanaImpresion.document.getElementById('ticket');
            ticket.style.display = 'block';
            ventanaImpresion.print();
            ventanaImpresion.close();
        };
    }
    // Funcion para cargar los datos de la base de datos (clientes, servicios, negocio)
    async function cargarDatosBase() {
        // Cargar clientes
        let resClientes = await fetch(URL_BASE + 'obtenerListaClientes');
        let jsonClientes = await resClientes.json();
    
        // Cargar servicios
        let resServicios = await fetch(URL_BASE + 'listaServicios');
        let jsonServicios = await resServicios.json();
    
        // Mapear estructura de datos a lo que espera el frontend
        clientes = (jsonClientes.data || []).map(c => ({
            id: c.ClienteID,
            nombre: c.Nombre,
            telefono: c.Telefono,
            email: c.Email
        }));
    
        servicios = (jsonServicios.data || []).map(s => ({
            id: s.ServicioID,
            nombre: s.NombreServicio,
            descripcion: s.DescripcionServicio,
            precio: parseFloat(s.Precio)
        }));
    
        console.log('Clientes: ', clientes);
        console.log('Servicios: ', servicios);
    }
    // Registrar Cliente
    function registrarCliente(cliente) {
        let data = {
            Nombre: cliente.nombre,
            Telefono: cliente.telefono,
            Email: cliente.email
        };

        fetch(URL_BASE + 'registrarCliente', {
            method: 'POST',
            body: JSON.stringify(data)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error en la solicitud: ${response.status}`);
            }
            return response.json()  ;
        })
        .then(result => {
            console.log(result);
            alert(result.message);
        })
        .catch(error => {
            console.error('Error al registrar el cliente:', error);
        }); 
    }
    

    // Exportarlas al objeto global
    window.guardarCliente = guardarCliente;
    window.buscarCliente = buscarCliente;
    window.abrirModalCliente = abrirModalCliente;
    window.cerrarModalCliente = cerrarModalCliente;
    window.finalizarVenta = finalizarVenta;
    window.imprimirTicket = imprimirTicket;
    window.actualizarCarrito = actualizarCarrito;
    window.seleccionarCliente = seleccionarCliente;
})();