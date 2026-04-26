<?php
session_start();
header("Content-Type: application/json");
include("../modelos/conexion.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = $_POST["usuario"] ?? "";
    $contrasena = $_POST["contrasena"] ?? "";

    if (empty($usuario) || empty($contrasena)) {
        echo json_encode(["success" => false, "mensaje" => "Usuario y contraseña son requeridos"]);
        exit();
    }

    $sql = "SELECT usuario, nombre, apellido, rol FROM empleado WHERE usuario = ? AND contrasena = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ss", $usuario, $contrasena);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $empleado = $resultado->fetch_assoc();
        $_SESSION["usuario"] = $empleado["usuario"];
        $_SESSION["nombre"] = $empleado["nombre"];
        $_SESSION["apellido"] = $empleado["apellido"];
        $_SESSION["rol"] = $empleado["rol"];

        echo json_encode(["success" => true, "mensaje" => "Login exitoso", "rol" => $empleado["rol"]]);
    } else {
        echo json_encode(["success" => false, "mensaje" => "Usuario o contraseña incorrectos"]);
    }

    $stmt->close();
}
?>
