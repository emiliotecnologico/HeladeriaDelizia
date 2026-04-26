<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once "library/configServer.php";
require_once "library/consulSQL.php";

// Obtener productos del carrito actual - CORREGIDO: verificación más robusta
$carrito_productos = [];
$total_carrito = 0;
$hay_productos = false;

// DEBUG: Mostrar contenido del carrito para diagnóstico
error_log("DEBUG Carrito session: " . print_r($_SESSION['carrito'] ?? 'NO EXISTE', true));

if (isset($_SESSION['carrito']) && is_array($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
    foreach($_SESSION['carrito'] as $codigoProd => $cantidad) {
        if ($cantidad > 0) {
            $consulta = ejecutarSQL::consultar("SELECT * FROM producto WHERE CodigoProd='$codigoProd'");
            if ($consulta && mysqli_num_rows($consulta) > 0) {
                $fila = mysqli_fetch_array($consulta, MYSQLI_ASSOC);
                $precioFinal = $fila['Precio'];
                $subtotal = $precioFinal * $cantidad;
                $total_carrito += $subtotal;
                
                $carrito_productos[] = [
                    'codigo' => $codigoProd,
                    'nombre' => $fila['NombreProd'],
                    'precio' => $precioFinal,
                    'cantidad' => $cantidad,
                    'subtotal' => $subtotal
                ];
                $hay_productos = true;
                mysqli_free_result($consulta);
            }
        }
    }
}

// DEBUG: Mostrar productos procesados
error_log("DEBUG Productos procesados: " . print_r($carrito_productos, true));
error_log("DEBUG Hay productos: " . ($hay_productos ? 'SI' : 'NO'));
?>

<!-- Modal de pedido simplificado -->
<div class="modal fade" id="modalPedido" tabindex="-1" role="dialog" aria-labelledby="modalPedidoLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #2c3e50; color: white;">
                <h4 class="modal-title" id="modalPedidoLabel" style="font-size: 24px;">
                    <i class="fa fa-credit-card"></i> Confirmar Venta
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; font-size: 28px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form method="POST" role="form" enctype="multipart/form-data" id="formPagoModal">
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    
                    <!-- Lista de productos - CORREGIDO: Mostrar siempre los productos del carrito actual -->
                    <div class="panel panel-warning">
                        <div class="panel-heading" style="background-color: #f39c12; color: white;">
                            <h5 class="panel-title" style="font-size: 20px;">
                                <i class="fa fa-shopping-cart"></i> Productos en el Carrito 
                                <span class="badge badge-light" id="cart-items-count" style="font-size: 18px;"><?php echo count($carrito_productos); ?></span>
                            </h5>
                        </div>
                        <div class="panel-body">
                            <div id="cart-modal-content">
                                <?php if($hay_productos && !empty($carrito_productos)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th style="font-size: 18px;">Producto</th>
                                                    <th width="80" style="font-size: 18px;">Cantidad</th>
                                                    <th width="100" style="font-size: 18px;">Precio Unit.</th>
                                                    <th width="100" style="font-size: 18px;">Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($carrito_productos as $producto): ?>
                                                <tr>
                                                    <td style="font-size: 17px;"><?php echo htmlspecialchars($producto['nombre']); ?></td>
                                                    <td class="text-center" style="font-size: 17px;"><?php echo $producto['cantidad']; ?></td>
                                                    <td class="text-right" style="font-size: 17px;">Bs. <?php echo number_format($producto['precio'], 2); ?></td>
                                                    <td class="text-right" style="font-size: 17px;">Bs. <?php echo number_format($producto['subtotal'], 2); ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                            <tfoot class="table-success">
                                                <tr>
                                                    <th colspan="3" class="text-right" style="font-size: 18px;">TOTAL:</th>
                                                    <th class="text-right" style="font-size: 18px;">Bs. <?php echo number_format($total_carrito, 2); ?></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-warning text-center" id="alert-carrito-vacio" style="font-size: 18px;">
                                        <i class="fa fa-exclamation-triangle"></i> No hay productos en el carrito
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Búsqueda de cliente -->
                    <div class="panel panel-primary">
                        <div class="panel-heading" style="background-color: #2980b9; color: white;">
                            <h5 class="panel-title" style="font-size: 20px;">
                                <i class="fa fa-users"></i> Seleccionar Cliente
                                <button type="button" class="btn btn-warning btn-xs pull-right" id="btn-registrar-cliente" style="margin-top: -5px; background-color: #e67e22; border-color: #d35400; color: white; font-size: 16px; padding: 8px 12px;">
                                    <i class="fa fa-plus"></i> Registrar Cliente
                                </button>
                            </h5>
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label for="search-ci" style="font-size: 18px;">Buscar Cliente</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="search-ci" name="search-ci" placeholder="C.I. o nombre del cliente" autocomplete="off" style="font-size: 17px; padding: 12px;">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-primary" id="btn-search-client" style="font-size: 17px; padding: 12px 20px;">
                                            <i class="fa fa-search"></i> Buscar
                                        </button>
                                        <button type="button" class="btn btn-info" id="btn-busqueda-avanzada" style="background-color: #16a085; border-color: #1abc9c; color: white; font-size: 17px; padding: 12px 20px;">
                                            <i class="fa fa-search-plus"></i> Avanzada
                                        </button>
                                    </span>
                                </div>
                            </div>
                            
                            <div id="suggestions-list" class="suggestions-container" style="display: none;">
                                <div id="suggestions-content" class="suggestions-content"></div>
                            </div>
                            
                            <div id="cliente-info" style="display: none; margin-top: 20px; padding: 20px; background: #f8f9fa; border-radius: 8px; border-left: 5px solid #27ae60;">
                                <h6 style="color: #27ae60; margin-bottom: 20px; font-size: 20px;">
                                    <i class="fa fa-user-check"></i> Cliente Seleccionado
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p style="font-size: 17px;"><strong>C.I.:</strong> <span id="info-ci" style="color: #2980b9; font-size: 17px;"></span></p>
                                        <p style="font-size: 17px;"><strong>Nombre:</strong> <span id="info-nombre" style="font-size: 17px;"></span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p style="font-size: 17px;"><strong>Teléfono:</strong> <span id="info-telefono" style="font-size: 17px;"></span></p>
                                        <p style="font-size: 17px;"><strong>Dirección:</strong> <span id="info-direccion" style="font-size: 17px;"></span></p>
                                    </div>
                                </div>
                                <input type="hidden" name="id_cliente" id="id_cliente">
                            </div>

                            <div id="client-error" class="alert alert-danger" style="display: none; margin-top: 20px; font-size: 17px;">
                                <i class="fa fa-exclamation-triangle"></i> No se encontró ningún cliente.
                            </div>

                            <div id="new-client-option" class="alert alert-info" style="display: none; margin-top: 20px; font-size: 17px;">
                                <button type="button" class="btn btn-warning btn-sm" id="btn-new-client" style="background-color: #e67e22; border-color: #d35400; color: white; font-size: 16px; padding: 10px 15px;">
                                    <i class="fa fa-plus"></i> Registrar Nuevo Cliente
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Comprobante -->
                    <div class="form-group">
                        <label for="comprobante" style="font-size: 18px;">Comprobante de Pago</label>
                        <div class="file-input-wrapper">
                            <div class="file-input-display" id="fileDisplay" style="font-size: 17px; padding: 20px; border: 2px dashed #ddd; border-radius: 8px; background: #fafafa; cursor: pointer; transition: all 0.3s; text-align: center;">
                                <i class="fa fa-cloud-upload"></i> Seleccione comprobante
                            </div>
                            <input type="file" class="form-control-file" id="comprobante" name="comprobante" accept="image/jpeg,image/png,image/jpg,application/pdf" required style="font-size: 17px;">
                        </div>
                        <small class="form-text text-muted" style="font-size: 16px;">Formatos: JPG, PNG, PDF (max 5MB)</small>
                    </div>

                    <input type="hidden" name="tipo-envio" value="Recoger en Tienda">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" id="btn-cancelar-modal" style="font-size: 18px; padding: 12px 20px;">
                        <i class="fa fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-success" id="btn-confirm-pedido" <?php echo !$hay_productos ? 'disabled' : ''; ?> style="font-size: 18px; padding: 12px 20px;">
                        <i class="fa fa-check"></i> Confirmar Venta
                    </button>
                    <button type="button" class="btn btn-primary" id="btn-imprimir-factura" style="display: none; font-size: 18px; padding: 12px 20px;">
                        <i class="fa fa-print"></i> Imprimir Factura
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.modal-body { max-height: 70vh; overflow-y: auto; }

.suggestions-container {
    border: 2px solid #ddd; border-radius: 8px; margin-top: 10px; background: white;
    box-shadow: 0 3px 15px rgba(0,0,0,0.15);
}
.suggestions-content { max-height: 250px; overflow-y: auto; }
.suggestion-item {
    padding: 12px 15px; border-bottom: 2px solid #f0f0f0; cursor: pointer;
    transition: background 0.2s; font-size: 17px;
}
.suggestion-item:hover { background: #e3f2fd; }
.suggestion-item:last-child { border-bottom: none; }
.suggestion-ci { font-weight: bold; color: #2980b9; }
.suggestion-name { color: #333; margin-left: 10px; }

.file-input-wrapper { position: relative; }
.file-input-display {
    border: 2px dashed #ddd; padding: 20px; text-align: center; border-radius: 8px;
    background: #fafafa; cursor: pointer; transition: all 0.3s;
}
.file-input-display:hover { border-color: #2980b9; background: #f0f8ff; }
.file-input-wrapper input[type="file"] {
    position: absolute; top: 0; left: 0; width: 100%; height: 100%;
    opacity: 0; cursor: pointer;
}

/* Estilos para el estado posterior al pedido */
.modal-content.disabled-after-order {
    opacity: 0.9;
}
.modal-content.disabled-after-order .modal-body {
    pointer-events: none;
}

/* Ajustes para los paneles */
.panel-heading {
    padding: 15px 20px;
}

.panel-body {
    padding: 20px;
}

/* Mejoras para los botones */
.btn {
    font-size: 17px;
    padding: 10px 16px;
}

.btn-xs {
    padding: 8px 12px;
    font-size: 16px;
}
</style>

<script>
// Variables globales
var searchTimeout = null;
var numPedidoConfirmado = null;
var pedidoConfirmado = false;

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

function showConfirm(title, message, confirmCallback) {
    swal({
        title: title, text: message, type: "warning", showCancelButton: true,
        confirmButtonColor: "#3085d6", cancelButtonColor: "#d33",
        confirmButtonText: "Sí, confirmar", cancelButtonText: "Cancelar"
    }, function(isConfirm) {
        if (isConfirm && typeof confirmCallback === 'function') confirmCallback();
    });
}

// CORREGIDO: Función para actualizar el contenido del carrito en el modal
function actualizarCarritoModal() {
    $.ajax({
        url: basePath + '/process/get_cart_preview.php',
        type: 'GET',
        success: function(response) {
            // Actualizar el contador del carrito
            var cartCount = 0;
            var tempDiv = document.createElement('div');
            tempDiv.innerHTML = response;
            
            // Contar items del carrito
            var cartItems = tempDiv.querySelectorAll('.cart-item');
            cartCount = cartItems.length;
            
            // Actualizar badge
            $('#cart-items-count').text(cartCount);
            
            // Si hay productos, construir la tabla
            if (cartCount > 0) {
                var html = '<div class="table-responsive">';
                html += '<table class="table table-bordered table-sm">';
                html += '<thead class="thead-light"><tr><th style="font-size: 18px;">Producto</th><th width="80" style="font-size: 18px;">Cantidad</th><th width="100" style="font-size: 18px;">Precio Unit.</th><th width="100" style="font-size: 18px;">Subtotal</th></tr></thead>';
                html += '<tbody>';
                
                var total = 0;
                cartItems.forEach(function(item) {
                    var nombre = item.querySelector('.cart-item-name').textContent;
                    var detalles = item.querySelector('.cart-item-details').textContent;
                    
                    // Extraer cantidad y precio de los detalles
                    var cantidadMatch = detalles.match(/Cantidad:\s*(\d+)/);
                    var precioMatch = detalles.match(/x\s*Bs\.\s*([\d,]+\.\d{2})/);
                    var subtotalMatch = item.querySelector('.cart-item div:last-child').textContent.match(/Bs\.\s*([\d,]+\.\d{2})/);
                    
                    if (cantidadMatch && precioMatch && subtotalMatch) {
                        var cantidad = cantidadMatch[1];
                        var precio = parseFloat(precioMatch[1].replace(',', ''));
                        var subtotal = parseFloat(subtotalMatch[1].replace(',', ''));
                        total += subtotal;
                        
                        html += '<tr>';
                        html += '<td style="font-size: 17px;">' + nombre + '</td>';
                        html += '<td class="text-center" style="font-size: 17px;">' + cantidad + '</td>';
                        html += '<td class="text-right" style="font-size: 17px;">Bs. ' + precio.toFixed(2) + '</td>';
                        html += '<td class="text-right" style="font-size: 17px;">Bs. ' + subtotal.toFixed(2) + '</td>';
                        html += '</tr>';
                    }
                });
                
                html += '</tbody>';
                html += '<tfoot class="table-success">';
                html += '<tr><th colspan="3" class="text-right" style="font-size: 18px;">TOTAL:</th><th class="text-right" style="font-size: 18px;">Bs. ' + total.toFixed(2) + '</th></tr>';
                html += '</tfoot>';
                html += '</table>';
                html += '</div>';
                
                $('#cart-modal-content').html(html);
                $('#btn-confirm-pedido').prop('disabled', false);
                $('#alert-carrito-vacio').hide();
            } else {
                // No hay productos
                $('#cart-modal-content').html('<div class="alert alert-warning text-center" id="alert-carrito-vacio" style="font-size: 18px;"><i class="fa fa-exclamation-triangle"></i> No hay productos en el carrito</div>');
                $('#btn-confirm-pedido').prop('disabled', true);
            }
        },
        error: function() {
            console.error('Error al cargar el carrito');
        }
    });
}

function buscarSugerencias(termino) {
    if (!termino || termino.length < 2) {
        $('#suggestions-list').hide(); return;
    }
    
    $.ajax({
        url: basePath + '/process/buscar_sugerencias_clientes.php',
        type: 'GET', data: { termino: termino }, dataType: 'json',
        success: function(response) {
            if (response.success && response.clientes.length > 0) {
                var html = '';
                response.clientes.forEach(function(cliente) {
                    html += '<div class="suggestion-item" data-ci="' + cliente.NIT + '" data-id="' + cliente.id + '">' +
                            '<span class="suggestion-ci">' + cliente.NIT + '</span>' +
                            '<span class="suggestion-name">' + cliente.NombreCompleto + ' ' + cliente.Apellido + '</span>' +
                            '</div>';
                });
                $('#suggestions-content').html(html);
                $('#suggestions-list').show();
            } else {
                $('#suggestions-list').hide();
            }
        }
    });
}

function buscarCliente(ci, idCliente) {
    if (!ci && !idCliente) {
        $('#client-error').hide(); $('#cliente-info').hide(); $('#new-client-option').hide();
        if (!pedidoConfirmado) {
            $('#btn-confirm-pedido').prop('disabled', true);
        }
        return;
    }
    
    $('#cliente-info').hide(); $('#client-error').hide(); $('#new-client-option').hide();
    
    $.ajax({
        url: basePath + '/process/buscar_cliente.php',
        type: 'GET', data: { ci: ci || '', id: idCliente || 0 }, dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#info-ci').text(response.nit);
                $('#info-nombre').text(response.nombre_completo + ' ' + response.apellido);
                $('#info-telefono').text(response.telefono || 'No especificado');
                $('#info-direccion').text(response.direccion || 'No especificada');
                $('#id_cliente').val(response.id);
                $('#cliente-info').show();
                $('#client-error').hide();
                $('#new-client-option').hide();
                $('#suggestions-list').hide();
                if (!pedidoConfirmado) {
                    $('#btn-confirm-pedido').prop('disabled', false);
                }
                $('#search-ci').val(response.nit);
            } else {
                $('#cliente-info').hide();
                $('#client-error').show().html('<i class="fa fa-exclamation-triangle"></i> ' + (response.message || 'Cliente no encontrado.'));
                $('#new-client-option').show();
                if (!pedidoConfirmado) {
                    $('#btn-confirm-pedido').prop('disabled', true);
                }
            }
        }
    });
}

function confirmarPedido(event) {
    event.preventDefault();
    
    // Verificar si ya se confirmó un pedido
    if (pedidoConfirmado) {
        return false;
    }
    
    if (!$('#id_cliente').val()) {
        showAlert('Error', 'Seleccione un cliente.', 'error'); return;
    }
    var comprobante = $('#comprobante')[0].files[0];
    if (!comprobante) {
        showAlert('Error', 'Seleccione un comprobante.', 'error'); return;
    }
    
    showConfirm('¿Confirmar pedido?', 'Esta acción no se puede deshacer.', function() {
        procesarConfirmacionPedido();
    });
}

function procesarConfirmacionPedido() {
    $('#btn-confirm-pedido').html('<i class="fa fa-spinner fa-spin"></i> Procesando...').prop('disabled', true);
    var formData = new FormData($('#formPagoModal')[0]);
    
    $.ajax({
        url: basePath + '/process/confirmcompra.php',
        type: 'POST', data: formData, processData: false, contentType: false,
        success: function(response) {
            console.log('Respuesta confirmcompra:', response);
            try {
                var data = typeof response === 'string' ? JSON.parse(response) : response;
                
                if (data.success) {
                    // Marcar que el pedido fue confirmado
                    pedidoConfirmado = true;
                    
                    // Guardar número de pedido para imprimir factura
                    numPedidoConfirmado = data.num_pedido;
                    console.log('Número de pedido confirmado:', numPedidoConfirmado);
                    
                    // Mostrar mensaje de éxito
                    showAlert('¡Éxito!', data.message || 'Pedido confirmado correctamente', 'success');
                    
                    // Ocultar botón de confirmar y mostrar botón de imprimir factura
                    $('#btn-confirm-pedido').hide();
                    $('#btn-imprimir-factura').show();
                    
                    // Aplicar estilo de deshabilitado al modal
                    $('.modal-content').addClass('disabled-after-order');
                    
                    // Actualizar contador del carrito a 0
                    $('#cart-count').text('0');
                    $('#cart-items-count').text('0');
                    
                    // Vaciar carrito usando tu función existente
                    vaciarCarritoDespuesPedido();
                    
                } else {
                    $('#btn-confirm-pedido').html('<i class="fa fa-check"></i> Confirmar Pedido').prop('disabled', false);
                    showAlert('Error', data.message || 'Error al confirmar el pedido', 'error');
                }
            } catch (e) {
                console.error('Error parseando respuesta:', e, response);
                $('#btn-confirm-pedido').html('<i class="fa fa-check"></i> Confirmar Pedido').prop('disabled', false);
                showAlert('Error', 'Error al procesar la respuesta del servidor', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error AJAX:', status, error, xhr.responseText);
            $('#btn-confirm-pedido').html('<i class="fa fa-check"></i> Confirmar Pedido').prop('disabled', false);
            showAlert('Error', 'Error de conexión: ' + error, 'error');
        }
    });
}

// Función para vaciar carrito usando tu archivo existente
function vaciarCarritoDespuesPedido() {
    $.ajax({
        url: basePath + '/process/vaciar_carrito_ajax.php',
        type: 'GET',
        success: function(response) {
            console.log('Carrito vaciado después del pedido');
            // Tu archivo ya maneja el SweetAlert y recarga, pero en este caso no queremos recargar
            // porque estamos mostrando el botón de imprimir factura
        }
    });
}

function imprimirFactura() {
    if (numPedidoConfirmado) {
        window.open(basePath + '/report/factura.php?id=' + numPedidoConfirmado, '_blank');
        
        // Mostrar mensaje de éxito y opción para cerrar
        swal({
            title: "Factura Generada",
            text: "La factura se ha abierto en una nueva ventana. ¿Desea cerrar este modal?",
            type: "success",
            showCancelButton: true,
            confirmButtonClass: "btn-primary",
            confirmButtonText: "Sí, cerrar",
            cancelButtonText: "No, mantener abierto",
            closeOnConfirm: false,
            closeOnCancel: true
        }, function(isConfirm) {
            if (isConfirm) {
                $('#modalPedido').modal('hide');
                // Recargar la página para actualizar la interfaz
                setTimeout(function() {
                    location.reload();
                }, 500);
            }
        });
    } else {
        showAlert('Error', 'No hay número de pedido para imprimir la factura.', 'error');
    }
}

function resetearModal() {
    pedidoConfirmado = false;
    numPedidoConfirmado = null;
    $('.modal-content').removeClass('disabled-after-order');
    $('#formPagoModal')[0].reset();
    $('#formPagoModal :input').prop('disabled', false);
    $('#cliente-info, #client-error, #new-client-option, #suggestions-list').hide();
    $('#btn-confirm-pedido').prop('disabled', true).show();
    $('#btn-imprimir-factura').hide();
    $('#fileDisplay').html('<i class="fa fa-cloud-upload"></i> Seleccione comprobante');
    
    // CORREGIDO: Actualizar el carrito cada vez que se abre el modal
    actualizarCarritoModal();
}

$(document).ready(function() {
    $(document).off('submit', '#formPagoModal');
    
    $('#modalPedido').on('shown.bs.modal', function () {
        resetearModal();
        $('#search-ci').focus();
    });

    $('#modalPedido').on('hidden.bs.modal', function () {
        // Solo recargar si se confirmó un pedido
        if (pedidoConfirmado) {
            setTimeout(function() {
                location.reload();
            }, 100);
        }
    });

    $('#btn-cancelar-modal').on('click', function() {
        // Si hay un pedido confirmado, recargar la página
        if (pedidoConfirmado) {
            location.reload();
        }
    });

    $('#search-ci').on('input', function() {
        var termino = $(this).val().trim();
        if (searchTimeout) clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() { buscarSugerencias(termino); }, 300);
    });

    $(document).on('click', '.suggestion-item', function() {
        buscarCliente($(this).data('ci'), $(this).data('id'));
    });

    $('#btn-search-client').on('click', function() {
        var termino = $('#search-ci').val().trim();
        if (termino) buscarCliente(termino);
    });

    $('#search-ci').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            var termino = $(this).val().trim();
            if (termino) buscarCliente(termino);
        }
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#search-ci, #suggestions-list').length) {
            $('#suggestions-list').hide();
        }
    });

    // Cargar modales externos
    $('#btn-new-client, #btn-registrar-cliente').on('click', function() {
        if (!pedidoConfirmado) {
            $('#new-nit').val($('#search-ci').val().trim());
            $('#modalNuevoCliente').modal('show');
        }
    });

    $('#btn-busqueda-avanzada').on('click', function() {
        if (!pedidoConfirmado) {
            $('#modalBusquedaAvanzada').modal('show');
        }
    });

    $('#comprobante').on('change', function() {
        if (!pedidoConfirmado) {
            var fileName = $(this).val().split('\\').pop();
            $('#fileDisplay').html(fileName ? '<i class="fa fa-file"></i> ' + fileName : '<i class="fa fa-cloud-upload"></i> Seleccione comprobante');
        }
    });

    $('#formPagoModal').on('submit', confirmarPedido);
    $('#btn-imprimir-factura').on('click', imprimirFactura);
});
</script>