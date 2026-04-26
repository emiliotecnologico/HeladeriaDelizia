<?php
// Verificar si la sesión ya está activa antes de iniciarla
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include '../library/consulSQL.php';

$codigo = consultasSQL::clean_string($_GET['codigo']);

if(isset($_SESSION['carrito'][$codigo])) {
    unset($_SESSION['carrito'][$codigo]);
    
    // Si el carrito queda vacío, eliminar también el timestamp
    if(count($_SESSION['carrito']) == 0) {
        unset($_SESSION['carrito_timestamp']);
    }
}

// Redirigir de vuelta al carrito
header("Location: ../carrito.php");
exit();
?>