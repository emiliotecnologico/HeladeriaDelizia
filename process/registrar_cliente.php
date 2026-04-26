<?php
session_start();
include '../library/configServer.php';
include '../library/consulSQL.php';

// Headers para JSON
header('Content-Type: application/json');

$response = array('success' => false, 'message' => 'Error desconocido');

try {
    if(isset($_POST['nit']) && isset($_POST['nombre_completo']) && isset($_POST['apellido']) && isset($_POST['telefono'])) {
        $nit = consultasSQL::clean_string($_POST['nit']);
        $nombre_completo = consultasSQL::clean_string($_POST['nombre_completo']);
        $apellido = consultasSQL::clean_string($_POST['apellido']);
        $telefono = consultasSQL::clean_string($_POST['telefono']);
        $direccion = isset($_POST['direccion']) ? consultasSQL::clean_string($_POST['direccion']) : '';
        
        // Verificar si el cliente ya existe
        $verificar = ejecutarSQL::consultar("SELECT * FROM clientes WHERE NIT = '$nit'");
        if($verificar && mysqli_num_rows($verificar) > 0) {
            $response['message'] = "Ya existe un cliente con este C.I.";
            mysqli_free_result($verificar);
        } else {
            // Insertar nuevo cliente (sin el campo Nombre y Email)
            $registro = ejecutarSQL::consultar("INSERT INTO clientes (NIT, NombreCompleto, Apellido, Direccion, Telefono) 
                                               VALUES ('$nit', '$nombre_completo', '$apellido', '$direccion', '$telefono')");
            
            if($registro) {
                $response['success'] = true;
                $response['nit'] = $nit;
                $response['message'] = "Cliente registrado correctamente";
            } else {
                $response['message'] = "Error al registrar el cliente en la base de datos";
            }
        }
    } else {
        $response['message'] = "Datos incompletos. Faltan campos obligatorios.";
    }
} catch (Exception $e) {
    $response['message'] = "Error: " . $e->getMessage();
}

echo json_encode($response);
?>