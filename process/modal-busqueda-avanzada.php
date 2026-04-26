<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- Modal búsqueda avanzada simplificada -->
<div class="modal fade" id="modalBusquedaAvanzada" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #16a085; color: white;">
                <h4 class="modal-title" style="font-size: 24px;"><i class="fa fa-search-plus"></i> Búsqueda Avanzada</h4>
                <button type="button" class="close" data-dismiss="modal" style="color: white; font-size: 28px;">&times;</button>
            </div>
            <div class="modal-body">
                <form id="formBusquedaAvanzada">
                    <div class="form-group">
                        <label for="avanzada-nit" style="font-size: 18px;">C.I.</label>
                        <input type="text" class="form-control" id="avanzada-nit" name="avanzada-nit" placeholder="Ingrese C.I. o NIT" style="font-size: 17px; padding: 12px;">
                    </div>
                    <div class="form-group">
                        <label for="avanzada-nombre" style="font-size: 18px;">Nombre Completo</label>
                        <input type="text" class="form-control" id="avanzada-nombre" name="avanzada-nombre" placeholder="Ingrese nombre completo" style="font-size: 17px; padding: 12px;">
                    </div>
                </form>
                
                <div id="resultados-busqueda-avanzada" style="margin-top: 25px; max-height: 350px; overflow-y: auto;">
                    <div class="text-center text-muted">
                        <i class="fa fa-search fa-3x"></i><br>
                        <span style="font-size: 18px;">Complete los criterios de búsqueda</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="font-size: 18px; padding: 12px 20px;">Cerrar</button>
                <button type="button" class="btn btn-info" id="btn-buscar-avanzada" style="background-color: #16a085; border-color: #1abc9c; color: white; font-size: 18px; padding: 12px 20px;">
                    <i class="fa fa-search"></i> Buscar
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.cliente-resultado {
    padding: 15px; border: 2px solid #e0e0e0; border-radius: 8px; margin-bottom: 12px;
    cursor: pointer; transition: all 0.3s; font-size: 17px;
}
.cliente-resultado:hover { background: #f8f9fa; border-color: #2980b9; }
.cliente-nombre { font-weight: bold; color: #2980b9; font-size: 18px; }
.cliente-detalle { color: #666; font-size: 16px; margin-top: 6px; }
</style>

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

function realizarBusquedaAvanzada() {
    var formData = $('#formBusquedaAvanzada').serialize();
    $('#resultados-busqueda-avanzada').html('<div class="text-center" style="font-size: 18px;"><i class="fa fa-spinner fa-spin"></i> Buscando...</div>');
    
    $.ajax({
        url: basePath + '/process/busqueda_avanzada_clientes.php',
        type: 'POST', data: formData, dataType: 'json',
        success: function(response) {
            if (response.success && response.clientes.length > 0) {
                var html = '';
                response.clientes.forEach(function(cliente) {
                    html += '<div class="cliente-resultado" data-ci="' + cliente.NIT + '" data-id="' + cliente.id + '">' +
                            '<div class="cliente-nombre">' + cliente.NombreCompleto + ' ' + cliente.Apellido + '</div>' +
                            '<div class="cliente-detalle">C.I.: ' + cliente.NIT + ' | Tel: ' + (cliente.Telefono || 'N/A') + '</div>' +
                            '</div>';
                });
                $('#resultados-busqueda-avanzada').html(html);
            } else {
                $('#resultados-busqueda-avanzada').html('<div class="alert alert-warning text-center" style="font-size: 18px;">No se encontraron clientes.</div>');
            }
        },
        error: function() {
            $('#resultados-busqueda-avanzada').html('<div class="alert alert-danger text-center" style="font-size: 18px;">Error de conexión.</div>');
        }
    });
}

$(document).ready(function() {
    $('#btn-buscar-avanzada').on('click', realizarBusquedaAvanzada);

    $(document).on('click', '.cliente-resultado', function() {
        $('#modalBusquedaAvanzada').modal('hide');
        if (typeof buscarCliente === 'function') {
            buscarCliente($(this).data('ci'), $(this).data('id'));
        }
    });
});
</script>