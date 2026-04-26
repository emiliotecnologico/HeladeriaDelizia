<?php
session_start();
include '../library/configServer.php';
include '../library/consulSQL.php';

// Verificar que se esté actualizando el propio perfil o sea administrador
if (!isset($_SESSION['UserNIT']) && !isset($_SESSION['nombreAdmin'])) {
    echo '<script>swal("ERROR", "No tiene permisos para realizar esta acción", "error");</script>';
    exit;
}

// Recibir y limpiar datos
$nitOldVendedor = consultasSQL::clean_string($_POST['nit-old']);
$nitVendedor = consultasSQL::clean_string($_POST['vendedor-nit']);
$nameVendedor = consultasSQL::clean_string($_POST['vendedor-name']);
$fullnameVendedor = consultasSQL::clean_string($_POST['vendedor-fullname']);
$apeVendedor = consultasSQL::clean_string($_POST['vendedor-lastname']);
$dirVendedor = consultasSQL::clean_string($_POST['vendedor-dir']);
$phoneVendedor = consultasSQL::clean_string($_POST['vendedor-phone']);
$emailVendedor = consultasSQL::clean_string($_POST['vendedor-email']);
$passVendedor1 = consultasSQL::clean_string($_POST['vendedor-pass1']);
$passVendedor2 = consultasSQL::clean_string($_POST['vendedor-pass2']);

// Verificar permisos: vendedor solo puede modificar sus propios datos
$esVendedorPropio = (isset($_SESSION['UserNIT']) && $_SESSION['UserNIT'] == $nitOldVendedor);
$esAdministrador = isset($_SESSION['nombreAdmin']);

if (!$esAdministrador && !$esVendedorPropio) {
    echo '<script>swal("ERROR", "No tiene permisos para modificar este perfil", "error");</script>';
    exit;
}

// Si es vendedor (no administrador), no permitir cambiar NIT
if ($esVendedorPropio && !$esAdministrador) {
    $nitVendedor = $nitOldVendedor; // No permitir cambiar NIT
}

// Verificar que el NIT no esté duplicado (excepto para el mismo vendedor)
if ($nitVendedor != $nitOldVendedor) {
    $checkNit = ejecutarSQL::consultar("SELECT * FROM vendedores WHERE NIT='$nitVendedor'");
    if(mysqli_num_rows($checkNit) > 0){
        echo '<script>swal("ERROR", "El C.I. que ha ingresado ya está registrado en el sistema, por favor ingrese otro número de C.I.", "error");</script>';
        exit;
    }
}

// Verificar que el email no esté duplicado (excepto para el mismo vendedor)
$checkEmail = ejecutarSQL::consultar("SELECT * FROM vendedores WHERE Email='$emailVendedor' AND NIT != '$nitOldVendedor'");
if(mysqli_num_rows($checkEmail) > 0){
    echo '<script>swal("ERROR", "El email que ha ingresado ya está registrado en el sistema, por favor ingrese otro email.", "error");</script>';
    exit;
}

// Si se ingresaron contraseñas, validar que coincidan
if($passVendedor1 != "" || $passVendedor2 != ""){
    if($passVendedor1 != $passVendedor2){
        echo '<script>swal("ERROR", "Las contraseñas no coinciden, por favor verifique.", "error");</script>';
        exit;
    }else{
        $claveVendedor = md5($passVendedor1);
        // Actualizar con contraseña (sin estado)
        if(consultasSQL::UpdateSQL("vendedores", 
            "NIT='$nitVendedor', 
            Nombre='$nameVendedor', 
            NombreCompleto='$fullnameVendedor', 
            Apellido='$apeVendedor', 
            Clave='$claveVendedor', 
            Direccion='$dirVendedor', 
            Telefono='$phoneVendedor', 
            Email='$emailVendedor'", 
            "NIT='$nitOldVendedor'")){
            
            // Actualizar sesión si es el propio usuario
            if ($esVendedorPropio) {
                $_SESSION['nombreUser'] = $nameVendedor;
                $_SESSION['UserNIT'] = $nitVendedor;
            }
            
            echo '<script>
                swal({
                  title: "Vendedor actualizado",
                  text: "Los datos del vendedor se actualizaron correctamente",
                  type: "success",
                  showCancelButton: false,
                  confirmButtonClass: "btn-success",
                  confirmButtonText: "Aceptar",
                  closeOnConfirm: false
                  },
                  function(isConfirm) {
                  if (isConfirm) {
                    location.reload();
                  }
                });
            </script>';
        }else{
            echo '<script>swal("ERROR", "Ocurrió un error inesperado, por favor intente nuevamente", "error");</script>';
        }
    }
}else{
    // Actualizar sin contraseña (sin estado)
    if(consultasSQL::UpdateSQL("vendedores", 
        "NIT='$nitVendedor', 
        Nombre='$nameVendedor', 
        NombreCompleto='$fullnameVendedor', 
        Apellido='$apeVendedor', 
        Direccion='$dirVendedor', 
        Telefono='$phoneVendedor', 
        Email='$emailVendedor'", 
        "NIT='$nitOldVendedor'")){
        
        // Actualizar sesión si es el propio usuario
        if ($esVendedorPropio) {
            $_SESSION['nombreUser'] = $nameVendedor;
            $_SESSION['UserNIT'] = $nitVendedor;
        }
        
        echo '<script>
            swal({
              title: "Vendedor actualizado",
              text: "Los datos del vendedor se actualizaron correctamente",
              type: "success",
              showCancelButton: false,
              confirmButtonClass: "btn-success",
              confirmButtonText: "Aceptar",
              closeOnConfirm: false
              },
              function(isConfirm) {
              if (isConfirm) {
                location.reload();
              }
            });
        </script>';
    }else{
        echo '<script>swal("ERROR", "Ocurrió un error inesperado, por favor intente nuevamente", "error");</script>';
    }
}
?>