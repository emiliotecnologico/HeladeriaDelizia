<div class="panel panel-info">
    <div class="panel-heading text-center">
        <h4 style="font-size: 24px;"><i class="fa fa-chart-line"></i> Ganancias Generadas - Últimos 7 Días</h4>
    </div>
    <div class="panel-body">
        <?php if (!empty($gananciasDiarias)): ?>
            <!-- Gráfico de barras local -->
            <div class="chart-container">
                <h5 class="text-center" style="margin-bottom: 25px; color: #333; font-size: 22px;">
                    <i class="fa fa-chart-bar"></i> Evolución de Ganancias Diarias
                </h5>
                
                <div class="simple-chart">
                    <?php
                    $maxGanancia = max(array_column($gananciasDiarias, 'ganancia_diaria'));
                    $totalGanancias = array_sum(array_column($gananciasDiarias, 'ganancia_diaria'));
                    
                    foreach ($gananciasDiarias as $index => $ganancia):
                        $width = ($ganancia['ganancia_diaria'] / $maxGanancia) * 100;
                        $fechaFormateada = date('d/m/Y', strtotime($ganancia['Fecha']));
                        $porcentaje = ($ganancia['ganancia_diaria'] / $totalGanancias) * 100;
                    ?>
                    <div class="chart-bar">
                        <div class="chart-label" style="font-size: 18px;">
                            <?php echo $fechaFormateada; ?>
                        </div>
                        <div class="chart-bar-container">
                            <div class="chart-bar-fill ganancias" data-width="<?php echo $width; ?>" style="width: 0%; font-size: 16px;">
                                Bs. <?php echo number_format($ganancia['ganancia_diaria'], 2); ?>
                            </div>
                        </div>
                        <div class="chart-value" style="font-size: 18px;">
                            <?php echo number_format($porcentaje, 1); ?>%
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Resumen de ganancias -->
            <div class="row" style="margin-top: 25px;">
                <div class="col-md-4">
                    <div class="stats-card">
                        <h4 style="font-size: 18px;"><i class="fa fa-calendar-day text-primary"></i> Ganancias Hoy</h4>
                        <h3 class="text-success" style="font-size: 28px;">Bs <?php 
                            $gananciaHoy = 0;
                            foreach($gananciasDiarias as $ganancia) {
                                if ($ganancia['Fecha'] == $fecha_actual) {
                                    $gananciaHoy = $ganancia['ganancia_diaria'];
                                    break;
                                }
                            }
                            echo number_format($gananciaHoy, 2);
                        ?></h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card">
                        <h4 style="font-size: 18px;"><i class="fa fa-chart-bar text-info"></i> Promedio Diario</h4>
                        <h3 class="text-info" style="font-size: 28px;">Bs <?php 
                            $total = 0;
                            foreach($gananciasDiarias as $ganancia) {
                                $total += $ganancia['ganancia_diaria'];
                            }
                            $promedio = count($gananciasDiarias) > 0 ? $total / count($gananciasDiarias) : 0;
                            echo number_format($promedio, 2);
                        ?></h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card">
                        <h4 style="font-size: 18px;"><i class="fa fa-coins text-warning"></i> Total 7 Días</h4>
                        <h3 class="text-warning" style="font-size: 28px;">Bs <?php echo number_format($total, 2); ?></h3>
                    </div>
                </div>
            </div>
            
            <!-- Tabla detallada -->
            <div class="table-responsive" style="margin-top: 25px;">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th style="font-size: 18px; padding: 15px;">Fecha</th>
                            <th style="font-size: 18px; padding: 15px;">Ganancias del Día</th>
                            <th style="font-size: 18px; padding: 15px;">Porcentaje</th>
                            <th style="font-size: 18px; padding: 15px;">Estado</th>
                            <th style="font-size: 18px; padding: 15px;">Tendencia</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $gananciaAnterior = 0;
                        $totalGanancias = array_sum(array_column($gananciasDiarias, 'ganancia_diaria'));
                        
                        foreach($gananciasDiarias as $index => $ganancia): 
                            $tendencia = '';
                            $colorTendencia = '';
                            $estado = '';
                            $colorEstado = '';
                            $porcentaje = ($ganancia['ganancia_diaria'] / $totalGanancias) * 100;
                            
                            if ($index > 0) {
                                if ($ganancia['ganancia_diaria'] > $gananciaAnterior) {
                                    $tendencia = '↑ Mejorando';
                                    $colorTendencia = 'text-success';
                                } elseif ($ganancia['ganancia_diaria'] < $gananciaAnterior) {
                                    $tendencia = '↓ Decreciendo';
                                    $colorTendencia = 'text-danger';
                                } else {
                                    $tendencia = '→ Estable';
                                    $colorTendencia = 'text-warning';
                                }
                            } else {
                                $tendencia = 'Nueva';
                                $colorTendencia = 'text-info';
                            }
                            
                            if ($ganancia['ganancia_diaria'] >= 1000) {
                                $estado = 'Excelente';
                                $colorEstado = 'success';
                            } elseif ($ganancia['ganancia_diaria'] >= 500) {
                                $estado = 'Bueno';
                                $colorEstado = 'info';
                            } elseif ($ganancia['ganancia_diaria'] >= 100) {
                                $estado = 'Regular';
                                $colorEstado = 'warning';
                            } else {
                                $estado = 'Bajo';
                                $colorEstado = 'danger';
                            }
                            
                            $gananciaAnterior = $ganancia['ganancia_diaria'];
                        ?>
                        <tr>
                            <td style="font-size: 17px; padding: 15px;"><strong><?php echo date('d/m/Y', strtotime($ganancia['Fecha'])); ?></strong></td>
                            <td class="text-success" style="text-align: right; font-size: 17px; padding: 15px;">
                                <strong>Bs <?php echo number_format($ganancia['ganancia_diaria'], 2); ?></strong>
                            </td>
                            <td class="text-info" style="text-align: center; font-size: 17px; padding: 15px;">
                                <strong><?php echo number_format($porcentaje, 1); ?>%</strong>
                            </td>
                            <td style="padding: 15px;">
                                <span class="label label-<?php echo $colorEstado; ?>" style="font-size: 16px; padding: 8px 12px;">
                                    <?php echo $estado; ?>
                                </span>
                            </td>
                            <td class="<?php echo $colorTendencia; ?>" style="font-size: 17px; padding: 15px;">
                                <strong><?php echo $tendencia; ?></strong>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #f8f9fa;">
                            <td class="text-right" style="font-size: 18px; padding: 15px;"><strong>TOTAL 7 DÍAS:</strong></td>
                            <td class="text-success" style="text-align: right; font-size: 18px; padding: 15px;"><strong>Bs <?php echo number_format($totalGanancias, 2); ?></strong></td>
                            <td style="text-align: center; font-size: 18px; padding: 15px;"><strong>100%</strong></td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
        <?php else: ?>
            <div class="no-data">
                <i class="fa fa-exclamation-circle fa-3x"></i>
                <h3 style="font-size: 28px;">No hay datos de ganancias</h3>
                <p style="font-size: 18px;">No se han registrado ganancias en los últimos 7 días.</p>
            </div>
        <?php endif; ?>
    </div>
</div>