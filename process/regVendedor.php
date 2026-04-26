<?php
include '../library/configServer.php';
include '../library/consulSQL.php';

$nitVendedor=consultasSQL::clean_string($_POST['vendedor-nit']);
$nameVendedor=consultasSQL::clean_string($_POST['vendedor-name']);
$fullnameVendedor=consultasSQL::clean_string($_POST['vendedor-fullname']);
$apeVendedor=consultasSQL::clean_string($_POST['vendedor-lastname']);
$passVendedor1=consultasSQL::clean_string($_POST['vendedor-pass1']);
$passVendedor2=consultasSQL::clean_string($_POST['vendedor-pass2']);
$dirVendedor=consultasSQL::clean_string($_POST['vendedor-dir']);
$phoneVendedor=consultasSQL::clean_string($_POST['vendedor-phone']);
$emailVendedor=consultasSQL::clean_string($_POST['vendedor-email']);

if(!$nitVendedor=="" && !$nameVendedor=="" && !$apeVendedor=="" && !$dirVendedor=="" && !$phoneVendedor=="" && !$emailVendedor=="" && !$fullnameVendedor=="" && !$passVendedor1=="" && !$passVendedor2==""){
    if($passVendedor1==$passVendedor2){
        $verificar= ejecutarSQL::consultar("SELECT * FROM vendedores WHERE NIT='".$nitVendedor."' OR Email='".$emailVendedor."'");
        $verificaltotal = mysqli_num_rows($verificar);
        if($verificaltotal<=0){
            $passVendedor1=md5($passVendedor1);
            if(consultasSQL::InsertSQL("vendedores", "NIT, Nombre, NombreCompleto, Apellido, Clave, Direccion, Telefono, Email", "'$nitVendedor','$nameVendedor','$fullnameVendedor','$apeVendedor','$passVendedor1','$dirVendedor','$phoneVendedor','$emailVendedor'")){
                echo '<script>
                    swal({
                      title: "Registro completado",
                      text: "El vendedor se registró con éxito",
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
            echo '<script>swal("ERROR", "El NIT o el email que ha ingresado ya está registrado en el sistema, por favor ingrese otro", "error");</script>';
        }
        mysqli_free_result($verificar);
    }else{
        echo '<script>swal("ERROR", "Las contraseñas no coinciden, por favor verifique.", "error");</script>';
    }
}else {
    echo '<script>swal("ERROR", "Los campos no pueden estar vacíos", "error");</script>';
}
?>