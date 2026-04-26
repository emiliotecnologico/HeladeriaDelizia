<?php
include '../library/configServer.php';
include '../library/consulSQL.php';

$nitCliente = consultasSQL::clean_string($_POST['nit-cli']);

// Activar el cliente (actualizar estado a 'activo')
if(consultasSQL::UpdateSQL("clientes", "Estado='activo'", "NIT='$nitCliente'")) {
    echo '<script>
        swal({
            title: "Cliente Activado",
            text: "El cliente se activó correctamente",
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
                window.location.href = "configAdmin.php?view=deactivatedclients";
            } else {
                window.location.href = "configAdmin.php?view=deactivatedclients";
            }
        });
    </script>';
} else {
    echo '<script>
        swal({
            title: "Error",
            text: "Ocurrió un error al activar el cliente",
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
                window.location.href = "configAdmin.php?view=deactivatedclients";
            } else {
                window.location.href = "configAdmin.php?view=deactivatedclients";
            }
        });
    </script>';
}
?>