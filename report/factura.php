<?php
session_start();
require_once '../tcpdf/tcpdf.php';
include '../library/configServer.php';
include '../library/consulSQL.php';

// Limpiar buffers al inicio
while (ob_get_level()) {
    ob_end_clean();
}

$id = $_GET['id'];

// Verificar que el ID sea válido
if (empty($id)) {
    die("Error: No se proporcionó un número de pedido válido.");
}

// Consultar la venta
$sVenta = ejecutarSQL::consultar("SELECT * FROM venta WHERE NumPedido='$id'");
if (!$sVenta) {
    die("Error: No se pudo encontrar la venta con el número de pedido: $id");
}

$dVenta = mysqli_fetch_array($sVenta, MYSQLI_ASSOC);
if (!$dVenta) {
    die("Error: No se encontraron datos para la venta #$id");
}

// Consultar el cliente
$sCliente = ejecutarSQL::consultar("SELECT * FROM clientes WHERE id='".$dVenta['id_cliente']."'");
if (!$sCliente) {
    die("Error: No se pudo encontrar el cliente asociado a esta venta.");
}

$dCliente = mysqli_fetch_array($sCliente, MYSQLI_ASSOC);
if (!$dCliente) {
    die("Error: No se encontraron datos del cliente.");
}

// Obtener detalles del pedido
$detalles = array();
$sDet = ejecutarSQL::consultar("SELECT * FROM detalle WHERE NumPedido='".$id."'");
if ($sDet) {
    while($fila1 = mysqli_fetch_array($sDet, MYSQLI_ASSOC)) {
        $consulta = ejecutarSQL::consultar("SELECT * FROM producto WHERE CodigoProd='".$fila1['CodigoProd']."'");
        if ($consulta) {
            $fila = mysqli_fetch_array($consulta, MYSQLI_ASSOC);
            
            $detalle = array(
                'NombreProd' => $fila['NombreProd'],
                'PrecioProd' => $fila1['PrecioProd'],
                'CantidadProductos' => $fila1['CantidadProductos'],
                'Subtotal' => $fila1['PrecioProd'] * $fila1['CantidadProductos']
            );
            
            $detalles[] = $detalle;
            mysqli_free_result($consulta);
        }
    }
    mysqli_free_result($sDet);
}

// Crear clase personalizada para el PDF (ESTILO IGUAL A REPORTE.PHP)
class MYPDF extends TCPDF {
    // Page header
    public function Header() {
        // Logo - RUTA CORREGIDA (sube un nivel desde report/)
        $image_file = '../assets/img/black-delizia1.jpg';
        if (file_exists($image_file)) {
            $this->Image($image_file, 15, 10, 25, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        }
        
        // Set font
        $this->SetFont('helvetica', 'B', 14);
        // Title
        $this->SetTextColor(0, 0, 0);
        $this->SetY(12);
        $this->Cell(0, 10, 'Heladería Delizia', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        
        // Subtitle
        $this->SetFont('helvetica', 'B', 10);
        $this->SetY(20);
        $this->Cell(0, 10, 'Oruro, Bolivia', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        
        // Report date/info
        $this->SetFont('helvetica', 'I', 9);
        $this->SetY(25);
        $this->Cell(0, 10, 'Factura de Venta', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        
        // Line
        $this->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        $this->Line(15, 32, 195, 32);
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Página '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

$pdf = new MYPDF('P', 'mm', 'LETTER', true, 'UTF-8', false);

// Configurar documento
$pdf->SetCreator('Heladeria Delizia');
$pdf->SetAuthor('Sistema Delizia');
$pdf->SetTitle('Factura #' . $id);
$pdf->SetSubject('Factura de Pedido');

// Configurar márgenes (IGUAL QUE REPORTE.PHP)
$pdf->SetMargins(15, 40, 15);
$pdf->SetHeaderMargin(10);
$pdf->SetFooterMargin(10);
$pdf->SetAutoPageBreak(TRUE, 25);

// Agregar página
$pdf->AddPage();

// HTML para la factura (ESTILO IGUAL QUE REPORTE.PHP)
$html = '
<style>
    .titulo { 
        font-family: helvetica; 
        font-size: 16px; 
        font-weight: bold; 
        color: #006699; 
        text-align: center; 
        margin-bottom: 10px;
    }
    .subtitulo { 
        font-family: helvetica; 
        font-size: 14px; 
        font-weight: bold; 
        color: #006699; 
        margin: 15px 0 10px 0;
        border-bottom: 1px solid #006699;
        padding-bottom: 5px;
    }
    .tabla {
        width: 100%;
        border-collapse: collapse;
        margin: 10px 0;
        font-size: 10px;
    }
    .tabla th {
        background-color: #c8e6ff;
        color: #006699;
        font-weight: bold;
        padding: 6px;
        border: 1px solid #006699;
        text-align: center;
    }
    .tabla td {
        padding: 5px;
        border: 1px solid #666666;
        text-align: left;
    }
    .centrado {
        text-align: center;
    }
    .derecha {
        text-align: right;
    }
    .resumen {
        background: #f8f9fa;
        padding: 10px;
        border-radius: 5px;
        margin: 10px 0;
        border-left: 4px solid #28a745;
    }
    .info-cliente {
        font-family: helvetica;
        font-size: 11px;
        margin: 3px 0;
    }
    .label {
        font-weight: bold;
        color: #006699;
    }
    .total-row {
        background-color: #c8e6ff;
        font-weight: bold;
        color: #006699;
    }
</style>

<div class="titulo">FACTURA DE VENTA #' . $id . '</div>
<div class="centrado">Fecha: ' . $dVenta['Fecha'] . '</div>
<br>

<div style="border: 1px solid #006699; padding: 10px; margin-bottom: 15px;">
    <div class="subtitulo">INFORMACIÓN DEL CLIENTE</div>
    <div class="info-cliente">
        <span class="label">Nombre:</span> ' . htmlspecialchars($dCliente['NombreCompleto'] . " " . $dCliente['Apellido']) . '
    </div>
    <div class="info-cliente">
        <span class="label">NIT/CI:</span> ' . $dCliente['NIT'] . '
    </div>
    <div class="info-cliente">
        <span class="label">Dirección:</span> ' . htmlspecialchars($dCliente['Direccion']) . '
    </div>
    <div class="info-cliente">
        <span class="label">Teléfono:</span> ' . $dCliente['Telefono'] . '
    </div>
</div>

<div class="subtitulo">DETALLE DEL PEDIDO</div>
<table class="tabla">
    <tr>
        <th width="50%">Producto</th>
        <th width="15%">Precio Unit.</th>
        <th width="15%">Cantidad</th>
        <th width="20%">Subtotal</th>
    </tr>';

// Agregar filas de productos
$suma = 0;
if (is_array($detalles) && !empty($detalles)) {
    foreach ($detalles as $detalle) {
        $html .= '
        <tr>
            <td>' . htmlspecialchars($detalle['NombreProd']) . '</td>
            <td class="centrado">Bs ' . number_format($detalle['PrecioProd'], 2) . '</td>
            <td class="centrado">' . $detalle['CantidadProductos'] . '</td>
            <td class="centrado">Bs ' . number_format($detalle['Subtotal'], 2) . '</td>
        </tr>';
        $suma += $detalle['Subtotal'];
    }
} else {
    $html .= '
    <tr>
        <td colspan="4" class="centrado">No hay productos en este pedido</td>
    </tr>';
}

// Fila total
$html .= '
    <tr class="total-row">
        <td colspan="3" class="derecha" style="padding-right: 10px;">TOTAL:</td>
        <td class="centrado"><strong>Bs ' . number_format($suma, 2) . '</strong></td>
    </tr>
</table>';

// Información adicional del pedido
$html .= '
<div class="resumen">
    <strong>INFORMACIÓN ADICIONAL DEL PEDIDO:</strong><br>
    - Estado del pedido: ' . ucfirst($dVenta['Estado']) . '<br>
    - Tipo de envío: ' . $dVenta['TipoEnvio'] . '<br>';
    
if (!empty($dVenta['NumDeposito'])) {
    $html .= '- Número de depósito: ' . $dVenta['NumDeposito'] . '<br>';
}

$html .= '</div>

<div style="margin-top: 30px; text-align: center; font-family: helvetica; font-size: 9px; color: #666666;">
    <p>¡Gracias por su compra en Heladería Delizia!</p>
    <p>Oruro, Bolivia - ' . date('d/m/Y H:i:s') . '</p>
</div>';

// Escribir el contenido HTML
$pdf->writeHTML($html, true, false, true, false, '');

// Enviar al navegador
$pdf->Output('factura_' . $id . '.pdf', 'I');

// Liberar recursos
mysqli_free_result($sVenta);
mysqli_free_result($sCliente);
exit();
?>