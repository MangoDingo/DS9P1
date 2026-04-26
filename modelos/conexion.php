<?php
    $host = "localhost";
    $user = "d42024";
    $pass = "1234";
    $db = "ds9p1";
    $conexion = mysqli_connect($host, $user, $pass, $db);

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    if (!$conexion) {
        header("Location: gestionar_productos.php?msg=" . urlencode("Error en la conexión a la base de datos."));
        exit();
    }
