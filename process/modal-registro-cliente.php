<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- Modal para nuevo cliente -->
<div class="modal fade" id="modalNuevoCliente" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #e67e22; color: white;">
                <h4 class="modal-title" style="font-size: 24px;"><i class="fa fa-user-plus"></i> Registrar Cliente</h4>
                <button type="button" class="close" data-dismiss="modal" style="color: white; font-size: 28px;">&times;</button>
            </div>
            <form id="formNuevoCliente">
                <div class="modal-body">
                    <div class="form-group">
                        <label style="font-size: 18px;">C.I.</label>
                        <input type="text" class="form-control" name="nit" id="new-nit" required style="font-size: 17px; padding: 12px;">
                    </div>
                    <div class="form-group">
                        <label style="font-size: 18px;">Nombre Completo</label>
                        <input type="text" class="form-control" name="nombre_completo" required style="font-size: 17px; padding: 12px;">
                    </div>
                    <div class="form-group">
                        <label style="font-size: 18px;">Apellido</label>
                        <input type="text" class="form-control" name="apellido" required style="font-size: 17px; padding: 12px;">
                    </div>
                    <div class="form-group">
                        <label style="font-size: 18px;">Teléfono</label>
                        <input type="text" class="form-control" name="telefono" required style="font-size: 17px; padding: 12px;">
                    </div>
                    <div class="form-group">
                        <label style="font-size: 18px;">Dirección</label>
                        <textarea class="form-control" name="direccion" rows="2" style="font-size: 17px; padding: 12px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="font-size: 18px; padding: 12px 20px;">Cancelar</button>
                    <button type="submit" class="btn btn-warning" style="background-color: #e67e22; border-color: #d35400; color: white; font-size: 18px; padding: 12px 20px;">
                        <i class="fa fa-save"></i> Registrar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function getBasePath() {
    var pathArray = window.location.pathname.split('/');
    var basePath = '';
    for (var i = 1; i < pathArray.length - 1; i++) {
        basePath += '/' + pathArray[i];
    }
    return basePath || '';
}

var basePath = getBasePath();

function showAlert(title, message, type) {
    swal({ title: title, text: message, type: type, confirmButtonText: "Aceptar" });
}

$(document).ready(function() {
    $('#formNuevoCliente').on('submit', function(e) {
        e.preventDefault();
        $.post(basePath + '/process/registrar_cliente.php', $(this).serialize(), function(response) {
            try {
                var data = typeof response === 'string' ? JSON.parse(response) : response;
                if (data.success) {
                    $('#modalNuevoCliente').modal('hide');
                    // Buscar automáticamente el cliente recién registrado
                    if (typeof buscarCliente === 'function') {
                        buscarCliente(data.nit);
                    }
                    showAlert('Éxito', 'Cliente registrado correctamente', 'success');
                    $('#formNuevoCliente')[0].reset();
                } else {
                    showAlert('Error', data.message || 'Error desconocido', 'error');
                }
            } catch (e) {
                showAlert('Error', 'Error al procesar la respuesta del servidor', 'error');
            }
        }).fail(function(xhr, status, error) {
            showAlert('Error', 'Error de conexión al registrar cliente', 'error');
        });
    });
});
</script>