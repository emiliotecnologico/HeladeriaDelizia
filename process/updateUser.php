<?php
include '../library/configServer.php';
include '../library/consulSQL.php';

$nitOld = consultasSQL::clean_string($_POST['nit-old']);
$nit = consultasSQL::clean_string($_POST['nit']);
$nombre = consultasSQL::clean_string($_POST['nombre']);
$nombreCompleto = consultasSQL::clean_string($_POST['nombre-completo']);
$apellido = consultasSQL::clean_string($_POST['apellido']);
$clave = consultasSQL::clean_string($_POST['clave']);
$clave2 = consultasSQL::clean_string($_POST['clave2']);
$direccion = consultasSQL::clean_string($_POST['direccion']);
$telefono = consultasSQL::clean_string($_POST['telefono']);
$email = consultasSQL::clean_string($_POST['email']);

// Verificar que las contraseñas coincidan si se están cambiando
if(!empty($clave)) {
    if($clave != $clave2) {
        echo '<script>
            swal({
                title: "Error",
                text: "Las contraseñas no coinciden",
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
                    window.location.href = "configAdmin.php?view=accountinfo&code='.$nitOld.'";
                } else {
                    window.location.href = "configAdmin.php?view=accountinfo&code='.$nitOld.'";
                }
            });
        </script>';
        exit();
    }
    $claveEncriptada = md5($clave);
    $campoClave = ", Clave='$claveEncriptada'";
} else {
    $campoClave = "";
}

// Verificar que el nuevo NIT no exista (si se cambió)
if($nit != $nitOld) {
    $verificarNit = ejecutarSQL::consultar("SELECT * FROM cliente WHERE NIT='$nit'");
    if(mysqli_num_rows($verificarNit) > 0) {
        echo '<script>
            swal({
                title: "Error",
                text: "El NIT ya existe en el sistema",
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
                    window.location.href = "configAdmin.php?view=accountinfo&code='.$nitOld.'";
                } else {
                    window.location.href = "configAdmin.php?view=accountinfo&code='.$nitOld.'";
                }
            });
        </script>';
        exit();
    }
}

// Actualizar cliente
$campos = "NIT='$nit', Nombre='$nombre', NombreCompleto='$nombreCompleto', Apellido='$apellido', Direccion='$direccion', Telefono='$telefono', Email='$email' $campoClave";

if(consultasSQL::UpdateSQL("cliente", $campos, "NIT='$nitOld'")) {
    echo '<script>
        swal({
            title: "Cliente Actualizado",
            text: "El cliente se actualizó correctamente",
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
                window.location.href = "configAdmin.php?view=accountlist";
            } else {
                window.location.href = "configAdmin.php?view=accountlist";
            }
        });
    </script>';
} else {
    echo '<script>
        swal({
            title: "Error",
            text: "Ocurrió un error al actualizar el cliente",
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
                window.location.href = "configAdmin.php?view=accountinfo&code='.$nitOld.'";
            } else {
                window.location.href = "configAdmin.php?view=accountinfo&code='.$nitOld.'";
            }
        });
    </script>';
}
?>