<p class="lead">
    Puede ver y actualizar la información de su cuenta aquí.
</p>
<ul class="breadcrumb" style="margin-bottom: 5px;">
    <li>
        <a href="configAdmin.php?view=admin">
            <i class="fa fa-plus-circle" aria-hidden="true"></i> &nbsp; Nuevo Administrador
        </a>
    </li>
    <li>
        <a href="configAdmin.php?view=adminlist"><i class="fa fa-list-ol" aria-hidden="true"></i> &nbsp; Administradores del Sistema</a>
    </li>
    <li class="active">
        <a href="configAdmin.php?view=account"><i class="fa fa-address-card" aria-hidden="true"></i> &nbsp; Mi cuenta</a>
    </li>
</ul>
<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <br>
            <div class="panel panel-info">
                <div class="panel-heading text-center">
                    <h4><i class="fa fa-user-circle" aria-hidden="true"></i> &nbsp; Actualizar Información de Cuenta</h4>
                </div>
                <div class="panel-body">
                    <?php
                    // Verificar si hay una sesión de administrador activa - CORREGIDO
                    if(isset($_SESSION['idAdmin'])) {
                        $admin = ejecutarSQL::consultar("SELECT * FROM administrador WHERE id='".$_SESSION['idAdmin']."'");
                        if($admin && mysqli_num_rows($admin) > 0) {
                            $dataAdmin = mysqli_fetch_array($admin, MYSQLI_ASSOC);
                    ?>
                    <form action="./process/updateAdmin.php" method="POST" role="form" class="FormCatElec" data-form="update">
                        <input type="hidden" name="admin-code" value="<?php echo $_SESSION['idAdmin']; ?>">
                        <input type="hidden" name="admin-name-old" value="<?php echo $dataAdmin['Nombre']; ?>">
                        
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group label-floating">
                                        <label class="control-label">Nombre de Usuario</label>
                                        <input class="form-control" type="text" name="admin-name" value="<?php echo $dataAdmin['Nombre']; ?>" maxlength="9" pattern="[a-zA-Z0-9]{4,9}" required>
                                    </div>
                                </div>
                                
                                <div class="col-xs-12">
                                    <div class="alert alert-info text-center" style="margin-top: 20px;">
                                        <i class="fa fa-info-circle"></i> 
                                        No es necesario actualizar la contraseña, sin embargo si desea hacerlo debe ingresar una nueva contraseña y volver a ingresarla
                                    </div>
                                </div>
                                
                                <div class="col-xs-12 col-sm-6">
                                    <div class="form-group label-floating">
                                        <label class="control-label">Nueva Contraseña</label>
                                        <input class="form-control" type="password" name="admin-pass1">
                                    </div>
                                </div>
                                
                                <div class="col-xs-12 col-sm-6">
                                    <div class="form-group label-floating">
                                        <label class="control-label">Repita la Nueva Contraseña</label>
                                        <input class="form-control" type="password" name="admin-pass2">
                                    </div>
                                </div>
                                
                                <div class="col-xs-12" style="margin-top: 30px;">
                                    <p class="text-center">
                                        <button type="submit" class="btn btn-primary btn-raised btn-lg">
                                            <i class="fa fa-refresh"></i> &nbsp; Actualizar cuenta
                                        </button>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </form>
                    <?php
                        } else {
                            echo '<div class="alert alert-danger text-center">Error: No se encontraron datos del administrador.</div>';
                        }
                    } else {
                        echo '<div class="alert alert-warning text-center">No hay una sesión de administrador activa. Por favor, inicie sesión.</div>';
                    }
                    ?>
                </div>
            </div>
            
            <!-- Información adicional de la cuenta -->
            <div class="panel panel-default">
                <div class="panel-heading text-center">
                    <h4><i class="fa fa-info-circle" aria-hidden="true"></i> &nbsp; Información de la Cuenta</h4>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 text-center">
                            <div class="well">
                                <h4><i class="fa fa-user text-primary"></i></h4>
                                <h5>Usuario Actual</h5>
                                <p class="lead"><?php echo isset($dataAdmin['Nombre']) ? $dataAdmin['Nombre'] : 'N/A'; ?></p>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6 text-center">
                            <div class="well">
                                <h4><i class="fa fa-shield text-success"></i></h4>
                                <h5>Nivel de Acceso</h5>
                                <p class="lead">Administrador</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>