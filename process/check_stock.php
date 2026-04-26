<?php
// Verificar si la sesión ya está activa antes de iniciarla
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include '../library/consulSQL.php';

$codigo = consultasSQL::clean_string($_GET['codigo']);
$productInfo = ejecutarSQL::consultar("SELECT Stock FROM producto WHERE CodigoProd='$codigo'");

if($productInfo && mysqli_num_rows($productInfo) > 0) {
    $productData = mysqli_fetch_array($productInfo, MYSQLI_ASSOC);
    $stockReal = $productData['Stock'];
    
    // Calcular stock reservado en el carrito del usuario actual
    $stockReservado = 0;
    if(isset($_SESSION['carro'])) {
        foreach($_SESSION['carro'] as $item) {
            if($item['producto'] == $codigo) {
                $stockReservado += $item['cantidad'];
            }
        }
    }
    
    $stockDisponible = max(0, $stockReal - $stockReservado);
    
    header('Content-Type: application/json');
    echo json_encode([
        'stockReal' => $stockReal,
        'stockReservado' => $stockReservado,
        'stockDisponible' => $stockDisponible
    ]);
} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Producto no encontrado']);
}
?>