<p class="lead" style="font-size: 20px;">
    Puede ver la lista de nuestras categorias de productos aqui.
</p>
<ul class="breadcrumb" style="margin-bottom: 5px; font-size: 17px;">
    <li>
        <a href="configAdmin.php?view=category">
            <i class="fa fa-plus-circle" aria-hidden="true"></i> &nbsp; Nueva Categoría
        </a>
    </li>
    <li>
        <a href="configAdmin.php?view=categorylist"><i class="fa fa-list-ol" aria-hidden="true"></i> &nbsp; Categoría de Productos</a>
    </li>
</ul>
<div class="container">
	<div class="row">
        <div class="col-xs-12">
            <br><br>
            <div class="panel panel-info">
                <div class="panel-heading text-center"><h4 style="font-size: 28px;">Categorías de Productos</h4></div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="">
                            <tr>
                            	<th class="text-center" style="font-size: 18px;">#</th>
                                <th class="text-center" style="font-size: 18px;">Código</th>
                                <th class="text-center" style="font-size: 18px;">Nombre</th>
                                <th class="text-center" style="font-size: 18px;">Descripción</th>
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

								$categorias=mysqli_query($mysqli,"SELECT SQL_CALC_FOUND_ROWS * FROM categoria LIMIT $inicio, $regpagina");

								$totalregistros = mysqli_query($mysqli,"SELECT FOUND_ROWS()");
								$totalregistros = mysqli_fetch_array($totalregistros, MYSQLI_ASSOC);

								$numeropaginas = ceil($totalregistros["FOUND_ROWS()"]/$regpagina);

								$cr=$inicio+1;
                              while($cate=mysqli_fetch_array($categorias, MYSQLI_ASSOC)){
                            ?>
                            <tr>
	                            <td class="text-center" style="font-size: 17px;"><?php echo $cr; ?></td>
	                        	<td class="text-center" style="font-size: 17px;"><?php echo $cate['CodigoCat']; ?></td>
	                        	<td class="text-center" style="font-size: 17px;"><?php echo $cate['Nombre']; ?></td>
	                        	<td class="text-center" style="font-size: 17px;"><?php echo $cate['Descripcion']; ?></td>
	                        	<td class="text-center">
	                        		<a href="configAdmin.php?view=categoryinfo&code=<?php echo $cate['CodigoCat']; ?>" class="btn btn-raised btn-xs btn-success" style="font-size: 16px;">Actualizar</a>
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
                            <a href="configAdmin.php?view=categorylist&pag=<?php echo $pagina-1; ?>" style="font-size: 17px;">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>


                    <?php
                        for($i=1; $i <= $numeropaginas; $i++ ){
                            if($pagina == $i){
                                echo '<li class="active"><a href="configAdmin.php?view=categorylist&pag='.$i.'" style="font-size: 17px;">'.$i.'</a></li>';
                            }else{
                                echo '<li><a href="configAdmin.php?view=categorylist&pag='.$i.'" style="font-size: 17px;">'.$i.'</a></li>';
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
                            <a href="configAdmin.php?view=categorylist&pag=<?php echo $pagina+1; ?>" style="font-size: 17px;">
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