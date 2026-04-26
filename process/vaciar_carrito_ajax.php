<?php
session_start();

// Vaciar el carrito y el timestamp
if (isset($_SESSION['carrito'])) {
    unset($_SESSION['carrito']);
}
if (isset($_SESSION['carrito_timestamp'])) {
    unset($_SESSION['carrito_timestamp']);
}

// Devolver respuesta JSON con formato idéntico al sistema
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'message' => 'Carrito vaciado exitosamente',
    'swal_script' => '
        <script>
            swal({
                title: "Carrito vaciado",
                text: "El carrito se ha vaciado exitosamente",
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
        </script>
    '
]);
?>