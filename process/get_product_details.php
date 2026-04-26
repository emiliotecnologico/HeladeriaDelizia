<?php
session_start();
require '../library/configServer.php';
require '../library/consulSQL.php';

if(isset($_GET['code'])){
    $codigo = consultasSQL::clean_string($_GET['code']);
    
    // Verificar que el producto existe
    $producto = ejecutarSQL::consultar("SELECT * FROM producto WHERE CodigoProd='$codigo'");
    if(mysqli_num_rows($producto) > 0) {
        // ACTIVAR producto - usando estado en minúscula según la nueva estructura
        if(consultasSQL::UpdateSQL("producto", "Estado='activo'", "CodigoProd='$codigo'")){
            echo '<script>
                swal({
                  title: "Producto Activado",
                  text: "El producto ha sido activado correctamente",
                  type: "success",
                  showCancelButton: true,
                  confirmButtonClass: "btn-success",
                  confirmButtonText: "Aceptar",
                  closeOnConfirm: false
                },
                function(isConfirm) {
                  if (isConfirm) {
                    window.location.href = "../configAdmin.php?view=deactivatedproducts";
                  }
                });
            </script>';
        } else {
            echo '<script>
                swal("ERROR", "Error al activar el producto", "error");
                setTimeout(function() {
                    window.location.href = "../configAdmin.php?view=deactivatedproducts";
                }, 2000);
            </script>';
        }
    } else {
        echo '<script>
            swal("ERROR", "Producto no encontrado", "error");
            setTimeout(function() {
                window.location.href = "../configAdmin.php?view=deactivatedproducts";
            }, 2000);
        </script>';
    }
} else {
    echo '<script>
        swal("ERROR", "Código de producto no especificado", "error");
        setTimeout(function() {
            window.location.href = "../configAdmin.php?view=deactivatedproducts";
        }, 2000);
    </script>';
}
?>