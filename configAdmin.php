<?php
session_start();
include './library/configServer.php';
include './library/consulSQL.php';
include './process/securityPanel.php';

// Verificación adicional de sesión
if (empty($_SESSION['nombreAdmin']) && (empty($_SESSION['nombreUser']) || !empty($_SESSION['esInvitado']))) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Administración</title>
    <?php include './inc/link.php'; ?>
    <style>
        /* Aumentar tamaños en el panel de administración */
        .nav-tabs > li > a {
            font-size: 18px;
            padding: 15px 20px;
        }
        
        .page-header h1 {
            font-size: 36px;
        }
        
        .page-header h1 img {
            height: 60px;
            margin-bottom: 15px;
        }
        
        .breadcrumb {
            font-size: 17px;
        }
        
        .breadcrumb a {
            font-size: 17px;
        }
        
        .lead {
            font-size: 20px;
        }
        
        .jumbotron h2 {
            font-size: 32px;
        }
        
        .jumbotron p {
            font-size: 20px;
        }
        
        .btn-block {
            font-size: 18px;
            padding: 15px;
        }
        
        .fa-2x {
            font-size: 2.5em;
        }
        
        /* Ajustes para las vistas de administración */
        .panel-heading h4 {
            font-size: 24px;
        }
        
        .table th {
            font-size: 18px;
            padding: 15px;
        }
        
        .table td {
            font-size: 17px;
            padding: 15px;
        }
        
        .pagination > li > a {
            font-size: 17px;
            padding: 12px 18px;
        }
        
        .alert {
            font-size: 17px;
            padding: 15px;
        }
        
        .form-control {
            font-size: 17px;
            padding: 12px;
        }
        
        label {
            font-size: 18px;
        }
        
        .btn {
            font-size: 17px;
            padding: 10px 16px;
        }
        
        .btn-raised {
            font-size: 17px;
            padding: 10px 16px;
        }
        
        .btn-xs {
            font-size: 16px;
            padding: 8px 12px;
        }
        
        .container-form-admin h3 {
            font-size: 28px;
        }
        
        legend {
            font-size: 20px;
        }
        
        .help-block {
            font-size: 16px;
        }
        
        /* Ajustes responsivos */
        @media (max-width: 768px) {
            .nav-tabs > li > a {
                font-size: 16px;
                padding: 12px 15px;
            }
            
            .page-header h1 {
                font-size: 30px;
            }
            
            .jumbotron h2 {
                font-size: 28px;
            }
            
            .jumbotron p {
                font-size: 18px;
            }
        }
        
        @media (max-width: 480px) {
            .page-header h1 {
                font-size: 26px;
            }
            
            .lead {
                font-size: 18px;
            }
            
            .breadcrumb {
                font-size: 16px;
            }
        }
    </style>
</head>
<body id="container-page-index">
    <?php include './inc/navbar.php'; ?>
    <section id="container-pedido">
        <div class="container">
            <div class="page-header">
              <h1>Panel de Administración <img src="assets/img/black-delizia1.jpg" alt="Logo Delizia" style="height: 60px; margin-bottom: 15px;"></h1>
            </div>
            <br><br><br>
            <div class="row">
                <div class="col-xs-12">
                    <!--====  Nav Tabs  ====-->
                    <ul class="nav nav-tabs nav-justified" style="margin-bottom: 20px;">
                      <?php if (!empty($_SESSION['nombreAdmin'])): ?>
                        <!-- Administrador: Todas las pestañas -->
                        <li>
                          <a href="configAdmin.php?view=productlist">
                            <i class="fa fa-cubes" aria-hidden="true"></i> &nbsp; Productos
                          </a>
                        </li>
                        <li>
                          <a href="configAdmin.php?view=categorylist">
                            <i class="fa fa-shopping-basket" aria-hidden="true"></i> &nbsp; Categorías
                          </a>
                        </li>
                        <li>
                          <a href="configAdmin.php?view=orderpending">
                            <i class="fa fa-shopping-cart" aria-hidden="true"></i> &nbsp; Ventas
                          </a>
                        </li>
                        <li>
                          <a href="configAdmin.php?view=vendedorlist">
                            <i class="fa fa-users" aria-hidden="true"></i> &nbsp; Vendedores
                          </a>
                        </li>
                        <li>
                          <a href="configAdmin.php?view=clientelist">
                            <i class="fa fa-address-card" aria-hidden="true"></i> &nbsp; Clientes
                          </a>
                        </li>
                      <?php elseif (!empty($_SESSION['nombreUser']) && empty($_SESSION['esInvitado'])): ?>
                        <!-- Vendedor: Solo Pedidos y Clientes -->
                        <li>
                          <a href="configAdmin.php?view=orderpending">
                            <i class="fa fa-shopping-cart" aria-hidden="true"></i> &nbsp; Ventas
                          </a>
                        </li>
                        <li>
                          <a href="configAdmin.php?view=clientelist">
                            <i class="fa fa-address-card" aria-hidden="true"></i> &nbsp; Clientes
                          </a>
                        </li>
                      <?php endif; ?>
                    </ul>
                    <?php
                      $content = $_GET['view'] ?? '';
                      
                      // WhiteList actualizada
                      $WhiteList = [
                          "product", "productlist", "productinfo", 
                          "category", "categorylist", "categoryinfo",
                          "vendedor", "vendedorlist", "vendedorinfo",
                          "cliente", "clientelist", "clienteedit",
                          "orderpending", "orderdelivered", "ordercancelled", "bank", 
                          "deactivatedproducts", "deactivatedclients",
                          "account"
                      ];
                      
                      // Verificar permisos para vendedores
                      if (!empty($_SESSION['nombreUser']) && empty($_SESSION['esInvitado'])) {
                          $vendedorAllowedViews = ["orderpending", "orderdelivered", "ordercancelled", "clientelist", "cliente"];
                          if (!empty($content) && !in_array($content, $vendedorAllowedViews)) {
                              echo '<div class="alert alert-danger text-center">
                                  <h4 style="font-size: 24px;"><i class="fa fa-exclamation-triangle"></i> Acceso Denegado</h4>
                                  <p style="font-size: 18px;">No tienes permisos para acceder a esta sección.</p>
                                  <a href="configAdmin.php?view=orderpending" class="btn btn-primary" style="font-size: 18px; padding: 12px 20px;">Ir a Pedidos</a>
                              </div>';
                              include './inc/footer.php';
                              exit();
                          }
                      }
                      
                      if(!empty($content)){
                        if(in_array($content, $WhiteList)){
                            $file_path = "./admin/" . $content . "-view.php";
                            if(is_file($file_path)){
                                include $file_path;
                            } else {
                                echo '<div class="alert alert-danger text-center">
                                    <h4 style="font-size: 24px;"><i class="fa fa-exclamation-triangle"></i> Archivo no encontrado</h4>
                                    <p style="font-size: 18px;">El archivo: <strong>' . htmlspecialchars($file_path) . '</strong> no existe</p>
                                </div>';
                            }
                        } else {
                            echo '<div class="alert alert-warning text-center">
                                <h4 style="font-size: 24px;"><i class="fa fa-exclamation-triangle"></i> Vista no permitida</h4>
                                <p style="font-size: 18px;">La vista <strong>' . htmlspecialchars($content) . '</strong> no está en la lista blanca</p>
                            </div>';
                        }
                      } else {
                        // Página de inicio según el tipo de usuario
                        if (!empty($_SESSION['nombreAdmin'])) {
                            echo '<div class="jumbotron text-center">
                                    <h2><i class="fa fa-home"></i> Bienvenido al Panel de Administración</h2>
                                    <p>Para empezar, por favor escoja una opción del menú de administración</p>
                                    <div class="row" style="margin-top: 30px;">
                                        <div class="col-md-2 col-md-offset-1">
                                            <a href="configAdmin.php?view=productlist" class="btn btn-primary btn-block">
                                                <i class="fa fa-cubes fa-2x"></i><br>Productos
                                            </a>
                                        </div>
                                        <div class="col-md-2">
                                            <a href="configAdmin.php?view=categorylist" class="btn btn-success btn-block">
                                                <i class="fa fa-shopping-basket fa-2x"></i><br>Categorías
                                            </a>
                                        </div>
                                        <div class="col-md-2">
                                            <a href="configAdmin.php?view=orderpending" class="btn btn-info btn-block">
                                                <i class="fa fa-shopping-cart fa-2x"></i><br>Pedidos
                                            </a>
                                        </div>
                                        <div class="col-md-2">
                                            <a href="configAdmin.php?view=vendedorlist" class="btn btn-warning btn-block">
                                                <i class="fa fa-users fa-2x"></i><br>Vendedores
                                            </a>
                                        </div>
                                        <div class="col-md-2">
                                            <a href="configAdmin.php?view=clientelist" class="btn btn-danger btn-block">
                                                <i class="fa fa-address-card fa-2x"></i><br>Clientes
                                            </a>
                                        </div>
                                    </div>
                                  </div>';
                        } elseif (!empty($_SESSION['nombreUser']) && empty($_SESSION['esInvitado'])) {
                            echo '<div class="jumbotron text-center">
                                    <h2><i class="fa fa-home"></i> Bienvenido al Panel de Vendedor</h2>
                                    <p>Para empezar, por favor escoja una opción del menú</p>
                                    <div class="row" style="margin-top: 30px;">
                                        <div class="col-md-4 col-md-offset-2">
                                            <a href="configAdmin.php?view=orderpending" class="btn btn-info btn-block">
                                                <i class="fa fa-shopping-cart fa-2x"></i><br>Pedidos
                                            </a>
                                        </div>
                                        <div class="col-md-4">
                                            <a href="configAdmin.php?view=clientelist" class="btn btn-danger btn-block">
                                                <i class="fa fa-address-card fa-2x"></i><br>Clientes
                                            </a>
                                        </div>
                                    </div>
                                  </div>';
                        }
                      }
                    ?>
                </div>
            </div>
        </div>
    </section>
    <?php include './inc/footer.php'; ?>
</body>
</html>