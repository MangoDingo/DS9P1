let todosProductos = [];
let productosActuales = [];
let paginaActual = 1;
const productosPorPagina = 8;
let categoriaActual = null;
let busquedaActual = "";
let carrito = [];
let productoSeleccionado = null; // Para el modal del producto

// Verificar si hay sesión y mostrar/ocultar opciones de admin
document.addEventListener("DOMContentLoaded", () => {
  cargarCarritoLocal();
  verificarSesion();
  cargarCategorias();
  cargarProductos();
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

function cargarCategorias() {
  fetch("../api/categorias.php")
    .then(res => res.json())
    .then(data => {
      let listaCategorias = document.getElementById("listaCategorias");
      listaCategorias.innerHTML = "";

      // Agregar opción "Todas las categorías"
      let btnTodas = document.createElement("a");
      btnTodas.className = "list-group-item active";
      btnTodas.href = "#";
      btnTodas.textContent = "Todas las categorías";
      btnTodas.onclick = (e) => {
        e.preventDefault();
        filtrarPorCategoria(null);
      };
      listaCategorias.appendChild(btnTodas);

      // Agregar categorías
      if (data && data.length > 0) {
        data.forEach(categoria => {
          let link = document.createElement("a");
          link.className = "list-group-item";
          link.href = "#";
          link.textContent = categoria.nombreCat;
          link.onclick = (e) => {
            e.preventDefault();
            filtrarPorCategoria(categoria.idCategoria, link);
          };
          listaCategorias.appendChild(link);
        });
      }
    })
    .catch(error => {
      console.error("Error al cargar categorías:", error);
      // Fallback: mostrar categorías estáticas
      let listaCategorias = document.getElementById("listaCategorias");
      listaCategorias.innerHTML = `
        <a class="list-group-item active" href="#" onclick="filtrarPorCategoria(null, this); return false;">Todas las categorías</a>
        <a class="list-group-item" href="#" onclick="filtrarPorCategoria(1, this); return false;">Discos duros</a>
        <a class="list-group-item" href="#" onclick="filtrarPorCategoria(2, this); return false;">Fuentes de poder</a>
        <a class="list-group-item" href="#" onclick="filtrarPorCategoria(3, this); return false;">Memorias ram</a>
        <a class="list-group-item" href="#" onclick="filtrarPorCategoria(4, this); return false;">Procesadores</a>
        <a class="list-group-item" href="#" onclick="filtrarPorCategoria(5, this); return false;">Tarjetas madre</a>
      `;
    });
}

function cargarProductos() {
  fetch("../api/productos.php")
    .then(res => res.json())
    .then(data => {
      todosProductos = data;
      productosActuales = data;
      mostrarPagina(1);
    })
    .catch(error => console.error("Error:", error));
}

function filtrarPorCategoria(idCategoria, elemento = null) {
  categoriaActual = idCategoria;
  busquedaActual = "";
  document.getElementById("inputBusqueda").value = "";

  productosActuales = idCategoria === null
    ? todosProductos
    : todosProductos.filter(p => p.idCategoria == idCategoria);

  paginaActual = 1;
  mostrarPagina(1);

  // Actualizar estilos del sidebar
  document.querySelectorAll("#listaCategorias .list-group-item").forEach(item => {
    item.classList.remove("active");
  });

  if (elemento) {
    elemento.classList.add("active");
  } else {
    document.querySelectorAll("#listaCategorias .list-group-item")[0].classList.add("active");
  }
}

function mostrarPagina(numeroPagina) {
  paginaActual = numeroPagina;
  const inicio = (numeroPagina - 1) * productosPorPagina;
  const fin = inicio + productosPorPagina;
  const productosAMostrar = productosActuales.slice(inicio, fin);

  let contenedor = document.getElementById("productos");
  contenedor.innerHTML = "";

  if (productosAMostrar.length === 0) {
    contenedor.innerHTML = `
      <div class="col-12">
        <div class="alert alert-info text-center" role="alert">
          No se encontraron productos
        </div>
      </div>
    `;
    // Ocultar paginación si no hay resultados
    document.getElementById("btnAnterior").style.display = "none";
    document.getElementById("btnSiguiente").style.display = "none";
    return;
  }

  // Mostrar paginación si hay resultados
  document.getElementById("btnAnterior").style.display = "block";
  document.getElementById("btnSiguiente").style.display = "block";

  productosAMostrar.forEach(p => {
    contenedor.innerHTML += `
      <div class="col-md-3 mb-4">
        <div class="card h-100 text-dark" style="cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
          <img src="../publics/productos/${p.imagen}" class="card-img-top" style="cursor: pointer;" onclick="abrirModalProducto(${JSON.stringify(p).replace(/"/g, '&quot;')})">
          <div class="card-body">
            <h5 class="card-title" style="cursor: pointer; max-height: 3em; overflow: hidden; text-overflow: ellipsis;" onclick="abrirModalProducto(${JSON.stringify(p).replace(/"/g, '&quot;')})">${p.nombre}</h5>
            <p class="card-text">$${p.precioVenta}</p>
            <button class="btn btn-dark w-100" onclick="event.stopPropagation(); agregarAlCarrito(${JSON.stringify(p).replace(/"/g, '&quot;')})">
              <i class="bi bi-cart-plus"></i> Agregar
            </button>
          </div>
        </div>
      </div>
    `;
  });

  // Actualizar número de página
  document.getElementById("numeroPagina").innerText = paginaActual;

  // Actualizar estado de botones
  const totalPaginas = Math.ceil(productosActuales.length / productosPorPagina);
  document.getElementById("btnAnterior").classList.toggle("disabled", paginaActual === 1);
  document.getElementById("btnSiguiente").classList.toggle("disabled", paginaActual === totalPaginas);
}

function paginaAnterior() {
  if (paginaActual > 1) {
    mostrarPagina(paginaActual - 1);
    window.scrollTo(0, 0);
  }
}

function paginaSiguiente() {
  const totalPaginas = Math.ceil(productosActuales.length / productosPorPagina);
  if (paginaActual < totalPaginas) {
    mostrarPagina(paginaActual + 1);
    window.scrollTo(0, 0);
  }
}

function buscar() {
  const termino = document.getElementById("inputBusqueda").value.toLowerCase().trim();

  if (termino === "") {
    mostrarNotificacion("Por favor ingresa un término de búsqueda", "advertencia");
    return;
  }

  // Limpiar el active de categorías
  document.querySelectorAll("#listaCategorias .list-group-item").forEach(item => {
    item.classList.remove("active");
  });

  // Guardar búsqueda y filtrar productos
  busquedaActual = termino;
  categoriaActual = null;

  productosActuales = todosProductos.filter(p =>
    p.nombre.toLowerCase().includes(termino)
  );

  paginaActual = 1;
  mostrarPagina(1);
}

// ========== FUNCIONES DEL CARRITO ==========

function cargarCarritoLocal() {
  const carritoGuardado = localStorage.getItem("carrito");
  carrito = carritoGuardado ? JSON.parse(carritoGuardado) : [];
  actualizarBadgeCarrito();
}

function guardarCarrito() {
  localStorage.setItem("carrito", JSON.stringify(carrito));
  actualizarBadgeCarrito();
}

function actualizarBadgeCarrito() {
  const cantidadTotal = carrito.reduce((total, producto) => total + producto.cantidad, 0);
  const badge = document.getElementById("cartBadge");
  if (badge) {
    badge.textContent = cantidadTotal;
    badge.style.display = cantidadTotal > 0 ? "inline-block" : "none";
  }
}

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

// ========== FUNCIONES DEL MODAL ==========

function abrirModalProducto(producto) {
  productoSeleccionado = producto;
  document.getElementById("modalProductoTitulo").textContent = producto.nombre;
  document.getElementById("modalProductoImagen").src = "../publics/productos/" + producto.imagen;
  document.getElementById("modalProductoDescripcion").textContent = producto.descripcion || "Sin descripción";
  document.getElementById("modalProductoPrecio").textContent = "$" + producto.precioVenta;
  document.querySelectorAll(".modalProductoCantidad").forEach(el => {
    el.textContent = producto.stock !== undefined && producto.stock !== null ? producto.stock : "N/A";
  });

  const modal = new bootstrap.Modal(document.getElementById("modalProducto"));
  modal.show();
}

function agregarAlCarritoDesdeModal() {
  if (productoSeleccionado) {
    agregarAlCarrito(productoSeleccionado);
  }
}

// === Decectar escaner de código de barras ===
(function(){
  let scanBuffer = "";
  let scanStart = 0;
  let scanTimeout = null;

  function resetScan() {
    scanBuffer = "";
    scanStart = 0;
    if (scanTimeout) clearTimeout(scanTimeout);
    scanTimeout = null;
  }

  function handleScan(code){
    const inputSearch = document.getElementById('inputBusqueda');
    if (inputSearch) {
      inputSearch.value = code;
      if (typeof buscar === 'function') buscar();
    }
  }

  document.addEventListener('keydown', function(e){
    if (e.key === 'Enter') {
      if (scanBuffer.length >= 6 && (Date.now() - scanStart) < 2000) {
        const code = scanBuffer;
        resetScan();
        handleScan(code);
        e.preventDefault();
      } else {
        resetScan();
      }
      return;
    }

    if (e.key.length === 1) {
      if (!scanStart) scanStart = Date.now();
      scanBuffer += e.key;
      if (scanTimeout) clearTimeout(scanTimeout);
      scanTimeout = setTimeout(function(){
        if (scanBuffer.length >= 6 && (Date.now() - scanStart) < 2000) {
          handleScan(scanBuffer);
        }
        resetScan();
      }, 300);
    }
  });

   let logoBtnPressTimer = null;
  const logoBtn = document.querySelector('.navbar-brand');

  if (logoBtn) {
    logoBtn.addEventListener('mousedown', (e) => {
      e.preventDefault();
      logoBtnPressTimer = setTimeout(() => {
        window.location.href = 'login.php';
      }, 5000);
    });

    logoBtn.addEventListener('mouseup', () => {
      if (logoBtnPressTimer) {
        clearTimeout(logoBtnPressTimer);
      }
    });

    logoBtn.addEventListener('mouseleave', () => {
      if (logoBtnPressTimer) {
        clearTimeout(logoBtnPressTimer);
      }
    });
  }
})();