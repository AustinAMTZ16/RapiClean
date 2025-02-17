<?php
include_once 'app/config/Database.php';

/* 
-- Tabla de Roles
    RolID INT AUTO_INCREMENT PRIMARY KEY, -- Identificador único del rol
    NombreRol VARCHAR(50) NOT NULL, -- Nombre del rol (Ej. Administrador, Cliente, Vendedor)
    Descripcion TEXT -- Descripción detallada del rol
-- Tabla de Permisos
    PermisoID INT AUTO_INCREMENT PRIMARY KEY, -- Identificador único del permiso
    NombrePermiso VARCHAR(100) NOT NULL, -- Nombre del permiso (Ej. Gestionar Usuarios, Ver Reportes)
    Descripcion TEXT -- Descripción detallada del permiso
-- Tabla RolesPermisos (Relación entre Roles y Permisos)
    RolPermisoID INT AUTO_INCREMENT PRIMARY KEY, -- Identificador único de la relación
    RolID INT NOT NULL, -- Identificador del rol
    PermisoID INT NOT NULL, -- Identificador del permiso
    FOREIGN KEY (RolID) REFERENCES Roles(RolID), -- Relación con la tabla Roles
    FOREIGN KEY (PermisoID) REFERENCES Permisos(PermisoID) -- Relación con la tabla Permisos
*/



class RolesModel {
    private $conn;
    
    public function __construct() {
        $this->conn = (new Database())->conn;
    }

    // Lista Permisos asignados a cada rol
    public function permisosAsignados($data) {
        // Limpia y filtra los datos antes de insertarlos en la base de datos
        $RolID = isset($data['RolID']) && $data['RolID'] !== '' ? $data['RolID'] : null;

        // Consulta base
        $sql = "SELECT 
                    r.RolID,
                    r.NombreRol,
                    r.Descripcion AS DescripcionRol,
                    p.PermisoID,
                    p.NombrePermiso,
                    p.Descripcion AS DescripcionPermiso
                FROM Roles r
                JOIN RolesPermisos rp ON r.RolID = rp.RolID
                JOIN Permisos p ON rp.PermisoID = p.PermisoID";

        // Verifica si se proporcionó un RolID
        if (!empty($RolID)) {
            $sql .= " WHERE r.RolID = :RolID";
        }

        // Preparar y ejecutar consulta
        $stmt = $this->conn->prepare($sql);

        if (!empty($RolID)) {
            $stmt->bindParam(':RolID', $RolID, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Lista de Roles
    public function listaRoles() {
        $sql = "SELECT 
                    r.RolID,
                    r.NombreRol,
                    r.Descripcion AS DescripcionRol
                FROM Roles r";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Lista de Permisos
    public function listaPermisos() {
        $sql = "SELECT 
                    p.PermisoID,
                    p.NombrePermiso,
                    p.Descripcion AS DescripcionPermiso
                FROM Permisos p";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Asignar Permisos a un Rol
    public function asignarPermisos($data) {
        // Limpia y filtra los datos antes de insertarlos en la base de datos   
        $RolID = isset($data['RolID']) && $data['RolID'] !== '' ? $data['RolID'] : null;
        $PermisoID = isset($data['PermisoID']) && $data['PermisoID'] !== '' ? $data['PermisoID'] : null;
        // Validar que los datos sean correctos
        if (!isset($data['RolID']) || !isset($data['PermisoID'])) {
            throw new Exception("Faltan datos de asignación de permisos.");
        }
        // Consulta SQL para asignar permisos a un rol
        $sql = "INSERT INTO RolesPermisos (RolID, PermisoID) VALUES (:RolID, :PermisoID)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':RolID', $RolID, PDO::PARAM_INT);
        $stmt->bindParam(':PermisoID', $PermisoID, PDO::PARAM_INT);
        $stmt->execute();
        // Verificar si se insertó alguna fila
        if ($stmt->rowCount() > 0) {
            return true;  // Permiso asignado
        } else {
            return false; // No se insertó ningún permiso
        }
    }    
    // Crear Rol
    public function crearRol($data) {
        // Limpia y filtra los datos antes de insertarlos en la base de datos
        $NombreRol = isset($data['NombreRol']) && $data['NombreRol'] !== '' ? $data['NombreRol'] : null;
        $Descripcion = isset($data['Descripcion']) && $data['Descripcion'] !== '' ? $data['Descripcion'] : null;
        // Validar que los datos sean correctos
        if (!isset($data['NombreRol']) || !isset($data['Descripcion'])) {
            throw new Exception("Faltan datos de creación de rol.");
        }
        // Consulta SQL para crear un rol
        $sql = "INSERT INTO Roles (NombreRol, Descripcion) VALUES (:NombreRol, :Descripcion)";  
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':NombreRol', $NombreRol);
        $stmt->bindParam(':Descripcion', $Descripcion);
        $stmt->execute();
        // Verificar si se insertó alguna fila
        if ($stmt->rowCount() > 0) {
            return true;  // Rol creado
        } else {
            return false; // No se insertó ningún rol
        }
    }
    // Crear Permiso
    public function crearPermiso($data) {
        // Limpia y filtra los datos antes de insertarlos en la base de datos
        $NombrePermiso = isset($data['NombrePermiso']) && $data['NombrePermiso'] !== '' ? $data['NombrePermiso'] : null;
        $Descripcion = isset($data['Descripcion']) && $data['Descripcion'] !== '' ? $data['Descripcion'] : null;
        // Validar que los datos sean correctos
        if (!isset($data['NombrePermiso']) || !isset($data['Descripcion'])) {
            throw new Exception("Faltan datos de creación de permiso.");
        }
        // Consulta SQL para crear un permiso
        $sql = "INSERT INTO Permisos (NombrePermiso, Descripcion) VALUES (:NombrePermiso, :Descripcion)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':NombrePermiso', $NombrePermiso);
        $stmt->bindParam(':Descripcion', $Descripcion);
        $stmt->execute();
        // Verificar si se insertó alguna fila
        if ($stmt->rowCount() > 0) {
            return true;  // Permiso creado
        } else {    
            return false; // No se insertó ningún permiso
        }
    }
    // Actualizar Rol
    public function actualizarRol($data) {
        // Validar que los datos sean correctos
        if (!isset($data['RolID'])) {
            throw new Exception("Faltan datos de actualización de rol.");
        }
        // Paso 1: Obtener el registro actual
        $sql = "SELECT * FROM Roles WHERE RolID = :RolID";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':RolID', $data['RolID']);
        $stmt->execute();
        $rol = $stmt->fetch(PDO::FETCH_ASSOC);
        // Paso 2: Validar que el rol exista
        if (!$rol) {
            throw new Exception("El rol no existe en la base de datos.");
        }
        // Paso 3: Usar los valores actuales como predeterminados
        $NombreRol = isset($data['NombreRol']) && $data['NombreRol'] !== '' ? $data['NombreRol'] : $rol['NombreRol'];
        $Descripcion = isset($data['Descripcion']) && $data['Descripcion'] !== '' ? $data['Descripcion'] : $rol['Descripcion'];
        // Consulta SQL para actualizar el rol
        $sql = "UPDATE Roles SET NombreRol = :NombreRol, Descripcion = :Descripcion WHERE RolID = :RolID";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':RolID', $data['RolID']);
        $stmt->bindParam(':NombreRol', $NombreRol);
        $stmt->bindParam(':Descripcion', $Descripcion);
        $stmt->execute();
        // Verificar si se actualizó alguna fila
        if ($stmt->rowCount() > 0) {
            return true;  // Rol actualizado
        } else {
            return false; // No se actualizó ningún rol 
        }
    }
    // Actualizar Permiso
    public function actualizarPermiso($data) {
        // Validar que los datos sean correctos
        if (!isset($data['PermisoID'])) {
            throw new Exception("Faltan datos de actualización de permiso.");
        }
        // Paso 1: Obtener el registro actual
        $sql = "SELECT * FROM Permisos WHERE PermisoID = :PermisoID";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':PermisoID', $data['PermisoID']);
        $stmt->execute();
        $permiso = $stmt->fetch(PDO::FETCH_ASSOC);
        // Paso 2: Validar que el permiso exista
        if (!$permiso) {
            throw new Exception("El permiso no existe en la base de datos.");
        }
        // Paso 3: Usar los valores actuales como predeterminados
        $NombrePermiso = isset($data['NombrePermiso']) && $data['NombrePermiso'] !== '' ? $data['NombrePermiso'] : $permiso['NombrePermiso'];
        $Descripcion = isset($data['Descripcion']) && $data['Descripcion'] !== '' ? $data['Descripcion'] : $permiso['Descripcion'];
        // Consulta SQL para actualizar el permiso
        $sql = "UPDATE Permisos SET NombrePermiso = :NombrePermiso, Descripcion = :Descripcion WHERE PermisoID = :PermisoID";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':PermisoID', $data['PermisoID']);
        $stmt->bindParam(':NombrePermiso', $NombrePermiso);
        $stmt->bindParam(':Descripcion', $Descripcion); 
        $stmt->execute();
        // Verificar si se actualizó alguna fila
        if ($stmt->rowCount() > 0) {
            return true;  // Permiso actualizado
        } else {
            return false; // No se actualizó ningún permiso
        }   
    }   
    // Eliminar Rol
    public function eliminarRol($data) {
        // Validar que los datos sean correctos
        if (!isset($data['RolID'])) {
            throw new Exception("Faltan datos de eliminación de rol.");
        }   
        // Consulta SQL para eliminar el rol
        $sql = "DELETE FROM Roles WHERE RolID = :RolID";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':RolID', $data['RolID']);
        $stmt->execute();
        // Verificar si se eliminó alguna fila
        if ($stmt->rowCount() > 0) {
            return true;  // Rol eliminado
        } else {
            return false; // No se eliminó ningún rol
        }
    }   
    // Eliminar Permiso
    public function eliminarPermiso($data) {
        // Validar que los datos sean correctos
        if (!isset($data['PermisoID'])) {
            throw new Exception("Faltan datos de eliminación de permiso.");
        }
        // Consulta SQL para eliminar el permiso
        $sql = "DELETE FROM Permisos WHERE PermisoID = :PermisoID";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':PermisoID', $data['PermisoID']);
        $stmt->execute();
        // Verificar si se eliminó alguna fila
        if ($stmt->rowCount() > 0) {
            return true;  // Permiso eliminado
        } else {
            return false; // No se eliminó ningún permiso
        }   
    }       
}