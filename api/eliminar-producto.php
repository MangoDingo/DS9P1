<?php
header("Content-Type: application/json");
include("../modelos/conexion.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "mensaje" => "Solicitud no válida"]);
    exit;
}

$idProducto = isset($_POST["idProducto"]) ? intval($_POST["idProducto"]) : 0;

// Validar ID
if ($idProducto <= 0) {
    echo json_encode(["success" => false, "mensaje" => "ID de producto inválido"]);
    exit;
}

// Obtener información del producto (necesitamos la imagen para eliminarla)
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

$producto = $resultSelect->fetch_assoc();
$imagen = $producto["imagen"];
$stmtSelect->close();

// Eliminar la imagen del servidor
$rutaImagen = "../publics/productos/" . $imagen;
if (file_exists($rutaImagen)) {
    unlink($rutaImagen);
}

// Eliminar el producto de la base de datos
$sqlDelete = "DELETE FROM productos WHERE idProducto = ?";
$stmtDelete = $conexion->prepare($sqlDelete);
if (!$stmtDelete) {
    echo json_encode(["success" => false, "mensaje" => "Hubo un problema al procesar tu solicitud"]);
    exit;
}

$stmtDelete->bind_param("i", $idProducto);

if ($stmtDelete->execute()) {
    echo json_encode(["success" => true, "mensaje" => "Producto eliminado correctamente"]);
} else {
    echo json_encode(["success" => false, "mensaje" => "No se pudo eliminar el producto"]);
}

$stmtDelete->close();
$conexion->close();
?>
