<?php 
    // NAVBAR.PHP - NO INICIAR SESIÓN AQUÍ, YA SE INICIÓ EN EL ARCHIVO PRINCIPAL
    error_reporting(E_PARSE);
?>
<nav id="navbar-auto-hidden" style="background-color: #3c8dbc; border-color: #367fa9;">
    <!-- Barra superior con logo centrado y botones de usuario - FIJA -->
    <div class="row navbar-top" style="background-color: #3c8dbc; height: 85px; position: fixed; top: 0; left: 0; right: 0; z-index: 1031;">
        <div class="col-xs-12">
            <div style="display: flex; justify-content: space-between; align-items: center; height: 85px; padding: 0 25px;">
                <!-- Botón Mi Perfil (izquierda) - SOLO para vendedores -->
                <?php if (!empty($_SESSION['nombreUser']) && empty($_SESSION['esInvitado'])): ?>
                    <a href="#!" class="userConBtn" data-code="<?php echo $_SESSION['UserNIT']; ?>" style="color: white; text-decoration: none; font-weight: bold; padding: 12px 20px; border: 2px solid white; border-radius: 6px; background-color: rgba(255,255,255,0.1); font-size: 18px;">
                        <i class="glyphicon glyphicon-user" style="margin-right: 8px; font-size: 20px;"></i> Mi Perfil
                    </a>
                <?php else: ?>
                    <div style="width: 120px;"></div> <!-- Espacio vacío para mantener alineación -->
                <?php endif; ?>

                <!-- Logo centrado -->
                <a href="index.php" class="logo-btn" style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-decoration: none;">
                    <img src="assets/img/logo_delizia-1.png" alt="Logo Delizia" class="logo-delizia" style="height: 60px; margin-bottom: -10px;">
                    <div class="logo-subtitle" style="color: white; margin-top: -5px; font-size: 16px; font-weight: bold;">Tienda Oruro-Central</div>
                </a>

                <!-- Botón Cerrar Sesión/Iniciar Sesión (derecha) -->
                <?php if (!empty($_SESSION['nombreAdmin']) || !empty($_SESSION['nombreUser'])): ?>
                    <!-- Usuarios normales: Cerrar Sesión -->
                    <a href="logout.php" class="exit-system" style="color: white; background-color: #dd4b39; text-decoration: none; font-weight: bold; padding: 12px 20px; border-radius: 6px; font-size: 18px;">
                        <i class="fa fa-sign-out" style="margin-right: 8px; font-size: 20px;"></i> Cerrar Sesión
                    </a>
                <?php elseif (!empty($_SESSION['esInvitado'])): ?>
                    <!-- Invitados: Iniciar Sesión -->
                    <a href="login.php" class="login-system" style="color: white; background-color: #28a745; text-decoration: none; font-weight: bold; padding: 12px 20px; border-radius: 6px; font-size: 18px;">
                        <i class="fa fa-sign-in" style="margin-right: 8px; font-size: 20px;"></i> Iniciar Sesión
                    </a>
                <?php else: ?>
                    <div style="width: 120px;"></div> <!-- Espacio vacío para mantener alineación -->
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Barra de navegación inferior CENTRADA Y FIJA -->
    <?php if (!empty($_SESSION['nombreAdmin']) || !empty($_SESSION['nombreUser'])): ?>
    <div class="row navbar-bottom hidden-xs" style="background-color: transparent; height: 60px; position: fixed; top: 85px; left: 0; right: 0; z-index: 1030;">
        <div class="col-xs-12">
            <div class="contenedor-tabla" style="height: 60px; display: table; margin: 0 auto; background-color: transparent; width: auto;">
                <div class="contenedor-tr" style="display: table-row;">
                    <?php
                        if (!empty($_SESSION['nombreAdmin'])) {
                            // Administrador: NO mostrar Productos, solo Estadísticas y Administración
                            echo '
                                <a href="sales-stats.php" class="table-cell-td" style="color: #3c8dbc; text-align: center; font-size: 20px;">Estadísticas</a>
                                <a href="configAdmin.php" class="table-cell-td" style="color: #3c8dbc; text-align: center; font-size: 20px;">Administración</a>
                            ';
                        } else if (!empty($_SESSION['nombreUser']) && empty($_SESSION['esInvitado'])) {
                            // Vendedor: MOSTRAR Productos Y Administración
                            echo '<a href="product.php" class="table-cell-td" style="color: #3c8dbc; text-align: center; font-size: 20px;">Productos</a>';
                            echo '<a href="configAdmin.php" class="table-cell-td" style="color: #3c8dbc; text-align: center; font-size: 20px;">Administración</a>';
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Mobile menu navbar - FIJA -->
    <div class="row visible-xs" style="position: fixed; top: 0; left: 0; right: 0; z-index: 1031; background-color: #3c8dbc;">
        <div class="col-xs-12" style="background-color: #3c8dbc; padding: 8px 0; text-align: center;">
            <?php if (!empty($_SESSION['nombreAdmin']) || !empty($_SESSION['nombreUser']) || !empty($_SESSION['esInvitado'])): ?>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0 15px;">
                    <!-- Botón menú móvil -->
                    <button class="btn btn-default button-mobile-menu" id="btn-mobile-menu" style="background-color: #367fa9; color: white; border: none; padding: 10px 15px; font-size: 18px;">
                        <i class="fa fa-th-list" style="font-size: 20px;"></i>&nbsp;&nbsp;Menú
                    </button>
                    
                    <!-- Logo móvil -->
                    <a href="index.php" class="logo-btn" style="display: inline-flex; flex-direction: column; align-items: center; justify-content: center; height: 70px;">
                        <img src="assets/img/logo_delizia-1.png" alt="Logo Delizia" class="logo-delizia" style="height: 50px; margin-bottom: -8px;">
                        <div class="logo-subtitle" style="color: white; margin-top: -5px; font-size: 14px; font-weight: bold;">Suc. Oruro-Central</div>
                    </a>
                    
                    <!-- Botón móvil -->
                    <?php if (!empty($_SESSION['nombreAdmin']) || !empty($_SESSION['nombreUser'])): ?>
                        <a href="logout.php" class="exit-system" style="color: white; background-color: #dd4b39; padding: 10px 15px; border-radius: 6px; text-decoration: none; font-size: 16px; font-weight: bold;">
                            <i class="fa fa-sign-out" style="font-size: 18px;"></i>
                        </a>
                    <?php elseif (!empty($_SESSION['esInvitado'])): ?>
                        <a href="login.php" class="login-system" style="color: white; background-color: #28a745; padding: 10px 15px; border-radius: 6px; text-decoration: none; font-size: 16px; font-weight: bold;">
                            <i class="fa fa-sign-in" style="font-size: 18px;"></i>
                        </a>
                    <?php else: ?>
                        <div style="width: 50px;"></div> <!-- Espacio vacío para mantener alineación -->
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <!-- Para no logueados: solo mostrar el logo centrado -->
                <a href="index.php" class="logo-btn" style="display: inline-flex; flex-direction: column; align-items: center; justify-content: center; height: 70px;">
                    <img src="assets/img/logo_delizia-1.png" alt="Logo Delizia" class="logo-delizia" style="height: 55px; margin-bottom: -10px;">
                    <div class="logo-subtitle" style="color: white; margin-top: -5px; font-size: 16px; font-weight: bold;">Suc. Oruro-Central</div>
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- Menú móvil - Solo mostrar si hay sesión activa (admin o vendedor) -->
<?php if (!empty($_SESSION['nombreAdmin']) || !empty($_SESSION['nombreUser'])): ?>
<div id="mobile-menu-list" class="hidden-sm hidden-md hidden-lg" style="background-color: rgba(255, 255, 255, 0.98); position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 1040; padding-top: 25px; display: none;">
    <br>
    <!-- Logo en versión móvil -->
    <a href="index.php" class="logo-btn" style="display: flex; flex-direction: column; align-items: center; justify-content: center;">
        <img src="assets/img/logo_delizia-1.png" alt="Logo Delizia" class="logo-delizia-mobile" style="height: 80px; margin-bottom: -10px;">
        <div class="logo-subtitle" style="color: #3c8dbc; margin-top: -5px; font-size: 18px; font-weight: bold;">Suc. Oruro-Central</div>
    </a>
    <button class="btn btn-default button-mobile-menu" id="button-close-mobile-menu" style="background-color: #367fa9; color: white; border: none; margin-top: 15px; padding: 12px 20px; font-size: 18px;">
        <i class="fa fa-times" style="font-size: 20px;"></i> Cerrar Menú
    </button>
    <br><br>
    <ul class="list-unstyled text-center" style="font-size: 20px;">
        <?php 
            if(!empty($_SESSION['nombreAdmin'])){
                // Administrador: NO mostrar Productos, solo Estadísticas y Administración
                echo '
                    <li style="margin-bottom: 15px;"><a href="sales-stats.php" style="color: #3c8dbc; font-weight: bold; font-size: 22px;">Estadísticas</a></li>
                    <li style="margin-bottom: 15px;"><a href="configAdmin.php" style="color: #3c8dbc; font-weight: bold; font-size: 22px;">Administración</a></li>';
            }elseif(!empty($_SESSION['nombreUser']) && empty($_SESSION['esInvitado'])){
                // Vendedor: MOSTRAR Productos, Administración y Mi Perfil
                echo '<li style="margin-bottom: 15px;"><a href="product.php" style="color: #3c8dbc; font-weight: bold; font-size: 22px;">Productos</a></li>';
                echo '<li style="margin-bottom: 15px;"><a href="configAdmin.php" style="color: #3c8dbc; font-weight: bold; font-size: 22px;">Administración</a></li>';
                echo '<li style="margin-bottom: 15px;"><a href="#" class="userConBtn" data-code="'.$_SESSION['UserNIT'].'" style="color: #3c8dbc; font-weight: bold; font-size: 22px;"><i class="glyphicon glyphicon-user" style="font-size: 20px;"></i> Mi Perfil</a></li>';
            }
        ?>
        <!-- Botón de cierre de sesión para el menú móvil -->
        <?php if (!empty($_SESSION['nombreAdmin']) || !empty($_SESSION['nombreUser'])): ?>
            <li style="margin-top: 25px;"><a href="logout.php" class="exit-system" style="color: white; background-color: #dd4b39; display: inline-block; padding: 15px 25px; border-radius: 6px; margin-top: 15px; font-weight: bold; font-size: 20px;"><i class="fa fa-sign-out" style="margin-right: 8px; font-size: 20px;"></i> Cerrar Sesión</a></li>
        <?php elseif (!empty($_SESSION['esInvitado'])): ?>
            <li style="margin-top: 25px;"><a href="login.php" class="login-system" style="color: white; background-color: #28a745; display: inline-block; padding: 15px 25px; border-radius: 6px; margin-top: 15px; font-weight: bold; font-size: 20px;"><i class="fa fa-sign-in" style="margin-right: 8px; font-size: 20px;"></i> Iniciar Sesión</a></li>
        <?php endif; ?>
    </ul>
</div>
<?php endif; ?>

<?php if(isset($_SESSION['nombreUser']) && empty($_SESSION['esInvitado'])): ?>
<div class="modal fade" id="ModalUpUser" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <form class="modal-content FormCatElec" method="POST" data-form="save" autocomplete="off">
      <div class="modal-header" style="background-color: #3c8dbc; color: white;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; font-size: 28px;"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel" style="font-size: 24px;">
            <i class="fa fa-user" style="font-size: 24px;"></i> Actualizar Mi Perfil
        </h4>
      </div>
      <div class="modal-body" id="UserConData">
        <div class="text-center">
            <i class="fa fa-spinner fa-spin fa-3x"></i>
            <p style="font-size: 18px;">Cargando datos...</p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal" style="font-size: 18px; padding: 10px 20px;">Cancelar</button>
        <button type="submit" class="btn btn-primary" style="font-size: 18px; padding: 10px 20px;">Guardar cambios</button>
      </div>
    </form>
  </div>
</div>
<?php  endif;?>

<!-- JavaScript para el navbar -->
<script>
$(document).ready(function(){
    // Manejo del menú móvil
    $('#btn-mobile-menu').click(function(){
        $('#mobile-menu-list').fadeIn();
    });
    
    $('#button-close-mobile-menu').click(function(){
        $('#mobile-menu-list').fadeOut();
    });
    
    // Manejo del modal de perfil de usuario - MEJORADO PARA VENDEDORES
    $('.userConBtn').click(function(e){
        e.preventDefault();
        var code = $(this).data('code');
        
        // Mostrar loading
        $('#UserConData').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i><p style="font-size: 18px;">Cargando datos...</p></div>');
        $('#ModalUpUser').modal('show');
        
        // Cargar datos del formulario - USANDO getClientData.php EXISTENTE
        $.get('./process/getClientData.php?code='+code, function(data) {
            $('#UserConData').html(data);
        }).fail(function() {
            $('#UserConData').html('<div class="alert alert-danger" style="font-size: 18px;">Error al cargar los datos</div>');
        });
    });
    
    // Manejar el envío del formulario del modal - VERSIÓN CORREGIDA
    $(document).on('submit', '#ModalUpUser form', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        var submitBtn = $(this).find('button[type="submit"]');
        var modal = $('#ModalUpUser');
        
        // Deshabilitar el botón para evitar múltiples envíos
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Guardando...');
        
        // USAR updateVendedor.php EXISTENTE
        $.post('./process/updateVendedor.php', formData, function(response) {
            // Habilitar el botón nuevamente
            submitBtn.prop('disabled', false).html('Guardar cambios');
            
            // Cerrar el modal inmediatamente
            modal.modal('hide');
            
            // Mostrar el mensaje de respuesta
            $('body').append(response);
            
        }).fail(function() {
            // Habilitar el botón en caso de error
            submitBtn.prop('disabled', false).html('Guardar cambios');
            swal("ERROR", "Error de conexión al enviar los datos", "error");
        });
    });
    
    // Centrado forzado del navbar en cada página
    function centerNavbar() {
        $('.navbar-bottom .contenedor-tabla').css({
            'margin': '0 auto',
            'display': 'table',
            'width': 'auto'
        });
    }
    
    // Ejecutar inmediatamente y después de un breve delay
    centerNavbar();
    setTimeout(centerNavbar, 100);
});

// =============================================
// SISTEMA COMPLETAMENTE DESACTIVADO
// NO HAY CIERRES AUTOMÁTICOS DE NINGÚN TIPO
// =============================================

console.log('🔓 Sistema de cierres automáticos DESACTIVADO - Sesión permanente');
</script>

<!-- Estilos corregidos para el navbar con botones centrados -->
<style>
    /* Barra principal */
    #navbar-auto-hidden {
        background-color: #3c8dbc;
        border-bottom: 2px solid #367fa9;
        padding: 0;
    }
    
    /* Barra superior FIJA - SIN OCULTARSE */
    .navbar-top {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        z-index: 1031 !important;
        background-color: #3c8dbc !important;
    }
    
    /* Barra inferior transparente y FIJA - CENTRADA */
    .navbar-bottom {
        background-color: transparent !important;
        box-shadow: none !important;
        border: none !important;
        position: fixed !important;
        top: 85px !important;
        left: 0 !important;
        right: 0 !important;
        z-index: 1030 !important;
        text-align: center !important;
    }
    
    /* Contenedor de tabla CENTRADO */
    .contenedor-tabla {
        display: table !important;
        height: 60px !important;
        margin: 0 auto !important;
        background-color: transparent !important;
        width: auto !important;
    }
    
    /* Fila de la tabla */
    .contenedor-tr {
        display: table-row !important;
    }
    
    /* Botones de navegación CENTRADOS - AUMENTADOS */
    .contenedor-tabla .table-cell-td {
        font-size: 20px !important;
        font-weight: 900 !important;
        padding: 0 30px !important;
        display: table-cell !important;
        vertical-align: middle !important;
        color: #3c8dbc !important;
        height: 60px;
        text-decoration: none;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background-color: transparent !important;
        text-shadow: 
            -1px -1px 0 white,
            1px -1px 0 white,
            -1px 1px 0 white,
            1px 1px 0 white,
            0 0 4px white,
            0 0 4px white;
        text-align: center !important;
    }
    
    /* Efecto hover para los botones de navegación */
    .contenedor-tabla .table-cell-td:hover {
        background-color: rgba(60, 141, 188, 0.15) !important;
        border-radius: 6px;
        color: #2d6da3 !important;
        transform: translateY(-2px);
    }
    
    /* Estilo específico para el botón de cerrar sesión */
    .exit-system {
        background-color: #dd4b39 !important;
        color: white !important;
        font-weight: bold !important;
        transition: all 0.3s ease;
        border: none !important;
    }
    
    .exit-system:hover {
        background-color: #c23321 !important;
        transform: translateY(-2px);
        box-shadow: 0 3px 8px rgba(0,0,0,0.25);
        color: white !important;
    }
    
    /* Estilo específico para el botón de iniciar sesión (invitados) */
    .login-system {
        background-color: #28a745 !important;
        color: white !important;
        font-weight: bold !important;
        transition: all 0.3s ease;
        border: none !important;
    }
    
    .login-system:hover {
        background-color: #218838 !important;
        transform: translateY(-2px);
        box-shadow: 0 3px 8px rgba(0,0,0,0.25);
        color: white !important;
    }
    
    /* Estilo para el botón de perfil */
    .userConBtn {
        transition: all 0.3s ease;
        border: 2px solid white !important;
        background-color: rgba(255, 255, 255, 0.1) !important;
    }
    
    .userConBtn:hover {
        background-color: rgba(255, 255, 255, 0.2) !important;
        transform: translateY(-2px);
        box-shadow: 0 3px 8px rgba(0,0,0,0.15);
        color: white !important;
    }
    
    /* Logo en versión desktop */
    .logo-delizia {
        height: 60px;
        transition: transform 0.3s ease;
    }
    
    /* Logo en versión móvil */
    .logo-delizia-mobile {
        height: 80px;
        display: block;
        margin: 0 auto;
        transition: transform 0.3s ease;
    }
    
    /* Subtítulo */
    .logo-subtitle {
        text-align: center;
        font-size: 16px;
        font-weight: bold;
        color: white;
        letter-spacing: 0.5px;
    }
    
    /* Efecto hover para indicar que es clickeable */
    .logo-btn:hover .logo-delizia,
    .logo-btn:hover .logo-delizia-mobile {
        transform: scale(1.05);
        opacity: 0.9;
    }
    
    /* Menú móvil con botones */
    #mobile-menu-list ul li a {
        font-size: 22px !important;
        font-weight: 900 !important;
        padding: 15px 0;
        display: block;
        color: #3c8dbc !important;
        text-decoration: none;
        transition: all 0.3s ease;
        text-shadow: 
            -1px -1px 0 white,
            1px -1px 0 white,
            -1px 1px 0 white,
            1px 1px 0 white,
            0 0 4px white,
            0 0 4px white;
    }
    
    /* Efecto hover para menú móvil */
    #mobile-menu-list ul li a:not(.exit-system):not(.login-system):hover {
        background-color: rgba(60, 141, 188, 0.1);
        border-radius: 6px;
        color: #2d6da3 !important;
    }
    
    /* Estilo para el contenedor del logo */
    .logo-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        text-decoration: none;
    }
    
    .table-cell-td {
        display: table-cell;
        vertical-align: middle;
        padding: 0 20px;
        text-decoration: none;
    }
    
    /* Estilos para el menú móvil */
    #mobile-menu-list {
        border-top: 2px solid #e0e0e0;
        padding: 20px;
        box-shadow: 0 3px 15px rgba(0,0,0,0.15);
    }
    
    .button-mobile-menu {
        background-color: #367fa9;
        border: 2px solid #2d6da3;
        color: white;
        padding: 10px 15px;
        font-size: 18px;
    }
    
    .button-mobile-menu:hover {
        background-color: #2d6da3;
        color: white;
    }
    
    /* Asegurar que el contenido no quede detrás de la barra fija */
    body {
        padding-top: 145px !important;
    }
    
    @media (max-width: 767px) {
        body {
            padding-top: 70px !important;
        }
        .navbar-bottom {
            display: none;
        }
    }
</style>