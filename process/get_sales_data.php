<?php
include './library/configServer.php';
include './library/consulSQL.php';

$ventas = ejecutarSQL::consultar("SELECT * FROM venta");

$labels = [];
$data = [];
while ($venta = mysqli_fetch_array($ventas)) {
    $labels[] = $venta['Fecha'];
    $data[] = $venta['TotalPagar'];
}

// Realiza las consultas para obtener el número de clientes, proveedores y administradores
$numeroClientes = consultasSQL::obtenerNumeroClientes();
$numeroProveedores = consultasSQL::obtenerNumeroProveedores();
$numeroAdministradores = consultasSQL::obtenerNumeroAdministradores();

header('Content-Type: application/json');
echo json_encode([
    'labels' => $labels,
    'data' => [
        'ventas' => $data,
        'clientes' => array_fill(0, count($labels), $numeroClientes),
        'proveedores' => array_fill(0, count($labels), $numeroProveedores),
        'administradores' => array_fill(0, count($labels), $numeroAdministradores)
    ]
]);
?>
