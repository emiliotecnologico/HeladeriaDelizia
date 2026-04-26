<?php
session_start();

// HEADERS PARA PREVENIR CACHE
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

include '../library/configServer.php';
include '../library/consulSQL.php';

$userName = consultasSQL::clean_string($_POST['nombre-login']);
$userPass = consultasSQL::clean_string($_POST['clave-login']);

if($userName != "" && $userPass != ""){
    // Verificar en la tabla de administradores primero
    $verAdmin = ejecutarSQL::consultar("SELECT * FROM administrador WHERE Nombre='$userName' AND Clave='".md5($userPass)."'");
    
    // Verificar en la tabla vendedores
    $verVendedor = ejecutarSQL::consultar("SELECT * FROM vendedores WHERE Nombre='$userName' AND Clave='".md5($userPass)."'");

    $AcountAdmin = mysqli_num_rows($verAdmin);
    $AcountVendedor = mysqli_num_rows($verVendedor);

    if($AcountAdmin >= 1){
        $adminData = mysqli_fetch_array($verAdmin, MYSQLI_ASSOC);
        $_SESSION['nombreAdmin'] = $userName;
        $_SESSION['claveAdmin'] = $userPass;
        $_SESSION['UserType'] = "Admin";
        $_SESSION['adminID'] = $adminData['id'];
        
        // REDIRECCIÓN DIRECTA SIN MENSAJES
        header("Location: ../index.php");
        exit();
    }
    else if($AcountVendedor >= 1){
        $vendedorData = mysqli_fetch_array($verVendedor, MYSQLI_ASSOC);
        $_SESSION['nombreUser'] = $userName;
        $_SESSION['claveUser'] = $userPass;
        $_SESSION['UserType'] = "User";
        $_SESSION['UserNIT'] = $vendedorData['NIT'];
        $_SESSION['UserNombreCompleto'] = $vendedorData['NombreCompleto'];
        $_SESSION['UserApellido'] = $vendedorData['Apellido'];
        $_SESSION['UserDireccion'] = $vendedorData['Direccion'];
        $_SESSION['UserTelefono'] = $vendedorData['Telefono'];
        $_SESSION['UserEmail'] = $vendedorData['Email'];
        
        // REDIRECCIÓN DIRECTA SIN MENSAJES
        header("Location: ../index.php");
        exit();
    }else{
        // REDIRECCIÓN DIRECTA AL LOGIN CON ERROR
        header("Location: ../login.php?error=1");
        exit();
    }
    
    mysqli_free_result($verAdmin);
    mysqli_free_result($verVendedor);
}else{
    // REDIRECCIÓN DIRECTA AL LOGIN CON ERROR
    header("Location: ../login.php?error=2");
    exit();
}
?>