<?php
header("Content-Type: application/json");
include("../modelos/conexion.php");

// Validar que sea una solicitud POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'mensaje' => 'Solicitud no válida']);
    exit;
}

// Validar campos requeridos
if (empty($_POST['idProducto']) || empty($_POST['nombre']) || empty($_POST['precioCosto']) || empty($_POST['precioVenta']) ||
    empty($_POST['stock']) || empty($_POST['unidad']) || empty($_POST['idCategoria']) || empty($_POST['idMarca']) ||
    empty($_POST['descripcion']) || empty($_FILES['imagen'])) {
    echo json_encode(['success' => false, 'mensaje' => 'Por favor completa todos los campos']);
    exit;
}

// Obtener datos
$idProducto = intval($_POST['idProducto']);
$nombre = trim($_POST['nombre']);
$precioCosto = floatval($_POST['precioCosto']);
$precioVenta = floatval($_POST['precioVenta']);
$stock = intval($_POST['stock']);
$unidad = trim($_POST['unidad']);
$idCategoria = intval($_POST['idCategoria']);
$idMarca = intval($_POST['idMarca']);
$descripcion = trim($_POST['descripcion']);
$imagen = $_FILES['imagen'];

// Validar datos
if ($precioCosto <= 0 || $precioVenta <= 0) {
    echo json_encode(['success' => false, 'mensaje' => 'Los precios deben ser mayores a 0']);
    exit;
}

if ($precioVenta < $precioCosto) {
    echo json_encode(['success' => false, 'mensaje' => 'El precio de venta debe ser mayor al precio de costo']);
    exit;
}

if ($stock < 0) {
    echo json_encode(['success' => false, 'mensaje' => 'El stock no puede ser negativo']);
    exit;
}

// Validar imagen
$tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$tipo = finfo_file($finfo, $imagen['tmp_name']);
finfo_close($finfo);

if (!in_array($tipo, $tiposPermitidos)) {
    echo json_encode(['success' => false, 'mensaje' => 'Por favor sube una imagen válida (JPG, PNG o GIF)']);
    exit;
}

if ($imagen['size'] > 5242880) { // 5MB
    echo json_encode(['success' => false, 'mensaje' => 'La imagen no debe superar 5MB']);
    exit;
}

// Crear nombre de archivo único
$extension = pathinfo($imagen['name'], PATHINFO_EXTENSION);
$nombreArchivo = uniqid() . '.' . $extension;
$rutaDestino = '../publics/productos/' . $nombreArchivo;

// Crear directorio si no existe
if (!is_dir('../publics/productos')) {
    mkdir('../publics/productos', 0777, true);
}

// Mover archivo
if (!move_uploaded_file($imagen['tmp_name'], $rutaDestino)) {
    echo json_encode(['success' => false, 'mensaje' => 'Error al guardar la imagen. Por favor intenta nuevamente']);
    exit;
}

// Insertar en base de datos
$sql = "INSERT INTO productos (idProducto, nombre, descripcion, stock, precioCosto, precioVenta, imagen, idCategoria, idMarca, unidad)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'mensaje' => 'Hubo un problema al procesar tu solicitud']);
    exit;
}

$stmt->bind_param("issiddsiiis", $idProducto, $nombre, $descripcion, $stock, $precioCosto, $precioVenta, $nombreArchivo, $idCategoria, $idMarca, $unidad);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'mensaje' => 'Producto guardado correctamente']);
} else {
    // Si falla la inserción, eliminar la imagen
    unlink($rutaDestino);
    echo json_encode(['success' => false, 'mensaje' => 'No se pudo guardar el producto']);
}

$stmt->close();
$conexion->close();
