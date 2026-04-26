<?php
header("Content-Type: application/json");
include("../modelos/conexion.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "mensaje" => "Solicitud no válida"]);
    exit;
}

$idProducto = isset($_POST["idProducto"]) ? intval($_POST["idProducto"]) : 0;
$nombre = isset($_POST["nombre"]) ? trim($_POST["nombre"]) : "";
$precioCosto = isset($_POST["precioCosto"]) ? floatval($_POST["precioCosto"]) : 0;
$precioVenta = isset($_POST["precioVenta"]) ? floatval($_POST["precioVenta"]) : 0;
$stock = isset($_POST["stock"]) ? intval($_POST["stock"]) : 0;
$unidad = isset($_POST["unidad"]) ? trim($_POST["unidad"]) : "";
$idCategoria = isset($_POST["idCategoria"]) ? intval($_POST["idCategoria"]) : 0;
$idMarca = isset($_POST["idMarca"]) ? intval($_POST["idMarca"]) : 0;
$descripcion = isset($_POST["descripcion"]) ? trim($_POST["descripcion"]) : "";

// Validaciones
if ($idProducto <= 0) {
    echo json_encode(["success" => false, "mensaje" => "ID de producto inválido"]);
    exit;
}

if (empty($nombre)) {
    echo json_encode(["success" => false, "mensaje" => "El nombre es requerido"]);
    exit;
}

if ($precioCosto <= 0 || $precioVenta <= 0) {
    echo json_encode(["success" => false, "mensaje" => "Los precios deben ser mayores a 0"]);
    exit;
}

if ($precioVenta < $precioCosto) {
    echo json_encode(["success" => false, "mensaje" => "El precio de venta debe ser mayor al precio de costo"]);
    exit;
}

if ($stock < 0) {
    echo json_encode(["success" => false, "mensaje" => "El stock no puede ser negativo"]);
    exit;
}

if (empty($unidad)) {
    echo json_encode(["success" => false, "mensaje" => "La unidad es requerida"]);
    exit;
}

if ($idCategoria <= 0 || $idMarca <= 0) {
    echo json_encode(["success" => false, "mensaje" => "Categoría y marca son requeridas"]);
    exit;
}

if (empty($descripcion)) {
    echo json_encode(["success" => false, "mensaje" => "La descripción es requerida"]);
    exit;
}

// Obtener producto actual para saber el nombre de la imagen
$sqlSelect = "SELECT imagen FROM productos WHERE idProducto = ?";
$stmtSelect = $conexion->prepare($sqlSelect);
if (!$stmtSelect) {
    echo json_encode(["success" => false, "mensaje" => "Hubo un problema al procesar tu solicitud"]);
    exit;
}

$stmtSelect->bind_param("i", $idProducto);
$stmtSelect->execute();
$resultSelect = $stmtSelect->get_result();

if ($resultSelect->num_rows === 0) {
    echo json_encode(["success" => false, "mensaje" => "El producto no existe"]);
    exit;
}

$productoActual = $resultSelect->fetch_assoc();
$imagenActual = $productoActual["imagen"];
$stmtSelect->close();

// Procesar imagen si se envía una nueva
$nombreImagen = $imagenActual;
if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] === UPLOAD_ERR_OK) {
    $directorio = "../publics/productos/";

    // Crear directorio si no existe
    if (!is_dir($directorio)) {
        mkdir($directorio, 0777, true);
    }

    // Obtener información del archivo
    $nombreArchivo = basename($_FILES["imagen"]["name"]);
    $rutaTemporal = $_FILES["imagen"]["tmp_name"];

    // Generar nombre único para la imagen
    $nombreImagen = time() . "_" . $nombreArchivo;
    $rutaDestino = $directorio . $nombreImagen;

    // Validar tipo de archivo
    $tiposPermitidos = ["image/jpeg", "image/png", "image/gif", "image/webp"];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $tipo = finfo_file($finfo, $rutaTemporal);
    finfo_close($finfo);

    if (!in_array($tipo, $tiposPermitidos)) {
        echo json_encode(["success" => false, "mensaje" => "Por favor sube una imagen válida (JPG, PNG, GIF o WebP)"]);
        exit;
    }

    // Mover archivo
    if (!move_uploaded_file($rutaTemporal, $rutaDestino)) {
        echo json_encode(["success" => false, "mensaje" => "Error al guardar la imagen. Por favor intenta nuevamente"]);
        exit;
    }

    // Eliminar imagen anterior si es diferente
    if ($imagenActual && file_exists($directorio . $imagenActual) && $imagenActual !== $nombreImagen) {
        unlink($directorio . $imagenActual);
    }
}

// Actualizar producto en la base de datos
$sqlUpdate = "UPDATE productos
              SET nombre = ?, precioCosto = ?, precioVenta = ?, stock = ?,
                  idCategoria = ?, idMarca = ?, descripcion = ?, imagen = ?, unidad = ?
              WHERE idProducto = ?";

$stmtUpdate = $conexion->prepare($sqlUpdate);
if (!$stmtUpdate) {
    echo json_encode(["success" => false, "mensaje" => "Hubo un problema al procesar tu solicitud"]);
    exit;
}

$stmtUpdate->bind_param("sddiiisssi", $nombre, $precioCosto, $precioVenta, $stock,
                        $idCategoria, $idMarca, $descripcion, $nombreImagen, $unidad, $idProducto);

if ($stmtUpdate->execute()) {
    echo json_encode(["success" => true, "mensaje" => "Producto actualizado correctamente"]);
} else {
    echo json_encode(["success" => false, "mensaje" => "No se pudo actualizar el producto"]);
}

$stmtUpdate->close();
$conexion->close();
?>
