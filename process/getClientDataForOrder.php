<?php
session_start();
require_once "../library/configServer.php";
require_once "../library/consulSQL.php";

header('Content-Type: application/json');

// Verificar que sea administrador o vendedor
if (empty($_SESSION['nombreAdmin']) && empty($_SESSION['nombreVendedor'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado']);
    exit();
}

if (!isset($_GET['nit']) || empty($_GET['nit'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'NIT no proporcionado']);
    exit();
}

$nit = consultasSQL::clean_string($_GET['nit']);

try {
    // Buscar cliente en la tabla clientes
    $cliente = ejecutarSQL::consultar("SELECT * FROM clientes WHERE NIT='$nit'");
    if (!$cliente) {
        throw new Exception("Error en la consulta a la base de datos");
    }
    
    if (mysqli_num_rows($cliente) >= 1) {
        $clienteData = mysqli_fetch_array($cliente, MYSQLI_ASSOC);
        
        // Verificar que el cliente esté activo
        if ($clienteData['Estado'] !== 'activo' && $clienteData['Estado'] !== null) {
            throw new Exception("El cliente está " . $clienteData['Estado']);
        }
        
        $response = [
            'success' => true,
            'nit' => $clienteData['NIT'],
            'nombre' => trim($clienteData['NombreCompleto'] . ' ' . $clienteData['Apellido']),
            'telefono' => $clienteData['Telefono'],
            'direccion' => $clienteData['Direccion'],
            'estado' => $clienteData['Estado'] ?? 'activo'
        ];
    } else {
        $response = ['success' => false, 'message' => 'Cliente no encontrado'];
    }
    
    mysqli_free_result($cliente);
    
} catch (Exception $e) {
    error_log("Error obteniendo datos de cliente: " . $e->getMessage());
    $response = ['success' => false, 'message' => $e->getMessage()];
}

echo json_encode($response);
?>