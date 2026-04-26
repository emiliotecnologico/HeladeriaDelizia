<?php
include '../library/configServer.php';
include '../library/consulSQL.php';

$nit = consultasSQL::clean_string($_POST['nit']);
$nombreCompleto = consultasSQL::clean_string($_POST['nombre-completo']);
$apellido = consultasSQL::clean_string($_POST['apellido']);
$direccion = consultasSQL::clean_string($_POST['direccion']);
$telefono = consultasSQL::clean_string($_POST['telefono']);

// Verificar que el NIT no exista en la tabla clientes
$verificarNit = ejecutarSQL::consultar("SELECT * FROM clientes WHERE NIT='$nit'");
if(mysqli_num_rows($verificarNit) > 0) {
    echo '<script>
        swal({
            title: "Error",
            text: "El C.I: ya existe en el sistema",
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
                window.location.href = "configAdmin.php?view=cliente";
            } else {
                window.location.href = "configAdmin.php?view=cliente";
            }
        });
    </script>';
    exit();
}

// Insertar cliente en la tabla clientes (sin campos Nombre, Clave, Email)
if(consultasSQL::InsertSQL("clientes", "NIT, NombreCompleto, Apellido, Direccion, Telefono", "'$nit','$nombreCompleto','$apellido','$direccion','$telefono'")) {
    echo '<script>
        swal({
            title: "Cliente Agregado",
            text: "El cliente se agregó correctamente",
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
            text: "Ocurrió un error al agregar el cliente",
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
                window.location.href = "configAdmin.php?view=cliente";
            } else {
                window.location.href = "configAdmin.php?view=cliente";
            }
        });
    </script>';
}
?>