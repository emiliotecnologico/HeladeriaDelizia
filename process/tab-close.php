<?php
// process/tab-close.php - VERSIÓN MÁS SEGURA
session_start();

// Solo procesar si hay una sesión activa y no es invitado
if ((!empty($_SESSION['nombreAdmin']) || !empty($_SESSION['nombreUser'])) && empty($_SESSION['esInvitado'])) {
    
    // Registrar en el log del servidor
    $usuario = $_SESSION['nombreAdmin'] ?? $_SESSION['nombreUser'] ?? 'Desconocido';
    error_log("🔴 Cierre REAL de pestaña - Usuario: " . $usuario . " - Hora: " . date('Y-m-d H:i:s'));
    
    // Destruir completamente la sesión
    $_SESSION = array();
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    session_destroy();
    
    echo "SESSION_DESTROYED";
    
} else {
    echo "NO_ACTION_NEEDED";
}
?>