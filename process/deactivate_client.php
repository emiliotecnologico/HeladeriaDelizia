<?php
include '../library/configServer.php';
include '../library/consulSQL.php';

$nitCliente = consultasSQL::clean_string($_POST['nit-cli']);

// Primero obtener el ID del cliente usando el NIT
$clienteData = ejecutarSQL::consultar("SELECT id FROM clientes WHERE NIT='$nitCliente'");
if($clienteData && mysqli_num_rows($clienteData) > 0) {
    $cliente = mysqli_fetch_array($clienteData, MYSQLI_ASSOC);
    $idCliente = $cliente['id'];
    mysqli_free_result($clienteData);
    
    // Verificar si el cliente tiene pedidos antes de desactivar (usando id_cliente)
    $checkPedidos = ejecutarSQL::consultar("SELECT * FROM venta WHERE id_cliente='$idCliente'");

    if(mysqli_num_rows($checkPedidos) > 0) {
        echo '<script>
            swal({
                title: "Error",
                text: "No se puede desactivar el cliente porque tiene pedidos asociados.",
                type: "error",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Aceptar",
                cancelButtonText: "Cancelar",
                closeOnConfirm: false,
                closeOnCancel: false
            },
            function(isConfirm) {
                if (isConfirm) {
                    window.location.href = "configAdmin.php?view=clientelist";
                } else {
                    window.location.href = "configAdmin.php?view=clientelist";
                }
            });
        </script>';
    } else {
        // Desactivar el cliente (actualizar estado a 'inactivo')
        if(consultasSQL::UpdateSQL("clientes", "Estado='inactivo'", "NIT='$nitCliente'")) {
            echo '<script>
                swal({
                    title: "Cliente Desactivado",
                    text: "El cliente se desactivó correctamente",
                    type: "success",
                    showCancelButton: true,
                    confirmButtonClass: "btn-success",
                    confirmButtonText: "Aceptar",
                    cancelButtonText: "Cancelar",
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function(isConfirm) {
                    if (isConfirm) {
                        window.location.href = "configAdmin.php?view=clientelist";
                    } else {
                        window.location.href = "configAdmin.php?view=clientelist";
                    }
                });
            </script>';
        } else {
            echo '<script>
                swal({
                    title: "Error",
                    text: "Ocurrió un error al desactivar el cliente",
                    type: "error",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Aceptar",
                    cancelButtonText: "Cancelar",
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function(isConfirm) {
                    if (isConfirm) {
                        window.location.href = "configAdmin.php?view=clientelist";
                    } else {
                        window.location.href = "configAdmin.php?view=clientelist";
                    }
                });
            </script>';
        }
    }
    mysqli_free_result($checkPedidos);
} else {
    echo '<script>
        swal({
            title: "Error",
            text: "Cliente no encontrado",
            type: "error",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Aceptar",
            cancelButtonText: "Cancelar",
            closeOnConfirm: false,
            closeOnCancel: false
        },
        function(isConfirm) {
            if (isConfirm) {
                window.location.href = "configAdmin.php?view=clientelist";
            } else {
                window.location.href = "configAdmin.php?view=clientelist";
            }
        });
    </script>';
}
?>