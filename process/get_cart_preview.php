<?php
session_start();
include '../library/configServer.php';
include '../library/consulSQL.php';

$suma = 0;
if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
    foreach($_SESSION['carrito'] as $codigoProd => $cantidad) {
        $consulta = ejecutarSQL::consultar("SELECT * FROM producto WHERE CodigoProd='$codigoProd'");
        if ($consulta && mysqli_num_rows($consulta) > 0) {
            $fila = mysqli_fetch_array($consulta, MYSQLI_ASSOC);
            $precioFinal = $fila['Precio'];
            $subtotal = $precioFinal * $cantidad;
            $suma += $subtotal;
            echo '<div class="cart-item">
                    <div class="cart-item-info">
                        <div class="cart-item-name">'.$fila['NombreProd'].'</div>
                        <div class="cart-item-details">Cantidad: '.$cantidad.' x Bs. '.number_format($precioFinal, 2).'</div>
                    </div>
                    <div>Bs. '.number_format($subtotal, 2).'</div>
                  </div>';
            mysqli_free_result($consulta);
        }
    }
    echo '<div class="cart-total">Total: Bs. '.number_format($suma, 2).'</div>';
    echo '<button class="btn-pedido" data-toggle="modal" data-target="#modalPedido">Realizar Pedido</button>';
    echo '<button class="btn btn-sm btn-warning btn-block mt-2" onclick="vaciarCarrito()">Vaciar Carrito</button>';
} else {
    echo '<p>Tu carrito está vacío</p>';
}
?>