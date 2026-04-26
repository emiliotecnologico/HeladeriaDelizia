<p class="lead" style="font-size: 20px;">
    Puede ver las ventas entregadas aqui.
</p>

<ul class="breadcrumb" style="margin-bottom: 5px; font-size: 17px;">
    <li>
        <a href="configAdmin.php?view=orderpending">
            <i class="fa fa-clock-o" aria-hidden="true"></i> &nbsp; Pendientes
        </a>
    </li>
    <li>
        <a href="configAdmin.php?view=orderdelivered">
            <i class="fa fa-check-circle" aria-hidden="true"></i> &nbsp; Entregados
        </a>
    </li>
    <li>
        <a href="configAdmin.php?view=ordercancelled">
            <i class="fa fa-times-circle" aria-hidden="true"></i> &nbsp; Cancelados
        </a>
    </li>
</ul>

<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <br><br>
            <div class="panel panel-success">
                <div class="panel-heading text-center"><h4 style="font-size: 28px;">Ventas Entregados</h4></div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="">
                            <tr>
                                <th class="text-center" style="font-size: 18px;">#</th>
                                <th class="text-center" style="font-size: 18px;">Fecha</th>
                                <th class="text-center" style="font-size: 18px;">Cliente</th>
                                <th class="text-center" style="font-size: 18px;">Total</th>
                                <th class="text-center" style="font-size: 18px;">Estado</th>
                                <th class="text-center" style="font-size: 18px;">Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $mysqli = mysqli_connect(SERVER, USER, PASS, BD);
                                mysqli_set_charset($mysqli, "utf8");

                                $pagina = isset($_GET['pag']) ? (int)$_GET['pag'] : 1;
                                $regpagina = 30;
                                $inicio = ($pagina > 1) ? (($pagina * $regpagina) - $regpagina) : 0;

                                // SOLO pedidos entregados
                                $pedidos=mysqli_query($mysqli,"SELECT SQL_CALC_FOUND_ROWS * FROM venta WHERE Estado='entregado' LIMIT $inicio, $regpagina");

                                $totalregistros = mysqli_query($mysqli,"SELECT FOUND_ROWS()");
                                $totalregistros = mysqli_fetch_array($totalregistros, MYSQLI_ASSOC);

                                $numeropaginas = ceil($totalregistros["FOUND_ROWS()"]/$regpagina);

                                $cr=$inicio+1;
                                while($order=mysqli_fetch_array($pedidos, MYSQLI_ASSOC)){
                            ?>
                            <tr>
                                <td class="text-center" style="font-size: 17px;"><?php echo $cr; ?></td>
                                <td class="text-center" style="font-size: 17px;"><?php echo $order['Fecha']; ?></td>
                                <td class="text-center" style="font-size: 17px;">
                                    <?php 
                                        // NOMBRE COMPLETO: NombreCompleto + Apellido
                                        $conUs= ejecutarSQL::consultar("SELECT NombreCompleto, Apellido FROM clientes WHERE id='".$order['id_cliente']."'");
                                        if(mysqli_num_rows($conUs) > 0) {
                                            $UsP=mysqli_fetch_array($conUs, MYSQLI_ASSOC);
                                            echo htmlspecialchars($UsP['NombreCompleto'] . ' ' . $UsP['Apellido']);
                                        } else {
                                            echo 'Cliente no encontrado';
                                        }
                                        mysqli_free_result($conUs);
                                    ?>
                                </td>
                                <td class="text-center" style="font-size: 17px;">Bs. <?php echo number_format($order['TotalPagar'], 2); ?></td>
                                <td class="text-center">
                                    <span class="label label-success" style="font-size: 16px;">Entregado</span>
                                </td>
                                <td class="text-center">
                                    <?php 
                                        if(isset($order['Adjunto']) && is_file("./assets/comprobantes/".$order['Adjunto'])){
                                            echo '<a href="./assets/comprobantes/'.$order['Adjunto'].'" target="_blank" class="btn btn-raised btn-xs btn-info btn-block" style="font-size: 16px;">Comprobante</a>';
                                        }
                                    ?>
                                    <a href="./report/factura.php?id=<?php echo $order['NumPedido'];  ?>" class="btn btn-raised btn-xs btn-primary btn-block" style="font-size: 16px;" target="_blank">Imprimir</a>
                                </td>
                            </tr>
                            <?php
                                $cr++;
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
                <?php if($numeropaginas>=1): ?>
                <div class="text-center">
                  <ul class="pagination">
                    <?php if($pagina == 1): ?>
                        <li class="disabled">
                            <a style="font-size: 17px;">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                    <?php else: ?>
                        <li>
                            <a href="configAdmin.php?view=orderdelivered&pag=<?php echo $pagina-1; ?>" style="font-size: 17px;">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php
                        for($i=1; $i <= $numeropaginas; $i++ ){
                            if($pagina == $i){
                                echo '<li class="active"><a href="configAdmin.php?view=orderdelivered&pag='.$i.'" style="font-size: 17px;">'.$i.'</a></li>';
                            }else{
                                echo '<li><a href="configAdmin.php?view=orderdelivered&pag='.$i.'" style="font-size: 17px;">'.$i.'</a></li>';
                            }
                        }
                    ?>

                    <?php if($pagina == $numeropaginas): ?>
                        <li class="disabled">
                            <a style="font-size: 17px;">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    <?php else: ?>
                        <li>
                            <a href="configAdmin.php?view=orderdelivered&pag=<?php echo $pagina+1; ?>" style="font-size: 17px;">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>
                  </ul>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>