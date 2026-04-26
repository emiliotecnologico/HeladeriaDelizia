<?php
session_start();
include '../library/configServer.php';
include '../library/consulSQL.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Error desconocido', 'cart_count' => 0];

try {
    if (isset($_POST['codigo']) && isset($_POST['cantidad'])) {
        $codigoProducto = consultasSQL::clean_string($_POST['codigo']);
        $cantidad = intval($_POST['cantidad']);
        
        if ($cantidad > 0) {
            // Verificar stock disponible
            $producto = ejecutarSQL::consultar("SELECT Stock, NombreProd, Precio FROM producto WHERE CodigoProd='$codigoProducto' AND Estado='activo'");
            
            if ($producto && mysqli_num_rows($producto) > 0) {
                $prodData = mysqli_fetch_array($producto, MYSQLI_ASSOC);
                $stockDisponible = $prodData['Stock'];
                $nombreProducto = $prodData['NombreProd'];
                
                // Inicializar carrito con estructura consistente
                if (!isset($_SESSION['carrito']) || !is_array($_SESSION['carrito'])) {
                    $_SESSION['carrito'] = array();
                }
                
                // Calcular stock ya reservado en carrito
                $stockReservado = 0;
                if (isset($_SESSION['carrito'][$codigoProducto])) {
                    $stockReservado = $_SESSION['carrito'][$codigoProducto];
                }
                
                $stockRealDisponible = max(0, $stockDisponible - $stockReservado);
                
                if ($cantidad <= $stockRealDisponible) {
                    // Agregar al carrito (estructura asociativa simple)
                    if (isset($_SESSION['carrito'][$codigoProducto])) {
                        $_SESSION['carrito'][$codigoProducto] += $cantidad;
                    } else {
                        $_SESSION['carrito'][$codigoProducto] = $cantidad;
                    }
                    
                    // Actualizar timestamp del carrito
                    $_SESSION['carrito_timestamp'] = time();
                    
                    // Calcular total de items
                    $totalItems = 0;
                    foreach ($_SESSION['carrito'] as $cant) {
                        $totalItems += $cant;
                    }
                    
                    $response = [
                        'success' => true, 
                        'message' => 'Producto agregado al carrito correctamente', 
                        'cart_count' => $totalItems,
                        'product_name' => $nombreProducto
                    ];
                } else {
                    $response = ['success' => false, 'message' => 'No hay suficiente stock disponible. Stock actual: ' . $stockRealDisponible];
                }
                mysqli_free_result($producto);
            } else {
                $response = ['success' => false, 'message' => 'Producto no encontrado o inactivo'];
            }
        } else {
            $response = ['success' => false, 'message' => 'La cantidad debe ser mayor a 0'];
        }
    } else {
        $response = ['success' => false, 'message' => 'Datos incompletos'];
    }
} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'Error del servidor: ' . $e->getMessage()];
}

echo json_encode($response);
?>