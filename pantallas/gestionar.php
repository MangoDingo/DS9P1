<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
      .list-group-item.active {
        background-color: #343a40;
        border-color: #343a40;
      }
      .dropdown-toggle {
        color: #212529 !important;
        text-decoration: none !important;
      }
      .dropdown-toggle:hover {
        color: #212529 !important;
      }
      .nav-link.active {
        color: #343a40 !important;
        border-bottom: 3px solid #343a40;
      }
      .list-group-item {
        border: none;
        padding: 10px 15px;
        cursor: pointer;
      }
      .list-group-item:hover {
        background-color: #f5f5f5;
      }
      .submenu {
        display: none;
        padding-left: 20px;
        margin-top: 5px;
      }
      .submenu.show {
        display: block;
      }
      .submenu-item {
        padding: 8px 15px;
        background-color: #f9f9f9;
        border-left: 3px solid #ddd;
        margin-bottom: 2px;
        cursor: pointer;
      }
      .submenu-item:hover {
        background-color: #eeeeee;
        border-left-color: #343a40;
      }
      .submenu-item.active {
        background-color: #e8e8e8;
        border-left-color: #343a40;
      }
      .contenedor-activo {
        display: none;
      }
      .contenedor-activo.show {
        display: block;
      }
      .tabla-productos {
        background-color: white;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        padding: 20px;
      }
      .tabla-productos img {
        max-width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 3px;
      }
      .btn-editar {
        background-color: #007bff;
        color: white;
        padding: 5px 10px;
        border: none;
        border-radius: 3px;
        cursor: pointer;
        margin-right: 5px;
        text-decoration: none;
      }
      .btn-editar:hover {
        background-color: #0056b3;
        color: white;
      }
      .btn-eliminar {
        background-color: #dc3545;
        color: white;
        padding: 5px 10px;
        border: none;
        border-radius: 3px;
        cursor: pointer;
        text-decoration: none;
      }
      .btn-eliminar:hover {
        background-color: #c82333;
        color: white;
      }
      .pagination .page-link {
        color: #343a40;
      }
      .pagination .page-link:hover {
        color: #343a40;
        background-color: #e9ecef;
      }
      .pagination .page-link.active,
      .pagination .page-item.active .page-link {
        background-color: #343a40;
        border-color: #343a40;
        color: white;
      }
      .pagination .page-link:focus {
        border-color: #343a40;
        box-shadow: 0 0 0 0.25rem rgba(52, 58, 64, 0.25);
      }
      .form-producto {
        background-color: white;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        padding: 20px;
      }
      .form-row-imagen {
        display: flex;
        gap: 20px;
        margin-bottom: 30px;
      }
      .preview-container {
        width: 150px;
        height: 150px;
        border: 2px dashed #ddd;
        border-radius: 5px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f9f9f9;
      }
      #previewImagen {
        max-width: 100%;
        max-height: 100%;
        object-fit: cover;
        display: none;
      }
      .btn-subir-imagen {
        background-color: #343a40;
        color: white;
        padding: 8px 16px;
        border: none;
        border-radius: 3px;
        cursor: pointer;
        margin-top: 10px;
      }
      .btn-subir-imagen:hover {
        background-color: #23272b;
        color: white;
      }
      .form-fields {
        flex: 1;
      }
      .form-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        margin-bottom: 20px;
      }
      .form-row-stock {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        margin-bottom: 20px;
      }
      .form-group {
        display: flex;
        flex-direction: column;
      }
      .form-group label {
        font-weight: bold;
        margin-bottom: 5px;
        color: #333;
      }
      .form-group input,
      .form-group select,
      .form-group textarea {
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 3px;
        font-family: inherit;
      }
      .form-group input:focus,
      .form-group select:focus,
      .form-group textarea:focus {
        outline: none;
        border-color: #343a40;
        box-shadow: 0 0 5px rgba(52, 58, 64, 0.25);
      }
      .form-group textarea {
        grid-column: 1 / -1;
        min-height: 120px;
        resize: vertical;
      }
      .form-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
      }
      .form-header h3 {
        margin: 0;
      }
      .btn-guardar {
        background-color: #28a745;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 3px;
        cursor: pointer;
        font-weight: bold;
      }
      .btn-guardar:hover {
        background-color: #218838;
        color: white;
      }
      .btn-limpiar {
        background-color: #6c757d;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 3px;
        cursor: pointer;
        font-weight: bold;
        margin-left: 10px;
      }
      .btn-limpiar:hover {
        background-color: #5a6268;
        color: white;
      }
      .notificacion {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 5px;
        color: white;
        font-weight: bold;
        z-index: 9999;
        max-width: 400px;
        animation: slideIn 0.3s ease-in-out;
      }
      .notificacion.error {
        background-color: #dc3545;
      }
      .notificacion.success {
        background-color: #28a745;
      }
      @keyframes slideIn {
        from {
          transform: translateX(450px);
          opacity: 0;
        }
        to {
          transform: translateX(0);
          opacity: 1;
        }
      }
      @keyframes slideOut {
        from {
          transform: translateX(0);
          opacity: 1;
        }
        to {
          transform: translateX(450px);
          opacity: 0;
        }
      }
      .modal-confirmacion {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 10000;
        justify-content: center;
        align-items: center;
      }
      .modal-confirmacion.show {
        display: flex;
      }
      .modal-contenido {
        background-color: white;
        padding: 30px;
        border-radius: 8px;
        max-width: 400px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      }
      .modal-titulo {
        font-size: 18px;
        font-weight: bold;
        color: #333;
        margin-bottom: 15px;
      }
      .modal-mensaje {
        color: #666;
        margin-bottom: 25px;
        line-height: 1.5;
      }
      .modal-botones {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
      }
      .modal-btn {
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: bold;
      }
      .modal-btn-cancelar {
        background-color: #6c757d;
        color: white;
      }
      .modal-btn-cancelar:hover {
        background-color: #5a6268;
      }
      .modal-btn-confirmar {
        background-color: #dc3545;
        color: white;
      }
      .modal-btn-confirmar:hover {
        background-color: #c82333;
      }
      .tablas-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
      }
      .tabla-seccion {
        background-color: white;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        padding: 20px;
      }
      .tabla-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
      }
      .tabla-header h4 {
        margin: 0;
      }
      .btn-anadir {
        background-color: #28a745;
        color: white;
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
      }
      .btn-anadir:hover {
        background-color: #218838;
      }
      .tabla-datos {
        width: 100%;
        border-collapse: collapse;
      }
      .tabla-datos th {
        background-color: #f5f5f5;
        padding: 10px;
        text-align: left;
        border-bottom: 2px solid #ddd;
        font-weight: bold;
      }
      .tabla-datos td {
        padding: 10px;
        border-bottom: 1px solid #eee;
      }
      .tabla-datos tr:hover {
        background-color: #f9f9f9;
      }
      .tabla-acciones {
        display: flex;
        gap: 8px;
      }
      .btn-table-editar {
        background-color: #007bff;
        color: white;
        padding: 5px 10px;
        border: none;
        border-radius: 3px;
        cursor: pointer;
        font-size: 12px;
      }
      .btn-table-editar:hover {
        background-color: #0056b3;
      }
      .btn-table-eliminar {
        background-color: #dc3545;
        color: white;
        padding: 5px 10px;
        border: none;
        border-radius: 3px;
        cursor: pointer;
        font-size: 12px;
      }
      .btn-table-eliminar:hover {
        background-color: #c82333;
      }
      .modal-formulario {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 10000;
        justify-content: center;
        align-items: center;
      }
      .modal-formulario.show {
        display: flex;
      }
      .modal-form-contenido {
        background-color: white;
        padding: 30px;
        border-radius: 8px;
        max-width: 400px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      }
      .modal-form-titulo {
        font-size: 20px;
        font-weight: bold;
        color: #333;
        margin-bottom: 20px;
      }
      .form-group-modal {
        margin-bottom: 15px;
      }
      .form-group-modal label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
        color: #333;
      }
      .form-group-modal input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-family: inherit;
        box-sizing: border-box;
      }
      .form-group-modal input:focus {
        outline: none;
        border-color: #343a40;
        box-shadow: 0 0 5px rgba(52, 58, 64, 0.25);
      }
      .modal-form-botones {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 25px;
      }
      .modal-form-btn {
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: bold;
      }
      .modal-form-btn-cancelar {
        background-color: #6c757d;
        color: white;
      }
      .modal-form-btn-cancelar:hover {
        background-color: #5a6268;
      }
      .modal-form-btn-guardar {
        background-color: #28a745;
        color: white;
      }
      .modal-form-btn-guardar:hover {
        background-color: #218838;
      }
    </style>
    <title>Gestionar</title>
</head>
<body class="bg-light">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Barra de navegacion -->
<nav class="navbar navbar-expand-lg bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">Tienda</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
      <ul class="navbar-nav w-100 align-items-center">
        <li class="nav-item">
          <a class="nav-link" href="index.php">Inicio</a>
        </li>
        <li class="nav-item" id="navGestionar">
          <a class="nav-link active" href="gestionar.php">Gestionar</a>
        </li>
        <li class="nav-item" id="navCarrito">
          <a class="nav-link" href="carrito.php">Carrito</a>
        </li>
        <li class="nav-item ms-auto">
          <div class="nav-link dropdown" id="navUsuario">
            <?php if(isset($_SESSION["usuario"])): ?>
              <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <?php echo $_SESSION["nombre"] . " " . $_SESSION["apellido"]; ?>
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="../api/logout.php">Cerrar sesión</a></li>
              </ul>
            <?php else: ?>
              <a class="nav-link" href="login.php">Iniciar sesión</a>
            <?php endif; ?>
          </div>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Contenido Principal -->
<div class="container-fluid mt-4">
  <div class="row">
    <!-- SIDEBAR -->
    <div class="col-md-3">
      <div class="list-group">
        <div class="list-group-item" onclick="toggleSubmenu('productosMenu')">
          <span>Productos</span>
          <i class="bi bi-chevron-down float-end"></i>
        </div>
        <div class="submenu show" id="productosMenu">
          <div class="submenu-item active" onclick="mostrarContenedor('listaProductos', this)">
            Lista de productos
          </div>
          <div class="submenu-item" onclick="mostrarContenedor('anadirProductos', this)">
            Añadir productos
          </div>
          <div class="submenu-item" onclick="mostrarContenedor('categorias', this)">
            Categorías
          </div>
        </div>
      </div>
    </div>

    <!-- CONTENIDO -->
    <div class="col-md-9">
      <!-- LISTA DE PRODUCTOS -->
      <div class="contenedor-activo show" id="listaProductos">
        <div class="tabla-productos">
          <h3 class="mb-4">Lista de Productos</h3>

          <table class="table table-striped table-hover">
            <thead class="table-dark">
              <tr>
                <th>Imagen</th>
                <th>Nombre</th>
                <th>Precio Venta</th>
                <th>Stock</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody id="tablaProductos">
              <!-- Los productos se cargan aquí con JavaScript -->
            </tbody>
          </table>

          <!-- PAGINACIÓN -->
          <nav aria-label="Paginación de productos" class="mt-4 d-flex justify-content-center">
            <ul class="pagination">
              <li class="page-item" id="btnAnterior">
                <a class="page-link" href="#" onclick="paginaAnterior(); return false;">Anterior</a>
              </li>
              <li class="page-item active" id="paginaActual">
                <span class="page-link">Página <span id="numeroPagina">1</span></span>
              </li>
              <li class="page-item" id="btnSiguiente">
                <a class="page-link" href="#" onclick="paginaSiguiente(); return false;">Siguiente</a>
              </li>
            </ul>
          </nav>
        </div>
      </div>

      <!-- AÑADIR PRODUCTOS -->
      <div class="contenedor-activo" id="anadirProductos">
        <div class="form-producto">
          <div class="form-header">
            <h3>Añadir Producto</h3>
            <div>
              <button type="button" class="btn-guardar" onclick="guardarProducto()">Guardar Producto</button>
              <button type="button" class="btn-limpiar" onclick="limpiarFormularioProducto()">Limpiar</button>
            </div>
          </div>

          <!-- Sección de imagen -->
          <div class="form-row-imagen">
            <div>
              <div class="preview-container">
                <img id="previewImagen" alt="Preview">
              </div>
              <button type="button" class="btn-subir-imagen" onclick="subirImagen()">Subir imagen</button>
            </div>

            <!-- Campos de nombre, precio costo y precio venta -->
            <div class="form-fields">
              <div class="form-row">
                <div class="form-group">
                  <label for="idProducto">ID Producto</label>
                  <input type="number" id="idProducto" placeholder="ID del producto" min="1">
                </div>
                <div class="form-group">
                  <label for="nombre">Nombre</label>
                  <input type="text" id="nombre" placeholder="Nombre del producto">
                </div>
                <div class="form-group">
                  <label for="precioCosto">Precio Costo</label>
                  <input type="number" id="precioCosto" placeholder="0.00" step="0.01" min="0">
                </div>
              </div>

              <div class="form-row">
                <div class="form-group">
                  <label for="precioVenta">Precio Venta</label>
                  <input type="number" id="precioVenta" placeholder="0.00" step="0.01" min="0">
                </div>
                <div class="form-group">
                  <label for="stock">Stock</label>
                  <input type="number" id="stock" placeholder="0" min="0">
                </div>
                <div class="form-group">
                  <label for="unidad">Unidad</label>
                  <input type="text" id="unidad">
                </div>
              </div>

              <div class="form-row">
                <div class="form-group">
                  <label for="selectCategoria">Categoría</label>
                  <select id="selectCategoria">
                    <option value="">Seleccionar categoría</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="selectMarca">Marca</label>
                  <select id="selectMarca">
                    <option value="">Seleccionar marca</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <!-- Descripción -->
          <div class="form-group">
            <label for="descripcion">Descripción</label>
            <textarea id="descripcion" placeholder="Ingresa la descripción del producto"></textarea>
          </div>
        </div>
      </div>

      <!-- CATEGORÍAS -->
      <div class="contenedor-activo" id="categorias">
        <div class="tablas-container">
          <!-- Tabla de Categorías -->
          <div class="tabla-seccion">
            <div class="tabla-header">
              <h4>Categorías</h4>
              <button class="btn-anadir" onclick="abrirModalCategoria()">+ Agregar</button>
            </div>
            <table class="tabla-datos">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nombre</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody id="tablaCategorias">
                <tr>
                  <td colspan="3" style="text-align: center; color: #999;">Cargando...</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Tabla de Marcas -->
          <div class="tabla-seccion">
            <div class="tabla-header">
              <h4>Marcas</h4>
              <button class="btn-anadir" onclick="abrirModalMarca()">+ Agregar</button>
            </div>
            <table class="tabla-datos">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nombre</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody id="tablaMarcas">
                <tr>
                  <td colspan="3" style="text-align: center; color: #999;">Cargando...</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal de Confirmación -->
<div id="modalConfirmacion" class="modal-confirmacion">
  <div class="modal-contenido">
    <div class="modal-titulo" id="modalTitulo">Confirmar acción</div>
    <div class="modal-mensaje" id="modalMensaje">¿Estás seguro?</div>
    <div class="modal-botones">
      <button class="modal-btn modal-btn-cancelar" onclick="cerrarModal()">Cancelar</button>
      <button class="modal-btn modal-btn-confirmar" id="btnConfirmar" onclick="confirmarAccion()">Eliminar</button>
    </div>
  </div>
</div>

<!-- Modal de Formulario (Categorías/Marcas) -->
<div id="modalFormulario" class="modal-formulario">
  <div class="modal-form-contenido">
    <div class="modal-form-titulo" id="modalFormTitulo">Agregar Categoría</div>
    <div class="form-group-modal">
      <label for="modalFormNombre">Nombre</label>
      <input type="text" id="modalFormNombre" placeholder="Ingresa el nombre">
    </div>
    <div class="modal-form-botones">
      <button class="modal-form-btn modal-form-btn-cancelar" onclick="cerrarModalFormulario()">Cancelar</button>
      <button class="modal-form-btn modal-form-btn-guardar" onclick="guardarDesdeModal()">Guardar</button>
    </div>
  </div>
</div>

<script src="js/gestionar.js"></script>

</body>
</html>
