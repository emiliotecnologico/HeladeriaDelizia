<p class="lead" style="font-size: 20px;">
    Puede controlar las ventas pendientes aqui.
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
            <div class="panel panel-warning">
                <div class="panel-heading text-center"><h4 style="font-size: 28px;">Ventas Pendientes</h4></div>
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

                                // SOLO pedidos pendientes (sin confirmado)
                                $pedidos=mysqli_query($mysqli,"SELECT SQL_CALC_FOUND_ROWS * FROM venta WHERE Estado = 'pendiente' LIMIT $inicio, $regpagina");

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
                                    <span class="label label-warning" style="font-size: 16px;">Pendiente</span>
                                </td>
                                <td class="text-center">
                                    <a href="#!" class="btn btn-raised btn-xs btn-success btn-block btn-up-order" style="font-size: 16px;" data-code="<?php echo $order['NumPedido']; ?>">Actualizar</a>
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
                            <a href="configAdmin.php?view=orderpending&pag=<?php echo $pagina-1; ?>" style="font-size: 17px;">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php
                        for($i=1; $i <= $numeropaginas; $i++ ){
                            if($pagina == $i){
                                echo '<li class="active"><a href="configAdmin.php?view=orderpending&pag='.$i.'" style="font-size: 17px;">'.$i.'</a></li>';
                            }else{
                                echo '<li><a href="configAdmin.php?view=orderpending&pag='.$i.'" style="font-size: 17px;">'.$i.'</a></li>';
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
                            <a href="configAdmin.php?view=orderpending&pag=<?php echo $pagina+1; ?>" style="font-size: 17px;">
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

<!-- Modal para actualizar pedidos pendientes -->
<div class="modal fade" id="modal-order" tabindex="-1" role="dialog" aria-labelledby="modalOrderLabel">
  <div class="modal-dialog modal-sm" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="font-size: 28px;"><span aria-hidden="true">&times;</span></button>
          <h5 class="modal-title text-center text-primary" id="modalOrderLabel" style="font-size: 22px;">Actualizar estado de la venta</h5>
        </div>
        <form action="./process/updatePedido.php" method="POST" class="FormCatElec" data-form="update" id="formUpdateOrder">
            <div class="modal-body" id="OrderSelect">
                <!-- Aquí se cargará el contenido dinámico -->
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-success btn-raised btn-sm" style="font-size: 16px;">Actualizar</button>
              <button type="button" class="btn btn-danger btn-raised btn-sm" style="font-size: 16px;" data-dismiss="modal">Cancelar</button>
            </div>
        </form>
      </div>
  </div>
</div>

<script>
    $(document).ready(function(){
        // Manejar el clic en Actualizar pedido
        $(document).on('click', '.btn-up-order', function(e){
            e.preventDefault();
            var code = $(this).attr('data-code');
            
            // Mostrar loading
            $('#OrderSelect').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i><br>Cargando...</div>');
            $('#modal-order').modal('show');
            
            $.ajax({
                url: './process/checkOrder.php',
                type: 'POST',
                data: { code: code },
                success: function(data){
                    $('#OrderSelect').html(data);
                },
                error: function(){
                    $('#OrderSelect').html('<div class="alert alert-danger">Error al cargar los datos del pedido</div>');
                }
            });
        });

        // Manejar el envío del formulario de actualización
        $(document).on('submit', '#formUpdateOrder', function(e){
            e.preventDefault();
            var form = $(this);
            var formData = form.serialize();
            
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                success: function(response){
                    // Recargar la página para ver los cambios
                    location.reload();
                },
                error: function(){
                    alert('Error al actualizar el pedido');
                }
            });
        });
    });
</script>