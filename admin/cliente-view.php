<p class="lead" style="font-size: 20px;">
    Puede agregar un nuevo cliente aquí.
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
            <div class="panel panel-info">
                <div class="panel-heading text-center"><h4 style="font-size: 28px;">Agregar Nuevo Cliente</h4></div>
                <div class="panel-body">
                    <form action="process/addclient.php" method="POST" class="FormCatElec" data-form="save">
                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group label-floating">
                                    <label class="control-label" style="font-size: 18px;">C.I.</label>
                                    <input class="form-control" type="text" style="font-size: 17px; padding: 12px;" name="nit" required>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <!-- ESPACIO VACÍO - Campo de usuario eliminado -->
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group label-floating">
                                    <label class="control-label" style="font-size: 18px;">Nombres</label>
                                    <input class="form-control" type="text" style="font-size: 17px; padding: 12px;" name="nombre-completo" required>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group label-floating">
                                    <label class="control-label" style="font-size: 18px;">Apellidos</label>
                                    <input class="form-control" type="text" style="font-size: 17px; padding: 12px;" name="apellido" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group label-floating">
                                    <label class="control-label" style="font-size: 18px;">Dirección</label>
                                    <input class="form-control" type="text" style="font-size: 17px; padding: 12px;" name="direccion" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group label-floating">
                                    <label class="control-label" style="font-size: 18px;">Teléfono</label>
                                    <input class="form-control" type="text" style="font-size: 17px; padding: 12px;" name="telefono" required>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <!-- ESPACIO VACÍO - Campo de email eliminado -->
                            </div>
                        </div>
                        <p class="text-center">
                            <button type="submit" class="btn btn-raised btn-success" style="font-size: 18px; padding: 12px 24px;">Agregar Cliente</button>
                        </p>
                    </form>
                </div>
            </div>
        </div>
	</div>
</div>