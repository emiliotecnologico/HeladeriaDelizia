<?php
error_reporting(E_PARSE);

// Verificar si estamos en una página de administración
$is_admin_page = (strpos($_SERVER['REQUEST_URI'], 'configAdmin.php') !== false);

if ($is_admin_page) {
    // Para páginas de administración, verificar que sea admin O vendedor (no invitado)
    if (empty($_SESSION['nombreAdmin']) && (empty($_SESSION['nombreUser']) || !empty($_SESSION['esInvitado']))) {
        // Debug: descomenta la siguiente línea para ver qué está pasando
        // die("Redirigiendo: No tiene permisos de administración. SESSION: " . print_r($_SESSION, true));
        
        header("Location: index.php");
        exit();
    }
}
?>