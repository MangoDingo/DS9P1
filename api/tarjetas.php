<?php
header("Content-Type: application/json");
include("../modelos/conexion.php");

$sql = "SELECT idTarjeta, tipo, CONCAT(SUBSTR(digitos, 1, 4), ' **** **** ', SUBSTR(digitos, 13, 4)) as numeroMascarado, fechaVence, saldo, saldoMaximo FROM tarjeta ORDER BY idTarjeta DESC";
$resultado = $conexion->query($sql);

$tarjetas = [];

if ($resultado) {
    while ($row = $resultado->fetch_assoc()) {
        $tarjetas[] = $row;
    }
} else {
    error_log("Error en la consulta: " . $conexion->error);
}

echo json_encode($tarjetas);
$conexion->close();
?>
