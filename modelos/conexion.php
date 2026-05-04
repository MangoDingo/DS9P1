<?php
    $host = "localhost";
    $user = "root";
    $pass = "";
    $db = "ds9p1";
    $conexion = mysqli_connect($host, $user, $pass, $db);

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    if (!$conexion) {
        header("Location: index.php" . urlencode("Error en la conexión a la base de datos."));
        exit();
    }
