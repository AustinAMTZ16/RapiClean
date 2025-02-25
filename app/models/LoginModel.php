<?php
include_once 'app/config/Database.php';

////Tabla Usuarios
//////UsuarioID = Identificador único del usuario
//////Nombre = Nombre completo del usuario
//////Email = Correo electrónico único del usuario
//////Password = Contraseña cifrada del usuario
//////RolID = Rol asignado al usuario
//////Estado = Estado del usuario
//////FechaCreacion = Fecha y hora de creación del usuario
//////UltimoAcceso  = Fecha y hora del último acceso del usuario

////Tabla Clientes
//////ClienteID = Identificador único del cliente
//////Nombre = Nombre completo del cliente
//////Email = Correo electrónico único del cliente
//////Password = Contraseña cifrada del cliente
//////PuntosRecompensa = Puntos de recompensa acumulados
//////Estado = Estado del cliente
//////FechaCreacion = Fecha y hora de registro del cliente
//////UltimoAcceso = Fecha y hora del último acceso del cliente

class LoginModel {
    private $conn;
    
    public function __construct() {
        $this->conn = (new Database())->conn;
    }

    // Iniciar sesión CLIENTE - USUARIO (Valida si esta activo el usuario o cliente y actualiza el ultimo acceso)
    public function iniciarSesion($data) {
        // Validar que los datos sean correctos
        if (!isset($data['Email']) || !isset($data['Password'])) {
            throw new Exception("Faltan datos de inicio de sesión.");
        }
        // Consulta SQL para obtener el usuario o cliente
        $sql = "SELECT 'Usuario' AS Tipo, UsuarioID AS ID, Nombre, Email, RolID AS Rol, Estado, FechaCreacion 
            FROM Usuarios 
            WHERE Email = :Email AND Password = :Password
            UNION 
            SELECT 'Cliente' AS Tipo, ClienteID AS ID, Nombre, Email, NULL AS Rol, Estado, FechaCreacion 
            FROM Clientes 
            WHERE Email = :Email AND Password = :Password";
        $stmt = $this->conn->prepare($sql);
        // Limpia y filtra los datos antes de insertarlos en la base de datos
        $data['Email'] = filter_var($data['Email'], FILTER_SANITIZE_EMAIL); 
        $data['Password'] = filter_var($data['Password'], FILTER_SANITIZE_STRING);
        $stmt->bindParam(':Email', $data['Email']);
        $stmt->bindParam(':Password', $data['Password']);

        // Ejecuta la consulta
        if ($stmt->execute()) {
            // Obtiene el resultado de la consulta
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verifica si se encontró algún usuario
            if ($usuario === false) {
                throw new Exception("El usuario no existe en la base de datos."); // 404 Not Found
            }
            // Validación de estado del usuario
            if ($usuario['Estado'] == 'Activo') {
                if($usuario['Tipo'] == 'Usuario'){
                    $sql = "SELECT UsuarioID,Nombre,Email,RolID,Estado FROM Usuarios WHERE UsuarioID = :UsuarioID";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bindParam(':UsuarioID', $usuario['ID']);
                    $stmt->execute();
                    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

                    //Update UltimoAcceso
                    $sql = "UPDATE Usuarios SET UltimoAcceso = NOW() WHERE UsuarioID = :UsuarioID";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bindParam(':UsuarioID', $usuario['UsuarioID']);
                    $stmt->execute();

                    return $usuario;
                }else if($usuario['Tipo'] == 'Cliente'){
                    $sql = "SELECT ClienteID,Nombre,Email,PuntosRecompensa,Estado FROM Clientes WHERE ClienteID = :ClienteID";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bindParam(':ClienteID', $usuario['ID']);
                    $stmt->execute();
                    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

                    //Update UltimoAcceso
                    $sql = "UPDATE Clientes SET UltimoAcceso = NOW() WHERE ClienteID = :ClienteID";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bindParam(':ClienteID', $usuario['ClienteID']);
                    $stmt->execute();

                    return $usuario;
                } 
            } else {
                throw new Exception("Usuario inactivo."); // 403 Forbidden
            }
        } else {
            throw new Exception("Error al iniciar sesión."); // 500 Internal Server Error
        }
    }

    //Registrar Usuario
    public function registrarUsuario($data) {
        // Limpia y filtra los datos antes de insertarlos en la base de datos
        $Nombre = isset($data['Nombre']) && $data['Nombre'] !== '' ? $data['Nombre'] : null;
        $Email = isset($data['Email']) && $data['Email'] !== '' ? $data['Email'] : null;
        $Password = isset($data['Password']) && $data['Password'] !== '' ? $data['Password'] : null;  
        // Validar que los datos sean correctos
        if (!isset($data['Nombre']) || !isset($data['Email']) || !isset($data['Password'])) {
            throw new Exception("Faltan datos de registro de usuario.");
        }
        // Consulta SQL para registrar el usuario
        $sql = "INSERT INTO Usuarios (Nombre, Email, Password, RolID, Estado) 
                VALUES (:Nombre, :Email, :Password, 11, 'Activo')";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':Nombre', $Nombre);
        $stmt->bindParam(':Email', $Email);
        $stmt->bindParam(':Password', $Password);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        } else {
            throw new Exception("Error al registrar el usuario.");
        }   
    }

    //Registrar Cliente
    public function registrarCliente($data) {
        // Limpia y filtra los datos antes de insertarlos en la base de datos
        $Nombre = isset($data['Nombre']) && $data['Nombre'] !== '' ? $data['Nombre'] : null;
        $Email = isset($data['Email']) && $data['Email'] !== '' ? $data['Email'] : null;
        $Password = isset($data['Password']) && $data['Password'] !== '' ? $data['Password'] : null;
        // Validar que los datos sean correctos
        if (!isset($data['Nombre']) || !isset($data['Email']) || !isset($data['Password'])) {
            throw new Exception("Faltan datos de registro de cliente.");
        }   
        // Consulta SQL para registrar el cliente
        $sql = "INSERT INTO Clientes (Nombre, Email, Password, RolID, Estado) 
                VALUES (:Nombre, :Email, :Password, 0, 'Activo')";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':Nombre', $Nombre);
        $stmt->bindParam(':Email', $Email);
        $stmt->bindParam(':Password', $Password);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId(); 
        } else {
            throw new Exception("Error al registrar el cliente.");
        }
    }

    //Actualizar Usuario
    public function actualizarUsuario($data) {
        // Validar que los datos sean correctos
        if (!isset($data['UsuarioID'])) {
            throw new Exception("Faltan datos de actualización de usuario.");
        }

        // Paso 1: Obtener el registro actual
        $sql = "SELECT * FROM Usuarios WHERE UsuarioID = :UsuarioID";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':UsuarioID', $data['UsuarioID']);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        // Paso 2: Validar que el usuario exista
        if (!$usuario) {
            throw new Exception("El usuario no existe en la base de datos.");
        }

        //Paso 3: Usar los valores actuales como predeterminados
        $Nombre = isset($data['Nombre']) && $data['Nombre'] !== '' ? $data['Nombre'] : $usuario['Nombre'];
        $Email = isset($data['Email']) && $data['Email'] !== '' ? $data['Email'] : $usuario['Email'];
        $Password = isset($data['Password']) && $data['Password'] !== '' ? $data['Password'] : $usuario['Password'];
        $RolID = isset($data['RolID']) && $data['RolID'] !== '' ? $data['RolID'] : $usuario['RolID'];
        $Estado = isset($data['Estado']) && $data['Estado'] !== '' ? $data['Estado'] : $usuario['Estado'];

        // Consulta SQL para actualizar el usuario
        $sql = "UPDATE Usuarios SET Nombre = :Nombre, Email = :Email, Password = :Password, RolID = 11, Estado = :Estado WHERE UsuarioID = :UsuarioID";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':UsuarioID', $data['UsuarioID']);
        $stmt->bindParam(':Nombre', $Nombre);
        $stmt->bindParam(':Email', $Email);
        $stmt->bindParam(':Estado', $Estado);
        $stmt->bindParam(':Password', $Password);

        if ($stmt->execute()) {
            return true;
        } else {
            throw new Exception("Error al actualizar el usuario.");
        }
    }

    //Actualizar Cliente
    public function actualizarCliente($data) {
        // Validar que los datos sean correctos
        if (!isset($data['ClienteID'])) {
            throw new Exception("Faltan datos de actualización de cliente.");
        }

        // Paso 1: Obtener el registro actual
        $sql = "SELECT * FROM Clientes WHERE ClienteID = :ClienteID";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':ClienteID', $data['ClienteID']);
        $stmt->execute();
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

        // Paso 2: Validar que el cliente exista
        if (!$cliente) {
            throw new Exception("El cliente no existe en la base de datos.");
        }
        // Paso 3: Usar los valores actuales como predeterminados
        $Nombre = isset($data['Nombre']) && $data['Nombre'] !== '' ? $data['Nombre'] : $cliente['Nombre'];
        $Email = isset($data['Email']) && $data['Email'] !== '' ? $data['Email'] : $cliente['Email'];
        $Password = isset($data['Password']) && $data['Password'] !== '' ? $data['Password'] : $cliente['Password'];
        $PuntosRecompensa = isset($data['PuntosRecompensa']) && $data['PuntosRecompensa'] !== '' ? $data['PuntosRecompensa'] : $cliente['PuntosRecompensa'];
        $Estado = isset($data['Estado']) && $data['Estado'] !== '' ? $data['Estado'] : $cliente['Estado'];

        // Consulta SQL para actualizar el cliente
        $sql = "UPDATE Clientes SET Nombre = :Nombre, Email = :Email, Password = :Password, PuntosRecompensa = :PuntosRecompensa, Estado = :Estado WHERE ClienteID = :ClienteID";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':ClienteID', $data['ClienteID']);
        $stmt->bindParam(':Nombre', $Nombre);
        $stmt->bindParam(':Email', $Email); 
        $stmt->bindParam(':Password', $Password);
        $stmt->bindParam(':PuntosRecompensa', $PuntosRecompensa);
        $stmt->bindParam(':Estado', $Estado);

        if ($stmt->execute()) {
            return true;
        } else {
            throw new Exception("Error al actualizar el cliente."); 
        }
    }

    //Eliminar Usuario
    public function eliminarUsuario($data) {
        // Limpia y filtra los datos antes de insertarlos en la base de datos
        $UsuarioID = isset($data['UsuarioID']) && $data['UsuarioID'] !== '' ? $data['UsuarioID'] : null;    
        // Validar que los datos sean correctos
        if (!isset($data['UsuarioID'])) {   
            throw new Exception("Faltan datos de eliminación de usuario.");
        }
        // Consulta SQL para eliminar el usuario
        $sql = "DELETE FROM Usuarios WHERE UsuarioID = :UsuarioID";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':UsuarioID', $UsuarioID);
        $stmt->execute();
        // Verificar si se eliminó alguna fila
        if ($stmt->rowCount() > 0) {
            return true;  // Usuario eliminado
        } else {
            return false; // No se eliminó ningún usuario (ID no existe)
        }
    }   

    //Eliminar Cliente
    public function eliminarCliente($data) {
        // Limpia y filtra los datos antes de insertarlos en la base de datos
        $ClienteID = isset($data['ClienteID']) && $data['ClienteID'] !== '' ? $data['ClienteID'] : null;    
        // Validar que los datos sean correctos
        if (!isset($data['ClienteID'])) {   
            throw new Exception("Faltan datos de eliminación de cliente.");
        }
        // Consulta SQL para eliminar el cliente
        $sql = "DELETE FROM Clientes WHERE ClienteID = :ClienteID";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':ClienteID', $ClienteID);
        $stmt->execute();
        // Verificar si se eliminó alguna fila
        if ($stmt->rowCount() > 0) {
            return true;  // Cliente eliminado
        } else {
            return false; // No se eliminó ningún cliente (ID no existe)
        }
    }   
    
    //Obtener Menu
    public function obtenerMenu($data) {
        // Validar que el campo Email esté presente y no esté vacío
        $usuarioEmail = isset($data['Email']) && !empty($data['Email']) ? $data['Email'] : null;
    
        // Si el Email no es válido, podemos retornar una respuesta vacía o lanzar una excepción
        if ($usuarioEmail === null) {
            throw new Exception('El correo electrónico es requerido.');
        }
    
        // Consulta SQL con los parámetros correctos
        $sql = "SELECT 
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
                WHERE u.Email = :Email  
                AND u.Estado = 'Activo'  
                AND m1.Estado = 'Activo'
                ORDER BY menu_id ASC;";
    
        try {
            // Preparar la consulta
            $stmt = $this->conn->prepare($sql); 
    
            // Bind de los parámetros
            $stmt->bindParam(':Email', $usuarioEmail, PDO::PARAM_STR);
    
            // Ejecutar la consulta
            $stmt->execute();
    
            // Retornar los resultados en formato de arreglo asociativo
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        } catch (PDOException $e) {
            // Manejo de errores
            error_log('Error en la consulta SQL: ' . $e->getMessage());
            return []; // O podrías lanzar una excepción dependiendo del flujo que deseas
        }
    }

    //Obtener Lista de Usuarios
    public function obtenerListaUsuarios() {
        $sql = "SELECT u.UsuarioID,u.Nombre,u.Email,u.RolID,r.NombreRol,r.Descripcion,u.Estado,u.FechaCreacion,u.UltimoAcceso FROM Usuarios u
                INNER JOIN Roles r ON u.RolID = r.RolID 
                ORDER BY UsuarioID ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }   

    //Obtener Lista de Clientes
    public function obtenerListaClientes() {
        $sql = "SELECT ClienteID,Nombre,Email,PuntosRecompensa,Estado,FechaCreacion,UltimoAcceso FROM Clientes ORDER BY ClienteID ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}   