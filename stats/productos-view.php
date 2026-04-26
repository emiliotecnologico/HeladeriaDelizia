<div class="panel panel-info">
    <div class="panel-heading text-center">
        <h4 style="font-size: 24px;"><i class="fa fa-cubes"></i> Top 10 Productos Más Vendidos - Hoy (<?php echo date('d/m/Y'); ?>)</h4>
    </div>
    <div class="panel-body">
        <?php if (!empty($productosMasVendidos)): ?>
            <!-- Gráfico de barras local -->
            <div class="chart-container">
                <h5 class="text-center" style="margin-bottom: 25px; color: #333; font-size: 22px;">
                    <i class="fa fa-chart-bar"></i> Distribución de Ventas por Producto
                </h5>
                
                <div class="simple-chart">
                    <?php
                    $maxValue = max(array_column($productosMasVendidos, 'total'));
                    foreach ($productosMasVendidos as $index => $producto):
                        $width = ($producto['total'] / $maxValue) * 100;
                        $totalVentas = $producto['total'] * $producto['PrecioProd'];
                        $porcentaje = ($producto['total'] / array_sum(array_column($productosMasVendidos, 'total'))) * 100;
                    ?>
                    <div class="chart-bar">
                        <div class="chart-label" title="<?php echo htmlspecialchars($producto['NombreProd']); ?>" style="font-size: 18px;">
                            <?php echo substr($producto['NombreProd'], 0, 25); ?><?php echo strlen($producto['NombreProd']) > 25 ? '...' : ''; ?>
                        </div>
                        <div class="chart-bar-container">
                            <div class="chart-bar-fill productos" data-width="<?php echo $width; ?>" style="width: 0%; font-size: 16px;">
                                <?php echo $producto['total']; ?> unidades
                            </div>
                        </div>
                        <div class="chart-value" style="font-size: 18px;">
                            <?php echo number_format($porcentaje, 1); ?>%
                        </div>
                    </div>
                    <div class="progress-stats">
                        <small style="font-size: 15px;">
                            <strong>Categoría:</strong> <?php echo htmlspecialchars($producto['Nombre']); ?> | 
                            <strong>Precio:</strong> Bs. <?php echo number_format($producto['PrecioProd'], 2); ?> | 
                            <strong>Total:</strong> Bs. <?php echo number_format($totalVentas, 2); ?>
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
                            <th style="font-size: 18px; padding: 15px;">Producto</th>
                            <th style="font-size: 18px; padding: 15px;">Categoría</th>
                            <th style="font-size: 18px; padding: 15px;">Unidades Vendidas</th>
                            <th style="font-size: 18px; padding: 15px;">Precio Unitario</th>
                            <th style="font-size: 18px; padding: 15px;">Total Generado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $contador = 1; ?>
                        <?php foreach($productosMasVendidos as $producto): ?>
                        <?php
                            // Manejo seguro del precio - usando PrecioProd que viene de la consulta
                            $precioUnitario = isset($producto['PrecioProd']) ? $producto['PrecioProd'] : 0;
                            $totalProducto = $precioUnitario * $producto['total'];
                        ?>
                        <tr>
                            <td style="font-size: 17px; padding: 15px;"><?php echo $contador++; ?></td>
                            <td style="font-size: 17px; padding: 15px;"><strong><?php echo htmlspecialchars($producto['NombreProd']); ?></strong></td>
                            <td style="font-size: 17px; padding: 15px;"><?php echo htmlspecialchars($producto['Nombre']); ?></td>
                            <td class="text-primary" style="text-align: center; font-size: 17px; padding: 15px;">
                                <strong><?php echo $producto['total']; ?></strong>
                            </td>
                            <td style="font-size: 17px; padding: 15px;">Bs. <?php echo number_format($precioUnitario, 2); ?></td>
                            <td class="text-success" style="font-size: 17px; padding: 15px;">
                                <strong>Bs <?php echo number_format($totalProducto, 2); ?></strong>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #f8f9fa;">
                            <td colspan="3" class="text-right" style="font-size: 18px; padding: 15px;"><strong>TOTALES:</strong></td>
                            <td class="text-center" style="font-size: 18px; padding: 15px;"><strong><?php echo array_sum(array_column($productosMasVendidos, 'total')); ?></strong></td>
                            <td></td>
                            <td class="text-success" style="font-size: 18px; padding: 15px;"><strong>Bs <?php 
                                $totalGeneral = 0;
                                foreach($productosMasVendidos as $producto) {
                                    $totalGeneral += $producto['total'] * $producto['PrecioProd'];
                                }
                                echo number_format($totalGeneral, 2);
                            ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
        <?php else: ?>
            <div class="no-data">
                <i class="fa fa-exclamation-circle fa-3x"></i>
                <h3 style="font-size: 28px;">No hay ventas de productos para hoy</h3>
                <p style="font-size: 18px;">No se han registrado ventas de productos para el día de hoy.</p>
            </div>
        <?php endif; ?>
    </div>
</div>