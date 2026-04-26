<?php
session_start(); 
// Verificar que sea administrador
if (empty($_SESSION['nombreAdmin'])) {
    header("Location: index.php");
    exit();
}

include './library/configServer.php';
include './library/consulSQL.php';

// Obtener la fecha actual y las fechas de los últimos 7 días para referencia
$fecha_actual = date('Y-m-d');
$fecha_ayer = date('Y-m-d', strtotime('-1 day'));
$fecha_inicio_semana = date('Y-m-d', strtotime('-6 days'));

// Realiza todas las consultas aquí para pasarlas a las vistas

// CONSULTA 1: Productos con más Ventas POR DÍA (hoy) - EXCLUYENDO CANCELADOS
$consultaProductosMasVendidos = ejecutarSQL::consultar("
    SELECT producto.NombreProd, categoria.Nombre, SUM(detalle.CantidadProductos) as total,
           producto.Precio as PrecioProd
    FROM detalle 
    JOIN producto ON detalle.CodigoProd = producto.CodigoProd 
    JOIN categoria ON producto.CodigoCat = categoria.CodigoCat 
    JOIN venta ON detalle.NumPedido = venta.NumPedido
    WHERE venta.Fecha = '$fecha_actual' 
    AND venta.Estado != 'cancelado'
    GROUP BY producto.CodigoProd, producto.NombreProd, categoria.Nombre, producto.Precio
    ORDER BY total DESC 
    LIMIT 10
");
$productosMasVendidos = array();
while ($fila = mysqli_fetch_array($consultaProductosMasVendidos)) {
    $productosMasVendidos[] = $fila;
}

// CONSULTA 2: Vendedores con más Ventas POR DÍA (hoy) - EXCLUYENDO CANCELADOS
$consultaVendedoresMasVentas = ejecutarSQL::consultar("
    SELECT CONCAT(vendedores.NombreCompleto, ' ', vendedores.Apellido) as NombreCompleto, 
           COUNT(venta.NumPedido) as total_ventas,
           SUM(venta.TotalPagar) as total_ganancias
    FROM venta 
    JOIN vendedores ON venta.id_vendedor = vendedores.NIT
    WHERE venta.Fecha = '$fecha_actual'
    AND venta.Estado != 'cancelado'
    GROUP BY vendedores.NIT, vendedores.NombreCompleto, vendedores.Apellido 
    ORDER BY total_ventas DESC, total_ganancias DESC 
    LIMIT 10
");
$vendedoresMasVentas = array();
while ($fila = mysqli_fetch_array($consultaVendedoresMasVentas)) {
    $vendedoresMasVentas[] = $fila;
}

// CONSULTA 3: Ganancias generadas POR DÍA (últimos 7 días) - EXCLUYENDO CANCELADOS
$consultaGananciasDiarias = ejecutarSQL::consultar("
    SELECT Fecha, SUM(TotalPagar) as ganancia_diaria
    FROM venta 
    WHERE Fecha BETWEEN '$fecha_inicio_semana' AND '$fecha_actual'
    AND Estado != 'cancelado'
    GROUP BY Fecha 
    ORDER BY Fecha ASC
");
$gananciasDiarias = array();
$fechasGanancias = array();
$valoresGanancias = array();
while ($fila = mysqli_fetch_array($consultaGananciasDiarias)) {
    $gananciasDiarias[] = $fila;
    $fechasGanancias[] = date('d/m', strtotime($fila['Fecha']));
    $valoresGanancias[] = floatval($fila['ganancia_diaria']);
}

// CONSULTA ADICIONAL: Estadísticas rápidas del día
$statsHoy = ejecutarSQL::consultar("
    SELECT 
        COUNT(*) as total_pedidos,
        SUM(TotalPagar) as total_ganancias,
        AVG(TotalPagar) as promedio_venta
    FROM venta 
    WHERE Fecha = '$fecha_actual' 
    AND Estado != 'cancelado'
");
$datosStats = mysqli_fetch_array($statsHoy, MYSQLI_ASSOC);

// Si no hay datos de ganancias para hoy, agregar un valor cero para mostrar
if (empty($gananciasDiarias)) {
    $fechasGanancias[] = date('d/m');
    $valoresGanancias[] = 0;
}

$view = isset($_GET['view']) ? $_GET['view'] : 'productos';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Estadísticas Diarias</title>
    <?php include './inc/link.php'; ?>
    
    <style>
        .panel-body {
            padding: 20px;
            overflow: hidden;
            font-size: 17px;
        }
        
        .stats-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            text-align: center;
        }
        
        .stats-header h3 {
            font-size: 32px;
            margin-bottom: 10px;
        }
        
        .stats-header p.lead {
            font-size: 20px;
            margin-bottom: 0;
        }
        
        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.12);
            border-left: 5px solid #667eea;
            text-align: center;
        }
        
        .stats-card h4 {
            font-size: 18px;
            margin-bottom: 12px;
            color: #6c757d;
        }
        
        .stats-card h3 {
            font-size: 32px;
            margin: 0;
            font-weight: bold;
        }
        
        .stats-card small {
            font-size: 14px;
            color: #6c757d;
        }
        
        .quick-stats {
            margin-bottom: 35px;
        }
        
        .quick-stats .col-md-4 {
            margin-bottom: 20px;
        }
        
        .date-badge {
            background: #28a745;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 16px;
            margin-left: 12px;
        }
        
        .no-data {
            text-align: center;
            padding: 50px;
            color: #6c757d;
            font-style: italic;
            background: #f8f9fa;
            border-radius: 12px;
            margin: 25px 0;
        }
        
        .no-data h3 {
            font-size: 28px;
            margin-bottom: 15px;
        }
        
        .no-data p {
            font-size: 18px;
        }
        
        .label {
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 16px;
            font-weight: bold;
        }
        
        /* ESTILOS ESPECÍFICOS PARA CENTRAR EL NAVBAR EN ESTADÍSTICAS */
        .navbar-bottom {
            text-align: center !important;
        }
        
        .navbar-bottom .contenedor-tabla {
            margin: 0 auto !important;
            display: table !important;
            width: auto !important;
        }
        
        .navbar-bottom .contenedor-tr {
            display: table-row !important;
            text-align: center !important;
        }
        
        .navbar-bottom .table-cell-td {
            text-align: center !important;
            display: table-cell !important;
            padding: 0 30px !important;
            font-size: 20px !important;
        }
        
        /* Asegurar que el contenido no quede detrás de la barra fija */
        #container-pedido {
            padding-top: 25px;
        }

        .print-btn-container {
            margin: 25px 0;
            text-align: center;
        }
        
        .print-btn-container .btn {
            font-size: 20px;
            padding: 15px 30px;
        }
        
        .stats-info {
            background: #e7f3ff;
            border-left: 5px solid #007bff;
            padding: 20px;
            margin-bottom: 25px;
            border-radius: 8px;
            font-size: 18px;
        }

        /* Estilos para gráficos alternativos - BARRAS SIMPLES AUMENTADAS */
        .simple-chart {
            background: #f8f9fa;
            border: 2px solid #dee2e6;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
        }
        
        .chart-bar {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.12);
        }
        
        .chart-label {
            width: 250px;
            font-weight: bold;
            margin-right: 20px;
            font-size: 18px;
        }
        
        .chart-bar-container {
            flex-grow: 1;
            background: #e9ecef;
            border-radius: 8px;
            overflow: hidden;
            position: relative;
        }
        
        .chart-bar-fill {
            background: linear-gradient(90deg, #007bff, #0056b3);
            height: 35px;
            border-radius: 8px;
            transition: width 0.5s ease;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding-right: 15px;
            color: white;
            font-weight: bold;
            font-size: 16px;
        }
        
        .chart-value {
            min-width: 100px;
            text-align: right;
            font-weight: bold;
            margin-left: 20px;
            font-size: 18px;
        }
        
        .progress-stats {
            margin-top: 12px;
            font-size: 14px;
            color: #6c757d;
        }
        
        /* Colores para diferentes categorías */
        .chart-bar-fill.productos { background: linear-gradient(90deg, #28a745, #1e7e34); }
        .chart-bar-fill.vendedores { background: linear-gradient(90deg, #ffc107, #e0a800); }
        .chart-bar-fill.ganancias { background: linear-gradient(90deg, #dc3545, #c82333); }
        
        /* Tabla de ganancias diarias - AUMENTADA */
        .ganancias-table {
            width: 100%;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.12);
        }
        
        .ganancias-table th {
            background: #343a40;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
        }
        
        .ganancias-table td {
            padding: 15px;
            text-align: center;
            border-bottom: 2px solid #dee2e6;
            font-size: 17px;
        }
        
        .ganancias-table tr:hover {
            background: #f8f9fa;
        }
        
        .total-row {
            background: #e7f3ff !important;
            font-weight: bold;
        }

        .chart-container {
            background: #f8f9fa;
            border: 2px solid #dee2e6;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
        }
        
        .chart-container h5 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        /* Tabs de navegación - AUMENTADAS */
        .nav-tabs > li > a {
            font-size: 18px;
            padding: 15px 20px;
        }
        
        .page-header h1 {
            font-size: 36px;
        }
        
        .page-header h1 img {
            height: 60px;
            margin-bottom: 15px;
        }

        /* Tablas generales - AUMENTADAS */
        .table th {
            font-size: 18px;
            padding: 15px;
        }
        
        .table td {
            font-size: 17px;
            padding: 15px;
        }
        
        .pagination > li > a {
            font-size: 17px;
            padding: 12px 18px;
        }

        /* Ajustes responsivos */
        @media (max-width: 768px) {
            .stats-header h3 {
                font-size: 28px;
            }
            
            .stats-header p.lead {
                font-size: 18px;
            }
            
            .stats-card h3 {
                font-size: 28px;
            }
            
            .chart-label {
                width: 180px;
                font-size: 16px;
            }
            
            .chart-value {
                font-size: 16px;
                min-width: 80px;
            }
            
            .nav-tabs > li > a {
                font-size: 16px;
                padding: 12px 15px;
            }
            
            .page-header h1 {
                font-size: 30px;
            }
        }
        
        @media (max-width: 480px) {
            .stats-header {
                padding: 15px;
            }
            
            .stats-header h3 {
                font-size: 24px;
            }
            
            .stats-card {
                padding: 20px;
            }
            
            .stats-card h3 {
                font-size: 24px;
            }
            
            .chart-bar {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .chart-label {
                width: 100%;
                margin-bottom: 10px;
            }
            
            .chart-bar-container {
                width: 100%;
            }
        }
    </style>
</head>
<body id="container-page-index">
    <?php include './inc/navbar.php'; ?>
    <section id="container-pedido">
        <div class="container">
            <div class="page-header">
              <h1>Estadísticas Diarias de Ventas <img src="assets/img/black-delizia1.jpg" alt="Logo Delizia" style="height: 60px; margin-bottom: 15px;"></h1>
            </div>
            
            <!-- Encabezado de estadísticas diarias -->
            <div class="stats-header">
                <h3><i class="fa fa-calendar"></i> Reporte del Día: <?php echo date('d/m/Y'); ?></h3>
                <p class="lead">Estadísticas actualizadas en tiempo real - Sistema 100% local</p>
            </div>
            
            <!-- Estadísticas rápidas del día -->
            <div class="quick-stats">
                <div class="row">
                    <div class="col-md-4">
                        <div class="stats-card">
                            <h4>Total Pedidos Hoy</h4>
                            <h3><?php echo $datosStats['total_pedidos'] ?? 0; ?></h3>
                            <small>Excluyendo cancelados</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card">
                            <h4>Ganancias Totales</h4>
                            <h3>Bs. <?php echo number_format($datosStats['total_ganancias'] ?? 0, 2); ?></h3>
                            <small>Ventas del día</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card">
                            <h4>Promedio por Venta</h4>
                            <h3>Bs. <?php echo number_format($datosStats['promedio_venta'] ?? 0, 2); ?></h3>
                            <small>Por pedido</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información sobre filtros -->
            <div class="stats-info">
                <i class="fa fa-info-circle"></i> 
                <strong>Sistema 100% local:</strong> Todas las estadísticas se generan desde tu base de datos local. 
                No se requiere conexión a internet. Los datos excluyen pedidos cancelados.
            </div>

            <br><br>

            <!-- Botón de Imprimir PDF -->
            <div class="print-btn-container">
                <button class="btn btn-success btn-raised btn-lg" onclick="generatePDF()">
                    <i class="fa fa-print"></i> Imprimir Reporte Diario (PDF)
                </button>
            </div>

            <div class="row">
                <div class="col-xs-12">
                    <!--====  Nav Tabs  ====-->
                    <ul class="nav nav-tabs nav-justified" style="margin-bottom: 20px;">
                        <li class="<?php echo ($view == 'productos' || !isset($view)) ? 'active' : ''; ?>">
                            <a href="sales-stats.php?view=productos">
                                <i class="fa fa-cubes"></i> Productos más vendidos
                                <span class="date-badge">Hoy</span>
                            </a>
                        </li>
                        <li class="<?php echo ($view == 'vendedores') ? 'active' : ''; ?>">
                            <a href="sales-stats.php?view=vendedores">
                                <i class="fa fa-users"></i> Vendedores con más ventas
                                <span class="date-badge">Hoy</span>
                            </a>
                        </li>
                        <li class="<?php echo ($view == 'ganancias') ? 'active' : ''; ?>">
                            <a href="sales-stats.php?view=ganancias">
                                <i class="fa fa-chart-line"></i> Ganancias generadas
                                <span class="date-badge">7 días</span>
                            </a>
                        </li>
                    </ul>
                    
                    <?php
                    $whiteList = ['productos', 'vendedores', 'ganancias'];
                    
                    if (in_array($view, $whiteList)) {
                        include "./stats/{$view}-view.php";
                    } else {
                        echo '<h2 class="text-center" style="font-size: 28px; padding: 40px 0;">Lo sentimos, la opción que ha seleccionado no se encuentra disponible</h2>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </section>
    <?php include './inc/footer.php'; ?>

    <script>
    // Función para generar PDF
    function generatePDF() {
        // Ruta correcta: report/reporte.php
        window.open('report/reporte.php', '_blank');
    }

    // Función para animar barras de progreso
    function animateBars() {
        $('.chart-bar-fill').each(function() {
            var width = $(this).data('width');
            $(this).css('width', '0%');
            
            setTimeout(() => {
                $(this).animate({
                    width: width + '%'
                }, 800);
            }, 200);
        });
    }

    // Inicializar cuando el DOM esté listo
    $(document).ready(function(){
        // Animar barras al cargar la página
        setTimeout(animateBars, 500);
        
        // Re-animar barras cuando se cambie de pestaña
        $('a[href*="view="]').on('click', function() {
            setTimeout(animateBars, 800);
        });

        // Script adicional para forzar el centrado del navbar en estadísticas
        function centerNavbar() {
            $('.navbar-bottom .contenedor-tabla').css({
                'margin': '0 auto',
                'display': 'table',
                'width': 'auto'
            });
        }
        
        centerNavbar();
        setTimeout(centerNavbar, 100);
        $(window).on('load', centerNavbar);
    });
    </script>
</body>
</html>