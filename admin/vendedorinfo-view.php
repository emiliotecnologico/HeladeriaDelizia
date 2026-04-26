<p class="lead" style="font-size: 20px;">
    Puede actualizar la información de los vendedores aquí.
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
            <div class="container-form-admin">
                <h3 class="text-primary text-center" style="font-size: 28px;">Actualizar Datos del Vendedor</h3>
                <?php
                	$nit=$_GET['nit'];
                	$vendedor=ejecutarSQL::consultar("SELECT * FROM vendedores WHERE NIT='$nit'");
                	$vend=mysqli_fetch_array($vendedor, MYSQLI_ASSOC);
                ?>
                <form action="./process/updateVendedor.php" method="POST" class="FormCatElec" data-form="update">
                	<input type="hidden" name="nit-old" value="<?php echo $vend['NIT']; ?>">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-xs-12">
                                <legend style="font-size: 22px;">Datos Básicos</legend>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group label-floating">
                                    <label class="control-label" style="font-size: 18px;">C.I.</label>
                                    <input class="form-control" type="text" style="font-size: 17px; padding: 12px;" name="vendedor-nit" value="<?php echo $vend['NIT']; ?>" required readonly>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group label-floating">
                                    <label class="control-label" style="font-size: 18px;">Nombre de Usuario</label>
                                    <input class="form-control" type="text" style="font-size: 17px; padding: 12px;" name="vendedor-name" value="<?php echo $vend['Nombre']; ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group label-floating">
                                    <label class="control-label" style="font-size: 18px;">Nombre Completo</label>
                                    <input class="form-control" type="text" style="font-size: 17px; padding: 12px;" name="vendedor-fullname" value="<?php echo $vend['NombreCompleto']; ?>" required>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group label-floating">
                                    <label class="control-label" style="font-size: 18px;">Apellido</label>
                                    <input class="form-control" type="text" style="font-size: 17px; padding: 12px;" name="vendedor-lastname" value="<?php echo $vend['Apellido']; ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="alert alert-info" style="font-size: 16px;">
                                    <i class="fa fa-info-circle"></i> <strong>Nota:</strong> No es necesario actualizar la contraseña después de editar algún dato del vendedor. Solo complete los campos de contraseña si desea cambiarla.
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group label-floating">
                                    <label class="control-label" style="font-size: 18px;">Nueva Contraseña (opcional)</label>
                                    <input class="form-control" type="password" style="font-size: 17px; padding: 12px;" name="vendedor-pass1">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group label-floating">
                                    <label class="control-label" style="font-size: 18px;">Repetir Nueva Contraseña</label>
                                    <input class="form-control" type="password" style="font-size: 17px; padding: 12px;" name="vendedor-pass2">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group label-floating">
                                    <label class="control-label" style="font-size: 18px;">Dirección</label>
                                    <input class="form-control" type="text" style="font-size: 17px; padding: 12px;" name="vendedor-dir" value="<?php echo $vend['Direccion']; ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group label-floating">
                                    <label class="control-label" style="font-size: 18px;">Teléfono</label>
                                    <input class="form-control" type="text" style="font-size: 17px; padding: 12px;" name="vendedor-phone" value="<?php echo $vend['Telefono']; ?>" required>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group label-floating">
                                    <label class="control-label" style="font-size: 18px;">Email</label>
                                    <input class="form-control" type="email" style="font-size: 17px; padding: 12px;" name="vendedor-email" value="<?php echo $vend['Email']; ?>" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="text-center">
                        <button type="submit" class="btn btn-success btn-raised" style="font-size: 18px; padding: 12px 24px;">Actualizar Vendedor</button>
                        <a href="configAdmin.php?view=vendedorlist" class="btn btn-default btn-raised" style="font-size: 18px; padding: 12px 24px;">Cancelar</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>