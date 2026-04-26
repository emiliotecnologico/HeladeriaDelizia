<?php
include '../library/configServer.php';
include '../library/consulSQL.php';

$nitCliente=consultasSQL::clean_string($_POST['clien-nit']);
$nameCliente=consultasSQL::clean_string($_POST['clien-name']);
$fullnameCliente=consultasSQL::clean_string($_POST['clien-fullname']);
$apeCliente=consultasSQL::clean_string($_POST['clien-lastname']);
$dirCliente=consultasSQL::clean_string($_POST['clien-dir']);
$phoneCliente=consultasSQL::clean_string($_POST['clien-phone']);
$emailCliente=consultasSQL::clean_string($_POST['clien-email']);

if(!$nitCliente=="" && !$nameCliente=="" && !$apeCliente=="" && !$dirCliente=="" && !$phoneCliente=="" && !$emailCliente=="" && !$fullnameCliente==""){
    // MODIFICADO: Ahora verifica en la tabla clientes en lugar de cliente
    $verificar= ejecutarSQL::consultar("SELECT * FROM clientes WHERE NIT='".$nitCliente."'");
    $verificaltotal = mysqli_num_rows($verificar);
    if($verificaltotal<=0){
        // MODIFICADO: Insertar en la tabla clientes (sin campo de contraseña)
        if(consultasSQL::InsertSQL("clientes", "NIT, Nombre, NombreCompleto, Apellido, Direccion, Telefono, Email", "'$nitCliente','$nameCliente','$fullnameCliente','$apeCliente','$dirCliente','$phoneCliente','$emailCliente'")){
            echo '<script>
                swal({
                  title: "Registro completado",
                  text: "El cliente se registró con éxito",
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
    }else{
        echo '<script>swal("ERROR", "El C.I. que ha ingresado ya está registrado en el sistema, por favor ingrese otro número de C.I.", "error");</script>';
    }
    mysqli_free_result($verificar);
}else {
    echo '<script>swal("ERROR", "Los campos no pueden estar vacíos", "error");</script>';
}
?>