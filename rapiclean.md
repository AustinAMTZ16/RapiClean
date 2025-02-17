###### ########################################################### ######
###### "RapiClean [Puebla,Pue.]" (Rapidez y limpieza en el nombre) ######
###### ########################################################### ######

# 1. ** Módulos principales **

## A. **Gestión de Inventario**
**Funciones principales**:
- **Registrar entrada y salida de productos** (detergentes, suavizantes, planchas, etc.).
- **Control de stock mínimo** con alertas de reabastecimiento.
- **Reportes de uso de inventario** por servicio.

## B. **Gestión de Servicios**
**Funciones principales**:
- **Registrar servicios de lavado**, secado y planchado.
- **Manejo de tarifas** por kilogramo o por prenda específica.
- **Gestión de pedidos** con estados: pendiente, en proceso, listo para entrega, entregado.
- **Personalización de servicios** (ejemplo: fragancias o suavizantes específicos).

## C. **Gestión de Ventas**
**Funciones principales**:
- **Registrar pedidos y ventas de servicios**.
- **Generar facturas** o tickets.
- **Manejo de métodos de pago** (efectivo, tarjeta, transferencias).
- **Reportes diarios, semanales y mensuales** de ingresos.

## D. **Gestión de Clientes y Recompensas**
**Funciones principales**:
- **Registro de clientes** con datos personales y preferencias.
- **Acumular puntos** por cada servicio adquirido.
- **Canjeo de puntos** por descuentos o servicios gratuitos.
- **Seguimiento de historial** de servicios de cada cliente.

# 2. ** Plan Inteligente y Eficiente para el Proceso de Lavado **
###### ########################################################################## ######
###### Flujo de trabajo desde la llegada del cliente hasta la entrega de la ropa  ######
###### ########################################################################## ######

### **Fase 1: Recepción del cliente**

#### **Recepción y registro del pedido**:
- Saludar al cliente y registrar su pedido en el sistema.
- Registrar datos clave:
    - **Nombre, contacto, tipo de servicio** (lavado, secado, planchado o todos).
    - **Tipo de prendas** (ej.: delicadas, algodón, edredones).
    - **Número de piezas y peso**.
    - **Etiquetar la ropa** con un identificador único (código QR o etiqueta física).

#### **Evaluación**:
- Inspeccionar la ropa frente al cliente para:
    - Detectar manchas específicas.
    - Confirmar el estado inicial de las prendas.
    - Ofrecer sugerencias para manchas difíciles (**servicios adicionales si aplica**).
- Generar recibo:
    - Generar un **recibo digital o impreso** con detalles del servicio, costo estimado y tiempo de entrega.

### **Fase 2: Clasificación y prelavado**

#### **Clasificación**:
- Separar las prendas según el tipo de tela y color:
    - **Ropa blanca, colores claros, colores oscuros**.
    - **Tela delicada vs. normal**.
- Registrar cada lote en el sistema para monitorear el proceso.

#### **Pretratamiento**:
- Aplicar soluciones de **prelavado** en manchas específicas.
- Registrar en el sistema los tratamientos aplicados (útil para medir eficiencia en manchas difíciles).

### **Fase 3: Lavado y secado**
#### **Lavado**:
- Cargar las lavadoras siguiendo el peso máximo recomendado.
- Utilizar **detergentes** y **suavizantes específicos** para cada tipo de tela.
- Configurar ciclos de lavado según las instrucciones del cliente o tipo de prenda (normal, delicado, rápido).

#### **Secado**:
- Verificar que las prendas sean aptas para secadora.
- Configurar la secadora según tipo de prenda (calor bajo, medio, alto).
- Usar etiquetas para evitar mezclar lotes de diferentes clientes.

### **Fase 4: Planchado y control de calidad**

#### **Planchado** (si aplica):
- Planchar las prendas de acuerdo con las especificaciones del cliente.
- Registrar en el sistema el tiempo dedicado al **planchado por prenda** o lote.

#### **Control de calidad**:
- Revisar las prendas para asegurar que no queden manchas o desperfectos.
- **Doblar y empacar** las prendas etiquetadas por cliente.

### **Fase 5: Entrega**

#### **Notificación al cliente**:
- Notificar por **SMS, WhatsApp o correo electrónico** que su pedido está listo para recoger.
- Proveer un resumen del pedido, incluyendo **puntos de recompensa** (si aplica).

#### **Entrega**:
- Verificar la **identidad del cliente** con el código del recibo.
- Registrar la entrega en el sistema, incluyendo comentarios del cliente (**satisfacción o reclamos**).


# 3. **Análisis del tiempo promedio del flujo**

## Registrar tiempos en cada etapa:
- **Update Tablas Fecha y Hora**
## Optimizar tiempos:
- **Identificar cuellos de botella**
## Monitorear la eficiencia:
- **reportes semanales/mensuales**


1. Hero Section (Carrusel Encabezado Principal) 
    * 3 componentes
2. Sección de Beneficios o Destacados 

    * 3 componentes

3. Sección "Acerca de"
    * 1 componentes

4. Sección de Servicios o Características Clave 
    * 3 componentes
    
5. Prueba Social / Testimonios NECESITO 
    * 4 componentes

6. Sección de Ventajas o Diferenciadores 
    * 4 componentes

7. FORMULARIO Llamado a la Acción Final 
    * 1 componentes

8. Sección de Contacto o Preguntas Frecuentes 
    * 1 Foter







-- Tabla de Secciones
CREATE TABLE Sections (
    section_id INT AUTO_INCREMENT PRIMARY KEY,
    section_name VARCHAR(255) NOT NULL UNIQUE,
    description TEXT
);

-- Tabla de Menú
CREATE TABLE MenuItems (
    menu_item_id INT AUTO_INCREMENT PRIMARY KEY,
    section_id INT NOT NULL,
    label VARCHAR(50) NOT NULL,
    url VARCHAR(255) NOT NULL,
    display_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP,
    is_current BOOLEAN DEFAULT 1,
    FOREIGN KEY (section_id) REFERENCES Sections(section_id)
);

-- Tabla Carrusel Hero
CREATE TABLE HeroCarousel (
    slide_id INT AUTO_INCREMENT PRIMARY KEY,
    image_url VARCHAR(255) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    button_text VARCHAR(50),
    button_link VARCHAR(255),
    display_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP,
    is_current BOOLEAN DEFAULT 1
);

-- Tabla de Beneficios
CREATE TABLE Benefits (
    benefit_id INT AUTO_INCREMENT PRIMARY KEY,
    icon_class VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    display_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP,
    is_current BOOLEAN DEFAULT 1
);

-- Tabla Acerca de
CREATE TABLE AboutContent (
    about_id INT AUTO_INCREMENT PRIMARY KEY,
    image_url VARCHAR(255) NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    button_text VARCHAR(50),
    button_link VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP,
    is_current BOOLEAN DEFAULT 1
);

-- Tabla de Servicios
CREATE TABLE Services (
    service_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    display_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP,
    is_current BOOLEAN DEFAULT 1
);

-- Tabla de Características de Servicios
CREATE TABLE ServiceFeatures (
    feature_id INT AUTO_INCREMENT PRIMARY KEY,
    service_id INT NOT NULL,
    feature_text VARCHAR(255) NOT NULL,
    display_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP,
    is_current BOOLEAN DEFAULT 1,
    FOREIGN KEY (service_id) REFERENCES Services(service_id)
);

-- Tabla de Testimonios
CREATE TABLE Testimonials (
    testimonial_id INT AUTO_INCREMENT PRIMARY KEY,
    author_name VARCHAR(255) NOT NULL,
    author_icon VARCHAR(50) NOT NULL,
    content TEXT NOT NULL,
    display_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP,
    is_current BOOLEAN DEFAULT 1
);

-- Tabla de FAQs
CREATE TABLE FAQs (
    faq_id INT AUTO_INCREMENT PRIMARY KEY,
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    button_text VARCHAR(50),
    button_link VARCHAR(255),
    display_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP,
    is_current BOOLEAN DEFAULT 1
);

-- Tabla de Contacto
CREATE TABLE ContactInfo (
    contact_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    map_embed_code TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP,
    is_current BOOLEAN DEFAULT 1
);

-- Tabla de Footer
CREATE TABLE FooterContent (
    footer_id INT AUTO_INCREMENT PRIMARY KEY,
    copyright_text VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP,
    is_current BOOLEAN DEFAULT 1
);

-- Tabla de Redes Sociales
CREATE TABLE SocialLinks (
    social_id INT AUTO_INCREMENT PRIMARY KEY,
    footer_id INT NOT NULL,
    platform VARCHAR(50) NOT NULL,
    url VARCHAR(255) NOT NULL,
    icon_class VARCHAR(50) NOT NULL,
    display_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP,
    is_current BOOLEAN DEFAULT 1,
    FOREIGN KEY (footer_id) REFERENCES FooterContent(footer_id)
);

-- Tabla de Usuarios (Para administración)
CREATE TABLE Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_login DATETIME
);

-- Tabla de Auditoría (Historial de cambios)
CREATE TABLE AuditLog (
    audit_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(50) NOT NULL,
    table_name VARCHAR(50) NOT NULL,
    record_id INT NOT NULL,
    old_value JSON,
    new_value JSON,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
);




-- Insertar secciones principales
INSERT INTO Sections (section_name, description) VALUES
('Inicio', 'Sección principal de la página'),
('Acerca de', 'Acerca del negocio'),
('Servicios', 'Listado de servicios de lavandería'),
('Blog', 'Artículos y noticias'),
('FAQs', 'Preguntas frecuentes'),
('Contacto', 'Información de contacto y formulario');

-- Menú de navegación
INSERT INTO MenuItems (section_id, label, url, display_order) VALUES
(1, 'Inicio', '/', 1),
(2, 'Acerca de', '#acerca', 2),
(2, 'Servicios', '#servicios', 3),
(4, 'Blog', '#blog', 4),
(5, 'FAQs', '#faqs', 5),
(3, 'Contacto', '#contacto', 6);

-- Carrusel Hero
INSERT INTO HeroCarousel (image_url, title, description, button_text, button_link) VALUES
('/rapicleanpuebla.com/assets/img/slide1.jpg', '¿Por qué elegirnos? Rapidez, calidad y confianza en cada servicio.','✅ Servicio en el mismo día <br>✅ Cuidado profesional de tu ropa <br>✅ Opciones ecológicas', 'Ver precios', 'https://api.whatsapp.com/message/LH5TWCRSG7HSF1?autoload=1&app_absent=0'),
('/rapicleanpuebla.com/assets/img/slide2.jpg', 'Ahorra tiempo, gana comodidad', 'Sin colas, sin esperas. ¡Nosotros nos encargamos! <br> ✅ Recoge tu ropa lista sin esfuerzo <br> ✅ Lavado con productos biodegradables <br> ✅ Personal capacitado', 'Prueba nuestro servicio', 'https://maps.app.goo.gl/LVbz2hsLTwNgQoyF8');

-- Beneficios
INSERT INTO Benefits (icon_class, title, description) VALUES
('fas fa-clock', 'Entrega Rápida', 'Servicio express de 24 horas'),
('fas fa-leaf', 'Ecológicos', 'Usamos detergentes biodegradables'),
('fas fa-shield-alt', 'Seguro', 'Protección contra daños y pérdidas');

-- Acerca de
INSERT INTO AboutContent (image_url, title, content) VALUES
('about.jpg', 'Lavandería Express MX', 
'15 años especializados en cuidado textil. Más de 50,000 clientes satisfechos en Puebla.');

-- Servicios
INSERT INTO Services (title, display_order) VALUES
('Lavado Básico', 1),
('Lavado en Seco', 2),
('Planchado Profesional', 3);

-- Características de servicios
INSERT INTO ServiceFeatures (service_id, feature_text) VALUES
(1, 'Incluye detergente hipoalergénico'),
(1, 'Secado a máquina'),
(2, 'Para prendas delicadas'),
(3, 'Planchado con vapor profesional');

-- Testimonios
INSERT INTO Testimonials (author_name, author_icon, content) VALUES
('María González', 'user1.jpg', '¡Mi ropa queda como nueva siempre! El mejor servicio de la ciudad.'),
('Carlos Martínez', 'user2.jpg', 'Rápido y confiable, nunca me han perdido una prenda.');

-- FAQs
INSERT INTO FAQs (question, answer) VALUES
('¿Qué tipo de prendas aceptan?', 'Aceptamos desde ropa casual hasta prendas delicadas y edredones.'),
('¿Hacen servicio a domicilio?', 'Sí, con costo adicional según zona.');

-- Contacto
INSERT INTO ContactInfo (email, phone, map_embed_code) VALUES
('contacto@lavanderiaexpress.com', '222 123 4567', 
'<iframe src="https://maps.google.com/maps?q=puebla&output=embed" width="600" height="450"></iframe>');

-- Footer
INSERT INTO FooterContent (copyright_text) VALUES
('© 2024 Lavandería Express MX - Todos los derechos reservados');

-- Redes Sociales
INSERT INTO SocialLinks (footer_id, platform, url, icon_class) VALUES
(1, 'Facebook', 'https://facebook.com/lavanderiaexpress', 'fab fa-facebook'),
(1, 'WhatsApp', 'https://wa.me/522221234567', 'fab fa-whatsapp');

-- Usuario Administrador
INSERT INTO Users (username, password_hash, email) VALUES
('admin', SHA2('Admin123!', 256), 'admin@lavanderiaexpress.com');

-- Ejemplo de auditoría
INSERT INTO AuditLog (user_id, action, table_name, record_id) VALUES
(1, 'CREATE', 'Services', 1);


-- Ver menú activo
SELECT * FROM MenuItems WHERE is_current = 1;

-- Obtener servicios con sus características
SELECT s.title, f.feature_text 
FROM Services s
JOIN ServiceFeatures f ON s.service_id = f.service_id
WHERE s.is_current = 1 AND f.is_current = 1;

-- Ver testimonios activos
SELECT author_name, content FROM Testimonials WHERE is_current = 1;








<?php
function generateWebsite() {
    // Configuración de la base de datos
    $host = 'localhost';
    $user = 'usuario';
    $pass = 'contraseña';
    $db = 'nombre_bd';
    
    // Conectar a la base de datos
    $conn = mysqli_connect($host, $user, $pass, $db);
    
    if (!$conn) {
        die("Error de conexión: " . mysqli_connect_error());
    }

    // Iniciar buffer de salida
    ob_start();
    
    // 1. Header
    $headerQuery = "SELECT * FROM Sections WHERE section_name = 'header'";
    $headerResult = mysqli_query($conn, $headerQuery);
    $header = mysqli_fetch_assoc($headerResult);
    
    // 2. Menú
    $menuQuery = "SELECT * FROM MenuItems WHERE is_current = 1 ORDER BY display_order";
    $menuResult = mysqli_query($conn, $menuQuery);
    
    // 3. Carrusel Hero
    $carouselQuery = "SELECT * FROM HeroCarousel WHERE is_current = 1 ORDER BY display_order";
    $carouselResult = mysqli_query($conn, $carouselQuery);
    
    // 4. Servicios
    $servicesQuery = "SELECT s.title, GROUP_CONCAT(f.feature_text SEPARATOR '|') AS features 
                     FROM Services s 
                     JOIN ServiceFeatures f ON s.service_id = f.service_id 
                     WHERE s.is_current = 1 AND f.is_current = 1 
                     GROUP BY s.title 
                     ORDER BY s.display_order";
    $servicesResult = mysqli_query($conn, $servicesQuery);
    
    // Construir HTML
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $header['title'] ?></title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    </head>
    <body>
        <header class="top">
            <div class="header-top">
                <div class="container">
                    <!-- Menú -->
                    <nav>
                        <ul class="nav-list">
                            <?php while($menuItem = mysqli_fetch_assoc($menuResult)): ?>
                            <li>
                                <a href="<?= $menuItem['url'] ?>"><?= $menuItem['label'] ?></a>
                            </li>
                            <?php endwhile; ?>
                        </ul>
                    </nav>
                </div>
            </div>
        </header>

        <!-- Carrusel -->
        <section class="hero-carousel">
            <?php while($slide = mysqli_fetch_assoc($carouselResult)): ?>
            <div class="carousel-slide">
                <img src="<?= $slide['image_url'] ?>" alt="<?= $slide['title'] ?>">
                <div class="carousel-content">
                    <h1><?= $slide['title'] ?></h1>
                    <p><?= $slide['description'] ?></p>
                    <a href="<?= $slide['button_link'] ?>" class="btn">
                        <?= $slide['button_text'] ?>
                    </a>
                </div>
            </div>
            <?php endwhile; ?>
        </section>

        <!-- Servicios -->
        <section class="services">
            <h2>Nuestros Servicios</h2>
            <div class="services-grid">
                <?php while($service = mysqli_fetch_assoc($servicesResult)): 
                    $features = explode('|', $service['features']);
                ?>
                <div class="service-card">
                    <h3><?= $service['title'] ?></h3>
                    <ul>
                        <?php foreach($features as $feature): ?>
                        <li><?= $feature ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endwhile; ?>
            </div>
        </section>

        <?php
        // Footer
        $footerQuery = "SELECT * FROM FooterContent WHERE is_current = 1";
        $footerResult = mysqli_query($conn, $footerQuery);
        $footer = mysqli_fetch_assoc($footerResult);
        ?>
        
        <footer>
            <p><?= $footer['copyright_text'] ?></p>
        </footer>
    </body>
    </html>
    <?php
    
    // Cerrar conexión
    mysqli_close($conn);
    
    // Obtener y limpiar el buffer
    $output = ob_get_clean();
    
    return $output;
}

// Uso:
$paginaWeb = generateWebsite();
echo $paginaWeb;
?>
