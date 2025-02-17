<?php
    // Declarar como variables globales
    // Incluir el controlador
    include_once 'app/controllers/LoginController.php';
    include_once 'app/controllers/RolesController.php'; 
    include_once 'app/controllers/ServiciosInventarioController.php';
    include_once 'app/controllers/RecompensaRecompensaDescuentosController.php';  
    include_once 'app/controllers/VentaController.php';
    // Instanciamos el controlador
    $controllerLogin = new LoginController();
    $controllerRoles = new RolesController();
    $controllerServiciosInventario = new ServiciosInventarioController();
    $controllerRecompensaDescuentos = new RecompensaRecompensaDescuentosController();
    $controllerVenta = new VentaController();
    //Obtener el método de la solicitud HTTP
    $requestMethod = $_SERVER['REQUEST_METHOD'];

    // Configurar CORS
    header("Access-Control-Allow-Origin: *"); // Permitir solicitudes desde cualquier origen
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PATCH, DELETE"); // Permitir métodos HTTP
    header("Access-Control-Allow-Headers: Content-Type"); // Permitir ciertos encabezados


    // TRY: CONTROLA LOS METODOS [GET-POST-PATCH-DALETE] Y EXCEPTION A ERROR 500
    try{
        if (isset($_GET['action'])) {
            $action = $_GET['action'];
            $data = json_decode(file_get_contents("php://input"));
            // Ejecutar el manejo según el método de la solicitud HTTP
            switch ($requestMethod) {
                case 'GET':
                    handleGetRequest($action, $data);
                    break;
                case 'POST':
                    handlePostRequest($action, $data);
                    break;
                case 'PATCH':
                    handlePatchRequest($action, $data);
                    break;
                case 'DELETE':
                    handleDeleteRequest($action, $data);
                    break;
                default:
                    http_response_code(404);
                    echo json_encode([
                        'Message' => 'Solicitud no válida.'
                    ], JSON_UNESCAPED_UNICODE);
                    break;
            }
        } else {
                http_response_code(404);
                echo json_encode([
                    'Message' => 'No hay acción. URL'
                ], JSON_UNESCAPED_UNICODE);
        }

    }catch(Exception $e){
        http_response_code(500);
        echo json_encode([
            'Message' => 'Error interno del servidor. Detalles: ' . $e->getMessage() . ' en línea ' . $e->getLine()
        ], JSON_UNESCAPED_UNICODE);
    }

    // Función para manejar las solicitudes POST
    function handlePostRequest($action, $data)
    {
        switch ($action) {
            case 'iniciarSesion':
                if(!empty($data)){
                    // Declarar como global
                    global $controllerLogin;
                    $respuesta = $controllerLogin->iniciarSesion((array) $data);
                }else{
                    echo "Datos no proporcionados";
                    exit;
                }
                // Verificar que $data esté presente y no contenga un error
                if (!empty($respuesta) && (!isset($respuesta['error']) || empty($respuesta['error']))) {
                    http_response_code(200);
                    echo json_encode(array('message' => 'Usuario logueado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                } else {
                    http_response_code(404);
                    echo json_encode(array('message' => 'Usuario no logueado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                }
                exit;
            case 'registrarUsuario':
                if(!empty($data)){
                    global $controllerLogin;
                    $respuesta = $controllerLogin->registrarUsuario((array) $data);
                }else{
                    echo "Datos no proporcionados";
                    exit;
                }
                // Verificar que $data esté presente y no contenga un error
                if (!empty($respuesta) && (!isset($respuesta['error']) || empty($respuesta['error']))) {
                    http_response_code(200);
                    echo json_encode(array('message' => 'Usuario registrado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                } else {
                    http_response_code(404);    
                    echo json_encode(array('message' => 'Usuario no registrado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                }
                exit;
            case 'registrarCliente':
                if(!empty($data)){
                    global $controllerLogin;
                    $respuesta = $controllerLogin->registrarCliente((array) $data);
                }else{
                    echo "Datos no proporcionados";
                    exit;
                }   
                // Verificar que $data esté presente y no contenga un error
                if (!empty($respuesta) && (!isset($respuesta['error']) || empty($respuesta['error']))) {
                    http_response_code(200);
                    echo json_encode(array('message' => 'Cliente registrado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                } else {
                    http_response_code(404);
                    echo json_encode(array('message' => 'Cliente no registrado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                }
                exit;
            case 'asignarPermisos':
                if(!empty($data)){
                    global $controllerRoles;
                    $respuesta = $controllerRoles->asignarPermisos((array) $data);
                }else{
                    echo "Datos no proporcionados";
                    exit;
                }
                // Verificar que $data esté presente y no contenga un error
                if (!empty($respuesta) && (!isset($respuesta['error']) || empty($respuesta['error']))) {
                    http_response_code(200);
                    echo json_encode(array('message' => 'Permisos asignados.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                } else {
                    http_response_code(404);
                    echo json_encode(array('message' => 'Permisos no asignados.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                }
                exit;
            case 'crearRol':
                if(!empty($data)){
                    global $controllerRoles;
                    $respuesta = $controllerRoles->crearRol((array) $data);
                }else{
                    echo "Datos no proporcionados"; 
                    exit;
                }
                // Verificar que $data esté presente y no contenga un error
                if (!empty($respuesta) && (!isset($respuesta['error']) || empty($respuesta['error']))) {
                    http_response_code(200);
                    echo json_encode(array('message' => 'Rol creado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);  
                } else {
                    http_response_code(404);
                    echo json_encode(array('message' => 'Rol no creado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                }
                exit;
            case 'crearPermiso':
                if(!empty($data)){  
                    global $controllerRoles;
                    $respuesta = $controllerRoles->crearPermiso((array) $data);
                }else{
                    echo "Datos no proporcionados";
                    exit;
                }
                // Verificar que $data esté presente y no contenga un error
                if (!empty($respuesta) && (!isset($respuesta['error']) || empty($respuesta['error']))) {
                    http_response_code(200);
                    echo json_encode(array('message' => 'Permiso creado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                } else {
                    http_response_code(404);    
                    echo json_encode(array('message' => 'Permiso no creado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                }
                exit;
            case 'asociarProductos':
                if(!empty($data)){
                    global $controllerServiciosInventario;
                    $respuesta = $controllerServiciosInventario->asociarProductos((array) $data);
                }else{
                    echo "Datos no proporcionados";
                    exit;
                }
                // Verificar que $data esté presente y no contenga un error
                if (!empty($respuesta) && (!isset($respuesta['error']) || empty($respuesta['error']))) {
                    http_response_code(200);
                    echo json_encode(array('message' => 'Productos asociados.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                } else {
                    http_response_code(404);
                    echo json_encode(array('message' => 'Productos no asociados.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                }
                exit;
            case 'crearServicio':
                if(!empty($data)){
                    global $controllerServiciosInventario;
                    $respuesta = $controllerServiciosInventario->crearServicio((array) $data);
                }else{
                    echo "Datos no proporcionados";
                    exit;
                }
                // Verificar que $data esté presente y no contenga un error
                if (!empty($respuesta) && (!isset($respuesta['error']) || empty($respuesta['error']))) {
                    http_response_code(200);
                    echo json_encode(array('message' => 'Servicio creado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                } else {
                    http_response_code(404);
                    echo json_encode(array('message' => 'Servicio no creado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                }
                exit;
            case 'crearProducto':
                if(!empty($data)){
                    global $controllerServiciosInventario;
                    $respuesta = $controllerServiciosInventario->crearProducto((array) $data);
                }else{
                    echo "Datos no proporcionados"; 
                    exit;
                }
                // Verificar que $data esté presente y no contenga un error
                if (!empty($respuesta) && (!isset($respuesta['error']) || empty($respuesta['error']))) {
                    http_response_code(200);    
                    echo json_encode(array('message' => 'Producto creado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE); 
                } else {
                    http_response_code(404);
                    echo json_encode(array('message' => 'Producto no creado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                }
                exit;
            case 'crearDescuento':
                if(!empty($data)){
                    global $controllerRecompensaDescuentos;
                    $respuesta = $controllerRecompensaDescuentos->crearDescuento((array) $data);
                }else{
                    echo "Datos no proporcionados"; 
                    exit;
                }
                // Verificar que $data esté presente y no contenga un error
                if (!empty($respuesta) && (!isset($respuesta['error']) || empty($respuesta['error']))) {
                    http_response_code(200);
                    echo json_encode(array('message' => 'Descuento creado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);    
                } else {
                    http_response_code(404);
                    echo json_encode(array('message' => 'Descuento no creado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                }
                exit;
            case 'crearRecompensa':
                if(!empty($data)){
                    global $controllerRecompensaDescuentos;
                    $respuesta = $controllerRecompensaDescuentos->crearRecompensa((array) $data);
                }else{
                    echo "Datos no proporcionados"; 
                    exit;
                }
                // Verificar que $data esté presente y no contenga un error
                if (!empty($respuesta) && (!isset($respuesta['error']) || empty($respuesta['error']))) {
                    http_response_code(200);
                    echo json_encode(array('message' => 'Recompensa creada.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);   
                } else {
                    http_response_code(404);
                    echo json_encode(array('message' => 'Recompensa no creada.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                }
                exit;   
            case 'crearCanjeRecompensa':
                if(!empty($data)){
                    global $controllerRecompensaDescuentos;
                    $respuesta = $controllerRecompensaDescuentos->crearCanjeRecompensa((array) $data);
                }else{
                    echo "Datos no proporcionados";
                    exit;   
                }
                // Verificar que $data esté presente y no contenga un error
                if (!empty($respuesta) && (!isset($respuesta['error']) || empty($respuesta['error']))) {
                    http_response_code(200);
                    echo json_encode(array('message' => 'Canje de recompensa creado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                } else {
                    http_response_code(404);
                    echo json_encode(array('message' => 'Canje de recompensa no creado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                }
                exit;
            case 'registrarVenta':
                    // Leer la entrada JSON del frontend
                    $data = json_decode(file_get_contents("php://input"), true);
                
                    if (empty($data)) {
                        http_response_code(400); // Bad Request
                        echo json_encode(['message' => 'Datos no proporcionados.'], JSON_UNESCAPED_UNICODE);
                        exit;
                    }
                
                    // Extraer los datos y validar que estén presentes
                    $usuarioID = $data['usuarioID'] ?? null;
                    $clienteID = $data['clienteID'] ?? null;
                    $metodoPago = $data['metodoPago'] ?? null;
                    $servicios = $data['servicios'] ?? [];
                    $codigoDescuento = $data['codigoDescuento'] ?? null;
                    $comentarios = $data['comentarios'] ?? null;
                
                    if (!$usuarioID || !$clienteID || !$metodoPago || empty($servicios)) {
                        http_response_code(400); // Bad Request
                        echo json_encode(['message' => 'Faltan datos obligatorios.'], JSON_UNESCAPED_UNICODE);
                        exit;
                    }
                
                    global $controllerVenta;
                    $respuesta = $controllerVenta->registrarVenta($usuarioID, $clienteID, $metodoPago, $servicios, $codigoDescuento, $comentarios);
                
                    if (!empty($respuesta) && (!isset($respuesta['error']) || empty($respuesta['error']))) {
                        http_response_code(200); // OK
                        echo json_encode(['message' => 'Venta registrada.', 'data' => $respuesta], JSON_UNESCAPED_UNICODE);
                    } else {
                        http_response_code(500); // Internal Server Error
                        echo json_encode(['message' => 'Error al registrar la venta.', 'data' => $respuesta], JSON_UNESCAPED_UNICODE);
                    }
                exit;    
            case 'obtenerMenu':
                global $controllerLogin;
                $respuesta = $controllerLogin->obtenerMenu((array) $data);
                if (!empty($respuesta) && (!isset($respuesta['error']) || empty($respuesta['error']))) {
                    http_response_code(200);
                    echo json_encode(array('message' => 'Menu obtenido.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);   
                } else {
                    http_response_code(404);
                    echo json_encode(array('message' => 'Menu no obtenido.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                }
                exit;          
            default:
                http_response_code(404);
                echo json_encode(['Message' => 'Acción POST desconocida.'], JSON_UNESCAPED_UNICODE);
                exit;
                break;
        }
     }

    // Función para manejar las solicitudes GET
    function handleGetRequest($action, $data)
    {
        switch ($action) {
            case 'permisosAsignados':
                global $controllerRoles;
                $respuesta = $controllerRoles->permisosAsignados((array) $data);
                if (!empty($respuesta) && (!isset($respuesta['error']) || empty($respuesta['error']))) {
                    http_response_code(200);
                    echo json_encode(array('message' => 'Permisos asignados.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                } else {
                    http_response_code(404);
                    echo json_encode(array('message' => 'Permisos no asignados.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                }
                exit;
            case 'listaRoles':
                global $controllerRoles;
                $respuesta = $controllerRoles->listaRoles();    
                if (!empty($respuesta) && (!isset($respuesta['error']) || empty($respuesta['error']))) {
                    http_response_code(200);
                    echo json_encode(array('message' => 'Roles listados.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                } else {
                    http_response_code(404);
                    echo json_encode(array('message' => 'Roles no listados.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                }
                exit;
            case 'listaPermisos':
                global $controllerRoles;
                $respuesta = $controllerRoles->listaPermisos();
                if (!empty($respuesta) && (!isset($respuesta['error']) || empty($respuesta['error']))) {
                    http_response_code(200);
                    echo json_encode(array('message' => 'Permisos listados.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                } else {
                    http_response_code(404);
                    echo json_encode(array('message' => 'Permisos no listados.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);    
                }
                exit;
            case 'productosAsociados':
                    global $controllerServiciosInventario;
                    $respuesta = $controllerServiciosInventario->productosAsociados((array) $data);
                    // Verificar que $data esté presente y no contenga un error
                    if (!empty($respuesta) && (!isset($respuesta['error']) || empty($respuesta['error']))) {
                        http_response_code(200);
                        echo json_encode(array('message' => 'Productos asociados obtenidos.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                    } else {
                        http_response_code(404);
                        echo json_encode(array('message' => 'Productos no asociados.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                }
                exit;
            case 'listaServicios':
                global $controllerServiciosInventario;
                $respuesta = $controllerServiciosInventario->listaServicios();
                if (!empty($respuesta) && (!isset($respuesta['error']) || empty($respuesta['error']))) {
                    http_response_code(200);
                    echo json_encode(array('message' => 'Servicios listados.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                } else {
                    http_response_code(404);
                    echo json_encode(array('message' => 'Servicios no listados.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                }
                exit;
            case 'listaInventario':
                global $controllerServiciosInventario;
                $respuesta = $controllerServiciosInventario->listaInventario();
                if (!empty($respuesta) && (!isset($respuesta['error']) || empty($respuesta['error']))) {
                    http_response_code(200);
                    echo json_encode(array('message' => 'Inventario listados.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                } else {
                    http_response_code(404);
                    echo json_encode(array('message' => 'Inventario no listados.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                }
                exit;
            case 'listarDescuentos':
                global $controllerRecompensaDescuentos;
                $respuesta = $controllerRecompensaDescuentos->listarDescuentos();
                if (!empty($respuesta) && (!isset($respuesta['error']) || empty($respuesta['error']))) {
                    http_response_code(200);
                    echo json_encode(array('message' => 'Descuentos listados.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE); 
                } else {
                    http_response_code(404);
                    echo json_encode(array('message' => 'Descuentos no listados.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                }
                exit;       
            case 'listarRecompensas':
                global $controllerRecompensaDescuentos;
                $respuesta = $controllerRecompensaDescuentos->listarRecompensas();
                if (!empty($respuesta) && (!isset($respuesta['error']) || empty($respuesta['error']))) {
                    http_response_code(200);
                    echo json_encode(array('message' => 'Recompensas listadas.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                } else {
                    http_response_code(404);
                    echo json_encode(array('message' => 'Recompensas no listadas.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                }
                exit;   
            case 'listarCanjesRecompensas':
                global $controllerRecompensaDescuentos;
                $respuesta = $controllerRecompensaDescuentos->listarCanjesRecompensas();
                if (!empty($respuesta) && (!isset($respuesta['error']) || empty($respuesta['error']))) {
                    http_response_code(200);
                    echo json_encode(array('message' => 'Canjes de recompensas listados.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);  
                } else {
                    http_response_code(404);
                    echo json_encode(array('message' => 'Canjes de recompensas no listados.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                }
                exit;   
            case 'listarVentas':
                // Leer la entrada JSON del frontend
                $data = json_decode(file_get_contents("php://input"), true);

                // Extraer los datos y validar que estén presentes
                $ventaID = $data['ventaID'] ?? null;
                $clienteID = $data['clienteID'] ?? null;
                
                global $controllerVenta;
                $respuesta = $controllerVenta->listarVentas($ventaID, $clienteID);
                if (!empty($respuesta) && (!isset($respuesta['error']) || empty($respuesta['error']))) {
                    http_response_code(200);
                    echo json_encode(array('message' => 'Ventas listadas.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                } else {
                    http_response_code(404);
                    echo json_encode(array('message' => 'Ventas no listadas.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                }   
                exit;  
            case 'listarSemaforoServicios':
                global $controllerVenta;
                $respuesta = $controllerVenta->listarSemaforoServicios((array) $data);
                if (!empty($respuesta) && (!isset($respuesta['error']) || empty($respuesta['error']))) {
                    http_response_code(200);
                    echo json_encode(array('message' => 'Semaforo de servicios listados.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                } else {
                    http_response_code(404);
                    echo json_encode(array('message' => 'Semaforo de servicios no listados.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                }
                exit;   
            
                
            default:
                http_response_code(404);
                echo json_encode(['Message' => 'Acción GET desconocida.'], JSON_UNESCAPED_UNICODE);
                exit;
                break;
        }
    }
    // Función para manejar las solicitudes PATCH
    function handlePatchRequest($action, $data)
    {
        switch ($action) {
            case 'actualizarUsuario':
                if(!empty($data)){
                    global $controllerLogin;
                    $respuesta = $controllerLogin->actualizarUsuario((array) $data);
                }else{
                    echo "Datos no proporcionados";
                    exit;
                }
                // Verificar que $data esté presente y no contenga un error
                if (!empty($respuesta) && (!isset($respuesta['error']) || empty($respuesta['error']))) {
                    http_response_code(200);
                    echo json_encode(array('message' => 'Usuario actualizado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                } else {
                    http_response_code(404);
                    echo json_encode(array('message' => 'Usuario no actualizado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                }
                exit;
            case 'actualizarCliente':
                if(!empty($data)){
                    global $controllerLogin;
                    $respuesta = $controllerLogin->actualizarCliente((array) $data);
                }else{
                    echo "Datos no proporcionados";
                    exit;
                }
                // Verificar que $data esté presente y no contenga un error
                if (!empty($respuesta) && (!isset($respuesta['error']) || empty($respuesta['error']))) {
                    http_response_code(200);
                    echo json_encode(array('message' => 'Cliente actualizado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                } else {
                    http_response_code(404);
                    echo json_encode(array('message' => 'Cliente no actualizado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                }
                exit;
            case 'actualizarRol':
                if(!empty($data)){
                    global $controllerRoles;
                    $respuesta = $controllerRoles->actualizarRol((array) $data);
                }else{
                    echo "Datos no proporcionados";
                    exit;
                }
                // Verificar que $data esté presente y no contenga un error
                if (!empty($respuesta) && (!isset($respuesta['error']) || empty($respuesta['error']))) {
                    http_response_code(200);
                    echo json_encode(array('message' => 'Rol actualizado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                } else {
                    http_response_code(404);
                    echo json_encode(array('message' => 'Rol no actualizado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                }
                exit;
            case 'actualizarPermiso':
                if(!empty($data)){
                    global $controllerRoles;
                    $respuesta = $controllerRoles->actualizarPermiso((array) $data);
                }else{
                    echo "Datos no proporcionados";
                    exit;
                }
                // Verificar que $data esté presente y no contenga un error
                if (!empty($respuesta) && (!isset($respuesta['error']) || empty($respuesta['error']))) {
                    http_response_code(200);
                    echo json_encode(array('message' => 'Permiso actualizado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                } else {
                    http_response_code(404);
                    echo json_encode(array('message' => 'Permiso no actualizado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                }
                exit;
            case 'actualizarServicio':
                if(!empty($data)){
                    global $controllerServiciosInventario;
                    $respuesta = $controllerServiciosInventario->actualizarServicio((array) $data);
                }else{
                    echo "Datos no proporcionados"; 
                    exit;
                }
                // Verificar que $data esté presente y no contenga un error
                if (!empty($respuesta) && (!isset($respuesta['error']) || empty($respuesta['error']))) {
                    http_response_code(200);    
                    echo json_encode(array('message' => 'Servicio actualizado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE); 
                } else {
                    http_response_code(404);
                    echo json_encode(array('message' => 'Servicio no actualizado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                }
                exit;
            case 'actualizarProducto':
                if(!empty($data)){
                    global $controllerServiciosInventario;
                    $respuesta = $controllerServiciosInventario->actualizarProducto((array) $data);
                }else{
                    echo "Datos no proporcionados"; 
                    exit;
                }
                // Verificar que $data esté presente y no contenga un error
                if (!empty($respuesta) && (!isset($respuesta['error']) || empty($respuesta['error']))) {
                    http_response_code(200);
                    echo json_encode(array('message' => 'Producto actualizado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                } else {
                    http_response_code(404);
                    echo json_encode(array('message' => 'Producto no actualizado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                }
                exit;
            case 'actualizarDescuento':
                if(!empty($data)){
                    global $controllerRecompensaDescuentos;
                    $respuesta = $controllerRecompensaDescuentos->actualizarDescuento((array) $data);
                }else{
                    echo "Datos no proporcionados";
                    exit;
                }
                // Verificar que $data esté presente y no contenga un error
                if (!empty($respuesta) && (!isset($respuesta['error']) || empty($respuesta['error']))) {
                    http_response_code(200);
                    echo json_encode(array('message' => 'Descuento actualizado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                } else {
                    http_response_code(404);
                    echo json_encode(array('message' => 'Descuento no actualizado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                }
                exit;   
            case 'actualizarRecompensa':
                if(!empty($data)){
                    global $controllerRecompensaDescuentos;
                    $respuesta = $controllerRecompensaDescuentos->actualizarRecompensa((array) $data);
                }else{
                    echo "Datos no proporcionados";
                    exit;
                }   
                // Verificar que $data esté presente y no contenga un error
                if (!empty($respuesta) && (!isset($respuesta['error']) || empty($respuesta['error']))) {
                    http_response_code(200);
                    echo json_encode(array('message' => 'Recompensa actualizada.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                } else {
                    http_response_code(404);
                    echo json_encode(array('message' => 'Recompensa no actualizada.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                }
                exit;   
            case 'actualizarCanjeRecompensa':
                if(!empty($data)){
                    global $controllerRecompensaDescuentos;
                    $respuesta = $controllerRecompensaDescuentos->actualizarCanjeRecompensa((array) $data);
                }else{
                    echo "Datos no proporcionados";
                    exit;
                }
                // Verificar que $data esté presente y no contenga un error
                if (!empty($respuesta) && (!isset($respuesta['error']) || empty($respuesta['error']))) {
                    http_response_code(200);
                    echo json_encode(array('message' => 'Canje de recompensa actualizado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                } else {
                    http_response_code(404);
                    echo json_encode(array('message' => 'Canje de recompensa no actualizado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                }
                exit;  
            case 'actualizarEstadoVenta':
                if(!empty($data)){
                    global $controllerVenta;
                    $respuesta = $controllerVenta->actualizarEstadoVenta((array) $data);
                }else{
                    echo "Datos no proporcionados";
                    exit;   
                }
                // Verificar que $data esté presente y no contenga un error
                if (!empty($respuesta) && (!isset($respuesta['error']) || empty($respuesta['error']))) {
                    http_response_code(200);
                    echo json_encode(array('message' => 'Estado de venta actualizado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                } else {
                    http_response_code(404);
                    echo json_encode(array('message' => 'Estado de venta no actualizado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                }
                exit;
            case 'actualizarEstadoSemaforo':
                if(!empty($data)){
                    global $controllerVenta;
                    $respuesta = $controllerVenta->actualizarEstadoSemaforo((array) $data);
                }else{
                    echo "Datos no proporcionados";
                    exit;
                }
                // Verificar que $data esté presente y no contenga un error
                if (!empty($respuesta) && (!isset($respuesta['error']) || empty($respuesta['error']))) {
                    http_response_code(200);
                    echo json_encode(array('message' => 'Estado de semaforo actualizado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                } else {
                    http_response_code(404);
                    echo json_encode(array('message' => 'Estado de semaforo no actualizado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                }
                exit;   
            default:
                http_response_code(404);
                echo json_encode(['Message' => 'Acción PATCH desconocida.'], JSON_UNESCAPED_UNICODE);
                break;
        }
    }
    // Función para manejar las solicitudes DELETE
    function handleDeleteRequest($action, $data)
    {
        switch ($action) {
            case 'eliminarUsuario':
                if(!empty($data)){
                    global $controllerLogin;
                    $respuesta = $controllerLogin->eliminarUsuario((array) $data);
                }else{
                    echo "Datos no proporcionados";
                    exit;
                }
                // Verificar que $data esté presente y no contenga un error
                if (!empty($respuesta) && (!isset($respuesta['error']) || empty($respuesta['error']))) {
                    http_response_code(200);
                    echo json_encode(array('message' => 'Usuario eliminado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                } else {
                    http_response_code(404);
                    echo json_encode(array('message' => 'Usuario no eliminado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                }
                exit;
            case 'eliminarCliente':
                if(!empty($data)){  
                    global $controllerLogin;
                    $respuesta = $controllerLogin->eliminarCliente((array) $data);
                }else{
                    echo "Datos no proporcionados";
                    exit;
                }
                // Verificar que $data esté presente y no contenga un error
                if (!empty($respuesta) && (!isset($respuesta['error']) || empty($respuesta['error']))) {
                    http_response_code(200);
                    echo json_encode(array('message' => 'Cliente eliminado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                } else {
                    http_response_code(404);    
                    echo json_encode(array('message' => 'Cliente no eliminado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                }
                exit;
            case 'eliminarRol':
                if(!empty($data)){
                    global $controllerRoles;
                    $respuesta = $controllerRoles->eliminarRol((array) $data);
                }else{
                    echo "Datos no proporcionados";
                    exit;
                }
                // Verificar que $data esté presente y no contenga un error
                if (!empty($respuesta) && (!isset($respuesta['error']) || empty($respuesta['error']))) {
                    http_response_code(200);
                    echo json_encode(array('message' => 'Rol eliminado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);   
                } else {
                    http_response_code(404);
                    echo json_encode(array('message' => 'Rol no eliminado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                }
                exit;
            case 'eliminarPermiso':
                if(!empty($data)){  
                    global $controllerRoles;
                    $respuesta = $controllerRoles->eliminarPermiso((array) $data);
                }else{
                    echo "Datos no proporcionados";
                    exit;
                }
                // Verificar que $data esté presente y no contenga un error
                if (!empty($respuesta) && (!isset($respuesta['error']) || empty($respuesta['error']))) {
                    http_response_code(200);
                    echo json_encode(array('message' => 'Permiso eliminado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                } else {
                    http_response_code(404);
                    echo json_encode(array('message' => 'Permiso no eliminado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                }
                exit;
            case 'eliminarServicio':
                if(!empty($data)){
                    global $controllerServiciosInventario;
                    $respuesta = $controllerServiciosInventario->eliminarServicio((array) $data);
                }else{
                    echo "Datos no proporcionados";
                    exit;
                }
                // Verificar que $data esté presente y no contenga un error
                if (!empty($respuesta) && (!isset($respuesta['error']) || empty($respuesta['error']))) {
                    http_response_code(200);
                    echo json_encode(array('message' => 'Servicio eliminado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                } else {
                    http_response_code(404);
                    echo json_encode(array('message' => 'Servicio no eliminado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                }
                exit;
            case 'eliminarProducto':
                if(!empty($data)){
                    global $controllerServiciosInventario;
                    $respuesta = $controllerServiciosInventario->eliminarProducto((array) $data);
                }else{
                    echo "Datos no proporcionados";
                    exit;
                }
                // Verificar que $data esté presente y no contenga un error
                if (!empty($respuesta) && (!isset($respuesta['error']) || empty($respuesta['error']))) {
                    http_response_code(200);
                    echo json_encode(array('message' => 'Producto eliminado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                } else {
                    http_response_code(404);
                    echo json_encode(array('message' => 'Producto no eliminado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                }
                exit;
            case 'eliminarDescuento':
                if(!empty($data)){
                    global $controllerRecompensaDescuentos;
                    $respuesta = $controllerRecompensaDescuentos->eliminarDescuento((array) $data);
                }else{
                    echo "Datos no proporcionados";
                    exit;   
                }
                // Verificar que $data esté presente y no contenga un error
                if (!empty($respuesta) && (!isset($respuesta['error']) || empty($respuesta['error']))) {
                    http_response_code(200);
                    echo json_encode(array('message' => 'Descuento eliminado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                } else {
                    http_response_code(404);
                    echo json_encode(array('message' => 'Descuento no eliminado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                }
                exit;
            case 'eliminarRecompensa':  
                if(!empty($data)){
                    global $controllerRecompensaDescuentos;
                    $respuesta = $controllerRecompensaDescuentos->eliminarRecompensa((array) $data);
                }else{
                    echo "Datos no proporcionados";
                    exit;
                }   
                // Verificar que $data esté presente y no contenga un error
                if (!empty($respuesta) && (!isset($respuesta['error']) || empty($respuesta['error']))) {
                    http_response_code(200);
                    echo json_encode(array('message' => 'Recompensa eliminada.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                } else {
                    http_response_code(404);
                    echo json_encode(array('message' => 'Recompensa no eliminada.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                }
                exit;
            case 'eliminarCanjeRecompensa': 
                if(!empty($data)){
                    global $controllerRecompensaDescuentos;
                    $respuesta = $controllerRecompensaDescuentos->eliminarCanjeRecompensa((array) $data);
                }else{
                    echo "Datos no proporcionados";
                    exit;
                }   
                // Verificar que $data esté presente y no contenga un error
                if (!empty($respuesta) && (!isset($respuesta['error']) || empty($respuesta['error']))) {
                    http_response_code(200);
                    echo json_encode(array('message' => 'Canje de recompensa eliminado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                } else {
                    http_response_code(404);    
                    echo json_encode(array('message' => 'Canje de recompensa no eliminado.', 'data' => $respuesta), JSON_UNESCAPED_UNICODE);
                }
                exit;
            default:
                http_response_code(404);
                echo json_encode(['Message' => 'Acción DELETE desconocida.'], JSON_UNESCAPED_UNICODE);
                break;
        }
    }
?>