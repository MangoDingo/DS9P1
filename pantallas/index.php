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
      .pagination .page-link {
        color: #343a40;
      }
      .nav-link.active {
        color: #343a40 !important;
        border-bottom: 3px solid #343a40;
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
      .card-img-top {
        height: 120px;
        object-fit: fill;
      }
      .card-body {
        padding: 10px;
      }
      .card-title {
        font-size: 0.9rem;
        margin-bottom: 5px;
      }
      .card-text {
        font-size: 0.85rem;
        margin-bottom: 8px;
      }
      .dropdown-toggle {
        color: #212529 !important;
        text-decoration: none !important;
      }
      .dropdown-toggle:hover {
        color: #212529 !important;
      }
      #cartBadge {
        font-size: 0.7rem;
        padding: 3px 6px;
        margin-left: 5px;
        vertical-align: super;
      }
      @keyframes slideIn {
        from {
          transform: translateX(400px);
          opacity: 0;
        }
        to {
          transform: translateX(0);
          opacity: 1;
        }
      }
    </style>
    <title>Inicio</title>
</head>
<body class="bg-light">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Barra de navegacion -->
<nav class="navbar navbar-expand-lg bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="#" onclick="cargarPagina('index'); return false;">TechIstmo</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
      <ul class="navbar-nav w-100 align-items-center">
        <li class="nav-item">
          <a class="nav-link active" href="index.php">Inicio</a>
        </li>
        <li class="nav-item" id="navGestionar" style="display: none;">
          <a class="nav-link" href="gestionar.php">Gestionar</a>
        </li>
        <li class="nav-item" id="navCarrito">
          <a class="nav-link" href="carrito.php" return false;">Carrito <span class="badge bg-dark" id="cartBadge" style="display: none;">0</span></a>
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
            <?php endif; ?>
          </div>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Contenedor de notificaciones -->
<div id="notificacionesContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 400px;"></div>

<!-- Portada -->
<div id="contenidoPrincipal">
  <div class="container-fluid p-0 position-relative">
    <img src="../publics/portada.jpg" class="w-100" style="height: 70vh;" alt="Imagen de portada">
    <!-- TEXTO CENTRADO -->
    <div class="position-absolute top-50 start-50 translate-middle text-center text-dark-gray">
      <h1 class="display-4 fw-bold">Bienvenido a la Tienda</h1>
      <p class="lead">Las mejores ofertas en tecnología</p>
    </div>
    <!-- BARRA DE BÚSQUEDA ENCIMA (ABAJO) -->
    <div class="position-absolute bottom-0 start-50 translate-middle-x w-100 mb-3">
      <div class="container">
        <div class="row">
          <div class="col-md-6 mx-auto">
            <div class="input-group shadow">
              <input type="text" class="form-control" id="inputBusqueda" placeholder="Buscar productos..." autofocus>
              <button class="btn btn-dark" type="button" id="btnBuscar" onclick="buscar(); return false;">Buscar</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="container-fluid mt-4">
    <div class="row">

      <!-- SIDEBAR -->
      <div class="col-md-3">
        <div class="list-group" id="listaCategorias"></div>
      </div>

      <!-- PRODUCTOS -->
      <div class="col-md-9">
        <div class="row" id="productos"></div>

        <!-- PAGINACIÓN -->
        <nav aria-label="Paginación de productos" class="mt-5 d-flex justify-content-center">
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
  </div>
</div>

<!-- Modal de Producto -->
<div class="modal fade" id="modalProducto" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalProductoTitulo"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-5 text-center">
            <img id="modalProductoImagen" src="" alt="" class="img-fluid" style="max-height: 300px; object-fit: cover; border-radius: 8px;">
          </div>
          <div class="col-md-7">
            <div class="mb-3">
              <h6 class="text-muted mb-2">Descripción</h6>
              <p id="modalProductoDescripcion" style="text-align: justify;"></p>
            </div>
            <div class="mb-3">
              <h6 class="text-muted">Precio</h6>
              <h4 class="text-dark" id="modalProductoPrecio" style="font-weight: bold;"></h4>
            </div>
            <div class="mb-3">
              <h6 class="text-muted">Cantidad</h6>
              <h5 class="text-dark modalProductoCantidad" style="font-weight: bold;">-</h5>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-dark" onclick="agregarAlCarritoDesdeModal()" data-bs-dismiss="modal">
          <i class="bi bi-cart-plus"></i> Agregar al carrito
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal de Producto -->
<div class="modal fade" id="modalProducto" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-light border-bottom">
        <h5 class="modal-title" id="modalProductoTitulo" style="font-weight: 600;"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <div class="row">
          <div class="col-md-5">
            <img id="modalProductoImagen" src="" alt="" class="img-fluid" style="height: 300px; object-fit: cover; border-radius: 8px; width: 100%;">
          </div>
          <div class="col-md-7">
            <div class="mb-4">
              <h6 class="text-muted text-uppercase" style="font-size: 0.85rem; letter-spacing: 0.5px;">Descripción</h6>
              <p id="modalProductoDescripcion" style="font-size: 0.95rem; line-height: 1.6; color: #555; text-align: justify;"></p>
            </div>
            <div class="mb-3">
              <h6 class="text-muted text-uppercase" style="font-size: 0.85rem; letter-spacing: 0.5px;">Precio</h6>
              <h4 id="modalProductoPrecio" style="font-weight: bold; color: #343a40;"></h4>
            </div>
            <div class="mb-3">
              <h6 class="text-muted text-uppercase" style="font-size: 0.85rem; letter-spacing: 0.5px;">Cantidad</h6>
              <h5 class="text-dark modalProductoCantidad" style="font-weight: bold; color: #343a40;">-</h5>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer border-top bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-dark" onclick="agregarAlCarritoDesdeModal()" data-bs-dismiss="modal">
          <i class="bi bi-cart-plus"></i> Agregar al carrito
        </button>
      </div>
    </div>
  </div>
</div>

<!-- JS EXTERNO -->
<script src="js/app.js"></script>
</body>
</html>