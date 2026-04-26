<?php
    header("Content-Type: application/json");
    include("../modelos/conexion.php");

    $sql = "SELECT idMarca, nombreMarc FROM marca";
    $resultado = $conexion->query($sql);

    $marcas = [];

    while ($row = $resultado->fetch_assoc()) {
        $marcas[] = $row;
    }
    echo json_encode($marcas);
