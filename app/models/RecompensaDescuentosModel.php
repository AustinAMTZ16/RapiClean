<?php
include_once 'app/config/Database.php';

/* 
-- Tabla de Descuentos
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
-- Tabla Recompensas
    RecompensaID INT AUTO_INCREMENT PRIMARY KEY, -- Identificador único de la recompensa
    Descripcion VARCHAR(255) NOT NULL, -- Descripción de la recompensa
    PuntosNecesarios INT NOT NULL -- Cantidad de puntos requeridos para obtener la recompensa
    UsuarioID INT NOT NULL, -- Usuario que creó la recompensa
    FOREIGN KEY (UsuarioID) REFERENCES Usuarios(UsuarioID) -- Relación con la tabla Usuarios
-- Tabla de Canjes de Recompensas (Ejemplo)
    CanjeID INT AUTO_INCREMENT PRIMARY KEY,
    ClienteID INT NOT NULL,
    RecompensaID INT NOT NULL,
    FechaCanje DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ClienteID) REFERENCES Clientes(ClienteID),
    FOREIGN KEY (RecompensaID) REFERENCES Recompensas(RecompensaID)
 */

class RecompensaDescuentosModel {
    private $conn;
    
    public function __construct() {
        $this->conn = (new Database())->conn;
    }

    //Crear Descuento
    public function crearDescuento($data) {
        // Limpia y filtra los datos antes de insertarlos en la base de datos
        $CodigoPromocion = isset($data['CodigoPromocion']) && $data['CodigoPromocion'] !== '' ? $data['CodigoPromocion'] : null;
        $Nombre = isset($data['Nombre']) && $data['Nombre'] !== '' ? $data['Nombre'] : null;
        $Descripcion = isset($data['Descripcion']) && $data['Descripcion'] !== '' ? $data['Descripcion'] : null;
        $TipoDescuento = isset($data['TipoDescuento']) && $data['TipoDescuento'] !== '' ? $data['TipoDescuento'] : null;
        $ValorDescuento = isset($data['ValorDescuento']) && $data['ValorDescuento'] !== '' ? $data['ValorDescuento'] : null;
        $VigenciaInicio = isset($data['VigenciaInicio']) && $data['VigenciaInicio'] !== '' ? $data['VigenciaInicio'] : null;
        $VigenciaFin = isset($data['VigenciaFin']) && $data['VigenciaFin'] !== '' ? $data['VigenciaFin'] : null;    
        $Estado = isset($data['Estado']) && $data['Estado'] !== '' ? $data['Estado'] : null;
        $UsuarioID = isset($data['UsuarioID']) && $data['UsuarioID'] !== '' ? $data['UsuarioID'] : null;
        // Validar que los datos sean correctos
        if (!isset($data['CodigoPromocion']) || !isset($data['Nombre']) || !isset($data['Descripcion']) || !isset($data['TipoDescuento']) || !isset($data['ValorDescuento']) || !isset($data['VigenciaInicio']) || !isset($data['VigenciaFin']) || !isset($data['Estado']) || !isset($data['UsuarioID'])) {
            throw new Exception("Faltan datos de creación de descuento.");
        }
        $sql = "INSERT INTO Descuentos (CodigoPromocion, Nombre, Descripcion, TipoDescuento, ValorDescuento, VigenciaInicio, VigenciaFin, Estado, UsuarioID) VALUES (:CodigoPromocion, :Nombre, :Descripcion, :TipoDescuento, :ValorDescuento, :VigenciaInicio, :VigenciaFin, :Estado, :UsuarioID)";
        $stmt = $this->conn->prepare($sql); 
        $stmt->bindParam(':CodigoPromocion', $CodigoPromocion);
        $stmt->bindParam(':Nombre', $Nombre);
        $stmt->bindParam(':Descripcion', $Descripcion);
        $stmt->bindParam(':TipoDescuento', $TipoDescuento);
        $stmt->bindParam(':ValorDescuento', $ValorDescuento);
        $stmt->bindParam(':VigenciaInicio', $VigenciaInicio);   
        $stmt->bindParam(':VigenciaFin', $VigenciaFin);
        $stmt->bindParam(':Estado', $Estado);
        $stmt->bindParam(':UsuarioID', $UsuarioID);
        $stmt->execute();
        // Verificar si se insertó alguna fila
        if ($stmt->rowCount() > 0) {
            return true;  // Descuento creado
        } else {    
            return false; // No se insertó ningún descuento  
        }
    }   
    //Crear Recompensa
    public function crearRecompensa($data) {
        // Limpia y filtra los datos antes de insertarlos en la base de datos
        $Descripcion = isset($data['Descripcion']) && $data['Descripcion'] !== '' ? $data['Descripcion'] : null;
        $PuntosNecesarios = isset($data['PuntosNecesarios']) && $data['PuntosNecesarios'] !== '' ? $data['PuntosNecesarios'] : null;
        $UsuarioID = isset($data['UsuarioID']) && $data['UsuarioID'] !== '' ? $data['UsuarioID'] : null;
        // Validar que los datos sean correctos
        if (!isset($data['Descripcion']) || !isset($data['PuntosNecesarios']) || !isset($data['UsuarioID'])) {
            throw new Exception("Faltan datos de creación de recompensa.");
        }   
        $sql = "INSERT INTO Recompensas (Descripcion, PuntosNecesarios, UsuarioID) VALUES (:Descripcion, :PuntosNecesarios, :UsuarioID)";
        $stmt = $this->conn->prepare($sql); 
        $stmt->bindParam(':Descripcion', $Descripcion);
        $stmt->bindParam(':PuntosNecesarios', $PuntosNecesarios);
        $stmt->bindParam(':UsuarioID', $UsuarioID);
        $stmt->execute();   
        // Verificar si se insertó alguna fila
        if ($stmt->rowCount() > 0) {
            return true;  // Recompensa creada
        } else {    
            return false; // No se insertó ninguna recompensa  
        }
    }   
    //Listar Descuentos
    public function listarDescuentos() {
        $sql = "SELECT 
                    d.DescuentoID,
                    d.CodigoPromocion,
                    d.Nombre,
                    d.Descripcion,
                    d.TipoDescuento,    
                    d.ValorDescuento,
                    d.VigenciaInicio,
                    d.VigenciaFin,
                    d.Estado,
                    d.FechaAlta,
                    d.UsuarioID
                FROM Descuentos d";
        $stmt = $this->conn->prepare($sql); 
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }   
    //Listar Recompensas
    public function listarRecompensas() {
        $sql = "SELECT 
                    r.RecompensaID,
                    r.Descripcion,
                    r.PuntosNecesarios
                FROM Recompensas r";
        $stmt = $this->conn->prepare($sql); 
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }   
    //Actualizar Descuento
    public function actualizarDescuento($data) {
        // Validar que los datos sean correctos
        if (!isset($data['DescuentoID'])) {
            throw new Exception("Faltan datos de actualización de descuento.");
        }
        // Paso 1: Obtener el registro actual  
        $sql = "SELECT * FROM Descuentos WHERE DescuentoID = :DescuentoID";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':DescuentoID', $data['DescuentoID']);
        $stmt->execute();
        $descuento = $stmt->fetch(PDO::FETCH_ASSOC);
        // Paso 2: Validar que el descuento exista
        if (!$descuento) {
            throw new Exception("El descuento no existe en la base de datos.");
        }   
        // Paso 3: Usar los valores actuales como predeterminados
        $CodigoPromocion = isset($data['CodigoPromocion']) && $data['CodigoPromocion'] !== '' ? $data['CodigoPromocion'] : $descuento['CodigoPromocion'];
        $Nombre = isset($data['Nombre']) && $data['Nombre'] !== '' ? $data['Nombre'] : $descuento['Nombre'];
        $Descripcion = isset($data['Descripcion']) && $data['Descripcion'] !== '' ? $data['Descripcion'] : $descuento['Descripcion'];
        $TipoDescuento = isset($data['TipoDescuento']) && $data['TipoDescuento'] !== '' ? $data['TipoDescuento'] : $descuento['TipoDescuento']; 
        $ValorDescuento = isset($data['ValorDescuento']) && $data['ValorDescuento'] !== '' ? $data['ValorDescuento'] : $descuento['ValorDescuento'];
        $VigenciaInicio = isset($data['VigenciaInicio']) && $data['VigenciaInicio'] !== '' ? $data['VigenciaInicio'] : $descuento['VigenciaInicio'];
        $VigenciaFin = isset($data['VigenciaFin']) && $data['VigenciaFin'] !== '' ? $data['VigenciaFin'] : $descuento['VigenciaFin'];
        $Estado = isset($data['Estado']) && $data['Estado'] !== '' ? $data['Estado'] : $descuento['Estado'];
        $UsuarioID = isset($data['UsuarioID']) && $data['UsuarioID'] !== '' ? $data['UsuarioID'] : $descuento['UsuarioID'];
        // Consulta SQL para actualizar el descuento
        $sql = "UPDATE Descuentos SET CodigoPromocion = :CodigoPromocion, Nombre = :Nombre, Descripcion = :Descripcion, TipoDescuento = :TipoDescuento, ValorDescuento = :ValorDescuento, VigenciaInicio = :VigenciaInicio, VigenciaFin = :VigenciaFin, Estado = :Estado, UsuarioID = :UsuarioID WHERE DescuentoID = :DescuentoID";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':DescuentoID', $data['DescuentoID']);
        $stmt->bindParam(':CodigoPromocion', $CodigoPromocion);
        $stmt->bindParam(':Nombre', $Nombre);
        $stmt->bindParam(':Descripcion', $Descripcion);
        $stmt->bindParam(':TipoDescuento', $TipoDescuento);
        $stmt->bindParam(':ValorDescuento', $ValorDescuento);   
        $stmt->bindParam(':VigenciaInicio', $VigenciaInicio);
        $stmt->bindParam(':VigenciaFin', $VigenciaFin);
        $stmt->bindParam(':Estado', $Estado);
        $stmt->bindParam(':UsuarioID', $UsuarioID);
        $stmt->execute();
        // Verificar si se actualizó alguna fila
        if ($stmt->rowCount() > 0) {
            return true;  // Descuento actualizado
        } else {
            return false; // No se actualizó ningún descuento
        }
    }   
    //Actualizar Recompensa
    public function actualizarRecompensa($data) {
        // Validar que los datos sean correctos
        if (!isset($data['RecompensaID'])) {
            throw new Exception("Faltan datos de actualización de recompensa.");
        }
        // Paso 1: Obtener el registro actual   
        $sql = "SELECT * FROM Recompensas WHERE RecompensaID = :RecompensaID";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':RecompensaID', $data['RecompensaID']);
        $stmt->execute();
        $recompensa = $stmt->fetch(PDO::FETCH_ASSOC);
        // Paso 2: Validar que la recompensa exista
        if (!$recompensa) {
            throw new Exception("La recompensa no existe en la base de datos.");
        }   
        // Paso 3: Usar los valores actuales como predeterminados
        $Descripcion = isset($data['Descripcion']) && $data['Descripcion'] !== '' ? $data['Descripcion'] : $recompensa['Descripcion'];
        $PuntosNecesarios = isset($data['PuntosNecesarios']) && $data['PuntosNecesarios'] !== '' ? $data['PuntosNecesarios'] : $recompensa['PuntosNecesarios'];
        $UsuarioID = isset($data['UsuarioID']) && $data['UsuarioID'] !== '' ? $data['UsuarioID'] : $recompensa['UsuarioID'];
        // Consulta SQL para actualizar la recompensa
        $sql = "UPDATE Recompensas SET Descripcion = :Descripcion, PuntosNecesarios = :PuntosNecesarios, UsuarioID = :UsuarioID WHERE RecompensaID = :RecompensaID";    
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':RecompensaID', $data['RecompensaID']);
        $stmt->bindParam(':Descripcion', $Descripcion);
        $stmt->bindParam(':PuntosNecesarios', $PuntosNecesarios);
        $stmt->bindParam(':UsuarioID', $UsuarioID);
        $stmt->execute();
        // Verificar si se actualizó alguna fila    
        if ($stmt->rowCount() > 0) {
            return true;  // Recompensa actualizada
        } else {
            return false; // No se actualizó ninguna recompensa
        }   
    }   
    //Eliminar Descuento
    public function eliminarDescuento($data) {
        // Validar que los datos sean correctos
        if (!isset($data['DescuentoID'])) {
            throw new Exception("Faltan datos de eliminación de descuento.");
        }
        // Consulta SQL para eliminar el descuento
        $sql = "DELETE FROM Descuentos WHERE DescuentoID = :DescuentoID";
        $stmt = $this->conn->prepare($sql); 
        $stmt->bindParam(':DescuentoID', $data['DescuentoID']);
        $stmt->execute();
        // Verificar si se eliminó alguna fila
        if ($stmt->rowCount() > 0) {
            return true;  // Descuento eliminado
        } else {        
            return false; // No se eliminó ningún descuento
        }   
    }   
    //Eliminar Recompensa
    public function eliminarRecompensa($data) {
        // Validar que los datos sean correctos
        if (!isset($data['RecompensaID'])) {
            throw new Exception("Faltan datos de eliminación de recompensa.");
        }
        // Consulta SQL para eliminar la recompensa
        $sql = "DELETE FROM Recompensas WHERE RecompensaID = :RecompensaID";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':RecompensaID', $data['RecompensaID']);
        $stmt->execute();
        // Verificar si se eliminó alguna fila
        if ($stmt->rowCount() > 0) {
            return true;  // Recompensa eliminada
        } else {
            return false; // No se eliminó ninguna recompensa
        }   
    }   
    //Crear Canje de Recompensa
    public function crearCanjeRecompensa($data) {
        // Limpia y filtra los datos antes de insertarlos en la base de datos
        $ClienteID = isset($data['ClienteID']) && $data['ClienteID'] !== '' ? $data['ClienteID'] : null;
        $RecompensaID = isset($data['RecompensaID']) && $data['RecompensaID'] !== '' ? $data['RecompensaID'] : null;
        // Validar que los datos sean correctos
        if (!isset($data['ClienteID']) || !isset($data['RecompensaID'])) {
            throw new Exception("Faltan datos de creación de canje de recompensa.");
        }
        // Consulta SQL para crear el canje de recompensa
        $sql = "INSERT INTO CanjesRecompensas (ClienteID, RecompensaID) VALUES (:ClienteID, :RecompensaID)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':ClienteID', $data['ClienteID']);
        $stmt->bindParam(':RecompensaID', $data['RecompensaID']);
        $stmt->execute();
        // Verificar si se insertó alguna fila
        if ($stmt->rowCount() > 0) {
            return true;  // Canje de recompensa creado
        } else {    
            return false; // No se insertó ningún canje de recompensa
        }   
    }   
    //Listar Canjes de Recompensas
    public function listarCanjesRecompensas() {
        $sql = "SELECT 
                    c.CanjeID,
                    c.ClienteID,
                    c.RecompensaID,
                    c.FechaCanje
                FROM CanjesRecompensas c";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }   
    //Actualizar Canje de Recompensa
    public function actualizarCanjeRecompensa($data) {
        // Validar que los datos sean correctos
        if (!isset($data['CanjeID'])) {
            throw new Exception("Faltan datos de actualización de canje de recompensa.");
        }
        // Paso 1: Obtener el registro actual
        $sql = "SELECT * FROM CanjesRecompensas WHERE CanjeID = :CanjeID";  
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':CanjeID', $data['CanjeID']);
        $stmt->execute();
        $canje = $stmt->fetch(PDO::FETCH_ASSOC);
        // Paso 2: Validar que el canje exista
        if (!$canje) {  
            throw new Exception("El canje de recompensa no existe en la base de datos.");
        }   
        // Paso 3: Usar los valores actuales como predeterminados
        $ClienteID = isset($data['ClienteID']) && $data['ClienteID'] !== '' ? $data['ClienteID'] : $canje['ClienteID'];
        $RecompensaID = isset($data['RecompensaID']) && $data['RecompensaID'] !== '' ? $data['RecompensaID'] : $canje['RecompensaID'];
        // Consulta SQL para actualizar el canje de recompensa
        $sql = "UPDATE CanjesRecompensas SET ClienteID = :ClienteID, RecompensaID = :RecompensaID WHERE CanjeID = :CanjeID";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':CanjeID', $data['CanjeID']);
        $stmt->bindParam(':ClienteID', $ClienteID);
        $stmt->bindParam(':RecompensaID', $RecompensaID);
        $stmt->execute();
        // Verificar si se actualizó alguna fila
        if ($stmt->rowCount() > 0) {
            return true;  // Canje de recompensa actualizado
        } else {
            return false; // No se actualizó ningún canje de recompensa
        }   
    }   
    //Eliminar Canje de Recompensa
    public function eliminarCanjeRecompensa($data) {
        // Validar que los datos sean correctos
        if (!isset($data['CanjeID'])) {
            throw new Exception("Faltan datos de eliminación de canje de recompensa.");
        }
        // Consulta SQL para eliminar el canje de recompensa
        $sql = "DELETE FROM CanjesRecompensas WHERE CanjeID = :CanjeID";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':CanjeID', $data['CanjeID']);
        $stmt->execute();
        // Verificar si se eliminó alguna fila
        if ($stmt->rowCount() > 0) {
            return true;  // Canje de recompensa eliminado
        } else {
            return false; // No se eliminó ningún canje de recompensa   
        }      
    }   
} 