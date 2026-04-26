<p class="lead" style="font-size: 20px;">
	Puede ver la lista de los productos aqui.
</p>
<ul class="breadcrumb" style="margin-bottom: 5px; font-size: 17px;">
    <li>
        <a href="configAdmin.php?view=product">
            <i class="fa fa-plus-circle" aria-hidden="true"></i> &nbsp; Nuevo Producto
        </a>
    </li>
    <li>
        <a href="configAdmin.php?view=productlist"><i class="fa fa-list-ol" aria-hidden="true"></i> &nbsp; Productos en Tienda</a>
    </li>
    <li>
        <a href="configAdmin.php?view=deactivatedproducts"><i class="fa fa-ban" aria-hidden="true"></i> &nbsp; Productos Desactivados</a>
    </li>
</ul>
<div class="container">
	<div class="row">
		<div class="col-xs-12">
            <br><br>
            <!-- BUSCADORES SEPARADOS PARA PRODUCTOS -->
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-12 col-sm-6">
                            <div class="form-group">
                                <label for="search-codigo" style="font-size: 18px;">Buscar por Código</label>
                                <input type="text" class="form-control" style="font-size: 17px; padding: 12px;" id="search-codigo" placeholder="Ingrese código del producto...">
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <div class="form-group">
                                <label for="search-nombre" style="font-size: 18px;">Buscar por Nombre</label>
                                <input type="text" class="form-control" style="font-size: 17px; padding: 12px;" id="search-nombre" placeholder="Ingrese nombre del producto...">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="panel panel-info">
              <div class="panel-heading text-center"><h4 style="font-size: 28px;">Productos en Tienda</h4></div>
                <div class="table-responsive">
                  <table class="table table-striped table-hover" id="tabla-productos">
                      <thead class="">
                          <tr>
                          	  <th class="text-center" style="font-size: 18px;">#</th>
                              <th class="text-center" style="font-size: 18px;">Código</th>
                              <th class="text-center" style="font-size: 18px;">Nombre</th>
                              <th class="text-center" style="font-size: 18px;">Categoría</th>
                              <th class="text-center" style="font-size: 18px;">Precio (Bs.)</th>
                              <th class="text-center" style="font-size: 18px;">Aumento (%)</th>
                              <th class="text-center" style="font-size: 18px;">Stock</th>
                              <th class="text-center" style="font-size: 18px;">Estado</th>
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

							// Consulta simplificada sin JOIN con administrador
							$productos=mysqli_query($mysqli,"SELECT SQL_CALC_FOUND_ROWS * 
                                                             FROM producto 
                                                             WHERE Estado='activo' 
                                                             LIMIT $inicio, $regpagina");

							$totalregistros = mysqli_query($mysqli,"SELECT FOUND_ROWS()");
							$totalregistros = mysqli_fetch_array($totalregistros, MYSQLI_ASSOC);

							$numeropaginas = ceil($totalregistros["FOUND_ROWS()"]/$regpagina);

							$cr=$inicio+1;
                            while($prod=mysqli_fetch_array($productos, MYSQLI_ASSOC)){
                        ?>
                        <tr>
                        	<td class="text-center" style="font-size: 17px;"><?php echo $cr; ?></td>
                        	<td class="text-center codigo" style="font-size: 17px;"><?php echo $prod['CodigoProd']; ?></td>
                        	<td class="text-center nombre" style="font-size: 17px;"><?php echo $prod['NombreProd']; ?></td>
                        	<td class="text-center" style="font-size: 17px;">
                        		<?php 
                        			$categ=ejecutarSQL::consultar("SELECT Nombre FROM categoria WHERE CodigoCat='".$prod['CodigoCat']."'");
                        			$datc=mysqli_fetch_array($categ, MYSQLI_ASSOC);
                        			echo $datc['Nombre'];
                        		?>
                        	</td>
                        	<td class="text-center" style="font-size: 17px;">Bs. <?php echo number_format($prod['Precio'], 2); ?></td>
                        	<td class="text-center" style="font-size: 17px;"><?php echo $prod['Aumento']; ?>%</td>
                        	<td class="text-center" style="font-size: 17px;"><?php echo $prod['Stock']; ?></td>
                        	<td class="text-center">
                        		<span class="label label-success" style="font-size: 16px;">Activo</span>
                        	</td>
                        	<td class="text-center">
                        		<a href="configAdmin.php?view=productinfo&code=<?php echo $prod['CodigoProd']; ?>" class="btn btn-raised btn-xs btn-success" style="font-size: 16px;">Actualizar</a>
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
                            <a href="configAdmin.php?view=productlist&pag=<?php echo $pagina-1; ?>" style="font-size: 17px;">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php
                        for($i=1; $i <= $numeropaginas; $i++ ){
                            if($pagina == $i){
                                echo '<li class="active"><a href="configAdmin.php?view=productlist&pag='.$i.'" style="font-size: 17px;">'.$i.'</a></li>';
                            }else{
                                echo '<li><a href="configAdmin.php?view=productlist&pag='.$i.'" style="font-size: 17px;">'.$i.'</a></li>';
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
                            <a href="configAdmin.php?view=productlist&pag=<?php echo $pagina+1; ?>" style="font-size: 17px;">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputCodigo = document.getElementById('search-codigo');
    const inputNombre = document.getElementById('search-nombre');
    const tabla = document.getElementById('tabla-productos');
    const filas = tabla.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

    function filtrarProductos() {
        const valorCodigo = inputCodigo.value.toLowerCase();
        const valorNombre = inputNombre.value.toLowerCase();

        for (let i = 0; i < filas.length; i++) {
            const celdaCodigo = filas[i].getElementsByClassName('codigo')[0];
            const celdaNombre = filas[i].getElementsByClassName('nombre')[0];
            
            const textoCodigo = celdaCodigo ? celdaCodigo.textContent.toLowerCase() : '';
            const textoNombre = celdaNombre ? celdaNombre.textContent.toLowerCase() : '';

            const coincideCodigo = textoCodigo.includes(valorCodigo);
            const coincideNombre = textoNombre.includes(valorNombre);

            if (coincideCodigo && coincideNombre) {
                filas[i].style.display = '';
            } else {
                filas[i].style.display = 'none';
            }
        }
    }

    inputCodigo.addEventListener('input', filtrarProductos);
    inputNombre.addEventListener('input', filtrarProductos);
});
</script>