<?php
header("Content-Type: application/json");
include("../modelos/conexion.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "mensaje" => "Solicitud no válida"]);
    exit;
}

$idMarca = isset($_POST["idMarca"]) ? intval($_POST["idMarca"]) : 0;
$nombreMarc = isset($_POST["nombreMarc"]) ? trim($_POST["nombreMarc"]) : "";

// Validar
if (empty($nombreMarc)) {
    echo json_encode(["success" => false, "mensaje" => "Por favor ingresa un nombre"]);
    exit;
}

if (strlen($nombreMarc) > 100) {
    echo json_encode(["success" => false, "mensaje" => "El nombre no puede exceder 100 caracteres"]);
    exit;
}

// Si tiene ID, es edición
if ($idMarca > 0) {
    $sql = "UPDATE marca SET nombreMarc = ? WHERE idMarca = ?";
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        echo json_encode(["success" => false, "mensaje" => "Hubo un problema al procesar tu solicitud"]);
        exit;
    }
    $stmt->bind_param("si", $nombreMarc, $idMarca);
} else {
    // Si no tiene ID, es creación
    $sql = "INSERT INTO marca (nombreMarc) VALUES (?)";
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        echo json_encode(["success" => false, "mensaje" => "Hubo un problema al procesar tu solicitud"]);
        exit;
    }
    $stmt->bind_param("s", $nombreMarc);
}

if ($stmt->execute()) {
    echo json_encode(["success" => true, "mensaje" => "Marca guardada correctamente"]);
} else {
    echo json_encode(["success" => false, "mensaje" => "No se pudo guardar la marca"]);
}

$stmt->close();
$conexion->close();
?>
