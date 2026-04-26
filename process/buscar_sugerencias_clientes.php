<?php
session_start();
include '../library/configServer.php';
include '../library/consulSQL.php';

header('Content-Type: application/json');

$response = array('success' => false, 'clientes' => array());

try {
    if(isset($_GET['termino']) && !empty($_GET['termino'])) {
        $termino = consultasSQL::clean_string($_GET['termino']);
        
        // Buscar por NIT o nombre
        $clientes = ejecutarSQL::consultar("SELECT * FROM clientes 
                                          WHERE (NIT LIKE '%$termino%' OR NombreCompleto LIKE '%$termino%' OR Apellido LIKE '%$termino%') 
                                          AND Estado='activo' 
                                          ORDER BY NombreCompleto 
                                          LIMIT 10");
        
        if($clientes && mysqli_num_rows($clientes) > 0) {
            while($cliente = mysqli_fetch_array($clientes, MYSQLI_ASSOC)) {
                $response['clientes'][] = $cliente;
            }
            $response['success'] = true;
            mysqli_free_result($clientes);
        } else {
            $response['success'] = true; // Éxito pero sin resultados
        }
    }
} catch (Exception $e) {
    $response['message'] = "Error: " . $e->getMessage();
}

echo json_encode($response);
?>