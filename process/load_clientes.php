<?php
require_once '../library/configServer.php';
require_once '../library/consulSQL.php';

header('Content-Type: text/html; charset=utf-8');

try {
    $mysqli = mysqli_connect(SERVER, USER, PASS, BD);
    if (!$mysqli) {
        throw new Exception("Error de conexión: " . mysqli_connect_error());
    }
    mysqli_set_charset($mysqli, "utf8");

    // Parámetros de búsqueda con validación
    $search_nit = isset($_GET['search_nit']) ? mysqli_real_escape_string($mysqli, trim($_GET['search_nit'])) : '';
    $search_apellido = isset($_GET['search_apellido']) ? mysqli_real_escape_string($mysqli, trim($_GET['search_apellido'])) : '';

    // Validar y sanitizar paginación
    $pagina = isset($_GET['pag']) ? max(1, (int)$_GET['pag']) : 1;
    $regpagina = 30;
    $inicio = ($pagina > 1) ? (($pagina * $regpagina) - $regpagina) : 0;

    // Construir consulta con filtros
    $where_conditions = [];

    if(!empty($search_nit)) {
        $where_conditions[] = "NIT LIKE '%$search_nit%'";
    }

    if(!empty($search_apellido)) {
        $where_conditions[] = "Apellido LIKE '%$search_apellido%'";
    }

    $where_clause = "";
    if(!empty($where_conditions)) {
        $where_clause = "WHERE " . implode(" AND ", $where_conditions);
    }

    // Primero obtenemos clientes activos
    $where_activos = $where_clause;
    if ($where_activos) {
        $where_activos .= " AND (Estado IS NULL OR Estado != 'desactivado')";
    } else {
        $where_activos = "WHERE Estado IS NULL OR Estado != 'desactivado'";
    }

    $clientes_activos = mysqli_query($mysqli, "SELECT SQL_CALC_FOUND_ROWS * FROM cliente $where_activos ORDER BY Nombre ASC LIMIT $inicio, $regpagina");

    $totalregistros = mysqli_query($mysqli, "SELECT FOUND_ROWS()");
    $totalregistros = mysqli_fetch_array($totalregistros, MYSQLI_ASSOC);

    $numeropaginas = ceil($totalregistros["FOUND_ROWS()"] / $regpagina);

    // Ahora obtenemos clientes desactivados (sin paginación)
    $where_desactivados = $where_clause;
    if ($where_desactivados) {
        $where_desactivados .= " AND Estado = 'desactivado'";
    } else {
        $where_desactivados = "WHERE Estado = 'desactivado'";
    }

    $clientes_desactivados = mysqli_query($mysqli, "SELECT * FROM cliente $where_desactivados ORDER BY Nombre ASC");

    $cr = $inicio + 1;
?>

<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th class="text-center">#</th>
            <th class="text-center">NIT</th>
            <th class="text-center">Usuario</th>
            <th class="text-center">Nombre Completo</th>
            <th class="text-center">Apellido</th>
            <th class="text-center">Dirección</th>
            <th class="text-center">Teléfono</th>
            <th class="text-center">Email</th>
            <th class="text-center">Estado</th>
            <th class="text-center">Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if(mysqli_num_rows($clientes_activos) > 0): ?>
            <?php while($cli = mysqli_fetch_array($clientes_activos, MYSQLI_ASSOC)): ?>
            <tr>
                <td class="text-center"><?php echo $cr; ?></td>
                <td class="text-center"><?php echo htmlspecialchars($cli['NIT']); ?></td>
                <td class="text-center"><?php echo htmlspecialchars($cli['Nombre']); ?></td>
                <td class="text-center"><?php echo htmlspecialchars($cli['NombreCompleto']); ?></td>
                <td class="text-center"><?php echo htmlspecialchars($cli['Apellido']); ?></td>
                <td class="text-center"><?php echo htmlspecialchars($cli['Direccion']); ?></td>
                <td class="text-center"><?php echo htmlspecialchars($cli['Telefono']); ?></td>
                <td class="text-center"><?php echo htmlspecialchars($cli['Email']); ?></td>
                <td class="text-center">
                    <span class="label label-success">Activo</span>
                </td>
                <td class="text-center">
                    <div class="btn-group" role="group">
                        <!-- Botón para editar cliente (si necesitas esta funcionalidad) -->
                        <button type="button" class="btn btn-success btn-xs" 
                                onclick="cargarFormularioCliente('<?php echo $cli['NIT']; ?>')"
                                title="Editar cliente">
                            <i class="fa fa-edit"></i> Editar
                        </button>
                        
                        <!-- Botón para desactivar cliente -->
                        <form action="process/deactivate_client.php" method="POST" class="FormCatElec" 
                              data-form="deactivate" style="display: inline;">
                            <input type="hidden" name="nit-cli" value="<?php echo htmlspecialchars($cli['NIT']); ?>">
                            <button type="submit" class="btn btn-warning btn-xs" title="Desactivar cliente">
                                <i class="fa fa-ban"></i> Desactivar
                            </button>    
                        </form>
                    </div>
                </td>
            </tr>
            <?php $cr++; endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="10" class="text-center">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> No se encontraron clientes activos
                    </div>
                </td>
            </tr>
        <?php endif; ?>

        <!-- Separador para usuarios desactivados -->
        <?php if(mysqli_num_rows($clientes_activos) > 0 && mysqli_num_rows($clientes_desactivados) > 0): ?>
        <tr class="table-section-header">
            <td colspan="10" class="text-center" style="background-color: #f8f9fa; font-weight: bold; border-top: 2px solid #dee2e6;">
                <i class="fa fa-users"></i> Usuarios Desactivados
            </td>
        </tr>
        <?php endif; ?>

        <!-- Clientes Desactivados -->
        <?php if(mysqli_num_rows($clientes_desactivados) > 0): ?>
            <?php while($cli_des = mysqli_fetch_array($clientes_desactivados, MYSQLI_ASSOC)): ?>
            <tr style="background-color: #f8f9fa;">
                <td class="text-center"><?php echo $cr; ?></td>
                <td class="text-center"><?php echo htmlspecialchars($cli_des['NIT']); ?></td>
                <td class="text-center"><?php echo htmlspecialchars($cli_des['Nombre']); ?></td>
                <td class="text-center"><?php echo htmlspecialchars($cli_des['NombreCompleto']); ?></td>
                <td class="text-center"><?php echo htmlspecialchars($cli_des['Apellido']); ?></td>
                <td class="text-center"><?php echo htmlspecialchars($cli_des['Direccion']); ?></td>
                <td class="text-center"><?php echo htmlspecialchars($cli_des['Telefono']); ?></td>
                <td class="text-center"><?php echo htmlspecialchars($cli_des['Email']); ?></td>
                <td class="text-center">
                    <span class="label label-default">Desactivado</span>
                </td>
                <td class="text-center">
                    <div class="btn-group" role="group">
                        <!-- Botón para editar cliente desactivado -->
                        <button type="button" class="btn btn-default btn-xs" 
                                onclick="cargarFormularioCliente('<?php echo $cli_des['NIT']; ?>')"
                                title="Editar cliente">
                            <i class="fa fa-edit"></i> Editar
                        </button>
                        
                        <!-- Botón para activar cliente -->
                        <form action="process/activate_client.php" method="POST" class="FormCatElec" 
                              data-form="activate" style="display: inline;">
                            <input type="hidden" name="nit-cli" value="<?php echo htmlspecialchars($cli_des['NIT']); ?>">
                            <button type="submit" class="btn btn-success btn-xs" title="Activar cliente">
                                <i class="fa fa-check"></i> Activar
                            </button>    
                        </form>
                    </div>
                </td>
            </tr>
            <?php $cr++; endwhile; ?>
        <?php endif; ?>

        <!-- Mensaje si no hay ningún cliente -->
        <?php if(mysqli_num_rows($clientes_activos) == 0 && mysqli_num_rows($clientes_desactivados) == 0): ?>
            <tr>
                <td colspan="10" class="text-center">
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle"></i> No se encontraron clientes con los criterios de búsqueda
                    </div>
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php if($numeropaginas > 1): ?>
<div class="text-center">
    <ul class="pagination">
        <?php if($pagina == 1): ?>
            <li class="disabled">
                <a aria-label="Anterior">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        <?php else: ?>
            <li>
                <a href="configAdmin.php?view=accountlist&pag=<?php echo $pagina-1; ?>&search_nit=<?php echo urlencode($search_nit); ?>&search_apellido=<?php echo urlencode($search_apellido); ?>" 
                   aria-label="Anterior">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        <?php endif; ?>

        <?php for($i = 1; $i <= $numeropaginas; $i++): ?>
            <?php if($pagina == $i): ?>
                <li class="active"><a><?php echo $i; ?></a></li>
            <?php else: ?>
                <li>
                    <a href="configAdmin.php?view=accountlist&pag=<?php echo $i; ?>&search_nit=<?php echo urlencode($search_nit); ?>&search_apellido=<?php echo urlencode($search_apellido); ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
            <?php endif; ?>
        <?php endfor; ?>

        <?php if($pagina == $numeropaginas): ?>
            <li class="disabled">
                <a aria-label="Siguiente">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        <?php else: ?>
            <li>
                <a href="configAdmin.php?view=accountlist&pag=<?php echo $pagina+1; ?>&search_nit=<?php echo urlencode($search_nit); ?>&search_apellido=<?php echo urlencode($search_apellido); ?>" 
                   aria-label="Siguiente">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        <?php endif; ?>
    </ul>
    <div class="text-muted">
        Mostrando página <?php echo $pagina; ?> de <?php echo $numeropaginas; ?> 
        (<?php echo $totalregistros["FOUND_ROWS()"]; ?> clientes encontrados)
    </div>
</div>
<?php endif; ?>

<?php
} catch (Exception $e) {
    error_log("Error cargando clientes: " . $e->getMessage());
    echo '<div class="alert alert-danger text-center">Error al cargar los clientes: ' . htmlspecialchars($e->getMessage()) . '</div>';
}

// Cerrar conexión
if (isset($mysqli)) {
    mysqli_close($mysqli);
}
?>