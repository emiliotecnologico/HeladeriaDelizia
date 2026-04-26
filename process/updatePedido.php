<?php
include '../library/configServer.php';
include '../library/consulSQL.php';

// **CORRECCIÓN: Verificar que los campos existan antes de usarlos**
if(!isset($_POST['num-pedido']) || !isset($_POST['pedido-status'])) {
    echo '<script>
        swal("ERROR", "Faltan datos requeridos para actualizar la venta", "error");
        setTimeout(function() {
            location.reload();
        }, 2000);
    </script>';
    exit();
}

$numPediUp = consultasSQL::clean_string($_POST['num-pedido']);
$estadPediUp = consultasSQL::clean_string($_POST['pedido-status']);

// **CORRECCIÓN: Validar que el estado sea uno de los permitidos**
$estadosPermitidos = ['pendiente', 'entregado', 'cancelado'];
if(!in_array($estadPediUp, $estadosPermitidos)) {
    echo '<script>
        swal("ERROR", "Estado no válido: ' . $estadPediUp . '", "error");
        setTimeout(function() {
            location.reload();
        }, 2000);
    </script>';
    exit();
}

// **CORRECCIÓN: Verificar que el pedido existe antes de actualizar**
$verificarPedido = ejecutarSQL::consultar("SELECT NumPedido, Estado FROM venta WHERE NumPedido='$numPediUp'");
if(mysqli_num_rows($verificarPedido) == 0) {
    echo '<script>
        swal("ERROR", "La venta no existe", "error");
        setTimeout(function() {
            location.reload();
        }, 2000);
    </script>';
    mysqli_free_result($verificarPedido);
    exit();
}

$pedidoActual = mysqli_fetch_array($verificarPedido, MYSQLI_ASSOC);
mysqli_free_result($verificarPedido);

// **CORRECCIÓN: Validar transiciones de estado lógicas**
$estadoActual = $pedidoActual['Estado'];

// Prevenir cambios inválidos
if ($estadoActual == 'entregado' && $estadPediUp != $estadoActual) {
    echo '<script>
        swal("ERROR", "No se puede modificar una venta entregada", "error");
        setTimeout(function() {
            location.reload();
        }, 2000);
    </script>';
    exit();
}

// **NUEVA VALIDACIÓN: Para cancelados, solo permitir cambiar a pendiente**
if ($estadoActual == 'cancelado' && $estadPediUp != 'pendiente') {
    echo '<script>
        swal("ERROR", "Una venta cancelada solo puede ser reactivada a pendiente", "error");
        setTimeout(function() {
            location.reload();
        }, 2000);
    </script>';
    exit();
}

// **CORRECCIÓN: Usar la función UpdateSQL correctamente**
if(consultasSQL::UpdateSQL("venta", "Estado='$estadPediUp', fecha_actualizacion=NOW()", "NumPedido='$numPediUp'")){
    
    // Redirigir automáticamente según el nuevo estado
    if($estadPediUp == 'cancelado') {
        echo '<script>
            swal({
                title: "¡Éxito!",
                text: "La venta se canceló correctamente y se movió a ventas canceladas",
                type: "success",
                confirmButtonText: "Aceptar",
                closeOnConfirm: false
            }, function(isConfirm){
                if (isConfirm) {
                    window.location.href = "configAdmin.php?view=ordercancelled";
                }
            });
        </script>';
    } else if ($estadoActual == 'cancelado' && $estadPediUp == 'pendiente') {
        // Caso especial: cuando se reactiva un pedido cancelado
        echo '<script>
            swal({
                title: "¡Éxito!",
                text: "La venta se reactivó correctamente y se movió a ventas pendientes",
                type: "success",
                confirmButtonText: "Aceptar",
                closeOnConfirm: false
            }, function(isConfirm){
                if (isConfirm) {
                    window.location.href = "configAdmin.php?view=orderpending";
                }
            });
        </script>';
    } else {
        echo '<script>
            swal({
                title: "¡Éxito!",
                text: "La venta se actualizó correctamente",
                type: "success",
                confirmButtonText: "Aceptar",
                closeOnConfirm: false
            }, function(isConfirm){
                if (isConfirm) {
                    location.reload();
                }
            });
        </script>';
    }
} else {
    // **CORRECCIÓN: Mostrar error específico de MySQL**
    $error = mysqli_error(ejecutarSQL::conectar());
    echo '<script>
        swal("ERROR", "Error al actualizar la venta: ' . addslashes($error) . '", "error");
        setTimeout(function() {
            location.reload();
        }, 3000);
    </script>';
}
?>