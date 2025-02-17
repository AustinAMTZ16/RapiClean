<?php
include_once 'app/config/Database.php';

/* 
-- Tabla de Servicios
    ServicioID INT AUTO_INCREMENT PRIMARY KEY, -- Identificador único del servicio
    NombreServicio VARCHAR(100) NOT NULL, -- Nombre del servicio
    Descripcion TEXT, -- Descripción del servicio
    Precio DECIMAL(10, 2) NOT NULL -- Costo del servicio
-- Tabla de Inventario
    ProductoID INT AUTO_INCREMENT PRIMARY KEY, -- Identificador único del producto
    NombreProducto VARCHAR(100) NOT NULL, -- Nombre del producto
    Cantidad INT NOT NULL, -- Cantidad disponible en el inventario
    Descripcion TEXT, -- Descripción del producto
    FechaActualizacion DATETIME DEFAULT CURRENT_TIMESTAMP -- Fecha y hora de la última actualización del inventario
-- Tabla intermedia: Relación Servicios-Inventario
    ServicioID INT NOT NULL, -- Identificador del servicio
    ProductoID INT NOT NULL, -- Identificador del producto o insumo
    CantidadRequerida INT NOT NULL, -- Cantidad del insumo necesario para el servicio
    PRIMARY KEY (ServicioID, ProductoID), -- Clave primaria compuesta
    FOREIGN KEY (ServicioID) REFERENCES Servicios(ServicioID), -- Relación con la tabla Servicios
    FOREIGN KEY (ProductoID) REFERENCES Inventario(ProductoID) -- Relación con la tabla Inventario
*/

class ServiciosInventarioModel {
    private $conn;
    
    public function __construct() {
        $this->conn = (new Database())->conn;
    }

    //Funcción para obtener los productos (insumos) asociados a cada servicio
    public function productosAsociados($data) {
        // Limpia y filtra los datos antes de insertarlos en la base de datos
        $ServicioID = isset($data['ServicioID']) && $data['ServicioID'] !== '' ? $data['ServicioID'] : null;
        // Consulta base
        $sql = "SELECT 
                    s.ServicioID,
                    s.NombreServicio,
                    s.Descripcion AS DescripcionServicio,
                    s.Precio,
                    i.ProductoID,
                    i.NombreProducto,
                    i.Cantidad AS CantidadDisponible,
                    i.Descripcion AS DescripcionProducto,
                    si.CantidadRequerida
                FROM Servicios s
                JOIN Servicio_Insumos si ON s.ServicioID = si.ServicioID
                JOIN Inventario i ON si.ProductoID = i.ProductoID";
        // Verificar si se proporcionó un ServicioID
        if (!empty($ServicioID)) {
            $sql .= " WHERE s.ServicioID = :ServicioID";
        }
        // Preparar y ejecutar consulta
        $stmt = $this->conn->prepare($sql);

        if (!empty($ServicioID)) {
            $stmt->bindParam(':ServicioID', $ServicioID, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Lista de Servicios
    public function listaServicios() {
        $sql = "SELECT 
                    s.ServicioID,
                    s.NombreServicio,
                    s.Descripcion AS DescripcionServicio,
                    s.Precio
                FROM Servicios s";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Lista de Inventario
    public function listaInventario() {
        $sql = "SELECT 
                    i.ProductoID,
                    i.NombreProducto,
                    i.Cantidad AS CantidadDisponible,
                    i.Descripcion AS DescripcionProducto    
                FROM Inventario i";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Asociar productos a un servicio
    public function asociarProductos($data) {
        // Limpia y filtra los datos antes de insertarlos en la base de datos
        $ServicioID = isset($data['ServicioID']) && $data['ServicioID'] !== '' ? $data['ServicioID'] : null;
        $ProductoID = isset($data['ProductoID']) && $data['ProductoID'] !== '' ? $data['ProductoID'] : null;
        $CantidadRequerida = isset($data['CantidadRequerida']) && $data['CantidadRequerida'] !== '' ? $data['CantidadRequerida'] : null;
        // Validar que los datos sean correctos
        if (!isset($data['ServicioID']) || !isset($data['ProductoID']) || !isset($data['CantidadRequerida'])) {
            throw new Exception("Faltan datos de asociación de productos.");
        }
        // Consulta base
        $sql = "INSERT INTO Servicio_Insumos (ServicioID, ProductoID, CantidadRequerida) VALUES (:ServicioID, :ProductoID, :CantidadRequerida)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':ServicioID', $ServicioID, PDO::PARAM_INT);
        $stmt->bindParam(':ProductoID', $ProductoID, PDO::PARAM_INT);
        $stmt->bindParam(':CantidadRequerida', $CantidadRequerida, PDO::PARAM_INT);
        $stmt->execute();
        // Verificar si se insertó alguna fila
        if ($stmt->rowCount() > 0) {
            return true;  // Producto asociado
        } else {
            return false; // No se insertó ningún producto
        }
    }
    // Crear Servicio
    public function crearServicio($data) {
        // Limpia y filtra los datos antes de insertarlos en la base de datos
        $NombreServicio = isset($data['NombreServicio']) && $data['NombreServicio'] !== '' ? $data['NombreServicio'] : null;
        $Descripcion = isset($data['Descripcion']) && $data['Descripcion'] !== '' ? $data['Descripcion'] : null;
        $Precio = isset($data['Precio']) && $data['Precio'] !== '' ? $data['Precio'] : null;
        // Validar que los datos sean correctos
        if (!isset($data['NombreServicio']) || !isset($data['Descripcion']) || !isset($data['Precio'])) {
            throw new Exception("Faltan datos de creación de servicio.");
        }
        $sql = "INSERT INTO Servicios (NombreServicio, Descripcion, Precio) VALUES (:NombreServicio, :Descripcion, :Precio)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':NombreServicio', $NombreServicio);
        $stmt->bindParam(':Descripcion', $Descripcion);
        $stmt->bindParam(':Precio', $Precio);
        $stmt->execute();
        // Verificar si se insertó alguna fila
        if ($stmt->rowCount() > 0) {
            return true;  // Servicio creado
        } else {
            return false; // No se insertó ningún servicio  
        }
    }
    // Crear Producto
    public function crearProducto($data) {
        // Limpia y filtra los datos antes de insertarlos en la base de datos
        $NombreProducto = isset($data['NombreProducto']) && $data['NombreProducto'] !== '' ? $data['NombreProducto'] : null;
        $Cantidad = isset($data['Cantidad']) && $data['Cantidad'] !== '' ? $data['Cantidad'] : null;
        $Descripcion = isset($data['Descripcion']) && $data['Descripcion'] !== '' ? $data['Descripcion'] : null;
        // Validar que los datos sean correctos
        if (!isset($data['NombreProducto']) || !isset($data['Cantidad']) || !isset($data['Descripcion'])) {
            throw new Exception("Faltan datos de creación de producto.");
        }
        $sql = "INSERT INTO Inventario (NombreProducto, Cantidad, Descripcion) VALUES (:NombreProducto, :Cantidad, :Descripcion)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':NombreProducto', $data['NombreProducto']);
        $stmt->bindParam(':Cantidad', $data['Cantidad']);
        $stmt->bindParam(':Descripcion', $data['Descripcion']);
        $stmt->execute();
        // Verificar si se insertó alguna fila
        if ($stmt->rowCount() > 0) {
            return true;  // Producto creado
        } else {
            return false; // No se insertó ningún producto
        }   
    }
    // Actualizar Servicio
    public function actualizarServicio($data) {
        // Validar que los datos sean correctos
        if (!isset($data['ServicioID'])) {
            throw new Exception("Faltan datos de actualización de servicio.");
        }
        // Paso 1: Obtener el registro actual   
        $sql = "SELECT * FROM Servicios WHERE ServicioID = :ServicioID";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':ServicioID', $data['ServicioID']);
        $stmt->execute();
        $servicio = $stmt->fetch(PDO::FETCH_ASSOC);
        // Paso 2: Validar que el servicio exista
        if (!$servicio) {
            throw new Exception("El servicio no existe en la base de datos.");
        }
        // Paso 3: Usar los valores actuales como predeterminados
        $NombreServicio = isset($data['NombreServicio']) && $data['NombreServicio'] !== '' ? $data['NombreServicio'] : $servicio['NombreServicio'];
        $Descripcion = isset($data['Descripcion']) && $data['Descripcion'] !== '' ? $data['Descripcion'] : $servicio['Descripcion'];
        $Precio = isset($data['Precio']) && $data['Precio'] !== '' ? $data['Precio'] : $servicio['Precio'];
        // Consulta SQL para actualizar el servicio
        $sql = "UPDATE Servicios SET NombreServicio = :NombreServicio, Descripcion = :Descripcion, Precio = :Precio WHERE ServicioID = :ServicioID";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':ServicioID', $data['ServicioID']);
        $stmt->bindParam(':NombreServicio', $NombreServicio);
        $stmt->bindParam(':Descripcion', $Descripcion);
        $stmt->bindParam(':Precio', $Precio);
        $stmt->execute();
        // Verificar si se actualizó alguna fila
        if ($stmt->rowCount() > 0) {
            return true;  // Servicio actualizado
        } else {
            return false; // No se actualizó ningún servicio
        }
    }
    // Actualizar Producto
    public function actualizarProducto($data) {
        // Validar que los datos sean correctos
        if (!isset($data['ProductoID'])) {
            throw new Exception("Faltan datos de actualización de producto.");
        }
        // Paso 1: Obtener el registro actual
        $sql = "SELECT * FROM Inventario WHERE ProductoID = :ProductoID";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':ProductoID', $data['ProductoID']);
        $stmt->execute();
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);
        // Paso 2: Validar que el producto exista
        if (!$producto) {
            throw new Exception("El producto no existe en la base de datos.");
        }
        // Paso 3: Usar los valores actuales como predeterminados
        $NombreProducto = isset($data['NombreProducto']) && $data['NombreProducto'] !== '' ? $data['NombreProducto'] : $producto['NombreProducto']; 
        $Cantidad = isset($data['Cantidad']) && $data['Cantidad'] !== '' ? $data['Cantidad'] : $producto['Cantidad'];
        $Descripcion = isset($data['Descripcion']) && $data['Descripcion'] !== '' ? $data['Descripcion'] : $producto['Descripcion'];
        // Consulta SQL para actualizar el producto
        $sql = "UPDATE Inventario SET NombreProducto = :NombreProducto, Cantidad = :Cantidad, Descripcion = :Descripcion WHERE ProductoID = :ProductoID";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':ProductoID', $data['ProductoID']);
        $stmt->bindParam(':NombreProducto', $NombreProducto);
        $stmt->bindParam(':Cantidad', $Cantidad);
        $stmt->bindParam(':Descripcion', $Descripcion);
        $stmt->execute();
        // Verificar si se actualizó alguna fila
        if ($stmt->rowCount() > 0) {
            return true;  // Producto actualizado
        } else {
            return false; // No se actualizó ningún producto
        }
    }
    // Eliminar Servicio
    public function eliminarServicio($data) {
        // Validar que los datos sean correctos
        if (!isset($data['ServicioID'])) {
            throw new Exception("Faltan datos de eliminación de servicio.");
        }
        // Consulta SQL para eliminar el servicio
        $sql = "DELETE FROM Servicios WHERE ServicioID = :ServicioID";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':ServicioID', $data['ServicioID']);
        $stmt->execute();
        // Verificar si se eliminó alguna fila
        if ($stmt->rowCount() > 0) {
            return true;  // Servicio eliminado
        } else {
            return false; // No se eliminó ningún servicio
        }
    }
    // Eliminar Producto
    public function eliminarProducto($data) {
        // Validar que los datos sean correctos
        if (!isset($data['ProductoID'])) {
            throw new Exception("Faltan datos de eliminación de producto.");
        }
        // Consulta SQL para eliminar el producto
        $sql = "DELETE FROM Inventario WHERE ProductoID = :ProductoID";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':ProductoID', $data['ProductoID']);
        $stmt->execute();
        // Verificar si se eliminó alguna fila
        if ($stmt->rowCount() > 0) {
            return true;  // Producto eliminado
        } else {
            return false; // No se eliminó ningún producto
        }
    }
}   