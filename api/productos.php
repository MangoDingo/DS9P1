<?php
    header("Content-Type: application/json");
    include("../modelos/conexion.php");

    $mostrar_todos = isset($_GET['admin']) && $_GET['admin'] == '1';
    $sql = $mostrar_todos ? "SELECT * FROM productos" : "SELECT * FROM productos WHERE stock > 0";
    $resultado = $conexion->query($sql);

    $productos = [];

    if ($resultado) {
        while ($row = $resultado->fetch_assoc()) {
            $productos[] = $row;
        }
    } else {
        error_log("Error en la consulta: " . $conexion->error);
    }
    
    echo json_encode($productos);
    $conexion->close();
?>