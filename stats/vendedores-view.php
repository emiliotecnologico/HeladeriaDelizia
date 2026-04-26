<div class="panel panel-info">
    <div class="panel-heading text-center">
        <h4 style="font-size: 24px;"><i class="fa fa-users"></i> Top 10 Vendedores con Más Ventas - Hoy (<?php echo date('d/m/Y'); ?>)</h4>
    </div>
    <div class="panel-body">
        <?php if (!empty($vendedoresMasVentas)): ?>
            <!-- Gráfico de barras local -->
            <div class="chart-container">
                <h5 class="text-center" style="margin-bottom: 25px; color: #333; font-size: 22px;">
                    <i class="fa fa-chart-bar"></i> Distribución de Ventas por Vendedor
                </h5>
                
                <div class="simple-chart">
                    <?php
                    $maxVentas = max(array_column($vendedoresMasVentas, 'total_ventas'));
                    $totalVentasGeneral = array_sum(array_column($vendedoresMasVentas, 'total_ventas'));
                    
                    foreach ($vendedoresMasVentas as $index => $vendedor):
                        $width = ($vendedor['total_ventas'] / $maxVentas) * 100;
                        $porcentajeVentas = ($vendedor['total_ventas'] / $totalVentasGeneral) * 100;
                        $porcentajeGanancias = ($vendedor['total_ganancias'] / array_sum(array_column($vendedoresMasVentas, 'total_ganancias'))) * 100;
                    ?>
                    <div class="chart-bar">
                        <div class="chart-label" title="<?php echo $vendedor['NombreCompleto']; ?>" style="font-size: 18px;">
                            <?php echo substr($vendedor['NombreCompleto'], 0, 25); ?><?php echo strlen($vendedor['NombreCompleto']) > 25 ? '...' : ''; ?>
                        </div>
                        <div class="chart-bar-container">
                            <div class="chart-bar-fill vendedores" data-width="<?php echo $width; ?>" style="width: 0%; font-size: 16px;">
                                <?php echo $vendedor['total_ventas']; ?> ventas
                            </div>
                        </div>
                        <div class="chart-value" style="font-size: 18px;">
                            <?php echo number_format($porcentajeVentas, 1); ?>%
                        </div>
                    </div>
                    <div class="progress-stats">
                        <small style="font-size: 15px;">
                            <strong>Ventas:</strong> <?php echo $vendedor['total_ventas']; ?> | 
                            <strong>Ganancias:</strong> Bs. <?php echo number_format($vendedor['total_ganancias'], 2); ?> |
                            <strong>Participación:</strong> <?php echo number_format($porcentajeGanancias, 1); ?>%
                        </small>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Tabla detallada -->
            <div class="table-responsive" style="margin-top: 25px;">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th style="font-size: 18px; padding: 15px;">#</th>
                            <th style="font-size: 18px; padding: 15px;">Vendedor</th>
                            <th style="font-size: 18px; padding: 15px;">Ventas Realizadas</th>
                            <th style="font-size: 18px; padding: 15px;">Total Generado</th>
                            <th style="font-size: 18px; padding: 15px;">Promedio por Venta</th>
                            <th style="font-size: 18px; padding: 15px;">Comisión Estimada (10%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $contador = 1; ?>
                        <?php foreach($vendedoresMasVentas as $vendedor): 
                            $promedioVenta = $vendedor['total_ventas'] > 0 ? $vendedor['total_ganancias'] / $vendedor['total_ventas'] : 0;
                        ?>
                        <tr>
                            <td style="font-size: 17px; padding: 15px;"><?php echo $contador++; ?></td>
                            <td style="font-size: 17px; padding: 15px;"><strong><?php echo $vendedor['NombreCompleto']; ?></strong></td>
                            <td class="text-primary" style="text-align: center; font-size: 17px; padding: 15px;">
                                <strong><?php echo $vendedor['total_ventas']; ?></strong>
                            </td>
                            <td class="text-success" style="font-size: 17px; padding: 15px;">
                                <strong>Bs <?php echo number_format($vendedor['total_ganancias'], 2); ?></strong>
                            </td>
                            <td class="text-info" style="font-size: 17px; padding: 15px;">
                                Bs <?php echo number_format($promedioVenta, 2); ?>
                            </td>
                            <td class="text-warning" style="font-size: 17px; padding: 15px;">
                                <strong>Bs <?php echo number_format($vendedor['total_ganancias'] * 0.10, 2); ?></strong>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #f8f9fa;">
                            <td colspan="2" class="text-right" style="font-size: 18px; padding: 15px;"><strong>TOTALES:</strong></td>
                            <td class="text-center" style="font-size: 18px; padding: 15px;"><strong><?php echo array_sum(array_column($vendedoresMasVentas, 'total_ventas')); ?></strong></td>
                            <td class="text-success" style="font-size: 18px; padding: 15px;"><strong>Bs <?php echo number_format(array_sum(array_column($vendedoresMasVentas, 'total_ganancias')), 2); ?></strong></td>
                            <td></td>
                            <td class="text-warning" style="font-size: 18px; padding: 15px;"><strong>Bs <?php echo number_format(array_sum(array_column($vendedoresMasVentas, 'total_ganancias')) * 0.10, 2); ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
        <?php else: ?>
            <div class="no-data">
                <i class="fa fa-exclamation-circle fa-3x"></i>
                <h3 style="font-size: 28px;">No hay ventas de vendedores para hoy</h3>
                <p style="font-size: 18px;">No se han registrado ventas por parte de vendedores para el día de hoy.</p>
            </div>
        <?php endif; ?>
    </div>
</div>