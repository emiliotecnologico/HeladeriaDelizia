<?php
session_start();
require_once '../library/configServer.php';
require_once '../library/consulSQL.php';

// Verificar que el código esté presente
if (!isset($_GET['code']) || empty($_GET['code'])) {
    die('<div class="alert alert-danger">Código no proporcionado</div>');
}

$code = consultasSQL::clean_string($_GET['code']);

// Determinar contexto: vendedor viendo su propio perfil
$esVendedorPropio = false;
$esAdministrador = isset($_SESSION['nombreAdmin']);

// Verificar permisos
if (isset($_SESSION['UserNIT']) && $_SESSION['UserNIT'] == $code) {
    // Vendedor viendo sus propios datos
    $esVendedorPropio = true;
    $vendedor = ejecutarSQL::consultar("SELECT * FROM vendedores WHERE NIT='$code'");
} elseif (isset($_SESSION['nombreAdmin'])) {
    // Administrador puede ver cualquier vendedor
    $vendedor = ejecutarSQL::consultar("SELECT * FROM vendedores WHERE NIT='$code'");
} else {
    die('<div class="alert alert-warning">Acceso denegado</div>');
}

if ($vendedor && mysqli_num_rows($vendedor) > 0) {
    $vend = mysqli_fetch_array($vendedor, MYSQLI_ASSOC);
    
    // Para el modal "Mi Perfil", siempre es el propio vendedor
    $esMiPerfil = $esVendedorPropio;
    ?>
    
    <input type="hidden" name="nit-old" value="<?php echo htmlspecialchars($vend['NIT']); ?>">
    
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="form-group label-floating">
                    <label class="control-label">C.I.</label>
                    <input class="form-control" type="text" name="vendedor-nit" value="<?php echo htmlspecialchars($vend['NIT']); ?>" 
                           <?php echo $esMiPerfil ? 'readonly style="background-color: #f5f5f5; cursor: not-allowed;"' : ''; ?> required>
                    <?php if($esMiPerfil): ?>
                        <small class="text-muted">El C.I. no se puede modificar</small>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group label-floating">
                    <label class="control-label">Nombre de Usuario</label>
                    <input class="form-control" type="text" name="vendedor-name" value="<?php echo htmlspecialchars($vend['Nombre']); ?>" 
                           pattern="[a-zA-Z0-9_]+" title="Solo letras, números y guiones bajos" required>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="form-group label-floating">
                    <label class="control-label">Nombres</label>
                    <input class="form-control" type="text" name="vendedor-fullname" value="<?php echo htmlspecialchars($vend['NombreCompleto']); ?>" required>
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group label-floating">
                    <label class="control-label">Apellidos</label>
                    <input class="form-control" type="text" name="vendedor-lastname" value="<?php echo htmlspecialchars($vend['Apellido']); ?>" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <div class="form-group label-floating">
                    <label class="control-label">Dirección</label>
                    <input class="form-control" type="text" name="vendedor-dir" value="<?php echo htmlspecialchars($vend['Direccion']); ?>" required>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="form-group label-floating">
                    <label class="control-label">Teléfono</label>
                    <input class="form-control" type="tel" name="vendedor-phone" value="<?php echo htmlspecialchars($vend['Telefono']); ?>" 
                           pattern="[0-9+\-\s()]{8,15}" title="Formato de teléfono válido" required>
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group label-floating">
                    <label class="control-label">Email</label>
                    <input class="form-control" type="email" name="vendedor-email" value="<?php echo htmlspecialchars($vend['Email']); ?>" required>
                </div>
            </div>
        </div>

        <?php if($esAdministrador && !$esMiPerfil): ?>
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label>Estado</label>
                    <select class="form-control" name="estado" required>
                        <option value="activo" <?php echo ($vend['Estado'] === 'activo') ? 'selected' : ''; ?>>Activo</option>
                        <option value="inactivo" <?php echo ($vend['Estado'] === 'inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                    </select>
                </div>
            </div>
        </div>
        <?php else: ?>
            <input type="hidden" name="estado" value="<?php echo htmlspecialchars($vend['Estado']); ?>">
        <?php endif; ?>

        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="form-group label-floating">
                    <label class="control-label">Nueva Contraseña (dejar en blanco para no cambiar)</label>
                    <input class="form-control" type="password" name="vendedor-pass1" placeholder="Ingrese nueva contraseña">
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group label-floating">
                    <label class="control-label">Confirmar Contraseña</label>
                    <input class="form-control" type="password" name="vendedor-pass2" placeholder="Confirme la nueva contraseña">
                </div>
            </div>
        </div>
    </div>
    
    <?php
    mysqli_free_result($vendedor);
} else {
    echo '<div class="alert alert-danger">Vendedor no encontrado</div>';
}
?>