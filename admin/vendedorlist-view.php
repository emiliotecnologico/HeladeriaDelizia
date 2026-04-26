<p class="lead" style="font-size: 20px;">
    Puede ver a nuestros vendedores aquí.
</p>
<ul class="breadcrumb" style="margin-bottom: 5px; font-size: 17px;">
    <li>
        <a href="configAdmin.php?view=vendedor">
            <i class="fa fa-plus-circle" aria-hidden="true"></i> &nbsp; Nuevo Vendedor
        </a>
    </li>
    <li>
        <a href="configAdmin.php?view=vendedorlist"><i class="fa fa-list-ol" aria-hidden="true"></i> &nbsp; Vendedores del Sistema</a>
    </li>
</ul>
<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <br><br>
            <div class="panel panel-info">
                <div class="panel-heading text-center"><h4 style="font-size: 28px;">Vendedores del Sistema</h4></div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="">
                            <tr>
                                <th class="text-center" style="font-size: 18px;">#</th>
                                <th class="text-center" style="font-size: 18px;">C.I.</th>
                                <th class="text-center" style="font-size: 18px;">Usuario</th>
                                <th class="text-center" style="font-size: 18px;">Nombre Completo</th>
                                <th class="text-center" style="font-size: 18px;">Email</th>
                                <th class="text-center" style="font-size: 18px;">Teléfono</th>
                                <th class="text-center" style="font-size: 18px;">Actualizar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $mysqli = mysqli_connect(SERVER, USER, PASS, BD);
                                mysqli_set_charset($mysqli, "utf8");

                                $pagina = isset($_GET['pag']) ? (int)$_GET['pag'] : 1;
                                $regpagina = 30;
                                $inicio = ($pagina > 1) ? (($pagina * $regpagina) - $regpagina) : 0;

                                // MODIFICADO: Consulta a la tabla vendedores
                                $vendedores=mysqli_query($mysqli,"SELECT SQL_CALC_FOUND_ROWS * FROM vendedores LIMIT $inicio, $regpagina");

                                $totalregistros = mysqli_query($mysqli,"SELECT FOUND_ROWS()");
                                $totalregistros = mysqli_fetch_array($totalregistros, MYSQLI_ASSOC);

                                $numeropaginas = ceil($totalregistros["FOUND_ROWS()"]/$regpagina);

                                $cr=$inicio+1;
                              while($vend=mysqli_fetch_array($vendedores, MYSQLI_ASSOC)){
                            ?>
                            <tr>
                                <td class="text-center" style="font-size: 17px;"><?php echo $cr; ?></td>
                                <td class="text-center" style="font-size: 17px;"><?php echo $vend['NIT']; ?></td>
                                <td class="text-center" style="font-size: 17px;"><?php echo $vend['Nombre']; ?></td>
                                <td class="text-center" style="font-size: 17px;"><?php echo $vend['NombreCompleto'] . ' ' . $vend['Apellido']; ?></td>
                                <td class="text-center" style="font-size: 17px;"><?php echo $vend['Email']; ?></td>
                                <td class="text-center" style="font-size: 17px;"><?php echo $vend['Telefono']; ?></td>
                                <td class="text-center">
                                    <a href="configAdmin.php?view=vendedorinfo&nit=<?php echo $vend['NIT']; ?>" class="btn btn-raised btn-xs btn-warning" style="font-size: 16px;">
                                        Actualizar
                                    </a>
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
                            <a href="configAdmin.php?view=vendedorlist&pag=<?php echo $pagina-1; ?>" style="font-size: 17px;">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php
                        for($i=1; $i <= $numeropaginas; $i++ ){
                            if($pagina == $i){
                                echo '<li class="active"><a href="configAdmin.php?view=vendedorlist&pag='.$i.'" style="font-size: 17px;">'.$i.'</a></li>';
                            }else{
                                echo '<li><a href="configAdmin.php?view=vendedorlist&pag='.$i.'" style="font-size: 17px;">'.$i.'</a></li>';
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
                            <a href="configAdmin.php?view=vendedorlist&pag=<?php echo $pagina+1; ?>" style="font-size: 17px;">
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