<?php
session_start();
require_once "../library/configServer.php";
require_once "../library/consulSQL.php";

header('Content-Type: application/json');

$response = array('success' => false, 'message' => 'Error desconocido');

try {
    // Verificar sesión de usuario
    if (empty($_SESSION['nombreUser']) && empty($_SESSION['nombreAdmin'])) {
        $response['message'] = "Debes iniciar sesión para realizar ventas.";
        echo json_encode($response);
        exit();
    }

    // Verificar carrito - CORREGIDO: verificación más robusta
    if (!isset($_SESSION['carrito']) || !is_array($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
        $response['message'] = "No hay productos en el carrito.";
        echo json_encode($response);
        exit();
    }

    // Verificar campos del formulario
    if (empty($_POST['id_cliente'])) {
        $response['message'] = "Debe seleccionar un cliente.";
        echo json_encode($response);
        exit();
    }

    // Verificar archivo de comprobante
    if (!isset($_FILES['comprobante']) || $_FILES['comprobante']['error'] != UPLOAD_ERR_OK) {
        $response['message'] = "Debe subir un comprobante de pago válido.";
        echo json_encode($response);
        exit();
    }

    // Validar tipo de archivo
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
    $fileType = $_FILES['comprobante']['type'];
    if (!in_array($fileType, $allowedTypes)) {
        $response['message'] = "Formato de archivo no permitido. Use JPG, PNG o PDF.";
        echo json_encode($response);
        exit();
    }

    // Validar tamaño del archivo (5MB máximo)
    if ($_FILES['comprobante']['size'] > 5 * 1024 * 1024) {
        $response['message'] = "El archivo es demasiado grande. Máximo 5MB.";
        echo json_encode($response);
        exit();
    }

    // Obtener datos del formulario
    $id_cliente = consultasSQL::clean_string($_POST['id_cliente']);
    $tipoEnvio = "Recoger en Tienda";
    
    // Determinar el vendedor basado en la sesión
    if (isset($_SESSION['UserNIT'])) {
        $id_vendedor = $_SESSION['UserNIT']; // Vendedor
    } else {
        $id_vendedor = NULL; // Administrador
    }

    // Verificar que el cliente exista
    $clienteCheck = ejecutarSQL::consultar("SELECT * FROM clientes WHERE id='$id_cliente' AND Estado='activo'");
    if (mysqli_num_rows($clienteCheck) == 0) {
        $response['message'] = "El cliente seleccionado no existe.";
        mysqli_free_result($clienteCheck);
        echo json_encode($response);
        exit();
    }
    mysqli_free_result($clienteCheck);
    
    // Calcular total del pedido y verificar stock
    $totalPagar = 0;
    $detalles = [];
    $productos_validos = 0;
    
    foreach ($_SESSION['carrito'] as $codigoProd => $cantidad) {
        if ($cantidad > 0) {
            // Obtener información del producto desde la base de datos
            $consultaProducto = ejecutarSQL::consultar("SELECT * FROM producto WHERE CodigoProd='$codigoProd' AND Estado='activo'");
            if (mysqli_num_rows($consultaProducto) > 0) {
                $productoData = mysqli_fetch_array($consultaProducto, MYSQLI_ASSOC);
                $precio = $productoData['Precio'];
                $stock = $productoData['Stock'];
                
                // Verificar stock
                if ($stock < $cantidad) {
                    $response['message'] = "No hay suficiente stock para el producto " . $productoData['NombreProd'] . ". Stock disponible: $stock";
                    mysqli_free_result($consultaProducto);
                    echo json_encode($response);
                    exit();
                }
                
                $detalles[] = [
                    'CodigoProd' => $codigoProd,
                    'Cantidad' => $cantidad,
                    'Precio' => $precio,
                    'Nombre' => $productoData['NombreProd']
                ];
                
                $totalPagar += $precio * $cantidad;
                $productos_validos++;
            } else {
                $response['message'] = "El producto con código $codigoProd no está disponible.";
                echo json_encode($response);
                exit();
            }
            mysqli_free_result($consultaProducto);
        }
    }
    
    // Verificar que haya al menos un producto válido
    if ($productos_validos == 0) {
        $response['message'] = "No hay productos válidos en el carrito.";
        echo json_encode($response);
        exit();
    }
    
    // Procesar archivo de comprobante
    $fileExtension = pathinfo($_FILES['comprobante']['name'], PATHINFO_EXTENSION);
    $fileName = "comprobante_" . time() . "_" . rand(1000, 9999) . "." . $fileExtension;
    $uploadDir = "../assets/comprobantes/";
    
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $filePath = $uploadDir . $fileName;
    
    if (!move_uploaded_file($_FILES['comprobante']['tmp_name'], $filePath)) {
        $response['message'] = "No se pudo guardar el comprobante.";
        echo json_encode($response);
        exit();
    }
    
    // Insertar venta
    $fechaActual = date('Y-m-d');
    $estado = "pendiente";
    
    $insertarVenta = ejecutarSQL::consultar("INSERT INTO venta (Fecha, id_cliente, id_vendedor, TotalPagar, Estado, TipoEnvio, Adjunto) 
                                           VALUES ('$fechaActual', '$id_cliente', '$id_vendedor', '$totalPagar', '$estado', '$tipoEnvio', '$fileName')");
    
    if (!$insertarVenta) {
        unlink($filePath);
        $response['message'] = "No se pudo crear la venta en la base de datos: " . mysqli_error(ejecutarSQL::conectar());
        echo json_encode($response);
        exit();
    }
    
    // Obtener NumPedido
    $result = ejecutarSQL::consultar("SELECT LAST_INSERT_ID() as NumPedido");
    $row = mysqli_fetch_assoc($result);
    $NumPedido = $row['NumPedido'];
    mysqli_free_result($result);
    
    // Insertar detalles y actualizar stock
    $detallesCount = 0;
    foreach ($detalles as $detalle) {
        $codigoProducto = $detalle['CodigoProd'];
        $cantidad = $detalle['Cantidad'];
        $precio = $detalle['Precio'];
        
        $insertDetalle = ejecutarSQL::consultar("INSERT INTO detalle (NumPedido, CodigoProd, CantidadProductos, PrecioProd) 
                                               VALUES ('$NumPedido', '$codigoProducto', '$cantidad', '$precio')");
        
        if ($insertDetalle) {
            // Actualizar stock del producto
            $updateStock = ejecutarSQL::consultar("UPDATE producto SET Stock = Stock - $cantidad WHERE CodigoProd='$codigoProducto'");
            $detallesCount++;
        } else {
            error_log("Error insertando detalle: " . mysqli_error(ejecutarSQL::conectar()));
        }
    }
    
    if ($detallesCount == count($detalles)) {
        // Limpiar carrito y timestamp (para mantener consistencia con tu vaciar_carrito_ajax.php)
        unset($_SESSION['carrito']);
        if (isset($_SESSION['carrito_timestamp'])) {
            unset($_SESSION['carrito_timestamp']);
        }
        
        $response['success'] = true;
        $response['message'] = "Venta confirmada exitosamente. Número de Venta: $NumPedido";
        $response['num_pedido'] = $NumPedido;
    } else {
        // Revertir la venta si hay error en los detalles
        ejecutarSQL::consultar("DELETE FROM venta WHERE NumPedido='$NumPedido'");
        unlink($filePath);
        $response['message'] = "No se pudieron guardar los detalles de la venta. Solo se insertaron $detallesCount de " . count($detalles) . " detalles.";
    }

} catch (Exception $e) {
    if (isset($filePath) && file_exists($filePath)) {
        unlink($filePath);
    }
    $response['message'] = "Ocurrió un problema al procesar tu venta: " . $e->getMessage();
}

echo json_encode($response);
?>