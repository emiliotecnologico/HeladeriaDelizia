<?php
// Verificar si la sesión ya está activa antes de iniciarla
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Tiempo de expiración en segundos (30 minutos = 1800 segundos)
$cart_expiry_time = 1800;

// Verificar si el carrito ha expirado
if (isset($_SESSION['carrito_timestamp'])) {
    $current_time = time();
    $cart_age = $current_time - $_SESSION['carrito_timestamp'];
    
    // Si el carrito tiene más de 30 minutos, vaciarlo
    if ($cart_age > $cart_expiry_time) {
        // Limpiar el carrito
        unset($_SESSION['carrito']);
        unset($_SESSION['carrito_timestamp']);
        
        // Opcional: Mostrar mensaje de expiración (solo si hay contenido)
        if (isset($_SESSION['carrito_expired_message'])) {
            unset($_SESSION['carrito_expired_message']);
        }
    }
} else {
    // Si no hay timestamp pero hay carrito, establecer uno nuevo
    if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
        $_SESSION['carrito_timestamp'] = time();
    }
}

// Actualizar timestamp en cada interacción con el carrito
// Esto se hace en add_to_cart_ajax.php automáticamente
?>