<?php
    header("Content-Type: application/json");
    include("../modelos/conexion.php");

    $sql = "SELECT * FROM categoria ORDER BY idCategoria";
    $resultado = $conexion->query($sql);

    $categorias = [];

    if ($resultado) {
        while ($row = $resultado->fetch_assoc()) {
            $categorias[] = $row;
        }
    }
    echo json_encode($categorias);
