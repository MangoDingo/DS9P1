let todosProductos = [];
let productosActuales = [];
let paginaActual = 1;
const productosPorPagina = 6;
let imagenSeleccionada = null;
let idProductoEnEdicion = null;
let idProductoOriginal = null;
let accionConfirmada = null;
let tipoModalFormulario = null; // 'categoria' o 'marca'
let idEditando = null;
let categorias = [];
let marcas = [];

// Función para mostrar notificaciones personalizadas
function mostrarNotificacion(mensaje, tipo = 'error') {
  const notificacion = document.createElement('div');
  notificacion.className = `notificacion ${tipo}`;
  notificacion.textContent = mensaje;
  document.body.appendChild(notificacion);

  // Mostrar notificación
  setTimeout(() => {
    notificacion.style.animation = 'slideOut 0.3s ease-in-out';
    setTimeout(() => {
      notificacion.remove();
    }, 300);
  }, 3000);
}

// Función para mostrar modal de confirmación
function mostrarConfirmacion(titulo, mensaje, callback) {
  document.getElementById("modalTitulo").textContent = titulo;
  document.getElementById("modalMensaje").textContent = mensaje;
  document.getElementById("modalConfirmacion").classList.add("show");
  accionConfirmada = callback;
}

// Función para cerrar modal
function cerrarModal() {
  document.getElementById("modalConfirmacion").classList.remove("show");
  accionConfirmada = null;
}

// Cerrar modal al hacer clic fuera de él
document.addEventListener("click", (e) => {
  const modal = document.getElementById("modalConfirmacion");
  if (e.target === modal) {
    cerrarModal();
  }
});

// Función para confirmar acción
function confirmarAccion() {
  if (accionConfirmada) {
    accionConfirmada();
  }
  cerrarModal();
}

document.addEventListener("DOMContentLoaded", () => {
  verificarSesion();
  cargarProductos();
  cargarCategorias();
  cargarMarcas();

  // Validar longitud máxima de los campos
  const inputId = document.getElementById("idProducto");
  inputId?.addEventListener("keypress", (e) => {
    if (!/[0-9]/.test(e.key) || e.target.value.length >= 20) {
      e.preventDefault();
    }
  });

  // Mantener presionado el botón Tienda 5 segundos para ir a login
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
});

function verificarSesion() {
  fetch("../api/verificar-sesion.php")
    .then(res => res.json())
    .then(data => {
      if (!data.logueado || data.rol !== 1) {
        window.location.href = "index.php";
      }
      document.getElementById("navCarrito").style.display = "block";
    })
    .catch(error => console.error("Error al verificar sesión:", error));
}

function toggleSubmenu(menuId) {
  const submenu = document.getElementById(menuId);
  submenu.classList.toggle('show');
}

function mostrarContenedor(contenedorId, elemento) {
  // Ocultar todos los contenedores
  document.querySelectorAll('.contenedor-activo').forEach(el => {
    el.classList.remove('show');
  });

  // Mostrar el contenedor seleccionado
  document.getElementById(contenedorId).classList.add('show');

  // Actualizar estilos del sidebar
  document.querySelectorAll('.submenu-item').forEach(item => {
    item.classList.remove('active');
  });
  elemento.classList.add('active');

  // Si es categorías, cargar las tablas
  if (contenedorId === 'categorias') {
    cargarCategoriasTabla();
    cargarMarcasTabla();
  }

  // Si es añadir productos, enfocar campo ID para escáner USB
  if (contenedorId === 'anadirProductos') {
    setTimeout(() => {
      const idInput = document.getElementById('idProducto');
      if (idInput) idInput.focus();
    }, 100);
  }
}

function cargarProductos() {
  fetch("../api/productos.php?admin=1")
    .then(res => res.json())
    .then(data => {
      todosProductos = data;
      productosActuales = data;
      mostrarPagina(1);
    })
    .catch(error => console.error("Error:", error));
}

function cargarCategorias() {
  fetch("../api/categorias.php")
    .then(res => res.json())
    .then(data => {
      let selectCategoria = document.getElementById("selectCategoria");
      if (selectCategoria) {
        selectCategoria.innerHTML = '<option value="">Seleccionar categoría</option>';
        data.forEach(cat => {
          selectCategoria.innerHTML += `<option value="${cat.idCategoria}">${cat.nombreCat}</option>`;
        });
      }
    })
    .catch(error => console.error("Error al cargar categorías:", error));
}

function cargarMarcas() {
  fetch("../api/marcas.php")
    .then(res => res.json())
    .then(data => {
      let selectMarca = document.getElementById("selectMarca");
      if (selectMarca) {
        selectMarca.innerHTML = '<option value="">Seleccionar marca</option>';
        data.forEach(marca => {
          selectMarca.innerHTML += `<option value="${marca.idMarca}">${marca.nombreMarc}</option>`;
        });
      }
    })
    .catch(error => console.error("Error al cargar marcas:", error));
}

function mostrarPagina(numeroPagina) {
  paginaActual = numeroPagina;
  const inicio = (numeroPagina - 1) * productosPorPagina;
  const fin = inicio + productosPorPagina;
  const productosAMostrar = productosActuales.slice(inicio, fin);

  console.log("productosAMostrar:", productosAMostrar);

  let tbody = document.getElementById("tablaProductos");
  tbody.innerHTML = "";

  productosAMostrar.forEach(p => {
    tbody.innerHTML += `
      <tr>
        <td><img src="../publics/productos/${p.imagen}" alt="${p.nombre}"></td>
        <td>${p.nombre}</td>
        <td>$${p.precioVenta}</td>
        <td>${p.stock}</td>
        <td>
          <a class="btn-editar" onclick="editarProducto(${p.idProducto}); return false;" href="#">Editar</a>
          <a class="btn-eliminar" onclick="eliminarProducto(${p.idProducto}); return false;" href="#">Eliminar</a>
        </td>
      </tr>
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
  }
}

function paginaSiguiente() {
  const totalPaginas = Math.ceil(productosActuales.length / productosPorPagina);
  if (paginaActual < totalPaginas) {
    mostrarPagina(paginaActual + 1);
  }
}

function editarProducto(idProducto) {
  console.log("editarProducto called with ID:", idProducto);

  // Buscar el producto en el array
  const producto = todosProductos.find(p => p.idProducto == idProducto);

  if (!producto) {
    mostrarNotificacion("Producto no encontrado", "error");
    return;
  }

  // Cargar los datos en el formulario
  document.getElementById("idProducto").value = producto.idProducto;
  document.getElementById("nombre").value = producto.nombre;
  document.getElementById("precioCosto").value = producto.precioCosto;
  document.getElementById("precioVenta").value = producto.precioVenta;
  document.getElementById("stock").value = producto.stock;
  document.getElementById("unidad").value = producto.unidad || "";
  document.getElementById("selectCategoria").value = producto.idCategoria;
  document.getElementById("selectMarca").value = producto.idMarca;
  document.getElementById("descripcion").value = producto.descripcion;

  // Cargar la imagen actual como preview
  document.getElementById("previewImagen").src = "../publics/productos/" + producto.imagen;
  document.getElementById("previewImagen").style.display = 'block';

  // Guardar el ID original
  idProductoOriginal = String(producto.idProducto);

  // Deshabilitar campo ID en modo edición
  document.getElementById("idProducto").disabled = true;

  // Setear imagenSeleccionada a un valor para que no falle la validación
  // (se considera que la imagen actual es válida)
  imagenSeleccionada = "existente";

  // Mostrar el contenedor de añadir productos (que ahora es editar)
  document.querySelectorAll('.contenedor-activo').forEach(el => {
    el.classList.remove('show');
  });
  document.getElementById("anadirProductos").classList.add('show');

  // Actualizar el título del formulario
  document.querySelector(".form-header h3").textContent = "Editar Producto";

  // Scroll al formulario
  document.querySelector(".form-producto").scrollIntoView({ behavior: 'smooth' });
}

function eliminarProducto(idProducto) {
  mostrarConfirmacion(
    "Eliminar producto",
    "¿Estás seguro de que deseas eliminar este producto?",
    () => {
      const formData = new FormData();
      formData.append("idProducto", idProducto);

      fetch("../api/eliminar-producto.php", {
        method: "POST",
        body: formData
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            mostrarNotificacion(data.mensaje, "success");
            // Recargar lista de productos
            cargarProductos();
          } else {
            mostrarNotificacion(data.mensaje, "error");
          }
        })
        .catch(error => {
          console.error("Error:", error);
          mostrarNotificacion("Hubo un problema al eliminar el producto. Por favor intenta nuevamente.", "error");
        });
    }
  );
}

// Funciones para el módulo de Añadir Productos
function subirImagen() {
  const input = document.createElement('input');
  input.type = 'file';
  input.accept = 'image/*';
  input.addEventListener('change', (e) => {
    const file = e.target.files[0];
    if (file) {
      imagenSeleccionada = file;
      const reader = new FileReader();
      reader.onload = (event) => {
        document.getElementById("previewImagen").src = event.target.result;
        document.getElementById("previewImagen").style.display = 'block';
      };
      reader.readAsDataURL(file);
    }
  });
  input.click();
}

function guardarProducto() {
  // Validar campos
  const idProducto = String(document.getElementById("idProducto").value).trim();
  const nombre = String(document.getElementById("nombre").value).trim();
  const precioCosto = String(document.getElementById("precioCosto").value).trim();
  const precioVenta = String(document.getElementById("precioVenta").value).trim();
  const stock = String(document.getElementById("stock").value).trim();
  const unidad = String(document.getElementById("unidad").value).trim();
  const categoria = document.getElementById("selectCategoria").value;
  const marca = document.getElementById("selectMarca").value;
  const descripcion = String(document.getElementById("descripcion").value).trim();

  console.log({idProducto, nombre, precioCosto, precioVenta, stock, unidad, categoria, marca, descripcion});

  // Validar ID
  if (!idProducto || isNaN(parseInt(idProducto)) || parseInt(idProducto) <= 0) {
    mostrarNotificacion("Por favor ingresa un ID de producto válido", "error");
    return;
  }

  // Verificar si el producto existe
  const productoExistente = todosProductos.find(p => p.idProducto == idProducto);

  // Si estamos editando y el usuario intenta cambiar el ID, no permitir
  if (idProductoOriginal && String(idProducto) !== idProductoOriginal) {
    mostrarNotificacion("No puedes cambiar el ID de un producto existente", "error");
    return;
  }

  // Validaciones
  if (!nombre || nombre === "") {
    mostrarNotificacion("Por favor ingresa el nombre del producto", "error");
    return;
  }

  if (nombre.length > 50) {
    mostrarNotificacion("El nombre no puede exceder 50 caracteres", "error");
    return;
  }

  if (!precioCosto || isNaN(parseFloat(precioCosto)) || parseFloat(precioCosto) <= 0) {
    mostrarNotificacion("Por favor ingresa un precio de costo válido", "error");
    return;
  }

  if (!precioVenta || isNaN(parseFloat(precioVenta)) || parseFloat(precioVenta) <= 0) {
    mostrarNotificacion("Por favor ingresa un precio de venta válido", "error");
    return;
  }

  if (parseFloat(precioVenta) < parseFloat(precioCosto)) {
    mostrarNotificacion("El precio de venta no puede ser menor que el precio de costo", "error");
    return;
  }

  if (!stock || isNaN(parseInt(stock)) || parseInt(stock) < 0) {
    mostrarNotificacion("Por favor ingresa una cantidad de stock válida", "error");
    return;
  }

  if (!unidad || unidad === "") {
    mostrarNotificacion("Por favor ingresa la unidad del producto", "error");
    return;
  }

  if (unidad.length > 20) {
    mostrarNotificacion("La unidad no puede exceder 20 caracteres", "error");
    return;
  }

  if (!categoria || categoria === "") {
    mostrarNotificacion("Por favor selecciona una categoría", "error");
    return;
  }

  if (!marca || marca === "") {
    mostrarNotificacion("Por favor selecciona una marca", "error");
    return;
  }

  if (!descripcion || descripcion === "") {
    mostrarNotificacion("Por favor ingresa la descripción", "error");
    return;
  }

  if (descripcion.length > 500) {
    mostrarNotificacion("La descripción no puede exceder 500 caracteres", "error");
    return;
  }

  // Si es un nuevo producto, requiere imagen
  if (!idProductoOriginal && !imagenSeleccionada) {
    mostrarNotificacion("Por favor selecciona una imagen", "error");
    return;
  }

  // Preparar FormData para enviar
  const formData = new FormData();
  formData.append("idProducto", idProducto);
  formData.append("nombre", nombre);
  formData.append("precioCosto", precioCosto);
  formData.append("precioVenta", precioVenta);
  formData.append("stock", stock);
  formData.append("unidad", unidad);
  formData.append("idCategoria", categoria);
  formData.append("idMarca", marca);
  formData.append("descripcion", descripcion);

  // Si es una imagen nueva (File object), agregarla
  if (imagenSeleccionada && imagenSeleccionada !== "existente" && imagenSeleccionada instanceof File) {
    formData.append("imagen", imagenSeleccionada);
  }

  // Determinar si es edición o creación
  const endpoint = idProductoOriginal
    ? "../api/actualizar-producto.php"
    : "../api/guardar-producto.php";

  console.log("idProductoOriginal:", idProductoOriginal);
  console.log("Endpoint:", endpoint);
  console.log("FormData entries:", Array.from(formData.entries()));

  // Enviar al servidor
  fetch(endpoint, {
    method: "POST",
    body: formData
  })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        const mensaje = idProductoOriginal
          ? "¡Producto actualizado correctamente!"
          : "¡Producto guardado correctamente!";
        mostrarNotificacion(mensaje, "success");
        // Limpiar formulario
        limpiarFormularioProducto();
        // Recargar lista de productos
        cargarProductos();
      } else {
        mostrarNotificacion(data.mensaje, "error");
      }
    })
    .catch(error => {
      console.error("Error:", error);
      mostrarNotificacion("Hubo un problema al guardar el producto. Por favor intenta nuevamente.", "error");
    });
}

function limpiarFormularioProducto() {
  document.getElementById("idProducto").value = "";
  document.getElementById("idProducto").disabled = false;
  document.getElementById("nombre").value = "";
  document.getElementById("precioCosto").value = "";
  document.getElementById("precioVenta").value = "";
  document.getElementById("stock").value = "";
  document.getElementById("unidad").value = "";
  document.getElementById("selectCategoria").value = "";
  document.getElementById("selectMarca").value = "";
  document.getElementById("descripcion").value = "";
  document.getElementById("previewImagen").src = "";
  document.getElementById("previewImagen").style.display = "none";
  imagenSeleccionada = null;
  idProductoEnEdicion = null;
  idProductoOriginal = null;

  // Restaurar título a "Añadir Producto"
  document.querySelector(".form-header h3").textContent = "Añadir Producto";
}

// FUNCIONES PARA EL MÓDULO DE CATEGORÍAS Y MARCAS

function cargarCategoriasTabla() {
  fetch("../api/categorias.php")
    .then(res => res.json())
    .then(data => {
      categorias = data;
      let html = "";
      data.forEach(cat => {
        html += `
          <tr>
            <td>${cat.idCategoria}</td>
            <td>${cat.nombreCat}</td>
            <td>
              <div class="tabla-acciones">
                <button class="btn-table-editar" onclick="abrirModalCategoria(${cat.idCategoria}, '${cat.nombreCat}')">Editar</button>
                <button class="btn-table-eliminar" onclick="eliminarCategoriaModal(${cat.idCategoria})">Eliminar</button>
              </div>
            </td>
          </tr>
        `;
      });
      document.getElementById("tablaCategorias").innerHTML = html || "<tr><td colspan='3' style='text-align: center; color: #999;'>No hay categorías</td></tr>";
    })
    .catch(error => {
      console.error("Error:", error);
      mostrarNotificacion("Error al cargar categorías", "error");
    });
}

function cargarMarcasTabla() {
  fetch("../api/marcas.php")
    .then(res => res.json())
    .then(data => {
      marcas = data;
      let html = "";
      data.forEach(marca => {
        html += `
          <tr>
            <td>${marca.idMarca}</td>
            <td>${marca.nombreMarc}</td>
            <td>
              <div class="tabla-acciones">
                <button class="btn-table-editar" onclick="abrirModalMarca(${marca.idMarca}, '${marca.nombreMarc}')">Editar</button>
                <button class="btn-table-eliminar" onclick="eliminarMarcaModal(${marca.idMarca})">Eliminar</button>
              </div>
            </td>
          </tr>
        `;
      });
      document.getElementById("tablaMarcas").innerHTML = html || "<tr><td colspan='3' style='text-align: center; color: #999;'>No hay marcas</td></tr>";
    })
    .catch(error => {
      console.error("Error:", error);
      mostrarNotificacion("Error al cargar marcas", "error");
    });
}

// MODAL DE FORMULARIO

function abrirModalCategoria(idCategoria = null, nombre = "") {
  tipoModalFormulario = 'categoria';
  idEditando = idCategoria;
  document.getElementById("modalFormTitulo").textContent = idCategoria ? "Editar Categoría" : "Agregar Categoría";
  document.getElementById("modalFormNombre").value = nombre;
  document.getElementById("modalFormulario").classList.add("show");
}

function abrirModalMarca(idMarca = null, nombre = "") {
  tipoModalFormulario = 'marca';
  idEditando = idMarca;
  document.getElementById("modalFormTitulo").textContent = idMarca ? "Editar Marca" : "Agregar Marca";
  document.getElementById("modalFormNombre").value = nombre;
  document.getElementById("modalFormulario").classList.add("show");
}

function cerrarModalFormulario() {
  document.getElementById("modalFormulario").classList.remove("show");
  tipoModalFormulario = null;
  idEditando = null;
  document.getElementById("modalFormNombre").value = "";
}

function guardarDesdeModal() {
  const nombre = document.getElementById("modalFormNombre").value.trim();

  if (!nombre) {
    mostrarNotificacion("Por favor ingresa un nombre", "error");
    return;
  }

  if (nombre.length > 100) {
    mostrarNotificacion("El nombre no puede exceder 100 caracteres", "error");
    return;
  }

  const formData = new FormData();

  if (tipoModalFormulario === 'categoria') {
    formData.append("nombreCat", nombre);
    if (idEditando) {
      formData.append("idCategoria", idEditando);
    }
    const endpoint = "../api/guardar-categoria.php";

    fetch(endpoint, {
      method: "POST",
      body: formData
    })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          mostrarNotificacion(data.mensaje, "success");
          cerrarModalFormulario();
          cargarCategoriasTabla();
        } else {
          mostrarNotificacion(data.mensaje, "error");
        }
      })
      .catch(error => {
        console.error("Error:", error);
        mostrarNotificacion("Hubo un error al guardar", "error");
      });
  } else if (tipoModalFormulario === 'marca') {
    formData.append("nombreMarc", nombre);
    if (idEditando) {
      formData.append("idMarca", idEditando);
    }
    const endpoint = "../api/guardar-marca.php";

    fetch(endpoint, {
      method: "POST",
      body: formData
    })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          mostrarNotificacion(data.mensaje, "success");
          cerrarModalFormulario();
          cargarMarcasTabla();
        } else {
          mostrarNotificacion(data.mensaje, "error");
        }
      })
      .catch(error => {
        console.error("Error:", error);
        mostrarNotificacion("Hubo un error al guardar", "error");
      });
  }
}

// ELIMINACIÓN

function eliminarCategoriaModal(idCategoria) {
  mostrarConfirmacion(
    "Eliminar Categoría",
    "¿Estás seguro de que deseas eliminar esta categoría?",
    () => {
      const formData = new FormData();
      formData.append("idCategoria", idCategoria);

      fetch("../api/eliminar-categoria.php", {
        method: "POST",
        body: formData
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            mostrarNotificacion(data.mensaje, "success");
            cargarCategoriasTabla();
          } else {
            mostrarNotificacion(data.mensaje, "error");
          }
        })
        .catch(error => {
          console.error("Error:", error);
          mostrarNotificacion("Hubo un error al eliminar", "error");
        });
    }
  );
}

function eliminarMarcaModal(idMarca) {
  mostrarConfirmacion(
    "Eliminar Marca",
    "¿Estás seguro de que deseas eliminar esta marca?",
    () => {
      const formData = new FormData();
      formData.append("idMarca", idMarca);

      fetch("../api/eliminar-marca.php", {
        method: "POST",
        body: formData
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            mostrarNotificacion(data.mensaje, "success");
            cargarMarcasTabla();
          } else {
            mostrarNotificacion(data.mensaje, "error");
          }
        })
        .catch(error => {
          console.error("Error:", error);
          mostrarNotificacion("Hubo un error al eliminar", "error");
        });
    }
  );
}

// Cerrar modal al hacer clic fuera
document.addEventListener("click", (e) => {
  const modalFormulario = document.getElementById("modalFormulario");
  if (e.target === modalFormulario) {
    cerrarModalFormulario();
  }
});

// === Detectar escaner de código de barras ===
(function(){
  let scanBuffer = "";
  let scanStart = 0;
  let scanTimeout = null;
  let idProductoEnfocado = false;

  const idInput = document.getElementById('idProducto');

  // Rastrear si idProducto está enfocado
  if (idInput) {
    idInput.addEventListener('focus', () => {
      idProductoEnfocado = true;
    });

    idInput.addEventListener('blur', () => {
      idProductoEnfocado = false;
      resetScan();
    });
  }

  function resetScan() {
    scanBuffer = "";
    scanStart = 0;
    if (scanTimeout) clearTimeout(scanTimeout);
    scanTimeout = null;
  }

  function handleAdminScan(code){
    if (idInput) {
      idInput.value = code;
    }
  }

  document.addEventListener('keydown', function(e){
    // Solo procesar si idProducto está enfocado
    if (!idProductoEnfocado) return;

    if (e.key === 'Enter') {
      if (scanBuffer.length >= 6 && (Date.now() - scanStart) < 2000) {
        const code = scanBuffer;
        resetScan();
        handleAdminScan(code);
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
          handleAdminScan(scanBuffer);
        }
        resetScan();
      }, 300);
    }
  });
})();
