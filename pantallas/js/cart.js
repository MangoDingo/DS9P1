let carrito = [];

// Cargar carrito cuando se carga la página
document.addEventListener("DOMContentLoaded", () => {
    cargarCarrito();
    verificarSesion();
    renderizarCarrito();
});

// ========== FUNCIÓN DE NOTIFICACIONES ==========
function mostrarNotificacion(mensaje, tipo = 'info', duracion = 4000) {
  const container = document.getElementById('notificacionesContainer');
  if (!container) return;

  const iconos = {
    'exito': 'bi-check-circle-fill',
    'error': 'bi-exclamation-circle-fill',
    'info': 'bi-info-circle-fill',
    'advertencia': 'bi-exclamation-triangle-fill'
  };

  const colores = {
    'exito': '#28a745',
    'error': '#dc3545',
    'info': '#17a2b8',
    'advertencia': '#ffc107'
  };

  const notificacion = document.createElement('div');
  notificacion.className = 'alert alert-dismissible fade show';
  notificacion.style.marginBottom = '10px';
  notificacion.style.borderRadius = '8px';
  notificacion.style.boxShadow = '0 2px 8px rgba(0,0,0,0.15)';
  notificacion.style.animation = 'slideIn 0.3s ease-out';

  const color = colores[tipo] || colores['info'];
  const icono = iconos[tipo] || iconos['info'];

  if (tipo === 'exito') notificacion.classList.add('alert-success');
  else if (tipo === 'error') notificacion.classList.add('alert-danger');
  else if (tipo === 'advertencia') notificacion.classList.add('alert-warning');
  else notificacion.classList.add('alert-info');

  notificacion.innerHTML = `
    <div style="display: flex; align-items: center; gap: 10px;">
      <i class="bi ${icono}" style="font-size: 1.2rem; color: ${color};"></i>
      <span>${mensaje}</span>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  `;

  container.appendChild(notificacion);

  // Auto-cerrar la notificación
  setTimeout(() => {
    notificacion.classList.remove('show');
    setTimeout(() => notificacion.remove(), 300);
  }, duracion);
}

// Verificar si hay sesión
function verificarSesion() {
    fetch("../api/verificar-sesion.php")
        .then(res => res.json())
        .then(data => {
            if (data.logueado) {
                if (data.rol === 1) {
                    document.getElementById("navGestionar").style.display = "block";
                }
            }
        })
        .catch(error => console.error("Error al verificar sesión:", error));
}

// Cargar carrito desde localStorage
function cargarCarrito() {
    const carritoGuardado = localStorage.getItem("carrito");
    carrito = carritoGuardado ? JSON.parse(carritoGuardado) : [];
}

// Guardar carrito en localStorage
function guardarCarrito() {
    localStorage.setItem("carrito", JSON.stringify(carrito));
    actualizarBadgeCarrito();
}

// Actualizar badge del carrito en la navbar
function actualizarBadgeCarrito() {
    const cantidadTotal = carrito.reduce((total, producto) => total + producto.cantidad, 0);
    document.getElementById("cartBadge").textContent = cantidadTotal;
}

// Renderizar productos en el carrito
function renderizarCarrito() {
    const contenedorProductos = document.getElementById("productosCarrito");
    const cantidadProductos = document.getElementById("cantidadProductos");

    if (carrito.length === 0) {
        contenedorProductos.innerHTML = `
            <div class="empty-cart">
                <div class="empty-cart-icon">
                    <i class="bi bi-cart-x"></i>
                </div>
                <h5>Tu carrito está vacío</h5>
                <p class="text-muted">No tienes productos agregados</p>
            </div>
        `;
        document.getElementById("btnEliminarTodos").disabled = true;
        document.getElementById("btnProcesarPago").disabled = true;
        ocultarDatos();
        return;
    }

    contenedorProductos.innerHTML = "";
    carrito.forEach((producto, index) => {
        const precioTotal = producto.precioVenta * producto.cantidad;
        contenedorProductos.innerHTML += `
            <div class="producto-item">
                <img src="../publics/productos/${producto.imagen}" alt="${producto.nombre}" class="producto-imagen">
                <div class="producto-detalles">
                    <div class="producto-nombre">${producto.nombre}</div>
                    <div class="producto-precio">$${parseFloat(producto.precioVenta).toFixed(2)}</div>
                </div>
                <div class="text-center">
                    <input type="number" class="form-control cantidad-input" value="${producto.cantidad}"
                           min="1" onchange="actualizarCantidad(${index}, this.value)">
                </div>
                <div class="text-end" style="min-width: 100px;">
                    <div class="producto-precio">$${precioTotal.toFixed(2)}</div>
                </div>
                <button class="btn btn-danger btn-eliminar" onclick="eliminarProducto(${index})">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `;
    });

    cantidadProductos.textContent = carrito.length;
    document.getElementById("btnEliminarTodos").disabled = false;
    document.getElementById("btnProcesarPago").disabled = false;
    mostrarDatos();
    calcularTotales();
}

// Actualizar cantidad de un producto
function actualizarCantidad(index, nuevaCantidad) {
    nuevaCantidad = parseInt(nuevaCantidad);

    if (nuevaCantidad < 1) {
        eliminarProducto(index);
        return;
    }

    carrito[index].cantidad = nuevaCantidad;
    guardarCarrito();
    renderizarCarrito();
}

// Eliminar un producto del carrito
function eliminarProducto(index) {
    carrito.splice(index, 1);
    guardarCarrito();
    renderizarCarrito();
}

// Eliminar todos los productos
function mostrarModalEliminar() {
    if (carrito.length === 0) return;
    const modal = new bootstrap.Modal(document.getElementById("modalConfirmacion"));
    modal.show();
}

function confirmarEliminarTodos() {
    carrito = [];
    guardarCarrito();
    renderizarCarrito();
    mostrarNotificacion("Todos los productos han sido eliminados", "exito");
}

// Calcular totales
function calcularTotales() {
    const subtotal = carrito.reduce((total, producto) => {
        return total + (producto.precioVenta * producto.cantidad);
    }, 0);

    const itbms = subtotal * 0.07; // 7% de ITBMS
    const total = subtotal + itbms;

    document.getElementById("subtotal").textContent = "$" + subtotal.toFixed(2);
    document.getElementById("itbms").textContent = "$" + itbms.toFixed(2);
    document.getElementById("total").textContent = "$" + total.toFixed(2);
}

// Mostrar/Ocultar formulario de datos
function mostrarDatos() {
    document.getElementById("datosFormulario").style.display = "block";
}

function ocultarDatos() {
    document.getElementById("datosFormulario").style.display = "none";
}

// Procesar pago
function procesarPago() {
    // Validar que hay productos
    if (carrito.length === 0) {
        mostrarNotificacion("Tu carrito está vacío", "advertencia");
        return;
    }

    // Obtener datos del formulario
    const numeroTarjeta = document.getElementById("numeroTarjeta").value.trim();
    const vencimiento = document.getElementById("vencimiento").value.trim();
    const cvv = document.getElementById("cvv").value.trim();
    const nombreTitular = document.getElementById("nombreTitular").value.trim();
    const metodoPago = document.querySelector('input[name="metodoPago"]:checked').value;

    // Validaciones del lado del cliente
    if (!numeroTarjeta || !vencimiento || !cvv || !nombreTitular) {
        mostrarNotificacion("Por favor completa todos los campos de la tarjeta", "advertencia");
        return;
    }

    // Validar número de tarjeta (solo dígitos)
    const numeroLimpio = numeroTarjeta.replace(/\s/g, "");
    if (!/^\d{16}$/.test(numeroLimpio)) {
        mostrarNotificacion("El número de tarjeta debe tener 16 dígitos", "error");
        return;
    }

    // Validar vencimiento (MM/YY)
    if (!/^\d{2}\/\d{2}$/.test(vencimiento)) {
        mostrarNotificacion("El vencimiento debe estar en formato MM/YY", "error");
        return;
    }

    // Validar CVV
    if (!/^\d{3,4}$/.test(cvv)) {
        mostrarNotificacion("El CVV debe tener 3 o 4 dígitos", "error");
        return;
    }

    // Cambiar estado del botón
    const boton = document.getElementById("btnProcesarPago");
    boton.disabled = true;
    boton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Procesando...';

    // Preparar datos para enviar al servidor
    const dataPago = {
        numeroTarjeta: numeroLimpio,
        vencimiento: vencimiento,
        cvv: cvv,
        nombreTitular: nombreTitular,
        metodoPago: metodoPago,
        carrito: carrito
    };

    // Enviar solicitud al servidor
    fetch("../api/procesar-pago.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(dataPago)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Mostrar modal de éxito con información de la compra
            const modalContent = document.querySelector("#modalExito .modal-body");
            modalContent.innerHTML = `
                <i class="bi bi-check-circle" style="font-size: 3rem; color: #28a745;"></i>
                <h6 class="mt-3">¡Compra realizada exitosamente!</h6>
                <div class="alert alert-info mt-3" style="text-align: left; font-size: 0.9rem;">
                    <p class="mb-2"><strong>Número de Factura:</strong> #${data.idFactura}</p>
                    <p class="mb-2"><strong>Nuevo Saldo:</strong> $${parseFloat(data.nuevoSaldo).toFixed(2)}</p>
                </div>
                <p class="text-muted">Recibirás una confirmación por correo electrónico</p>
            `;

            const modal = new bootstrap.Modal(document.getElementById("modalExito"));
            modal.show();

            // Limpiar carrito
            carrito = [];
            guardarCarrito();

            // Limpiar formulario
            document.getElementById("numeroTarjeta").value = "";
            document.getElementById("vencimiento").value = "";
            document.getElementById("cvv").value = "";
            document.getElementById("nombreTitular").value = "";
            document.getElementById("visa").checked = true;

            // Restaurar botón
            boton.disabled = false;
            boton.innerHTML = '<i class="bi bi-lock-fill"></i> Procesar pago';

            // Renderizar carrito vacío
            renderizarCarrito();
        } else {
            // Mostrar error
            mostrarNotificacion("Error en el pago: " + data.message, "error", 5000);
            boton.disabled = false;
            boton.innerHTML = '<i class="bi bi-lock-fill"></i> Procesar pago';
        }
    })
    .catch(error => {
        console.error("Error:", error);
        mostrarNotificacion("Error al conectar con el servidor", "error");
        boton.disabled = false;
        boton.innerHTML = '<i class="bi bi-lock-fill"></i> Procesar pago';
    });
}

// Formatear número de tarjeta mientras se escribe
document.addEventListener("DOMContentLoaded", () => {
    const inputNumero = document.getElementById("numeroTarjeta");
    inputNumero?.addEventListener("input", (e) => {
        let valor = e.target.value
            .replace(/\D/g, "")   // elimina todo lo que no sea número
            .slice(0, 16);        // limita a 16 dígitos
        let formateado = valor.replace(/(\d{4})(?=\d)/g, "$1 ");
        e.target.value = formateado;
    });

    const inputVencimiento = document.getElementById("vencimiento");
    inputVencimiento?.addEventListener("input", (e) => {
        let valor = e.target.value.replace(/\D/g, "");
        if (valor.length >= 2) {
            valor = valor.slice(0, 2) + "/" + valor.slice(2, 4);
        }
        e.target.value = valor;
    });

    const inputCVV = document.getElementById("cvv");
    inputCVV?.addEventListener("input", (e) => {
        e.target.value = e.target.value.replace(/\D/g, "");
    });
});

// Agregar producto al carrito desde otras páginas
function agregarAlCarrito(producto) {
    const productoExistente = carrito.find(p => p.idProducto === producto.idProducto);

    if (productoExistente) {
        productoExistente.cantidad++;
    } else {
        producto.cantidad = 1;
        carrito.push(producto);
    }

    guardarCarrito();
    mostrarNotificacion("El producto ha sido agregado a tu carrito", "exito");
}
