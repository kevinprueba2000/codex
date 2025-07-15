# AlquimiaTechnologic - Tienda Web

Una tienda web completa desarrollada en PHP con MySQL, inspirada en el diseño moderno y funcional. Especializada en software personalizado, aceites esenciales, figuras artesanales y suscripciones premium.

## 🚀 Características Principales

- **Diseño Moderno**: Interfaz atractiva y responsiva basada en Bootstrap 5
- **Panel de Administración**: Dashboard completo para gestión de productos, categorías, usuarios y pedidos
- **Sistema de Autenticación**: Login y registro de usuarios con roles (admin/customer)
- **Carrito de Compras**: Sistema de carrito con localStorage y base de datos
- **Gestión de Productos**: CRUD completo para productos con categorías
- **Responsive**: Adaptable a todos los dispositivos
- **Seguridad**: Protección CSRF, validación de datos y sesiones seguras

## 📋 Requisitos del Sistema

- **XAMPP** (Apache + MySQL + PHP 7.4+)
- **PHP 7.4** o superior
- **MySQL 5.7** o superior
- **Navegador web moderno**

## 🛠️ Instalación

### 1. Descargar e Instalar XAMPP

1. Descarga XAMPP desde [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Instala XAMPP en tu sistema
3. Inicia Apache y MySQL desde el panel de control de XAMPP

### 2. Configurar el Proyecto

1. **Clonar o descargar el proyecto**:
   ```bash
   # Clona el repositorio en la carpeta htdocs de XAMPP
   cd C:\xampp\htdocs
   git clone [URL_DEL_REPOSITORIO] TiendawebAlquimia
   ```

2. **Configurar la base de datos**:
   - Abre phpMyAdmin: `http://localhost/phpmyadmin`
   - Crea una nueva base de datos llamada `alquimia_technologic`
   - Importa el archivo SQL: `database/alquimia_db.sql`

3. **Configurar la conexión**:
   - Abre `config/database.php`
   - Verifica que los datos de conexión sean correctos:
     ```php
     private $host = 'localhost';
     private $dbname = 'alquimia_technologic';
     private $username = 'root';
     private $password = '';
     ```

### 3. Acceder al Sistema

- **Sitio web**: `http://localhost/TiendawebAlquimia`
- **Panel de administración**: `http://localhost/TiendawebAlquimia/admin/dashboard.php`

### 4. Credenciales por Defecto

**Administrador:**
- Usuario: `admin`
- Email: `admin@alquimiatechnologic.com`
- Contraseña: `password`

## 📁 Estructura del Proyecto

```
TiendawebAlquimia/
├── admin/                  # Panel de administración
│   ├── dashboard.php       # Dashboard principal
│   ├── products.php        # Gestión de productos
│   ├── categories.php      # Gestión de categorías
│   ├── users.php          # Gestión de usuarios
│   └── ...
├── assets/                 # Recursos estáticos
│   ├── css/               # Archivos CSS
│   ├── js/                # Archivos JavaScript
│   └── images/            # Imágenes
├── auth/                   # Sistema de autenticación
│   ├── login.php          # Página de login
│   ├── register.php       # Página de registro
│   └── logout.php         # Script de logout
├── classes/               # Clases PHP
│   ├── User.php           # Clase para usuarios
│   ├── Product.php        # Clase para productos
│   └── Category.php       # Clase para categorías
├── config/                # Configuración
│   ├── config.php         # Configuración general
│   └── database.php       # Configuración de BD
├── database/              # Base de datos
│   └── alquimia_db.sql    # Script de creación
├── index.php              # Página principal
└── README.md              # Este archivo
```

## 🎨 Características del Diseño

### Página Principal
- Hero section con gradiente atractivo
- Sección de categorías con iconos
- Productos destacados
- Información sobre la empresa
- Footer completo

### Panel de Administración
- Sidebar colapsible
- Dashboard con estadísticas
- Gestión completa de productos
- Sistema de notificaciones
- Diseño responsive

### Sistema de Autenticación
- Login y registro con validación
- Sesiones seguras
- Protección CSRF
- Roles de usuario

## 🔧 Configuración Adicional

### Personalizar la Configuración

Edita `config/config.php` para personalizar:

```php
define('SITE_URL', 'http://localhost/TiendawebAlquimia');
define('SITE_NAME', 'AlquimiaTechnologic');
define('ADMIN_EMAIL', 'admin@alquimiatechnologic.com');
```

### Agregar Imágenes de Productos

1. Coloca las imágenes en `assets/images/products/`
2. Nombra las imágenes usando el slug del producto
3. Ejemplo: `aceite-lavanda.jpg` para el producto con slug `aceite-lavanda`

### Configurar Email (Opcional)

Para funcionalidad de email, configura SMTP en `config/config.php`:

```php
// Configuración de email
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'tu-email@gmail.com');
define('SMTP_PASSWORD', 'tu-contraseña');
```

## 🚀 Uso del Sistema

### Como Administrador

1. **Acceder al panel**: `http://localhost/TiendawebAlquimia/admin/dashboard.php`
2. **Gestionar productos**: Crear, editar y eliminar productos
3. **Gestionar categorías**: Organizar productos por categorías
4. **Ver usuarios**: Administrar usuarios registrados
5. **Configurar sitio**: Personalizar configuraciones

### Como Usuario

1. **Registrarse**: Crear una cuenta nueva
2. **Explorar productos**: Navegar por categorías
3. **Agregar al carrito**: Seleccionar productos
4. **Realizar pedido**: Completar compra

## 🔒 Seguridad

- Validación de datos en servidor y cliente
- Protección contra inyección SQL con PDO
- Tokens CSRF para formularios
- Sesiones seguras
- Sanitización de entrada de datos

## 📱 Responsive Design

El sitio está optimizado para:
- Desktop (1200px+)
- Tablet (768px - 1199px)
- Mobile (< 768px)

## 🎯 Funcionalidades Principales

### Productos
- ✅ Listado de productos
- ✅ Búsqueda y filtros
- ✅ Productos destacados
- ✅ Gestión de stock
- ✅ Imágenes múltiples

### Carrito
- ✅ Agregar/quitar productos
- ✅ Actualizar cantidades
- ✅ Persistencia en localStorage
- ✅ Cálculo de totales

### Usuarios
- ✅ Registro y login
- ✅ Perfiles de usuario
- ✅ Roles y permisos
- ✅ Gestión de sesiones

## 🐛 Solución de Problemas

### Error de Conexión a Base de Datos
```
Error: SQLSTATE[HY000] [1045] Access denied for user 'root'@'localhost'
```
**Solución**: Verifica que MySQL esté ejecutándose en XAMPP y que las credenciales en `config/database.php` sean correctas.

### Error 404 en Rutas
```
Error: The requested URL was not found on this server.
```
**Solución**: Asegúrate de que el proyecto esté en la carpeta `htdocs` de XAMPP y que Apache esté ejecutándose.

### Problemas de Permisos
```
Error: Permission denied
```
**Solución**: Verifica que la carpeta del proyecto tenga permisos de lectura/escritura.

## 📞 Soporte

Para soporte técnico o preguntas:
- Email: admin@alquimiatechnologic.com
- Documentación: Consulta este README

## 🔄 Actualizaciones Futuras

- [ ] Sistema de pagos (PayPal, Stripe)
- [ ] Notificaciones por email
- [ ] Sistema de reviews
- [ ] Múltiples idiomas
- [ ] API REST
- [ ] PWA (Progressive Web App)

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Consulta el archivo `LICENSE` para más detalles.

---

**Desarrollado con ❤️ por AlquimiaTechnologic** 