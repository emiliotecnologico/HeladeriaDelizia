<p class="lead" style="font-size: 20px;">
    Puede agregar un nuevo vendedor aquí.
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
                <h3 class="text-info text-center" style="font-size: 28px;">Agregar un Nuevo Vendedor</h3>
                <form action="process/regVendedor.php" method="POST" role="form" class="FormCatElec" data-form="save">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group label-floating">
                                    <label class="control-label" style="font-size: 18px;">C.I.</label>
                                    <input class="form-control" type="text" style="font-size: 17px; padding: 12px;" name="vendedor-nit" required>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group label-floating">
                                    <label class="control-label" style="font-size: 18px;">Nombre de Usuario</label>
                                    <input class="form-control" type="text" style="font-size: 17px; padding: 12px;" name="vendedor-name" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group label-floating">
                                    <label class="control-label" style="font-size: 18px;">Nombre Completo</label>
                                    <input class="form-control" type="text" style="font-size: 17px; padding: 12px;" name="vendedor-fullname" required>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group label-floating">
                                    <label class="control-label" style="font-size: 18px;">Apellido</label>
                                    <input class="form-control" type="text" style="font-size: 17px; padding: 12px;" name="vendedor-lastname" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group label-floating">
                                    <label class="control-label" style="font-size: 18px;">Ingrese la Contraseña</label>
                                    <input class="form-control" type="password" style="font-size: 17px; padding: 12px;" name="vendedor-pass1" required>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group label-floating">
                                    <label class="control-label" style="font-size: 18px;">Repita la Contraseña</label>
                                    <input class="form-control" type="password" style="font-size: 17px; padding: 12px;" name="vendedor-pass2" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group label-floating">
                                    <label class="control-label" style="font-size: 18px;">Dirección</label>
                                    <input class="form-control" type="text" style="font-size: 17px; padding: 12px;" name="vendedor-dir" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group label-floating">
                                    <label class="control-label" style="font-size: 18px;">Teléfono</label>
                                    <input class="form-control" type="text" style="font-size: 17px; padding: 12px;" name="vendedor-phone" required>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group label-floating">
                                    <label class="control-label" style="font-size: 18px;">Email</label>
                                    <input class="form-control" type="email" style="font-size: 17px; padding: 12px;" name="vendedor-email" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="text-center"><button type="submit" class="btn btn-primary btn-raised" style="font-size: 18px; padding: 12px 24px;">Agregar Vendedor</button></p>
                </form>
            </div>
        </div>
    </div>
</div>