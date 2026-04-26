<?php
session_start();

$totalItems = 0;
if (isset($_SESSION['carrito']) && is_array($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $cantidad) {
        $totalItems += $cantidad;
    }
}

echo $totalItems;
?>