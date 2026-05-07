# Tienda TechIstmo

Aplicación web de tienda en línea desarrollada en PHP con MySQL y Bootstrap. Permite listar productos, filtrar por categoría, buscar, ver detalles en modales, agregar productos al carrito y procesar pagos simulados. Incluye panel administrativo para gestionar productos, categorías y marcas.

## Características principales

- Catálogo de productos con paginación.
- Búsqueda de productos.
- Filtro por categorías.
- Modal de producto con información detallada y cantidad disponible.
- Carrito de compras en el navegador usando `localStorage`.
- Formulario de pago simulado con validación.
- Panel de gestión (`gestionar.php`) para usuarios administradores.
- Gestión de productos, categorías y marcas desde el panel administrativo.
- Inicio de sesión con control de sesión y cierre de sesión.

## Estructura del proyecto

- `api/` - APIs PHP para operaciones CRUD y de sesión.
  - `login.php` - inicia sesión de usuario.
  - `logout.php` - cierra sesión.
  - `verificar-sesion.php` - verifica si el usuario está logueado.
  - `productos.php`, `categorias.php`, `marcas.php` - obtienen datos de productos, categorías y marcas.
  - `guardar-producto.php`, `guardar-categoria.php`, `guardar-marca.php` - guardan o actualizan registros.
  - `eliminar-producto.php`, `eliminar-categoria.php`, `eliminar-marca.php` - eliminan registros.
  - `procesar-pago.php` - procesa el pago simulado.
  - `tarjetas.php` - obtiene datos de tarjetas disponibles.

- `database/` - Esquema de base de datos.
  - `ds9p1.sql` - archivo SQL para crear la base de datos y cargar datos iniciales.

- `modelos/` - Conexión a la base de datos.
  - `conexion.php` - configuración de conexión MySQL.

- `pantallas/` - Vistas públicas y privadas.
  - `index.php` - página principal de la tienda.
  - `login.php` - página de inicio de sesión.
  - `carrito.php` - vista del carrito de compras.
  - `gestionar.php` - panel administrativo.
  - `js/` - scripts JavaScript.
    - `app.js` - lógica del catálogo, filtros, carrito y modales.
    - `cart.js` - lógica de vista de carrito y pago.
    - `gestionar.js` - lógica del panel de gestión.

- `publics/productos/` - imágenes de los productos.

## Requisitos

- PHP 7.x o superior.
- MySQL/MariaDB.
- XAMPP, WAMP o servidor local equivalente.
- Navegador moderno con soporte para JavaScript.

## Instalación

1. Copia el proyecto a la carpeta de tu servidor local, por ejemplo `c:\xampp\htdocs\DS9P1`.
2. Inicia Apache y MySQL desde XAMPP.
3. Crea la base de datos desde `database/ds9p1.sql` en phpMyAdmin o mediante consola.
4. Verifica que el archivo `modelos/conexion.php` tenga las credenciales correctas de la base de datos.

```php
$host = "localhost";
$user = "root";
$pass = "";
$db = "ds9p1";
```

5. Abre el proyecto en el navegador:

```text
http://localhost/DS9P1/pantallas/index.php
```

## Uso

- Navega por la tienda desde `index.php`.
- Haz clic en un producto para ver el modal con detalles, precio y cantidad disponible.
- Agrega productos al carrito y verifica el badge en la barra de navegación.
- En el carrito, ajusta cantidades o elimina productos y procede a pago.
- Si tienes acceso de administrador, ingresa a `gestionar.php` para administrar productos, categorías y marcas.

## Notas importantes

- El carrito se guarda en el `localStorage` del navegador y no en la base de datos.
- El procesado de pago es solo una simulación visual; no realiza transacciones reales.
- El proyecto usa `history.replaceState` para que la URL siempre muestre `index.php`.

