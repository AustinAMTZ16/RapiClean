-- Creación de la base de datos
CREATE DATABASE IF NOT EXISTS RapiClean;
USE RapiClean;

-- Tabla de Servicios
CREATE TABLE Servicios (
    ServicioID INT AUTO_INCREMENT PRIMARY KEY, -- Identificador único del servicio
    NombreServicio VARCHAR(100) NOT NULL, -- Nombre del servicio
    Descripcion TEXT, -- Descripción del servicio
    Precio DECIMAL(10, 2) NOT NULL -- Costo del servicio
);
-- Tabla de Inventario
CREATE TABLE Inventario (
    ProductoID INT AUTO_INCREMENT PRIMARY KEY, -- Identificador único del producto
    NombreProducto VARCHAR(100) NOT NULL, -- Nombre del producto
    Cantidad INT NOT NULL, -- Cantidad disponible en el inventario
    Descripcion TEXT, -- Descripción del producto
    FechaActualizacion DATETIME DEFAULT CURRENT_TIMESTAMP -- Fecha y hora de la última actualización del inventario
);
-- Tabla intermedia: Relación Servicios-Inventario
CREATE TABLE Servicio_Insumos (
    ServicioID INT NOT NULL, -- Identificador del servicio
    ProductoID INT NOT NULL, -- Identificador del producto o insumo
    CantidadRequerida INT NOT NULL, -- Cantidad del insumo necesario para el servicio
    PRIMARY KEY (ServicioID, ProductoID), -- Clave primaria compuesta
    FOREIGN KEY (ServicioID) REFERENCES Servicios(ServicioID), -- Relación con la tabla Servicios
    FOREIGN KEY (ProductoID) REFERENCES Inventario(ProductoID) -- Relación con la tabla Inventario
);


-- Tabla de Ventas (Tickets)
CREATE TABLE Ventas (
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
);
-- Tabla de Tickets (Servicios adquiridos en una venta)
CREATE TABLE Tickets (
    TicketID INT AUTO_INCREMENT PRIMARY KEY, -- Identificador único del ticket
    VentaID INT NOT NULL, -- Relación con la venta
    ServicioID INT NOT NULL, -- Servicio adquirido
    Cantidad INT NOT NULL CHECK (Cantidad > 0), -- Cantidad de servicios adquiridos
    PrecioUnitario DECIMAL(10, 2) NOT NULL, -- Precio unitario del servicio
    Subtotal DECIMAL(10, 2) GENERATED ALWAYS AS (Cantidad * PrecioUnitario) STORED, -- Cálculo del subtotal
    FOREIGN KEY (VentaID) REFERENCES Ventas(VentaID) ON DELETE CASCADE, -- Relación con Ventas
    FOREIGN KEY (ServicioID) REFERENCES Servicios(ServicioID) -- Relación con Servicios
);
-- Tabla de SemaforoServicios 
CREATE TABLE SemaforoServicios (
    SemaforoServiciosID INT AUTO_INCREMENT PRIMARY KEY, -- Identificador único del SemaforoServicios
    VentaID INT NOT NULL, -- Cliente que realizó el pedido
    EstadoPedido ENUM('Registrado', 'En Proceso', 'Listo', 'Enviado', 'Entregado', 'Pagado','Facturado') DEFAULT 'Registrado', -- Estado del pedido
    FechaCambioEstado DATETIME DEFAULT CURRENT_TIMESTAMP,
    Comentario VARCHAR(255)  NULL,
    FOREIGN KEY (VentaID) REFERENCES Ventas(VentaID) -- Relación con la tabla Clientes
);


-- Tabla de Descuentos
CREATE TABLE Descuentos (
    DescuentoID INT AUTO_INCREMENT PRIMARY KEY, -- Identificador único del descuento
    CodigoPromocion VARCHAR(50) NOT NULL UNIQUE, -- Código único de la promoción
    Nombre VARCHAR(100) NOT NULL, -- Nombre de la promoción
    Descripcion TEXT, -- Descripción detallada de la promoción
    TipoDescuento ENUM('Monto', 'Porcentaje') NOT NULL, -- Define si el descuento es un monto fijo o un porcentaje
    ValorDescuento DECIMAL(10, 2) NOT NULL, -- Monto o porcentaje del descuento
    VigenciaInicio DATE NOT NULL, -- Fecha de inicio de la promoción
    VigenciaFin DATE NOT NULL, -- Fecha de finalización de la promoción
    Estado ENUM('Activo', 'Inactivo') DEFAULT 'Activo', -- Estado de la promoción
    FechaAlta DATETIME DEFAULT CURRENT_TIMESTAMP, -- Fecha en la que se registró la promoción
    UsuarioID INT NOT NULL, -- Usuario que creó el descuento
    FOREIGN KEY (UsuarioID) REFERENCES Usuarios(UsuarioID) -- Relación con la tabla Usuarios
);
-- Tabla Recompensas
CREATE TABLE Recompensas (
    RecompensaID INT AUTO_INCREMENT PRIMARY KEY, -- Identificador único de la recompensa
    Descripcion VARCHAR(255) NOT NULL, -- Descripción de la recompensa
    PuntosNecesarios INT NOT NULL, -- Cantidad de puntos requeridos para obtener la recompensa
    UsuarioID INT NOT NULL, -- Usuario que creó la recompensa
    FOREIGN KEY (UsuarioID) REFERENCES Usuarios(UsuarioID) -- Relación con la tabla Usuarios
);
-- Tabla de Canjes de Recompensas (Ejemplo)
CREATE TABLE CanjesRecompensas (
    CanjeID INT AUTO_INCREMENT PRIMARY KEY,
    ClienteID INT NOT NULL,
    RecompensaID INT NOT NULL,
    FechaCanje DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ClienteID) REFERENCES Clientes(ClienteID),
    FOREIGN KEY (RecompensaID) REFERENCES Recompensas(RecompensaID)
);




-- Tabla de Roles (Define los tipos de usuarios)
CREATE TABLE Roles (
    RolID INT AUTO_INCREMENT PRIMARY KEY, -- Identificador único del rol
    NombreRol VARCHAR(50) NOT NULL, -- Nombre del rol (Ej. Administrador, Cliente, Vendedor)
    Descripcion TEXT -- Descripción detallada del rol
    Etado ENUM('Activo', 'Inactivo') DEFAULT 'Activo',
    Fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de Menús (Define las opciones del menú y submenús)
CREATE TABLE Menus (
    MenuID INT AUTO_INCREMENT PRIMARY KEY,
    NombreMenu VARCHAR(100) NOT NULL,
    Icono VARCHAR(100) DEFAULT NULL,  -- Ícono opcional
    Url VARCHAR(255) DEFAULT NULL,    -- URL de la opción del menú
    Orden INT NOT NULL DEFAULT 1,     -- Orden en el menú
    Parent_id INT DEFAULT NULL,       -- Para submenús
    Estado ENUM('Activo', 'Inactivo') DEFAULT 'Activo',
    FOREIGN KEY (Parent_id) REFERENCES Menus(MenuID) ON DELETE CASCADE
);

-- Tabla de Relación entre Roles y Menús (Define qué rol tiene acceso a qué menú)
CREATE TABLE Roles_Menus (
    Rol_MenuID INT AUTO_INCREMENT PRIMARY KEY,
    RolID INT NOT NULL,
    MenuID INT NOT NULL,
    FOREIGN KEY (RolID) REFERENCES Roles(RolID) ON DELETE CASCADE,
    FOREIGN KEY (MenuID) REFERENCES Menus(MenuID) ON DELETE CASCADE
);






-- Tabla de Usuarios
CREATE TABLE Usuarios (
    UsuarioID INT AUTO_INCREMENT PRIMARY KEY, -- Identificador único del usuario
    Nombre VARCHAR(100) NOT NULL, -- Nombre completo del usuario
    Email VARCHAR(100) NOT NULL UNIQUE, -- Correo electrónico único del usuario
    Password VARCHAR(255) NOT NULL, -- Contraseña cifrada del usuario
    RolID INT NOT NULL, -- Rol asignado al usuario
    Estado ENUM('Activo', 'Inactivo') DEFAULT 'Activo', -- Estado del usuario
    FechaCreacion DATETIME DEFAULT CURRENT_TIMESTAMP, -- Fecha y hora de creación del usuario
    UltimoAcceso DATETIME, -- Fecha y hora del último acceso del usuario
    FOREIGN KEY (RolID) REFERENCES Roles(RolID) -- Relación con la tabla Roles
);
-- Tabla de Clientes
CREATE TABLE Clientes (
    ClienteID INT AUTO_INCREMENT PRIMARY KEY, -- Identificador único del cliente
    Nombre VARCHAR(100) NOT NULL, -- Nombre completo del cliente
    Email VARCHAR(100) NOT NULL UNIQUE, -- Correo electrónico único del cliente
    Password VARCHAR(255) NOT NULL, -- Contraseña cifrada del cliente
    PuntosRecompensa INT DEFAULT 0, -- Puntos de recompensa acumulados
    Estado ENUM('Activo', 'Inactivo') DEFAULT 'Activo', -- Estado del cliente
    FechaCreacion DATETIME DEFAULT CURRENT_TIMESTAMP, -- Fecha y hora de registro del cliente
    UltimoAcceso DATETIME -- Fecha y hora del último acceso del cliente
);





-- Tabla ReporteIngresos
CREATE TABLE ReporteIngresos (
    ReporteID INT AUTO_INCREMENT PRIMARY KEY,
    Fecha DATE NOT NULL,
    TotalIngresos DECIMAL(10, 2) NOT NULL,
    UsuarioID INT NOT NULL,
    FOREIGN KEY (UsuarioID) REFERENCES Usuarios(UsuarioID)
);
-- Tabla ReporteInventario
CREATE TABLE ReporteInventario (
    ReporteID INT AUTO_INCREMENT PRIMARY KEY,
    Fecha DATE NOT NULL,
    ProductoID INT NOT NULL,
    CantidadUsada INT NOT NULL,
    UsuarioID INT NOT NULL,
    FOREIGN KEY (ProductoID) REFERENCES Inventario(ProductoID),
    FOREIGN KEY (UsuarioID) REFERENCES Usuarios(UsuarioID)
);
-- Tabla ReportePedidos
CREATE TABLE ReportePedidos (
    ReporteID INT AUTO_INCREMENT PRIMARY KEY,
    PedidoID INT NOT NULL,
    Fecha DATE NOT NULL,
    EstadoPedido ENUM('Pendiente', 'En Proceso', 'Listo', 'Entregado'),
    Total DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (PedidoID) REFERENCES Pedidos(PedidoID)
);
-- Tabla ReporteRecompensas
CREATE TABLE ReporteRecompensas (
    ReporteID INT AUTO_INCREMENT PRIMARY KEY,
    ClienteID INT NOT NULL,
    RecompensaID INT NOT NULL,
    Fecha DATE NOT NULL,
    PuntosUsados INT NOT NULL,
    FOREIGN KEY (ClienteID) REFERENCES Clientes(ClienteID),
    FOREIGN KEY (RecompensaID) REFERENCES Recompensas(RecompensaID)
);





-- Procedimiento para Registrar los Puntos CALL AsignarPuntosRecompensa(1, 150.00);
DELIMITER $$
    CREATE PROCEDURE AsignarPuntosRecompensa(
        IN p_ClienteID INT,
        IN p_TotalVenta DECIMAL(10, 2)
    )
    BEGIN
        DECLARE v_Puntos INT;

        -- Calcular puntos en base al total de la venta
        SET v_Puntos = FLOOR(p_TotalVenta / 10); -- 1 punto por cada $10

        -- Actualizar los puntos en la tabla Clientes
        UPDATE Clientes
        SET PuntosRecompensa = PuntosRecompensa + v_Puntos
        WHERE ClienteID = p_ClienteID;
    END$$
DELIMITER ;
-- SP AplicarDescuento Estado Pagado CALL AplicarDescuento(1, '123456');
DELIMITER $$
    CREATE PROCEDURE AplicarDescuento(
        IN p_VentaID INT,
        IN p_CodigoPromocion VARCHAR(50)
    )
    BEGIN
        DECLARE v_DescuentoID INT;
        DECLARE v_TipoDescuento ENUM('Monto', 'Porcentaje');
        DECLARE v_ValorDescuento DECIMAL(10, 2);
        DECLARE v_MontoTotal DECIMAL(10, 2);
        DECLARE v_MontoDescuento DECIMAL(10, 2);

        -- Obtener los datos del descuento
        SELECT DescuentoID, TipoDescuento, ValorDescuento
        INTO v_DescuentoID, v_TipoDescuento, v_ValorDescuento
        FROM Descuentos
        WHERE CodigoPromocion = p_CodigoPromocion
        AND Estado = 'Activo'
        AND CURRENT_DATE BETWEEN VigenciaInicio AND VigenciaFin;

        IF v_DescuentoID IS NULL THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'El código de promoción no es válido o está vencido.';
        END IF;

        -- Obtener el total de la venta
        SELECT Total INTO v_MontoTotal
        FROM Ventas
        WHERE VentaID = p_VentaID;

        -- Calcular el monto del descuento
        IF v_TipoDescuento = 'Porcentaje' THEN
            SET v_MontoDescuento = (v_MontoTotal * v_ValorDescuento) / 100;
        ELSE
            SET v_MontoDescuento = v_ValorDescuento;
        END IF;

        -- Asegurar que el monto de descuento no exceda el total
        IF v_MontoDescuento > v_MontoTotal THEN
            SET v_MontoDescuento = v_MontoTotal;
        END IF;

        -- Actualizar la venta con el descuento aplicado
        UPDATE Ventas
        SET DescuentoID = v_DescuentoID,
            MontoDescuento = v_MontoDescuento,
            Total = v_MontoTotal - v_MontoDescuento,
            EstadoVenta = 'Pagado'
        WHERE VentaID = p_VentaID;
    END$$
DELIMITER ;
-- SP AplicarDescuento Estado Pendiente CALL AplicarDescuento(1, '123456');
DELIMITER $$
    CREATE PROCEDURE AplicarDescuento(
        IN p_VentaID INT,
        IN p_CodigoPromocion VARCHAR(50)
    )
    BEGIN
        DECLARE v_DescuentoID INT;
        DECLARE v_TipoDescuento ENUM('Monto', 'Porcentaje');
        DECLARE v_ValorDescuento DECIMAL(10, 2);
        DECLARE v_MontoTotal DECIMAL(10, 2);
        DECLARE v_MontoDescuento DECIMAL(10, 2);

        -- Verificar si el código de promoción es válido
        SELECT DescuentoID, TipoDescuento, ValorDescuento
        INTO v_DescuentoID, v_TipoDescuento, v_ValorDescuento
        FROM Descuentos
        WHERE CodigoPromocion = p_CodigoPromocion
        AND Estado = 'Activo'
        AND CURRENT_DATE BETWEEN VigenciaInicio AND VigenciaFin;

        -- Si el descuento no es válido, lanzar error
        IF v_DescuentoID IS NULL THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'El código de promoción no es válido o está vencido.';
        END IF;

        -- Obtener el total de la venta
        SELECT Total INTO v_MontoTotal
        FROM Ventas
        WHERE VentaID = p_VentaID;

        -- Calcular el descuento
        IF v_TipoDescuento = 'Porcentaje' THEN
            SET v_MontoDescuento = (v_MontoTotal * v_ValorDescuento) / 100;
        ELSE
            SET v_MontoDescuento = v_ValorDescuento;
        END IF;

        -- Asegurar que el descuento no exceda el total de la venta
        IF v_MontoDescuento > v_MontoTotal THEN
            SET v_MontoDescuento = v_MontoTotal;
        END IF;

        -- Actualizar la venta con el descuento aplicado
        UPDATE Ventas
        SET DescuentoID = v_DescuentoID,
            MontoDescuento = v_MontoDescuento,
            Total = v_MontoTotal - v_MontoDescuento,
            EstadoVenta = 'Pendiente' -- Se mantiene pendiente para cobrar
        WHERE VentaID = p_VentaID;
    END$$
DELIMITER ;
-- Reducir el Inventario CALL ReducirInventario(1);
DELIMITER $$
    CREATE PROCEDURE ReducirInventario(IN p_ServicioID INT)
    BEGIN
        -- Declara variables para iterar sobre los insumos del servicio
        DECLARE done INT DEFAULT 0;
        DECLARE vProductoID INT;
        DECLARE vCantidadRequerida INT;

        -- Cursor para recorrer los insumos del servicio
        DECLARE insumos CURSOR FOR
            SELECT ProductoID, CantidadRequerida
            FROM Servicio_Insumos
            WHERE ServicioID = p_ServicioID;  -- 🔹 Ahora usa el parámetro correctamente

        -- Manejo de fin del cursor
        DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

        -- Abre el cursor
        OPEN insumos;

        insumos_loop: LOOP
            FETCH insumos INTO vProductoID, vCantidadRequerida;
            IF done THEN
                LEAVE insumos_loop;
            END IF;

            -- Reduce la cantidad en el inventario
            UPDATE Inventario
            SET Cantidad = Cantidad - vCantidadRequerida,
                FechaActualizacion = NOW()
            WHERE ProductoID = vProductoID;

            -- Verifica si hay errores (ejemplo: cantidad negativa)
            IF ROW_COUNT() = 0 OR (SELECT Cantidad FROM Inventario WHERE ProductoID = vProductoID) < 0 THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error: No hay suficiente inventario para completar el servicio.';
            END IF;
        END LOOP;

        -- Cierra el cursor
        CLOSE insumos;
    END$$
DELIMITER ;
-- verificará si el cliente tiene suficientes puntos, y si es así, realizará el canje y actualizará los puntos del cliente, adicional se actulizar la venta en el campo RecompensaID. CALL CanjearRecompensa(1, 1);
DELIMITER $$
    CREATE PROCEDURE CanjearRecompensa(
        IN p_ClienteID INT,
        IN p_RecompensaID INT
    )
    BEGIN
        DECLARE puntos_actuales INT;
        DECLARE puntos_necesarios INT;
        
        -- Obtener los puntos del cliente
        SELECT PuntosRecompensa INTO puntos_actuales
        FROM Clientes
        WHERE ClienteID = p_ClienteID;

        -- Obtener los puntos necesarios para la recompensa
        SELECT PuntosNecesarios INTO puntos_necesarios
        FROM Recompensas
        WHERE RecompensaID = p_RecompensaID;
        
        -- Verificar si el cliente tiene suficientes puntos
        IF puntos_actuales >= puntos_necesarios THEN
            -- Realizar el canje de recompensa
            INSERT INTO CanjesRecompensas (ClienteID, RecompensaID)
            VALUES (p_ClienteID, p_RecompensaID);
            
            -- Restar los puntos al cliente
            UPDATE Clientes
            SET PuntosRecompensa = PuntosRecompensa - puntos_necesarios
            WHERE ClienteID = p_ClienteID;
            
            -- Mensaje de éxito
            SELECT 'Canje realizado con éxito' AS mensaje;
        ELSE
            -- Mensaje de error si no tiene suficientes puntos
            SELECT 'No tiene suficientes puntos para canjear esta recompensa' AS mensaje;
        END IF;
    END $$
DELIMITER ;


-- Insertar roles iniciales
    INSERT INTO Roles (NombreRol, Descripcion) VALUES 
    ('Administrador', 'Acceso completo al sistema'),
    ('Gerente', 'Acceso KPIS'),
    ('SubGerente', 'Acceso Edicción'),
    ('Empleado', 'Acceso limitado a funciones específicas');
-- Insertar permisos iniciales (ejemplo)
    INSERT INTO Permisos (NombrePermiso, Descripcion) VALUES
    ('Gestionar Usuarios', 'Permite gestionar usuarios en el sistema'),
    ('Gestionar Inventario', 'Permite añadir, editar y eliminar productos del inventario'),
    ('Ver Reportes', 'Permite visualizar reportes de ingresos e inventario');
-- Relacionar permisos con roles (ejemplo)
    INSERT INTO RolesPermisos (RolID, PermisoID) VALUES
    (1, 1), -- Administrador tiene permiso para gestionar usuarios
    (1, 2), -- Administrador puede gestionar inventario
    (1, 3), -- Administrador puede ver reportes
    (2, 1), -- Gerente solo puede ver reportes
    (2, 2), -- Gerente solo puede ver reportes
    (2, 3), -- Gerente solo puede ver reportes
    (3, 1), -- SubGerente solo puede ver reportes
    (3, 2), -- SubGerente solo puede ver reportes
    (3, 3), -- SubGerente solo puede ver reportes
    (4, 3); -- Empleado solo puede ver reportes
-- Inserción de servicios
    INSERT INTO Servicios (NombreServicio, Descripcion, Precio)
    VALUES 
    ('Lavado de Ropa', 'Servicio de lavado de ropa con detergentes especiales', 100.00),
    ('Secado de Ropa', 'Secado de ropa con máquina de secado', 80.00),
    ('Planchado de Ropa', 'Planchado de ropa de algodón, lino y sintéticos', 150.00),
    ('Lavado, Secado y Planchado', 'Servicio completo de lavado, secado y planchado de ropa', 250.00);
-- Inserción de productos en el inventario
    INSERT INTO Inventario (NombreProducto, Cantidad, Descripcion)
    VALUES 
    ('Detergente Líquido', 100, 'Detergente líquido para lavado de ropa'),
    ('Suavizante', 50, 'Suavizante para dejar la ropa suave y fragante'),
    ('Blanqueador', 30, 'Blanqueador para ropa blanca'),
    ('Aromatizante de Ropa', 40, 'Aromatizante para dar fragancia a la ropa'),
    ('Plancha de Vapor', 10, 'Plancha de vapor profesional para planchar ropa'),
    ('Funda para Planchar', 20, 'Funda para planchado que protege la tela');
-- Inserción de la relación entre servicios e insumos
    INSERT INTO Servicio_Insumos (ServicioID, ProductoID, CantidadRequerida)
    VALUES 
    (1, 1, 1),  -- Lavado de Ropa usa 1 Detergente Líquido
    (1, 2, 1),  -- Lavado de Ropa usa 1 Suavizante
    (1, 3, 1),  -- Lavado de Ropa usa 1 Blanqueador
    (2, 4, 1),  -- Secado de Ropa usa 1 Aromatizante de Ropa
    (3, 5, 1),  -- Planchado de Ropa usa 1 Plancha de Vapor
    (3, 6, 1),  -- Planchado de Ropa usa 1 Funda para Planchar
    (4, 1, 1),  -- Lavado, Secado y Planchado usa 1 Detergente Líquido
    (4, 2, 1),  -- Lavado, Secado y Planchado usa 1 Suavizante
    (4, 4, 1),  -- Lavado, Secado y Planchado usa 1 Aromatizante de Ropa
    (4, 5, 1),  -- Lavado, Secado y Planchado usa 1 Plancha de Vapor
    (4, 6, 1);  -- Lavado, Secado y Planchado usa 1 Funda para Planchar
-- Insertar Descuentos
    INSERT INTO Descuentos (CodigoPromocion, Nombre, Descripcion, TipoDescuento, ValorDescuento, VigenciaInicio, VigenciaFin, UsuarioID) VALUES 
    ('DESC50', 'Descuento 50 MXN', 'Descuento fijo de 50 pesos', 'Monto', 50.00, '2025-01-01', '2025-12-31', 1),
    ('DESC10', 'Descuento 10%', 'Descuento del 10%', 'Porcentaje', 10.00, '2025-01-01', '2025-12-31', 1);



-- Insertar Usuarios
    INSERT INTO Usuarios (Nombre, Email, Password, RolID) VALUES 
    ('Admin', 'admin@lavanderia.com', 'password123', 1),
    ('Empleado1', 'empleado1@lavanderia.com', 'password123', 2);
-- Insertar Clientes
    INSERT INTO Clientes (Nombre, Email, Password) VALUES 
    ('Cliente1', 'cliente1@example.com', 'password123'),
    ('Cliente2', 'cliente2@example.com', 'password123');



-- Ejercicios de Venta y Puntos de Recompensa
-- Ejercicio 1: Registrar una Venta Normal (Sin Descuento)
-- Objetivo: Insertar una venta de un servicio y asignar los puntos de recompensa al cliente.
-- 1️⃣ Inserta una venta de prueba:
    INSERT INTO Ventas (UsuarioID, ServicioID, ClienteID, Total, EstadoVenta)
    VALUES (1, 2, 3, 250.00, 'Pagado');
-- 2️⃣ Llama al procedimiento almacenado para asignar los puntos de recompensa:
    CALL AsignarPuntosRecompensa(3, 250.00);
-- 3️⃣ Llama al procedimiento almacenado para reducir inventario
    CALL ReducirInventario(2);




-- Ejercicio 2: Registrar una Venta con Descuento en Porcentaje
-- Objetivo: Aplicar un descuento en porcentaje a una venta.
-- 1️⃣ Inserta un descuento del 20%:
    INSERT INTO Descuentos (CodigoPromocion, Nombre, Descripcion, TipoDescuento, ValorDescuento, VigenciaInicio, VigenciaFin, Estado, UsuarioID)
    VALUES ('DESC20', 'Descuento 20%', 'Descuento del 20% en cualquier servicio', 'Porcentaje', 20, '2024-01-01', '2025-12-31', 'Activo', 1);
-- 2️⃣ Inserta una venta:
    INSERT INTO Ventas (UsuarioID, ServicioID, ClienteID, Total, EstadoVenta)
    VALUES (1, 2, 3, 500.00, 'Pendiente');
-- 3️⃣ Aplica el descuento a la venta:
    CALL AplicarDescuento(1, 'DESC20');
-- 4️⃣ Asigna puntos de recompensa al cliente después del descuento:
    SELECT Total FROM Ventas WHERE VentaID = 1;  -- Obtiene el total final
    CALL AsignarPuntosRecompensa(3, (SELECT Total FROM Ventas WHERE VentaID = 1));
-- 5️⃣ Llama al procedimiento almacenado para reducir inventario
    CALL ReducirInventario(2);



-- Ejercicio 3: Registrar una Venta con Descuento en Monto Fijo
-- Objetivo: Aplicar un descuento de $50 en una venta.
-- 1️⃣ Inserta un descuento de $50:
    INSERT INTO Descuentos (CodigoPromocion, Nombre, Descripcion, TipoDescuento, ValorDescuento, VigenciaInicio, VigenciaFin, Estado, UsuarioID)
    VALUES ('DESC50', 'Descuento $50', 'Descuento de $50 en cualquier servicio', 'Monto', 50, '2024-01-01', '2025-12-31', 'Activo', 1);
-- 2️⃣ Inserta una venta:
    INSERT INTO Ventas (UsuarioID, ServicioID, ClienteID, Total, EstadoVenta)
    VALUES (1, 2, 3, 300.00, 'Pendiente');
-- 3️⃣ Aplica el descuento:
    CALL AplicarDescuento(2, 'DESC50');
-- 4️⃣ Asigna puntos de recompensa:
    CALL AsignarPuntosRecompensa(3, (SELECT Total FROM Ventas WHERE VentaID = 2));
-- 5️⃣ Llama al procedimiento almacenado para reducir inventario
    CALL ReducirInventario(2);



SELECT v.VentaID, c.Nombre AS Cliente, v.Fecha, v.Total, v.EstadoVenta, v.MetodoPago,
       s.NombreServicio AS Servicio, t.Cantidad, t.PrecioUnitario, t.Subtotal
FROM Ventas v
JOIN Clientes c ON v.ClienteID = c.ClienteID
JOIN Tickets t ON v.VentaID = t.VentaID
JOIN Servicios s ON t.ServicioID = s.ServicioID
WHERE v.VentaID = 1;















1️⃣ Registrar la Venta
    INSERT INTO Ventas (UsuarioID, ClienteID, Total, EstadoVenta, MetodoPago)
    VALUES (1, 1, 130.00, 'Pendiente', 'Efectivo');

2️⃣ Obtener el ID de la Venta recién creada
    SELECT LAST_INSERT_ID() AS VentaID;
    (Supongamos que devuelve VentaID = 1)

3️⃣ Insertar Servicios en la Tabla Tickets
    INSERT INTO Tickets (VentaID, ServicioID, Cantidad, PrecioUnitario, Subtotal)
    VALUES 
        (1, 1, 2, 50.00, 100.00),  -- Lavado (2 unidades x $50)
        (1, 2, 1, 30.00, 30.00);   -- Planchado (1 unidad x $30)

4️⃣ Consultar la Venta con sus Servicios
    SELECT v.VentaID, c.Nombre AS Cliente, v.Fecha, v.Total, v.EstadoVenta, v.MetodoPago,
        s.NombreServicio AS Servicio, t.Cantidad, t.PrecioUnitario, t.Subtotal
    FROM Ventas v
    JOIN Clientes c ON v.ClienteID = c.ClienteID
    JOIN Tickets t ON v.VentaID = t.VentaID
    JOIN Servicios s ON t.ServicioID = s.ServicioID
    WHERE v.VentaID = 1;

-- SP AplicarDescuento Estado Pendiente CALL AplicarDescuento(1, '123456');
-- Reducir el Inventario CALL ReducirInventario(1);
✅ Salida esperada:
    +---------+-----------+---------------------+--------+-----------+---------+---------+----------+---------------+
    | VentaID | Cliente   | Fecha               | Total  | Estado    | Metodo  | Servicio | Cantidad | Subtotal      |
    +---------+-----------+---------------------+--------+-----------+---------+---------+----------+---------------+
    | 1       | Juan Pérez | 2024-02-04 12:00:00 | 130.00 | Pagado    | TDC     | Lavado  | 2        | 100.00        |
    | 1       | Juan Pérez | 2024-02-04 12:00:00 | 130.00 | Pagado    | TDC     | Planchado | 1       | 30.00         |
    +---------+-----------+---------------------+--------+-----------+---------+---------+----------+---------------+


Crear un funcion para registrar la venta y los servicios adquiridos y en caso de aplicar un descuento, se debe aplicar al total de la venta. Tambien se debe reducir el inventario de los servicios adquiridos.

la funcio debe resivir los siguientes parametros: 
data_venta[
    {
        UsuarioID, ClienteID, Total, EstadoVenta, MetodoPago
    }
],
data_tickets[
    {
        VentaID, ServicioID, Cantidad, PrecioUnitario, Subtotal
    },
    {
        VentaID, ServicioID, Cantidad, PrecioUnitario, Subtotal
    }
]





-- pintar el menu de la pagina

SELECT 
    u.UsuarioID,
    u.Nombre AS usuario,
    u.Email,
    r.NombreRol,
    m1.MenuID AS menu_id, 
    m1.NombreMenu AS menu, 
    m1.Url AS menu_url, 
    m2.MenuID AS sub_id, 
    m2.NombreMenu AS sub_menu, 
    m2.Url AS sub_url
FROM Usuarios u
JOIN Roles r ON u.RolID = r.RolID
JOIN Roles_Menus rm ON r.RolID = rm.RolID
JOIN Menus m1 ON rm.MenuID = m1.MenuID
LEFT JOIN Menus m2 ON m1.MenuID = m2.Parent_id  
WHERE u.Email = 'admin@lavanderia.com'  -- Filtrar por el usuario autenticado
AND u.Estado = 'Activo'  
AND m1.Estado = 'Activo'
ORDER BY menu_id ASC;


<?php
$conexion = new mysqli('localhost', 'root', '', 'RapiClean');

$usuarioEmail = 'admin@lavanderia.com'; // Aquí pondrás el email del usuario autenticado
$query = "
    SELECT 
        u.UsuarioID,
        u.Nombre AS usuario,
        u.Email,
        r.NombreRol,
        m1.MenuID AS menu_id, 
        m1.NombreMenu AS menu, 
        m1.Url AS menu_url, 
        m2.MenuID AS sub_id, 
        m2.NombreMenu AS sub_menu, 
        m2.Url AS sub_url
    FROM Usuarios u
    JOIN Roles r ON u.RolID = r.RolID
    JOIN Roles_Menus rm ON r.RolID = rm.RolID
    JOIN Menus m1 ON rm.MenuID = m1.MenuID
    LEFT JOIN Menus m2 ON m1.MenuID = m2.Parent_id  
    WHERE u.Email = ?  
    AND u.Estado = 'Activo'  
    AND m1.Estado = 'Activo'
    ORDER BY menu_id ASC;
";

$stmt = $conexion->prepare($query);
$stmt->bind_param('s', $usuarioEmail);
$stmt->execute();
$resultado = $stmt->get_result();
$menu = [];

while ($row = $resultado->fetch_assoc()) {
    $menu_id = $row['menu_id'];
    
    if (!isset($menu[$menu_id])) {
        $menu[$menu_id] = [
            'nombre' => $row['menu'],
            'url' => $row['menu_url'],
            'submenus' => []
        ];
    }
    
    if (!empty($row['sub_id'])) {
        $menu[$menu_id]['submenus'][] = [
            'nombre' => $row['sub_menu'],
            'url' => $row['sub_url']
        ];
    }
}

echo "<ul>";
foreach ($menu as $m) {
    echo "<li><a href='{$m['url']}'>{$m['nombre']}</a>";
    
    if (!empty($m['submenus'])) {
        echo "<ul>";
        foreach ($m['submenus'] as $sub) {
            echo "<li><a href='{$sub['url']}'>{$sub['nombre']}</a></li>";
        }
        echo "</ul>";
    }
    
    echo "</li>";
}
echo "</ul>";

$conexion->close();
?>
