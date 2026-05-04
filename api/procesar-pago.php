<?php
header("Content-Type: application/json");
include("../modelos/conexion.php");

$response = [
    "success" => false,
    "message" => "",
    "idFactura" => null
];

// Recibir datos JSON
$data = json_decode(file_get_contents("php://input"), true);

// Validaciones básicas
if (!isset($data['numeroTarjeta']) || !isset($data['cvv']) || !isset($data['vencimiento']) || !isset($data['carrito'])) {
    $response["message"] = "Por favor completa todos los campos de la tarjeta";
    echo json_encode($response);
    exit();
}

$numeroTarjeta = preg_replace('/\D/', '', $data['numeroTarjeta']); // Solo dígitos
$cvv = preg_replace('/\D/', '', $data['cvv']);
$vencimiento = $data['vencimiento'];
$carrito = $data['carrito'];

// Validar que el número de tarjeta tenga exactamente 16 dígitos
if (strlen($numeroTarjeta) !== 16) {
    $response["message"] = "El número de tarjeta debe tener exactamente 16 dígitos";
    echo json_encode($response);
    exit();
}

// Validar que el carrito no esté vacío
if (empty($carrito)) {
    $response["message"] = "El carrito está vacío";
    echo json_encode($response);
    exit();
}

// Calcular totales
$subtotal = 0;
foreach ($carrito as $producto) {
    $subtotal += $producto['precioVenta'] * $producto['cantidad'];
}

$itbms = $subtotal * 0.07; // 7% de ITBMS
$total = $subtotal + $itbms;

try {
    // Buscar la tarjeta en la base de datos por número exacto y CVV
    $sqlTarjeta = "SELECT * FROM tarjeta WHERE digitos = ? AND codSeguridad = ?";
    $stmt = $conexion->prepare($sqlTarjeta);

    if (!$stmt) {
        $response["message"] = "No pudimos procesar tu pago en este momento. Intenta más tarde";
        echo json_encode($response);
        exit();
    }

    $stmt->bind_param("ss", $numeroTarjeta, $cvv);
    $stmt->execute();
    $resultadoTarjeta = $stmt->get_result();

    if ($resultadoTarjeta->num_rows === 0) {
        $response["message"] = "Los datos de la tarjeta no coinciden. Verifica el número, vencimiento y CVV";
        echo json_encode($response);
        exit();
    }

    $tarjeta = $resultadoTarjeta->fetch_assoc();
    $idTarjeta = $tarjeta['idTarjeta'];
    $saldoActual = $tarjeta['saldo'];

    // Validar que la fecha de vencimiento coincida (solo mes/año)
    $fechaBD = $tarjeta['fechaVence']; // Formato: yyyy-mm-dd o mm/yy

    // Si la fecha está en formato yyyy-mm-dd, convertir a mm/yy
    if (strpos($fechaBD, '-') !== false) {
        $fechaBD = date('m/y', strtotime($fechaBD));
    }

    if ($fechaBD !== $vencimiento) {
        $response["message"] = "Los datos de la tarjeta no coinciden. Verifica el número, vencimiento y CVV";
        echo json_encode($response);
        exit();
    }

    // Validar que el saldo sea suficiente
    if ($saldoActual < $total) {
        $response["message"] = "Saldo insuficiente en tu tarjeta. Disponible: $" . number_format($saldoActual, 2);
        echo json_encode($response);
        exit();
    }

    // Iniciar transacción y verificar stock con bloqueo (FOR UPDATE)
    $conexion->begin_transaction();

    // Verificar stock para cada producto (bloqueando filas)
    $sqlStock = "SELECT stock FROM productos WHERE idProducto = ? FOR UPDATE";
    $stmtStock = $conexion->prepare($sqlStock);
    if (!$stmtStock) {
        throw new Exception("No pudimos completar tu compra. Intenta nuevamente");
    }

    foreach ($carrito as $item) {
        $idProductoCheck = isset($item['idProducto']) ? (int)$item['idProducto'] : (int)$item['id'];
        $cantidadCheck = isset($item['cantidad']) ? (int)$item['cantidad'] : 1;

        $stmtStock->bind_param("i", $idProductoCheck);
        $stmtStock->execute();
        $resultStock = $stmtStock->get_result();

        if ($resultStock->num_rows === 0) {
            throw new Exception("Producto con ID {$idProductoCheck} no encontrado");
        }

        $rowStock = $resultStock->fetch_assoc();
        $stockActual = isset($rowStock['stock']) ? (int)$rowStock['stock'] : 0;

        if ($stockActual < $cantidadCheck) {
            throw new Exception("Stock insuficiente para el producto ID {$idProductoCheck}");
        }
    }
    $stmtStock->close();

    // Crear factura
    $sqlFactura = "INSERT INTO factura (idTarjeta, subtotal, itbms, total) VALUES (?, ?, ?, ?)";
    $stmtFactura = $conexion->prepare($sqlFactura);

    if (!$stmtFactura) {
        throw new Exception("No pudimos completar tu compra. Intenta nuevamente");
    }

    $stmtFactura->bind_param("iddd", $idTarjeta, $subtotal, $itbms, $total);
    $stmtFactura->execute();

    $idFactura = $conexion->insert_id;

    // Insertar detalles de la factura (factura_detalle)
    $sqlDetalle = "INSERT INTO factura_detalle (idFactura, idProducto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)";
    $stmtDetalle = $conexion->prepare($sqlDetalle);

    if (!$stmtDetalle) {
        throw new Exception("No pudimos completar tu compra. Intenta nuevamente");
    }

    foreach ($carrito as $item) {
        $idProducto = isset($item['idProducto']) ? (int)$item['idProducto'] : (int)$item['id'];
        $cantidad = isset($item['cantidad']) ? (int)$item['cantidad'] : 1;
        $precioUnitario = isset($item['precioVenta']) ? (float)$item['precioVenta'] : (float)$item['precio'];

        $stmtDetalle->bind_param("iiid", $idFactura, $idProducto, $cantidad, $precioUnitario);
        if (!$stmtDetalle->execute()) {
            throw new Exception("No pudimos guardar los detalles de la factura");
        }
    }

    // Disminuir stock de los productos
    $sqlActualizarStock = "UPDATE productos SET stock = stock - ? WHERE idProducto = ?";
    $stmtActualizarStock = $conexion->prepare($sqlActualizarStock);
    if (!$stmtActualizarStock) {
        throw new Exception("No pudimos actualizar el stock de los productos");
    }

    foreach ($carrito as $item) {
        $idProductoUp = isset($item['idProducto']) ? (int)$item['idProducto'] : (int)$item['id'];
        $cantidadUp = isset($item['cantidad']) ? (int)$item['cantidad'] : 1;

        $stmtActualizarStock->bind_param("ii", $cantidadUp, $idProductoUp);
        if (!$stmtActualizarStock->execute()) {
            throw new Exception("No pudimos actualizar el stock para el producto ID {$idProductoUp}");
        }
    }
    $stmtActualizarStock->close();

    // Actualizar saldo de la tarjeta
    $nuevoSaldo = $saldoActual - $total;
    $sqlActualizarSaldo = "UPDATE tarjeta SET saldo = ? WHERE idTarjeta = ?";
    $stmtActualizar = $conexion->prepare($sqlActualizarSaldo);

    if (!$stmtActualizar) {
        throw new Exception("No pudimos completar tu compra. Intenta nuevamente");
    }

    $stmtActualizar->bind_param("di", $nuevoSaldo, $idTarjeta);
    $stmtActualizar->execute();

    // Confirmar transacción
    $conexion->commit();

    $response["success"] = true;
    $response["message"] = "Pago procesado exitosamente";
    $response["idFactura"] = $idFactura;
    $response["carrito"] = $carrito;
    $response["subtotal"] = $subtotal;
    $response["itbms"] = $itbms;
    $response["total"] = $total;

} catch (Exception $e) {
    // Revertir transacción en caso de error
    if ($conexion) {
        $conexion->rollback();
    }
    $response["message"] = "No pudimos procesar tu pago. Intenta más tarde o contacta al banco";
    error_log("Error en procesar-pago.php: " . $e->getMessage());
}

echo json_encode($response);
if ($conexion) {
    $conexion->close();
}
?>
