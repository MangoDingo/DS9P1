<?php
header("Content-Type: application/json");
include("../modelos/conexion.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "mensaje" => "Solicitud no válida"]);
    exit;
}

$idCategoria = isset($_POST["idCategoria"]) ? intval($_POST["idCategoria"]) : 0;
$nombreCat = isset($_POST["nombreCat"]) ? trim($_POST["nombreCat"]) : "";

// Validar
if (empty($nombreCat)) {
    echo json_encode(["success" => false, "mensaje" => "Por favor ingresa un nombre"]);
    exit;
}

if (strlen($nombreCat) > 100) {
    echo json_encode(["success" => false, "mensaje" => "El nombre no puede exceder 100 caracteres"]);
    exit;
}

// Si tiene ID, es edición
if ($idCategoria > 0) {
    $sql = "UPDATE categoria SET nombreCat = ? WHERE idCategoria = ?";
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        echo json_encode(["success" => false, "mensaje" => "Hubo un problema al procesar tu solicitud"]);
        exit;
    }
    $stmt->bind_param("si", $nombreCat, $idCategoria);
} else {
    // Si no tiene ID, es creación
    $sql = "INSERT INTO categoria (nombreCat) VALUES (?)";
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        echo json_encode(["success" => false, "mensaje" => "Hubo un problema al procesar tu solicitud"]);
        exit;
    }
    $stmt->bind_param("s", $nombreCat);
}

if ($stmt->execute()) {
    echo json_encode(["success" => true, "mensaje" => "Categoría guardada correctamente"]);
} else {
    echo json_encode(["success" => false, "mensaje" => "No se pudo guardar la categoría"]);
}

$stmt->close();
$conexion->close();
?>
