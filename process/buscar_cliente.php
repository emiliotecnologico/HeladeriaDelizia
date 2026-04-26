<?php
session_start();
include '../library/configServer.php';
include '../library/consulSQL.php';

// Headers para JSON
header('Content-Type: application/json');

$response = array('success' => false, 'message' => 'Error desconocido');

try {
    if(isset($_GET['ci']) || isset($_GET['id'])) {
        $ci = isset($_GET['ci']) ? consultasSQL::clean_string($_GET['ci']) : '';
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        // Construir la consulta según los parámetros recibidos
        if($id > 0) {
            // Buscar por ID (prioridad)
            $query = "SELECT * FROM clientes WHERE id = '$id' AND Estado = 'activo'";
        } else if(!empty($ci)) {
            // Buscar por CI
            $query = "SELECT * FROM clientes WHERE NIT = '$ci' AND Estado = 'activo'";
        } else {
            $response['message'] = "Parámetros insuficientes";
            echo json_encode($response);
            exit;
        }
        
        $cliente = ejecutarSQL::consultar($query);
        
        if($cliente && mysqli_num_rows($cliente) > 0) {
            $clienteData = mysqli_fetch_assoc($cliente);
            $response = array(
                'success' => true,
                'id' => $clienteData['id'],
                'nit' => $clienteData['NIT'],
                'nombre_completo' => $clienteData['NombreCompleto'],
                'apellido' => $clienteData['Apellido'],
                'telefono' => $clienteData['Telefono'],
                'direccion' => $clienteData['Direccion']
            );
        } else {
            $response['message'] = "Cliente no encontrado";
        }
        
        if($cliente) {
            mysqli_free_result($cliente);
        }
    } else {
        $response['message'] = "No se recibieron parámetros";
    }
} catch (Exception $e) {
    $response['message'] = "Error: " . $e->getMessage();
}

echo json_encode($response);
?>