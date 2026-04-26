<?php
session_start();
header("Content-Type: application/json");

if (isset($_SESSION["usuario"])) {
    echo json_encode([
        "logueado" => true,
        "rol" => $_SESSION["rol"],
        "nombre" => $_SESSION["nombre"],
        "apellido" => $_SESSION["apellido"]
    ]);
} else {
    echo json_encode([
        "logueado" => false,
        "rol" => null
    ]);
}
?>
