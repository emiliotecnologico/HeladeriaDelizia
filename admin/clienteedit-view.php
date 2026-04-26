<p class="lead" style="font-size: 20px;">
    Puede actualizar la información de los clientes aquí.
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
            <div class="container-form-admin">
                <h3 class="text-primary text-center" style="font-size: 28px;">Actualizar Datos del Cliente</h3>
                <?php
                	// MODIFICADO: Usar ID en lugar de NIT para la consulta
                	$id=$_GET['id'];
                	$cliente=ejecutarSQL::consultar("SELECT * FROM clientes WHERE id='$id'");
                	$cli=mysqli_fetch_array($cliente, MYSQLI_ASSOC);
                ?>
                <form action="./process/updateCliente.php" method="POST" class="FormCatElec" data-form="update">
                	<!-- MODIFICADO: Usar ID en lugar de NIT antiguo -->
                	<input type="hidden" name="id" value="<?php echo $cli['id']; ?>">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group label-floating">
                                    <label class="control-label" style="font-size: 18px;">C.I.</label>
                                    <input class="form-control" type="text" style="font-size: 17px; padding: 12px;" name="nit" value="<?php echo $cli['NIT']; ?>" required>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <!-- ELIMINADO: Campo de Nombre de Usuario -->
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group label-floating">
                                    <label class="control-label" style="font-size: 18px;">Nombres</label>
                                    <input class="form-control" type="text" style="font-size: 17px; padding: 12px;" name="nombre-completo" value="<?php echo $cli['NombreCompleto']; ?>" required>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group label-floating">
                                    <label class="control-label" style="font-size: 18px;">Apellidos</label>
                                    <input class="form-control" type="text" style="font-size: 17px; padding: 12px;" name="apellido" value="<?php echo $cli['Apellido']; ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group label-floating">
                                    <label class="control-label" style="font-size: 18px;">Dirección</label>
                                    <input class="form-control" type="text" style="font-size: 17px; padding: 12px;" name="direccion" value="<?php echo $cli['Direccion']; ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group label-floating">
                                    <label class="control-label" style="font-size: 18px;">Teléfono</label>
                                    <input class="form-control" type="text" style="font-size: 17px; padding: 12px;" name="telefono" value="<?php echo $cli['Telefono']; ?>" required>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <!-- ELIMINADO: Campo de Email -->
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group">
                                    <label style="font-size: 18px; margin-bottom: 8px; display: block;">Estado</label>
                                    <select class="form-control" style="font-size: 17px; padding: 12px; height: auto; line-height: 1.5;" name="estado">
                                        <?php
                                            // Mostrar en mayúsculas pero guardar en minúsculas
                                            if($cli['Estado']=="activo"){
                                                echo '
                                                    <option value="activo" selected>Activo (Actual)</option>
                                                    <option value="inactivo">Inactivo</option>
                                                ';
                                            }else{
                                                echo '
                                                    <option value="activo">Activo</option>
                                                    <option value="inactivo" selected>Inactivo (Actual)</option>
                                                ';
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <p class="text-center">
                            <button type="submit" class="btn btn-success btn-raised" style="font-size: 18px; padding: 12px 24px;">Actualizar Cliente</button>
                            <a href="configAdmin.php?view=clientelist" class="btn btn-default btn-raised" style="font-size: 18px; padding: 12px 24px;">Cancelar</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
	</div>
</div>