<?php
include_once 'app/config/Database.php';

/* 
-- Tabla de Ventas (Tickets)
    VentaID INT AUTO_INCREMENT PRIMARY KEY, -- Identificador único de la venta
    UsuarioID INT NOT NULL, -- Usuario que realizó la venta
    ClienteID INT NULL, -- Cliente que adquirió el servicio (opcional)
    Fecha DATETIME DEFAULT CURRENT_TIMESTAMP, -- Fecha y hora de la venta
    Total DECIMAL(10, 2) NOT NULL, -- Monto total de la venta
    EstadoVenta ENUM('Pagado', 'Pendiente', 'Cancelado') DEFAULT 'Pendiente', -- Estado de la venta
    MetodoPago ENUM('TDC', 'TDB', 'MercadoPago', 'PayPal'), -- Método de Pago
    DescuentoID INT NULL, -- Descuento aplicado en la venta (opcional)
    MontoDescuento DECIMAL(10, 2) DEFAULT 0, -- Monto del descuento aplicado
    FOREIGN KEY (UsuarioID) REFERENCES Usuarios(UsuarioID), -- Relación con Usuarios
    FOREIGN KEY (ClienteID) REFERENCES Clientes(ClienteID), -- Relación con Clientes
    FOREIGN KEY (DescuentoID) REFERENCES Descuentos(DescuentoID) -- Relación con Descuentos
-- Tabla de Tickets (Servicios adquiridos en una venta)
    TicketID INT AUTO_INCREMENT PRIMARY KEY, -- Identificador único del ticket
    VentaID INT NOT NULL, -- Relación con la venta
    ServicioID INT NOT NULL, -- Servicio adquirido
    Cantidad INT NOT NULL CHECK (Cantidad > 0), -- Cantidad de servicios adquiridos
    PrecioUnitario DECIMAL(10, 2) NOT NULL, -- Precio unitario del servicio
    Subtotal DECIMAL(10, 2) GENERATED ALWAYS AS (Cantidad * PrecioUnitario) STORED, -- Cálculo del subtotal
    FOREIGN KEY (VentaID) REFERENCES Ventas(VentaID) ON DELETE CASCADE, -- Relación con Ventas
    FOREIGN KEY (ServicioID) REFERENCES Servicios(ServicioID) -- Relación con Servicios
-- Tabla de SemaforoServicios 
    SemaforoServiciosID INT AUTO_INCREMENT PRIMARY KEY, -- Identificador único del SemaforoServicios
    VentaID INT NOT NULL, -- Cliente que realizó el pedido
    EstadoPedido ENUM('Registrado', 'En Proceso', 'Listo', 'Enviado', 'Entregado', 'Pagado','Facturado','Cancelado') DEFAULT 'Registrado', -- Estado del pedido
    FechaCambioEstado DATETIME DEFAULT CURRENT_TIMESTAMP,
    Comentario VARCHAR(255)  NULL,
    FOREIGN KEY (VentaID) REFERENCES Ventas(VentaID) -- Relación con la tabla Clientes
*/

class VentaModel {
    private $conn;
    
    public function __construct() {
        $this->conn = (new Database())->conn;
    }
    // 1️⃣ Registrar una Venta con o sin descuento
    public function registrarVenta($usuarioID, $clienteID, $metodoPago, $servicios, $codigoDescuento = null, $comentarios = null){
        try {
            $this->conn->beginTransaction(); // Inicia la transacción
    
            // 1️⃣ Insertar la venta con estado "Pendiente"
            $sqlVenta = "INSERT INTO Ventas (UsuarioID, ClienteID, Total, EstadoVenta, MetodoPago)
                         VALUES (:usuarioID, :clienteID, 0, 'Pendiente', :metodoPago)";
            $stmt = $this->conn->prepare($sqlVenta);
            $stmt->execute([
                ':usuarioID' => $usuarioID,
                ':clienteID' => $clienteID,
                ':metodoPago' => $metodoPago
            ]);
    
            // 2️⃣ Obtener el ID de la venta recién creada
            $ventaID = $this->conn->lastInsertId();
    
            // 3️⃣ Insertar los servicios en la tabla Tickets y calcular el total
            $totalVenta = 0; // Variable para acumular el total
            $sqlTicket = "INSERT INTO Tickets (VentaID, ServicioID, Cantidad, PrecioUnitario)
                          VALUES (:ventaID, :servicioID, :cantidad, :precioUnitario)";
            $stmtTicket = $this->conn->prepare($sqlTicket);
    
            foreach ($servicios as $servicio) {
                // Calculamos el subtotal
                $subtotal = $servicio['cantidad'] * $servicio['precio'];
                $totalVenta += $subtotal; // Acumulamos en el total
    
                $stmtTicket->execute([
                    ':ventaID' => $ventaID,
                    ':servicioID' => $servicio['id'],
                    ':cantidad' => $servicio['cantidad'],
                    ':precioUnitario' => $servicio['precio']
                ]);
    
                // 4️⃣ Llamar al procedimiento almacenado para reducir el inventario
                $sqlInventario = "CALL ReducirInventario(:servicioID)";
                $stmtInventario = $this->conn->prepare($sqlInventario);
                $stmtInventario->execute([
                    ':servicioID' => $servicio['id']
                ]);
            }

            // 5️⃣ Actualizar el total de la venta en la tabla Ventas
            $sqlUpdateVenta = "UPDATE Ventas SET Total = :totalVenta WHERE VentaID = :ventaID";
            $stmtUpdateVenta = $this->conn->prepare($sqlUpdateVenta);
            $stmtUpdateVenta->execute([
                ':totalVenta' => $totalVenta,
                ':ventaID' => $ventaID
            ]);

            // 6️⃣ Aplicar descuento si hay un código de descuento
            if ($codigoDescuento) {
                $sqlDescuento = "CALL AplicarDescuento(:ventaID, :codigoDescuento)";
                $stmtDescuento = $this->conn->prepare($sqlDescuento);
                $stmtDescuento->execute([
                    ':ventaID' => $ventaID,
                    ':codigoDescuento' => $codigoDescuento
                ]);
            }

            // 7️⃣ Registrar el estado en la tabla SemaforoServicios
            $comentario = $comentarios ? $comentarios : 'Venta registrada.';
            $sqlSemaforo = "INSERT INTO SemaforoServicios (VentaID, EstadoPedido, Comentario)
            VALUES (:ventaID, 'Registrado', :comentario)";
            $stmtSemaforo = $this->conn->prepare($sqlSemaforo);
            $stmtSemaforo->execute([
                ':ventaID' => $ventaID,
                ':comentario' => $comentario
            ]);

            // 8️⃣ Confirmar la transacción
            $this->conn->commit();
            return ['success' => true, 'ventaID' => $ventaID];
    
        } catch (Exception $e) {
            $this->conn->rollBack(); // Revertir cambios en caso de error
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    // 2️⃣  Lista de Ventas y Tickets por cliente o todos los clientes
    public function listarVentas($ventaID = null, $clienteID = null){
        // Quiero listar las ventas por cliente o todas las ventas
        // También quiero listar los tickets de cada venta de cada cliente
        try {
            // Consulta base
            $sql = "SELECT v.VentaID, c.Nombre AS Cliente, v.Fecha, v.Total, v.EstadoVenta, v.MetodoPago, 
                        s.NombreServicio AS Servicio, t.Cantidad, t.PrecioUnitario, t.Subtotal
                    FROM Ventas v
                    JOIN Clientes c ON v.ClienteID = c.ClienteID
                    JOIN Tickets t ON v.VentaID = t.VentaID
                    JOIN Servicios s ON t.ServicioID = s.ServicioID";

            // Condiciones opcionales para filtrar por VentaID o ClienteID
            $conditions = [];
            $params = [];

            if ($ventaID) {
                $conditions[] = "v.VentaID = :ventaID";
                $params[':ventaID'] = $ventaID;
            }

            if ($clienteID) {
                $conditions[] = "v.ClienteID = :clienteID";
                $params[':clienteID'] = $clienteID;
            }

            // Si existen condiciones, las agregamos a la consulta
            if (count($conditions) > 0) {
                $sql .= " WHERE " . implode(" AND ", $conditions);
            }

            // Preparamos la sentencia
            $stmt = $this->conn->prepare($sql);

            // Ejecutamos con los parámetros
            $stmt->execute($params);

            // Retornamos los resultados
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            // Manejo de errores
            return "Error: " . $e->getMessage();
        }
    }
    // 3️⃣  Actualizar el estado de la venta
    public function actualizarEstadoVenta($data){
        try {
            //limpiar los datos para evitar inyecciones SQL     
            $data = array_map('trim', $data);
            $data = array_map('strip_tags', $data);
            $data = array_map('htmlspecialchars', $data);
            $data = array_map('addslashes', $data);
            $data = array_map('urldecode', $data);
            //actualizar el estado de la venta  
            $sql = "UPDATE Ventas SET EstadoVenta = :nuevoEstado WHERE VentaID = :ventaID";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':nuevoEstado' => $data['nuevoEstado'],
                ':ventaID' => $data['ventaID']
            ]);
            // Verificar si se actualizó alguna fila    
            if ($stmt->rowCount() > 0) {
                // Ahora actualizar la tabla SemaforoServicios
                // Determinar el estado para SemaforoServicios
                $estadoSemaforo = '';
                switch ($data['nuevoEstado']) {
                    case 'Pagado':
                        $estadoSemaforo = 'Pagado';
                        break;
                    case 'Cancelado':
                        $estadoSemaforo = 'Cancelado';
                        break;
                    default:
                        $estadoSemaforo = 'Registrado';
                }

                // Actualizar en la tabla SemaforoServicios
                $sqlSemaforo = "UPDATE SemaforoServicios 
                SET EstadoPedido = :estadoSemaforo, FechaCambioEstado = CURRENT_TIMESTAMP 
                WHERE VentaID = :ventaID";
                $stmtSemaforo = $this->conn->prepare($sqlSemaforo);
                $stmtSemaforo->execute([
                ':ventaID' => $data['ventaID'],
                ':estadoSemaforo' => $estadoSemaforo
                ]); 
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }
    // 4️⃣  Actualizar el estado de la venta en la tabla SemaforoServicios
    public function actualizarEstadoSemaforo($data){
        try {
            //limpiar los datos para evitar inyecciones SQL     
            $data = array_map('trim', $data);
            $data = array_map('strip_tags', $data); 
            $data = array_map('htmlspecialchars', $data);
            $data = array_map('addslashes', $data);
            $data = array_map('urldecode', $data);
            //actualizar el estado de la venta en la tabla SemaforoServicios
            $sql = "UPDATE SemaforoServicios SET EstadoPedido = :nuevoEstado, FechaCambioEstado = CURRENT_TIMESTAMP, Comentario = :comentario WHERE VentaID = :ventaID";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':nuevoEstado' => $data['nuevoEstado'],
                ':ventaID' => $data['ventaID'],
                ':comentario' => $data['comentario']
            ]);
            return true;
        } catch (Exception $e) {
            return false;   
        }
    }
    // 5️⃣  Lista de estado y comentarios del semaforo de servicios
    public function listarSemaforoServicios($data){
        try {
            $sql = "SELECT EstadoPedido, Comentario, FechaCambioEstado FROM SemaforoServicios";
            $params = [];
    
            // Si viene el ID de venta, filtramos por él
            if (!empty($data['ventaID'])) {
                if (!is_numeric($data['ventaID'])) {
                    throw new Exception("El ID de venta debe ser numérico.");
                }
                $sql .= " WHERE VentaID = :ventaID";
                $params[':ventaID'] = (int) $data['ventaID']; // Convertir a entero para seguridad
            }
    
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en listarSemaforoServicios: " . $e->getMessage());
            return false;
        }
    }
}
?>
