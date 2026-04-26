<p class="lead" style="font-size: 20px;">
    Puede agregar productos aqui.
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
            <div class="container-form-admin">
                <h3 class="text-primary text-center" style="font-size: 28px;">Agregar un Producto a la Tienda</h3>
                <form action="./process/regproduct.php" method="POST" enctype="multipart/form-data" class="FormCatElec" data-form="save">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-xs-12">
                                <legend style="font-size: 22px;">Datos Básicos</legend>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4">
                              <div class="form-group label-floating">
                                <label class="control-label" style="font-size: 18px;">Código de Producto</label>
                                <input type="text" class="form-control" style="font-size: 17px; padding: 12px;" required maxlength="30" name="prod-codigo">
                              </div>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4">
                              <div class="form-group label-floating">
                                <label class="control-label" style="font-size: 18px;">Nombre de Producto</label>
                                <input type="text" class="form-control" style="font-size: 17px; padding: 12px;" required maxlength="100" name="prod-name">
                              </div>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4">
                              <div class="form-group label-floating">
                                <label class="control-label" style="font-size: 18px;">Precio (Bs.)</label>
                                <input type="text" class="form-control" style="font-size: 17px; padding: 12px;" required maxlength="20" pattern="[0-9.]{1,20}" name="prod-price">
                              </div>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4">
                              <div class="form-group label-floating">
                                <label class="control-label" style="font-size: 18px;">Aumento (%)</label>
                                <input type="text" class="form-control" style="font-size: 17px; padding: 12px;" required maxlength="2" pattern="[0-9]{1,2}" name="prod-aumento" value="0">
                              </div>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4">
                              <div class="form-group label-floating">
                                <label class="control-label" style="font-size: 18px;">Stock Disponible</label>
                                <input type="text" class="form-control" style="font-size: 17px; padding: 12px;" required maxlength="20" pattern="[0-9]{1,20}" name="prod-stock">
                              </div>
                            </div>
                            <div class="col-xs-12">
                                <legend style="font-size: 22px;">Categoría y Estado</legend>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4">
                              <div class="form-group">
                                <label style="font-size: 18px; margin-bottom: 8px; display: block;">Categoría</label>
                                <select class="form-control" style="font-size: 17px; padding: 12px; height: auto; line-height: 1.5;" name="prod-categoria" required>
                                    <?php
                                        $categoriac= ejecutarSQL::consultar("SELECT * FROM categoria WHERE Estado='activa'");
                                        while($catec=mysqli_fetch_array($categoriac, MYSQLI_ASSOC)){
                                            echo '<option value="'.$catec['CodigoCat'].'">'.$catec['Nombre'].'</option>';
                                        }
                                    ?>
                                </select>
                              </div>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4">
                              <div class="form-group">
                                <label style="font-size: 18px; margin-bottom: 8px; display: block;">Estado</label>
                                <select class="form-control" style="font-size: 17px; padding: 12px; height: auto; line-height: 1.5;" name="prod-estado">
                                    <option value="activo" selected>Activo</option>
                                    <option value="inactivo">Inactivo</option>
                                </select>
                              </div>
                            </div>
                            <div class="col-xs-12">
                                <legend style="font-size: 22px;">Imagen/Foto del producto</legend>
                                <p class="text-center text-primary" style="font-size: 16px;">
                                    Seleccione una imagen/foto en el siguiente campo. Formato de imágenes admitido png y jpg. Tamaño máximo 5MB
                                </p>
                            </div>
                            <div class="col-xs-12">
                                <div class="form-group">
                                  <input type="file" name="img" accept=".jpg,.jpeg,.png" style="font-size: 17px;">
                                  <div class="input-group">
                                    <input type="text" readonly class="form-control" style="font-size: 17px; padding: 12px;" placeholder="Seleccione la imagen del producto...">
                                      <span class="input-group-btn input-group-sm">
                                        <button type="button" class="btn btn-fab btn-fab-mini" style="font-size: 18px;">
                                          <i class="fa fa-file-image-o" aria-hidden="true"></i>
                                        </button>
                                      </span>
                                  </div>
                                    <p class="help-block" style="font-size: 16px;">Formato de imágenes admitido png y jpg. Tamaño máximo 5MB</p>
                                </div>
                            </div>
                        </div>
                    </div>
                <p class="text-center"><button type="submit" class="btn btn-primary btn-raised" style="font-size: 18px; padding: 12px 24px;">Agregar a la Tienda</button></p>
                </form>
            </div>
        </div>     
    </div>
</div>