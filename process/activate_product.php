<?php
    // Cambiado a configServer.php y consulSQL.php para consistencia
    require '../library/configServer.php';
    require '../library/consulSQL.php';
    
    if(isset($_GET['code'])){
        $codigo = consultasSQL::clean_string($_GET['code']);
        // CORREGIDO: Usando consultasSQL::UpdateSQL y estado en minúscula
        if(consultasSQL::UpdateSQL("producto", "Estado='activo'", "CodigoProd='$codigo'")){
            header('Location: ../configAdmin.php?view=deactivatedproducts&success=Producto activado correctamente');
        } else {
            header('Location: ../configAdmin.php?view=deactivatedproducts&error=Error al activar el producto');
        }
    }
?>