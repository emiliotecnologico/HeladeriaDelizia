<p class="lead" style="font-size: 20px;">
    Puede ver la lista de clientes aquí.
</p>
<ul class="breadcrumb" style="margin-bottom: 5px; font-size: 17px;">
    <li>
        <a href="configAdmin.php?view=cliente">
            <i class="fa fa-plus-circle" aria-hidden="true"></i> &nbsp; Nuevo Cliente
        </a>
    </li>
    <li>
        <a href="configAdmin.php?view=clientelist"><i class="fa fa-list-ol" aria-hidden="true"></i> &nbsp; Lista de Clientes</a>
    </li>
    <li>
        <a href="configAdmin.php?view=deactivatedclients"><i class="fa fa-ban" aria-hidden="true"></i> &nbsp; Clientes Desactivados</a>
    </li>
</ul>
<div class="container">
	<div class="row">
		<div class="col-xs-12">
            <br><br>
            <!-- BUSCADORES SEPARADOS PARA CLIENTES -->
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-12 col-sm-6">
                            <div class="form-group">
                                <label for="search-ci" style="font-size: 18px;">Buscar por C.I.</label>
                                <input type="text" class="form-control" style="font-size: 17px; padding: 12px;" id="search-ci" placeholder="Ingrese C.I. del cliente...">
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <div class="form-group">
                                <label for="search-nombre" style="font-size: 18px;">Buscar por Nombre</label>
                                <input type="text" class="form-control" style="font-size: 17px; padding: 12px;" id="search-nombre" placeholder="Ingrese nombre del cliente...">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-info">
              <div class="panel-heading text-center"><h4 style="font-size: 28px;">Clientes Registrados</h4></div>
                <div class="table-responsive">
                  <table class="table table-striped table-hover" id="tabla-clientes">
                      <thead class="">
                          <tr>
                          	  <th class="text-center" style="font-size: 18px;">#</th>
                              <th class="text-center" style="font-size: 18px;">C.I.</th>
                              <th class="text-center" style="font-size: 18px;">Nombre Completo</th>
                              <th class="text-center" style="font-size: 18px;">Teléfono</th>
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

							// MODIFICADO: Consulta a la tabla clientes
							$clientes=mysqli_query($mysqli,"SELECT SQL_CALC_FOUND_ROWS * FROM clientes WHERE Estado='activo' LIMIT $inicio, $regpagina");

							$totalregistros = mysqli_query($mysqli,"SELECT FOUND_ROWS()");
							$totalregistros = mysqli_fetch_array($totalregistros, MYSQLI_ASSOC);

							$numeropaginas = ceil($totalregistros["FOUND_ROWS()"]/$regpagina);

							$cr=$inicio+1;
                            while($cliente=mysqli_fetch_array($clientes, MYSQLI_ASSOC)){
                        ?>
                        <tr>
                        	<td class="text-center" style="font-size: 17px;"><?php echo $cr; ?></td>
                        	<td class="text-center ci" style="font-size: 17px;"><?php echo $cliente['NIT']; ?></td>
                        	<td class="text-center nombre" style="font-size: 17px;"><?php echo $cliente['NombreCompleto'] . ' ' . $cliente['Apellido']; ?></td>
                        	<td class="text-center" style="font-size: 17px;"><?php echo $cliente['Telefono']; ?></td>
                        	<td class="text-center">
                        		<span class="label label-success" style="font-size: 16px;">Activo</span>
                        	</td>
                        	<td class="text-center">
                        		<!-- MODIFICADO: Enlace actualizado para clientes -->
                        		<a href="configAdmin.php?view=clienteedit&id=<?php echo $cliente['id']; ?>" class="btn btn-raised btn-xs btn-warning" style="font-size: 16px;">Actualizar</a>
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
                            <a href="configAdmin.php?view=clientelist&pag=<?php echo $pagina-1; ?>" style="font-size: 17px;">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php
                        for($i=1; $i <= $numeropaginas; $i++ ){
                            if($pagina == $i){
                                echo '<li class="active"><a href="configAdmin.php?view=clientelist&pag='.$i.'" style="font-size: 17px;">'.$i.'</a></li>';
                            }else{
                                echo '<li><a href="configAdmin.php?view=clientelist&pag='.$i.'" style="font-size: 17px;">'.$i.'</a></li>';
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
                            <a href="configAdmin.php?view=clientelist&pag=<?php echo $pagina+1; ?>" style="font-size: 17px;">
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
    const inputCI = document.getElementById('search-ci');
    const inputNombre = document.getElementById('search-nombre');
    const tabla = document.getElementById('tabla-clientes');
    const filas = tabla.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

    function filtrarClientes() {
        const valorCI = inputCI.value.toLowerCase();
        const valorNombre = inputNombre.value.toLowerCase();

        for (let i = 0; i < filas.length; i++) {
            const celdaCI = filas[i].getElementsByClassName('ci')[0];
            const celdaNombre = filas[i].getElementsByClassName('nombre')[0];
            
            const textoCI = celdaCI ? celdaCI.textContent.toLowerCase() : '';
            const textoNombre = celdaNombre ? celdaNombre.textContent.toLowerCase() : '';

            const coincideCI = textoCI.includes(valorCI);
            const coincideNombre = textoNombre.includes(valorNombre);

            if (coincideCI && coincideNombre) {
                filas[i].style.display = '';
            } else {
                filas[i].style.display = 'none';
            }
        }
    }

    inputCI.addEventListener('input', filtrarClientes);
    inputNombre.addEventListener('input', filtrarClientes);
});
</script>