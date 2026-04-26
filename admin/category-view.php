<p class="lead" style="font-size: 20px;">
    Puede registrar las categorias de sus productos aqui.
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
            <div class="container-form-admin">
                <h3 class="text-info text-center" style="font-size: 28px;">Agregar Nueva Categoría</h3>
                <form action="process/regcategori.php" method="POST" class="FormCatElec" data-form="save">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-xs-12 col-sm-6 col-md-4">
                                <div class="form-group label-floating">
                                    <label class="control-label" style="font-size: 18px;">Código</label>
                                    <input class="form-control" type="text" style="font-size: 17px; padding: 12px;" name="categ-code" maxlength="9" required="">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4">
                                <div class="form-group label-floating">
                                    <label class="control-label" style="font-size: 18px;">Nombre</label>
                                    <input class="form-control" type="text" style="font-size: 17px; padding: 12px;" name="categ-name" maxlength="30" required="">
                                </div>  
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4">
                                <div class="form-group label-floating">
                                    <label class="control-label" style="font-size: 18px;">Descripción</label>
                                    <input class="form-control" type="text" style="font-size: 17px; padding: 12px;" name="categ-descrip" required="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="text-center"><button type="submit" class="btn btn-primary btn-raised" style="font-size: 18px; padding: 12px 24px;">Agregar Categoría</button></p>
                </form>
            </div>
        </div>
    </div>
</div>