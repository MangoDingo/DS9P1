<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .list-group-item.active {
            background-color: #343a40;
            border-color: #343a40;
        }
        .card {
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .producto-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
            gap: 15px;
        }
        .producto-item:last-child {
            border-bottom: none;
        }
        .producto-imagen {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
        }
        .producto-detalles {
            flex: 1;
        }
        .producto-nombre {
            font-weight: 600;
            margin-bottom: 5px;
        }
        .producto-precio {
            color: #343a40;
            font-weight: 600;
            font-size: 1.1rem;
        }
        .cantidad-input {
            width: 60px;
        }
        .resumen-total {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .total-monto {
            font-size: 1.8rem;
            font-weight: bold;
            color: #343a40;
        }
        .radio-tarjeta {
            margin-bottom: 15px;
            padding: 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .radio-tarjeta:hover {
            border-color: #343a40;
            background-color: #f8f9fa;
        }
        .radio-tarjeta input[type="radio"] {
            margin-right: 10px;
        }
        .icono-tarjeta {
            font-size: 2rem;
            margin-right: 10px;
        }
        .empty-cart {
            text-align: center;
            padding: 60px 20px;
        }
        .empty-cart-icon {
            font-size: 4rem;
            color: #ccc;
            margin-bottom: 20px;
        }
        .form-grupo {
            margin-bottom: 15px;
        }
        .btn-eliminar {
            padding: 5px 10px;
            font-size: 0.85rem;
        }
        .badge-cantidad {
            background-color: #343a40;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
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
    <title>Carrito de Compras</title>
</head>
<body class="bg-light">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">TechIstmo</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav w-100 align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Inicio</a>
                    </li>
                    <li class="nav-item" id="navGestionar" style="display: none;">
                        <a class="nav-link" href="gestionar.php">Gestionar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="carrito.php">Carrito <span class="badge badge-cantidad" id="cartBadge">0</span></a>
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

    <!-- Contenido principal -->
    <div class="container mt-5 mb-5">
        <h2 class="mb-4">Carrito de Compras</h2>

        <div class="row" id="carritoContent">
            <!-- LADO IZQUIERDO: PRODUCTOS -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">Productos (<span id="cantidadProductos">0</span>)</h5>
                    </div>
                    <div id="productosCarrito"></div>
                </div>

                <!-- Botones de acciones -->
                <div class="mt-3 d-flex gap-2">
                    <button class="btn btn-outline-danger" id="btnEliminarTodos" onclick="mostrarModalEliminar()">
                        <i class="bi bi-trash"></i> Eliminar todo
                    </button>
                    <a href="index.php" class="btn btn-outline-dark">
                        <i class="bi bi-arrow-left"></i> Continuar comprando
                    </a>
                </div>
            </div>

            <!-- LADO DERECHO: RESUMEN Y PAGO -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">Resumen de compra</h5>
                    </div>
                    <div class="card-body">
                        <!-- Resumen de totales -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span id="subtotal">$0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span>ITBMS (7%):</span>
                                <span id="itbms">$0.00</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <span class="total-monto">Total:</span>
                                <span class="total-monto" id="total">$0.00</span>
                            </div>
                        </div>

                        <!-- Selección de tarjeta -->
                        <div class="mt-4 mb-4">
                            <h6 class="mb-3">Método de pago</h6>

                            <div class="radio-tarjeta">
                                <input type="radio" id="visa" name="metodoPago" value="visa" checked>
                                <label for="visa" class="mb-0" style="cursor: pointer; flex: 1;">
                                    <i class="bi bi-credit-card icono-tarjeta" style="color: #1A1F71;"></i>
                                    <strong>Visa</strong>
                                </label>
                            </div>

                            <div class="radio-tarjeta">
                                <input type="radio" id="mastercard" name="metodoPago" value="mastercard">
                                <label for="mastercard" class="mb-0" style="cursor: pointer; flex: 1;">
                                    <i class="bi bi-credit-card icono-tarjeta" style="color: #FF5F00;"></i>
                                    <strong>Mastercard</strong>
                                </label>
                            </div>
                        </div>

                        <!-- Información de tarjeta -->
                        <div id="datosFormulario">
                            <div class="form-grupo">
                                <label class="form-label">Número de tarjeta</label>
                                <input type="text" class="form-control" id="numeroTarjeta" placeholder="0000 0000 0000 0000" maxlength="19">
                            </div>

                            <div class="row">
                                <div class="col-6 form-grupo">
                                    <label class="form-label">Vencimiento</label>
                                    <input type="text" class="form-control" id="vencimiento" placeholder="MM/YY" maxlength="5">
                                </div>
                                <div class="col-6 form-grupo">
                                    <label class="form-label">CVV</label>
                                    <input type="text" class="form-control" id="cvv" placeholder="000" maxlength="4">
                                </div>
                            </div>

                            <div class="form-grupo">
                                <label class="form-label">Nombre del titular</label>
                                <input type="text" class="form-control" id="nombreTitular" placeholder="Nombre completo">
                            </div>
                        </div>

                        <button class="btn btn-success w-100 mt-4" id="btnProcesarPago" onclick="procesarPago()">
                            <i class="bi bi-lock-fill"></i> Procesar pago
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación -->
    <div class="modal fade" id="modalConfirmacion" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">Confirmar eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar todos los productos del carrito?</p>
                    <p class="text-muted small">Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" onclick="confirmarEliminarTodos()" data-bs-dismiss="modal">Eliminar todo</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalExito" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">¡Compra realizada!</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-4" id="modalContent">
                    <i class="bi bi-check-circle" style="font-size: 3rem; color: #28a745;"></i>
                    <h6 class="mt-3">Tu compra se ha procesado exitosamente</h6>
                    <p class="text-muted">Recibirás una confirmación por correo electrónico</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal" onclick="window.location.href='index.php'">Volver al inicio</button>
                </div>
            </div>
        </div>
    </div>

    <script src="js/cart.js"></script>
</body>
</html>
