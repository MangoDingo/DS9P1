<?php
header("Content-Type: application/json");
include("../modelos/conexion.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "mensaje" => "Solicitud no válida"]);
    exit;
}

$idMarca = isset($_POST["idMarca"]) ? intval($_POST["idMarca"]) : 0;

if ($idMarca <= 0) {
    echo json_encode(["success" => false, "mensaje" => "ID inválido"]);
    exit;
}

$sql = "DELETE FROM marca WHERE idMarca = ?";
$stmt = $conexion->prepare($sql);
if (!$stmt) {
    echo json_encode(["success" => false, "mensaje" => "Hubo un problema al procesar tu solicitud"]);
    exit;
}

$stmt->bind_param("i", $idMarca);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "mensaje" => "Marca eliminada correctamente"]);
} else {
    echo json_encode(["success" => false, "mensaje" => "No se pudo eliminar la marca"]);
}

$stmt->close();
$conexion->close();
?>
