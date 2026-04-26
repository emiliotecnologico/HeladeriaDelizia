<?php
session_start();

// Verificar si la sesión está activa y el usuario tiene permisos
if (!isset($_SESSION['nombreAdmin']) || empty($_SESSION['nombreAdmin'])) {
    die("Acceso denegado. Debe iniciar sesión como administrador.");
}

// Limpiar buffers
while (ob_get_level()) {
    ob_end_clean();
}

ob_start();

try {
    // Incluir TCPDF - RUTA CORREGIDA (sube un nivel desde report/)
    require_once('../tcpdf/tcpdf.php');
    
    // Incluir configuración y consultas - RUTAS CORREGIDAS
    include '../library/configServer.php';
    include '../library/consulSQL.php';

    // Obtener la fecha actual y las fechas de los últimos 7 días para referencia
    $fecha_actual = date('Y-m-d');
    $fecha_inicio_semana = date('Y-m-d', strtotime('-6 days'));

    // CONSULTA 1: Productos con más Ventas POR DÍA (hoy)
    $consultaProductosMasVendidos = ejecutarSQL::consultar("
        SELECT producto.NombreProd, categoria.Nombre, SUM(detalle.CantidadProductos) as total,
               producto.Precio as PrecioProd
        FROM detalle 
        JOIN producto ON detalle.CodigoProd = producto.CodigoProd 
        JOIN categoria ON producto.CodigoCat = categoria.CodigoCat 
        JOIN venta ON detalle.NumPedido = venta.NumPedido
        WHERE venta.Fecha = '$fecha_actual'
        GROUP BY producto.CodigoProd, producto.NombreProd, categoria.Nombre, producto.Precio
        ORDER BY total DESC 
        LIMIT 10
    ");
    $productosMasVendidos = array();
    while ($fila = mysqli_fetch_array($consultaProductosMasVendidos)) {
        $productosMasVendidos[] = $fila;
    }

    // CONSULTA 2: Vendedores con más Ventas POR DÍA (hoy)
    $consultaVendedoresMasVentas = ejecutarSQL::consultar("
        SELECT CONCAT(vendedores.NombreCompleto, ' ', vendedores.Apellido) as NombreCompleto, 
               COUNT(venta.NumPedido) as total_ventas,
               SUM(venta.TotalPagar) as total_ganancias
        FROM venta 
        JOIN vendedores ON venta.id_vendedor = vendedores.NIT
        WHERE venta.Fecha = '$fecha_actual'
        GROUP BY vendedores.NIT, vendedores.NombreCompleto, vendedores.Apellido 
        ORDER BY total_ventas DESC, total_ganancias DESC 
        LIMIT 10
    ");
    $vendedoresMasVentas = array();
    while ($fila = mysqli_fetch_array($consultaVendedoresMasVentas)) {
        $vendedoresMasVentas[] = $fila;
    }

    // CONSULTA 3: Ganancias generadas POR DÍA (últimos 7 días)
    $consultaGananciasDiarias = ejecutarSQL::consultar("
        SELECT Fecha, SUM(TotalPagar) as ganancia_diaria
        FROM venta 
        WHERE Fecha BETWEEN '$fecha_inicio_semana' AND '$fecha_actual'
        AND Estado != 'cancelado'
        GROUP BY Fecha 
        ORDER BY Fecha ASC
    ");
    $gananciasDiarias = array();
    while ($fila = mysqli_fetch_array($consultaGananciasDiarias)) {
        $gananciasDiarias[] = $fila;
    }

    // Crear clase personalizada para el PDF
    class MYPDF extends TCPDF {
        // Page header
        public function Header() {
            // Logo - RUTA CORREGIDA (sube un nivel desde report/)
            $image_file = '../assets/img/logo_delizia-1.png';
            if (file_exists($image_file)) {
                $this->Image($image_file, 15, 10, 25, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
            }
            
            // Set font
            $this->SetFont('helvetica', 'B', 14);
            // Title
            $this->SetTextColor(0, 150, 200);
            $this->SetY(12);
            $this->Cell(0, 10, 'Heladería Delizia', 0, false, 'C', 0, '', 0, false, 'M', 'M');
            
            // Subtitle
            $this->SetFont('helvetica', 'B', 10);
            $this->SetY(20);
            $this->Cell(0, 10, 'Tienda Oruro-Central', 0, false, 'C', 0, '', 0, false, 'M', 'M');
            
            // Report date
            $this->SetFont('helvetica', 'I', 9);
            $this->SetY(25);
            $this->Cell(0, 10, 'Reporte del ' . date('d/m/Y'), 0, false, 'C', 0, '', 0, false, 'M', 'M');
            
            // Blue line
            $this->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 150, 200)));
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

    // Crear PDF con clase personalizada
    $pdf = new MYPDF('P', 'mm', 'LETTER', true, 'UTF-8', false);
    
    // Configurar documento
    $pdf->SetCreator('Heladeria Delizia');
    $pdf->SetAuthor('Sistema Delizia');
    $pdf->SetTitle('Reporte de Estadísticas Diarias');
    $pdf->SetSubject('Reporte de Ventas Diarias');
    
    // Configurar márgenes
    $pdf->SetMargins(15, 40, 15);
    $pdf->SetHeaderMargin(10);
    $pdf->SetFooterMargin(10);
    $pdf->SetAutoPageBreak(TRUE, 25);
    
    // Agregar marca de agua en cada página
    $pdf->SetAlpha(0.1);
    $watermark = '../assets/img/logo_delizia-2.png';
    if (file_exists($watermark)) {
        $pdf->Image($watermark, 50, 100, 110, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
    }
    $pdf->SetAlpha(1);
    
    // Agregar página
    $pdf->AddPage();
    
    // Contenido del PDF
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
    </style>
    
    <div class="titulo">REPORTE DIARIO DE ESTADÍSTICAS</div>
    <div class="centrado">Fecha del Reporte: ' . date('d/m/Y') . '</div>
    <br>
    ';
    
    // Resumen del día
    $totalVentasHoy = 0;
    $totalProductosVendidos = 0;
    
    foreach($productosMasVendidos as $producto) {
        $totalProductosVendidos += $producto['total'];
    }
    
    foreach($gananciasDiarias as $ganancia) {
        if ($ganancia['Fecha'] == $fecha_actual) {
            $totalVentasHoy = $ganancia['ganancia_diaria'];
        }
    }
    
    $html .= '
    <div class="resumen">
        <strong>RESUMEN DEL DÍA:</strong><br>
        - Total Productos Vendidos Hoy: ' . $totalProductosVendidos . ' unidades<br>
        - Total Ventas Hoy: Bs ' . number_format($totalVentasHoy, 2) . '<br>
        - Vendedores Activos: ' . count($vendedoresMasVentas) . '
    </div>
    ';
    
    // Productos más vendidos HOY
    $html .= '<div class="subtitulo">TOP 10 PRODUCTOS MÁS VENDIDOS - HOY</div>';
    $html .= '<table class="tabla">';
    $html .= '<tr><th width="50%">Producto (Categoría)</th><th width="20%">Precio Unit.</th><th width="15%">Cantidad</th><th width="15%">Total</th></tr>';
    
    if (!empty($productosMasVendidos)) {
        foreach($productosMasVendidos as $producto) {
            $total = $producto['total'] * $producto['PrecioProd'];
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($producto['NombreProd'] . ' (' . $producto['Nombre'] . ')') . '</td>';
            $html .= '<td class="centrado">Bs ' . number_format($producto['PrecioProd'], 2) . '</td>';
            $html .= '<td class="centrado">' . $producto['total'] . '</td>';
            $html .= '<td class="centrado">Bs ' . number_format($total, 2) . '</td>';
            $html .= '</tr>';
        }
    } else {
        $html .= '<tr><td colspan="4" class="centrado">No hay ventas registradas para hoy</td></tr>';
    }
    $html .= '</table><br>';
    
    // Vendedores con más ventas HOY
    $html .= '<div class="subtitulo">TOP 10 VENDEDORES CON MÁS VENTAS - HOY</div>';
    $html .= '<table class="tabla">';
    $html .= '<tr><th width="60%">Vendedor</th><th width="20%">Ventas Realizadas</th><th width="20%">Total Vendido</th></tr>';
    
    if (!empty($vendedoresMasVentas)) {
        foreach($vendedoresMasVentas as $vendedor) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($vendedor['NombreCompleto']) . '</td>';
            $html .= '<td class="centrado">' . $vendedor['total_ventas'] . '</td>';
            $html .= '<td class="centrado">Bs ' . number_format($vendedor['total_ganancias'], 2) . '</td>';
            $html .= '</tr>';
        }
    } else {
        $html .= '<tr><td colspan="3" class="centrado">No hay ventas registradas para hoy</td></tr>';
    }
    $html .= '</table><br>';
    
    // Ganancias de los últimos 7 días
    $html .= '<div class="subtitulo">GANANCIAS DIARIAS (ÚLTIMOS 7 DÍAS)</div>';
    $html .= '<table class="tabla">';
    $html .= '<tr><th width="50%">Fecha</th><th width="50%">Ganancias del Día</th></tr>';
    
    if (!empty($gananciasDiarias)) {
        foreach($gananciasDiarias as $ganancia) {
            $html .= '<tr>';
            $html .= '<td>' . date('d/m/Y', strtotime($ganancia['Fecha'])) . '</td>';
            $html .= '<td class="centrado">Bs ' . number_format($ganancia['ganancia_diaria'], 2) . '</td>';
            $html .= '</tr>';
        }
    } else {
        $html .= '<tr><td colspan="2" class="centrado">No hay datos de ganancias disponibles</td></tr>';
    }
    $html .= '</table>';
    
    // Escribir el contenido HTML
    $pdf->writeHTML($html, true, false, true, false, '');
    
    // Limpiar buffer y enviar PDF
    ob_end_clean();
    
    // Enviar al navegador
    $pdf->Output('reporte_diario_' . date('Y-m-d') . '.pdf', 'I');
    exit();

} catch (Exception $e) {
    ob_end_clean();
    header('Content-Type: text/html; charset=utf-8');
    echo "<h2>Error al generar el PDF</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    exit();
}
?>