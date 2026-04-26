<?php
session_start(); 
include '../library/configServer.php';
include '../library/consulSQL.php';

$code = consultasSQL::clean_string($_POST['code']);
$selOrder = ejecutarSQL::consultar("SELECT Estado FROM venta WHERE NumPedido='".$code."'");

// Verificar si se encontró el pedido
if(mysqli_num_rows($selOrder) == 0) {
    echo '<div class="alert alert-danger">Venta na encontrado</div>';
    exit();
}

$peU = mysqli_fetch_array($selOrder, MYSQLI_ASSOC);
mysqli_free_result($selOrder);

echo '<input type="hidden" value="'.$code.'" name="num-pedido">';
echo '
    <div class="form-group">
        <label style="font-size: 16px; margin-bottom: 8px; display: block;">Estado actual:</label>
        <p class="form-control-static"><strong style="font-size: 16px;">'.ucfirst($peU['Estado']).'</strong></p>
    </div>
    <div class="form-group">
        <label style="font-size: 16px; margin-bottom: 8px; display: block;">Cambiar estado:</label>
        <select class="form-control" style="font-size: 16px; padding: 10px; height: auto; line-height: 1.5;" name="pedido-status" required>
';

$estadoActual = $peU['Estado'];

// Mostrar opciones basadas en el estado actual
switch($estadoActual) {
    case 'pendiente':
        echo '
            <option value="entregado">Entregado</option>
            <option value="cancelado">Cancelado</option>
        ';
        break;
        
    case 'entregado':
        echo '
            <option value="entregado" selected>Entregado (No se puede cambiar)</option>
        ';
        break;
        
    case 'cancelado':
        // SOLO permitir cambiar a pendiente desde cancelado
        echo '
            <option value="pendiente">Pendiente</option>
            <option value="cancelado" selected>Cancelado</option>
        ';
        break;
        
    default:
        echo '
            <option value="entregado">Entregado</option>
            <option value="cancelado">Cancelado</option>
        ';
}

echo '
        </select>
    </div>
';

// Mostrar mensaje informativo diferente según el estado
if($estadoActual == 'cancelado') {
    echo '
    <div class="alert alert-info" style="font-size: 14px; margin-top: 15px;">
        <i class="fa fa-info-circle"></i> 
        <strong>Nota:</strong> Al reactivar una venta cancelada, se moverá automáticamente a la sección de ventas pendientes.
    </div>
    ';
} else {
    echo '
    <div class="alert alert-info" style="font-size: 14px; margin-top: 15px;">
        <i class="fa fa-info-circle"></i> 
        <strong>Nota:</strong> Al cancelar una venta, se moverá automáticamente a la sección de ventas canceladas.
    </div>
    ';
}
?>