<?php
include '../library/configServer.php';
include '../library/consulSQL.php';

$idCliente = consultasSQL::clean_string($_POST['id']);
$nitCliente = consultasSQL::clean_string($_POST['nit']);
$nombreCompletoCliente = consultasSQL::clean_string($_POST['nombre-completo']);
$apellidoCliente = consultasSQL::clean_string($_POST['apellido']);
$dirCliente = consultasSQL::clean_string($_POST['direccion']);
$phoneCliente = consultasSQL::clean_string($_POST['telefono']);
$estadoCliente = consultasSQL::clean_string($_POST['estado']);

// Verificar que el NIT no esté duplicado (excepto para el mismo cliente)
$checkNit = ejecutarSQL::consultar("SELECT * FROM clientes WHERE NIT='$nitCliente' AND id != '$idCliente'");
if(mysqli_num_rows($checkNit) > 0){
    echo '<script>swal("ERROR", "El NIT/C.I. que ha ingresado ya está registrado en el sistema, por favor ingrese otro número de C.I.", "error");</script>';
    exit;
}

// Actualizar sin el campo Nombre (usuario) y Email
if(consultasSQL::UpdateSQL("clientes", "NIT='$nitCliente', NombreCompleto='$nombreCompletoCliente', Apellido='$apellidoCliente', Direccion='$dirCliente', Telefono='$phoneCliente', Estado='$estadoCliente'", "id='$idCliente'")){
    echo '<script>
        swal({
          title: "Cliente actualizado",
          text: "Los datos del cliente se actualizaron correctamente",
          type: "success",
          showCancelButton: true,
          confirmButtonClass: "btn-danger",
          confirmButtonText: "Aceptar",
          cancelButtonText: "Cancelar",
          closeOnConfirm: false,
          closeOnCancel: false
          },
          function(isConfirm) {
          if (isConfirm) {
            location.reload();
          } else {
            location.reload();
          }
        });
    </script>';
}else{
    echo '<script>swal("ERROR", "Ocurrió un error inesperado, por favor intente nuevamente", "error");</script>';
}
?>