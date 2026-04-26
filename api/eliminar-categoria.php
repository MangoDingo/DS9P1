<?php
header("Content-Type: application/json");
include("../modelos/conexion.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "mensaje" => "Solicitud no válida"]);
    exit;
}

$idCategoria = isset($_POST["idCategoria"]) ? intval($_POST["idCategoria"]) : 0;

if ($idCategoria <= 0) {
    echo json_encode(["success" => false, "mensaje" => "ID inválido"]);
    exit;
}

$sql = "DELETE FROM categoria WHERE idCategoria = ?";
$stmt = $conexion->prepare($sql);
if (!$stmt) {
    echo json_encode(["success" => false, "mensaje" => "Hubo un problema al procesar tu solicitud"]);
    exit;
}

$stmt->bind_param("i", $idCategoria);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "mensaje" => "Categoría eliminada correctamente"]);
} else {
    echo json_encode(["success" => false, "mensaje" => "No se pudo eliminar la categoría"]);
}

$stmt->close();
$conexion->close();
?>
