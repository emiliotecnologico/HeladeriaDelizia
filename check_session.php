<?php
session_start();

// Verificar si la sesión es válida
$response = ['valid' => false];

// Solo verificar para usuarios logueados (no invitados)
if ((!empty($_SESSION['nombreAdmin']) || !empty($_SESSION['nombreUser'])) && !isset($_SESSION['esInvitado'])) {
    if (isset($_SESSION['tab_identifier'])) {
        if (isset($_COOKIE['tab_identifier']) && $_COOKIE['tab_identifier'] === $_SESSION['tab_identifier']) {
            $response['valid'] = true;
        }
    }
} else {
    // Para invitados, siempre es válido
    $response['valid'] = true;
}

header('Content-Type: application/json');
echo json_encode($response);
?>