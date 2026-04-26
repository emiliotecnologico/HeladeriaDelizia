<?php
session_start();
include '../library/configServer.php';
include '../library/consulSQL.php';

header('Content-Type: application/json');
$response = array('success' => false, 'clientes' => array());

try {
    $nit = isset($_POST['avanzada-nit']) ? consultasSQL::clean_string($_POST['avanzada-nit']) : '';
    $nombre = isset($_POST['avanzada-nombre']) ? consultasSQL::clean_string($_POST['avanzada-nombre']) : '';

    $whereConditions = array("Estado='activo'");
    
    if (!empty($nit)) {
        $whereConditions[] = "NIT LIKE '%$nit%'";
    }
    if (!empty($nombre)) {
        $whereConditions[] = "(NombreCompleto LIKE '%$nombre%' OR Apellido LIKE '%$nombre%')";
    }

    $whereClause = implode(' AND ', $whereConditions);
    $query = "SELECT id, NIT, NombreCompleto, Apellido, Telefono, Direccion 
              FROM clientes WHERE $whereClause ORDER BY NombreCompleto LIMIT 50";

    $clientes = ejecutarSQL::consultar($query);
    
    if($clientes && mysqli_num_rows($clientes) > 0) {
        $response['success'] = true;
        while($cliente = mysqli_fetch_assoc($clientes)) {
            $response['clientes'][] = $cliente;
        }
    }
    
    if($clientes) mysqli_free_result($clientes);

} catch (Exception $e) {
    $response['message'] = "Error: " . $e->getMessage();
}

echo json_encode($response);
?>