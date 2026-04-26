<?php
session_start();
error_reporting(E_PARSE);

// HEADERS PARA PREVENIR CACHE
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Verificar si hay sesión activa o si es invitado
$esInvitado = false;
$esVendedor = false;

// Si no hay sesión activa, verificar si viene del enlace "Continuar como invitado"
if (empty($_SESSION['nombreAdmin']) && empty($_SESSION['nombreUser'])) {
    // Si se accede directamente al index sin sesión y sin ser invitado, redirigir al login
    if (!isset($_SESSION['esInvitado'])) {
        header("Location: login.php");
        exit();
    } else {
        $esInvitado = true;
    }
} else {
    // Verificar si es vendedor
    if (!empty($_SESSION['nombreUser'])) {
        $esVendedor = true;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Inicio</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    
    <?php include './inc/link.php'; ?>
    
    <style>
        /* ESTILOS ORIGINALES - MEJORADOS PARA LEGIBILIDAD */
        body {
            background-color: #ecf0f5;
            padding-top: 70px;
            background-image: url('assets/img/font-index.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            font-size: 16px; /* Tamaño base aumentado */
        }

        <?php if (!empty($_SESSION['nombreAdmin']) || !empty($_SESSION['nombreUser'])): ?>
        body {
            padding-top: 120px;
        }
        <?php endif; ?>

        /* CONTENEDOR PRINCIPAL DEL CARRUSEL REDUCIDO 70% */
        .carousel-outer-container {
            width: 70%; /* REDUCIDO DE 80% A 70% */
            margin: 0 auto;
            position: relative;
            background: white;
            border-radius: 10px; /* BORDES MÁS PEQUEÑOS */
            box-shadow: 0 4px 15px rgba(0,0,0,0.1); /* SOMBRA MÁS SUAVE */
            padding: 10px 10px 5px 10px; /* PADDING REDUCIDO EN LA PARTE INFERIOR */
        }

        /* CARRUSEL CON PROPORCIÓN ORIGINAL RESTAURADA */
        #slider-store {
            width: 100%;
            margin: 0;
            padding: 0;
            position: relative;
            overflow: hidden;
            border-radius: 8px; /* BORDES MÁS PEQUEÑOS */
        }

        /* PROPORCIÓN ORIGINAL RESTAURADA - 500px */
        .carousel-inner {
            height: 500px; /* ALTURA ORIGINAL RESTAURADA */
            width: 100%;
            overflow: hidden;
            position: relative;
        }
        
        .carousel-inner img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            transition: transform 0.3s ease;
        }

        /* EFECTO DE PREVIEW BORROSO EN LOS BORDES */
        .carousel-inner::before,
        .carousel-inner::after {
            content: '';
            position: absolute;
            top: 0;
            width: 80px; /* ANCHO REDUCIDO DEL EFECTO BORROSO */
            height: 100%;
            z-index: 2;
            pointer-events: none;
        }

        .carousel-inner::before {
            left: 0;
            background: linear-gradient(90deg, rgba(255,255,255,0.7) 0%, transparent 100%);
        }

        .carousel-inner::after {
            right: 0;
            background: linear-gradient(270deg, rgba(255,255,255,0.7) 0%, transparent 100%);
        }

        /* FLECHAS SIN CUADRADOS - TOTALMENTE LIMPIAS */
        .carousel-control {
            width: auto;
            height: auto;
            background: transparent !important;
            border-radius: 0;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0.7;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            border: none !important;
        }

        .carousel-control:hover {
            background: transparent !important;
            opacity: 1;
            transform: translateY(-50%) scale(1.1);
        }

        .carousel-control.left {
            left: 15px;
        }

        .carousel-control.right {
            right: 15px;
        }

        .carousel-control .glyphicon {
            color: #ffffff;
            font-size: 36px; /* TAMAÑO AUMENTADO PARA MEJOR VISIBILIDAD SIN FONDO */
            margin: 0;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.8);
            background: transparent !important; /* SIN FONDO */
            border-radius: 0;
            padding: 0; /* SIN PADDING */
        }

        /* INDICADORES - POSICIONADOS DENTRO DEL ESPACIO REDUCIDO */
        .carousel-indicators {
            bottom: 8px; /* AJUSTADO PARA EL ESPACIO REDUCIDO */
            z-index: 5;
            margin-bottom: 0;
        }

        .carousel-indicators li {
            background-color: rgba(255,255,255,0.5);
            border: 2px solid #007bff;
            width: 12px;
            height: 12px;
            margin: 0 4px;
        }

        .carousel-indicators .active {
            background-color: #007bff;
            width: 12px;
            height: 12px;
        }

        /* MENSAJES SIN BARRA - SOLO TEXTO CON BORDEADO */
        .carousel-caption {
            background: transparent !important; /* SIN FONDO */
            border: none;
            padding: 0;
            position: absolute;
            bottom: 20px; /* POSICIÓN AJUSTADA */
            left: 0;
            right: 0;
            width: 100%;
            text-align: center;
            font-weight: bold;
            font-size: 28px;
            color: white; /* COLOR BASE BLANCO */
            margin: 0;
            border-radius: 0;
            /* EFECTO DE BORDE CON MULTIPLES SOMBRAS */
            text-shadow: 
                -1px -1px 0 #007bff,
                1px -1px 0 #007bff,
                -1px 1px 0 #007bff,
                1px 1px 0 #007bff,
                -2px -2px 0 #007bff,
                2px -2px 0 #007bff,
                -2px 2px 0 #007bff,
                2px 2px 0 #007bff;
        }

        /* ELIMINAR ESPACIO EXTRA ENTRE CARRUSEL Y PRODUCTOS */
        #new-prod-index {
            margin-top: 10px;
            padding-top: 0;
        }

        /* CONTENEDOR ESPECIAL PARA VIDEO - FORMATO 16:9 */
        .video-special-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.12);
            padding: 25px;
            height: 100%;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
            border: 2px solid #e0e0e0;
            min-height: 480px; /* MISMA ALTURA QUE UN CONTENEDOR DE PRODUCTO */
            justify-content: center;
            align-items: center;
        }

        .video-special-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .video-special-container video {
            width: 100%;
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            max-height: 430px; /* DEJA ESPACIO PARA EL PADDING DEL CONTENEDOR */
        }

        /* ESTRUCTURA CORREGIDA: FILA CON 2 PRODUCTOS + VIDEO (OCUPANDO ESPACIO DE 2 PRODUCTOS) */
        .products-video-row {
            display: flex;
            flex-wrap: wrap;
            margin-left: -8px;
            margin-right: -8px;
            margin-bottom: 30px;
            align-items: stretch; /* ASEGURA QUE TODOS LOS ELEMENTOS TENGAN LA MISMA ALTURA */
        }

        .products-video-row .product-container {
            flex: 0 0 25%; /* CADA PRODUCTO OCUPA 25% (1 DE 4 COLUMNAS) */
            max-width: 25%;
            padding-left: 8px;
            padding-right: 8px;
            margin-bottom: 0;
            display: flex;
            flex-direction: column;
        }

        .video-double-width {
            flex: 0 0 50%; /* VIDEO OCUPA 50% (2 DE 4 COLUMNAS) */
            max-width: 50%;
            padding-left: 8px;
            padding-right: 8px;
            display: flex;
            flex-direction: column;
        }

        /* ESTILOS ORIGINALES PARA PRODUCTOS - RESTAURADOS COMPLETAMENTE */
        .product-container {
            margin-bottom: 30px;
            transition: all 0.3s ease;
        }
        
        .product-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.12);
            padding: 25px;
            height: 100%;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
            border: 2px solid #e0e0e0;
            min-height: 480px;
        }
        
        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.18);
        }
        
        .product-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 18px;
        }
        
        .product-title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 12px;
            color: #333;
            line-height: 1.3;
            min-height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        
        .product-description {
            color: #666;
            font-size: 17px;
            margin-bottom: 18px;
            flex-grow: 1;
            line-height: 1.4;
            text-align: center;
        }
        
        .price-section {
            margin-bottom: 18px;
            text-align: center;
        }
        
        .current-price {
            font-size: 26px;
            font-weight: bold;
            color: #28a745;
        }
        
        .original-price {
            text-decoration: line-through;
            color: #999;
            font-size: 20px;
            margin-left: 10px;
        }
        
        .stock-info {
            font-size: 16px;
            padding: 8px 14px;
            border-radius: 5px;
            display: inline-block;
            margin-bottom: 12px;
            font-weight: bold;
            text-align: center;
            width: 100%;
        }
        
        .in-stock {
            background: #d4edda;
            color: #155724;
            border: 2px solid #c3e6cb;
        }
        
        .out-of-stock {
            background: #f8d7da;
            color: #721c24;
            border: 2px solid #f5c6cb;
        }
        
        .sold-out {
            background: rgba(255, 0, 0, 0.85);
            color: white;
            padding: 12px 18px;
            border-radius: 6px;
            font-weight: bold;
            text-align: center;
            margin-top: 12px;
            font-size: 18px;
        }
        
        .category-badge {
            background: #17a2b8;
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 14px;
            margin-bottom: 10px;
            display: inline-block;
            font-weight: bold;
            text-align: center;
            width: 100%;
        }

        /* Estilos para el botón Ver Más - AUMENTADO */
        .btn-ver-mas {
            background: linear-gradient(135deg, #3c8dbc, #367fa9);
            color: white;
            border: none;
            padding: 16px 40px;
            font-size: 20px;
            font-weight: bold;
            border-radius: 30px;
            margin: 35px 0;
            transition: all 0.3s ease;
            box-shadow: 0 5px 20px rgba(60, 141, 188, 0.4);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-ver-mas:hover {
            background: linear-gradient(135deg, #367fa9, #2d6da3);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(60, 141, 188, 0.5);
        }
        
        .btn-ver-mas .fa {
            margin: 0 8px;
            font-size: 18px;
        }
        
        .ver-mas-container {
            text-align: center;
            padding: 25px 0;
        }
        
        .ver-mas-subtitle {
            color: #3c8dbc;
            font-weight: bold;
            margin-top: 12px;
            font-size: 22px;
        }

        /* Mensaje para invitados - AUMENTADO */
        .invitado-message {
            background-color: #f8f9fa;
            border-left: 5px solid #3c8dbc;
            padding: 20px;
            margin: 25px 0;
            border-radius: 6px;
            font-size: 18px;
        }

        .invitado-message h4 {
            font-size: 22px;
            margin-bottom: 10px;
        }

        /* Títulos y encabezados aumentados */
        .page-header h1 {
            font-size: 36px;
            margin-bottom: 15px;
        }
        
        .page-header h1 small {
            font-size: 24px;
        }

        /* Ajustes responsivos */
        @media (max-width: 992px) {
            .products-video-row .product-container,
            .video-double-width {
                flex: 0 0 50%;
                max-width: 50%;
            }
            
            .video-special-container video {
                max-height: 350px;
            }
        }

        @media (max-width: 768px) {
            .carousel-outer-container {
                width: 85%;
                padding: 8px 8px 4px 8px;
            }
            
            .carousel-inner {
                height: 350px;
            }
            
            .carousel-caption {
                font-size: 22px;
                bottom: 15px;
            }
            
            .carousel-control .glyphicon {
                font-size: 30px;
            }
            
            .carousel-indicators {
                bottom: 6px;
            }
            
            .carousel-indicators li,
            .carousel-indicators .active {
                width: 10px;
                height: 10px;
                margin: 0 3px;
            }
            
            .video-special-container {
                min-height: 350px;
                padding: 20px;
            }
            
            .video-special-container video {
                max-height: 300px;
            }
            
            .products-video-row .product-container,
            .video-double-width {
                flex: 0 0 100%;
                max-width: 100%;
            }
            
            .btn-ver-mas {
                padding: 14px 35px;
                font-size: 18px;
            }
            
            .product-title {
                font-size: 20px;
                min-height: 50px;
            }
            
            .product-description {
                font-size: 16px;
            }
            
            .current-price {
                font-size: 24px;
            }
            
            .product-card {
                min-height: 450px;
            }
        }
        
        @media (max-width: 480px) {
            .carousel-outer-container {
                width: 92%;
                padding: 5px 5px 3px 5px;
            }
            
            .carousel-inner {
                height: 250px;
            }
            
            .carousel-caption {
                font-size: 18px;
                bottom: 10px;
                text-shadow: 
                    -1px -1px 0 #007bff,
                    1px -1px 0 #007bff,
                    -1px 1px 0 #007bff,
                    1px 1px 0 #007bff;
            }
            
            .carousel-control .glyphicon {
                font-size: 26px;
            }
            
            .carousel-control.left {
                left: 8px;
            }
            
            .carousel-control.right {
                right: 8px;
            }
            
            .carousel-indicators {
                bottom: 4px;
            }
            
            .carousel-indicators li,
            .carousel-indicators .active {
                width: 8px;
                height: 8px;
                margin: 0 2px;
            }
            
            .video-special-container {
                min-height: 300px;
                padding: 15px;
            }
            
            .video-special-container video {
                max-height: 250px;
            }
            
            .btn-ver-mas {
                padding: 12px 30px;
                font-size: 16px;
            }
            
            .ver-mas-subtitle {
                font-size: 18px;
            }
            
            .invitado-message {
                font-size: 16px;
                padding: 15px;
            }
            
            .page-header h1 {
                font-size: 28px;
            }
            
            .page-header h1 small {
                font-size: 20px;
            }
            
            .product-card {
                min-height: 420px;
                padding: 20px;
            }
            
            .product-title {
                font-size: 18px;
                min-height: 45px;
            }
        }
    </style>
</head>

<body>
    <?php include './inc/navbar.php'; ?>
    
    <!-- Mostrar mensaje si es invitado -->
    <?php if ($esInvitado): ?>
    <div class="container">
        <div class="invitado-message">
            <h4><i class="fa fa-info-circle"></i> Modo Invitado</h4>
            <p>Estás navegando como invitado. <a href="login.php" style="color: #3c8dbc; font-weight: bold; font-size: 18px;">Inicia sesión</a> para acceder a todas las funciones.</p>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- CONTENEDOR DEL CARRUSEL CON BORDE INFERIOR REDUCIDO -->
    <div class="carousel-outer-container">
        <section id="slider-store" class="carousel slide" data-ride="carousel">

            <!-- Indicators -->
            <ol class="carousel-indicators">
                <li data-target="#slider-store" data-slide-to="0" class="active"></li>
                <li data-target="#slider-store" data-slide-to="1"></li>
                <li data-target="#slider-store" data-slide-to="2"></li>
            </ol>

            <!-- Wrapper for slides -->
            <div class="carousel-inner" role="listbox">
                <div class="item active">
                    <img src="./assets/img/slider1.jpg" alt="slider1">
                    <div class="carousel-caption">
                        En diferentes sabores
                    </div>
                </div>
                <div class="item">
                    <img src="./assets/img/slider2.jpg" alt="slider2">
                    <div class="carousel-caption">
                        En diferentes épocas
                    </div>
                </div>
                <div class="item">
                    <img src="./assets/img/slider3.jpg" alt="slider3">
                    <div class="carousel-caption">
                        En diferentes presentaciones
                    </div>
                </div>
            </div>

            <!-- Controls - SIN CUADRADOS DE FONDO -->
            <a class="left carousel-control" href="#slider-store" role="button" data-slide="prev">
                <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="right carousel-control" href="#slider-store" role="button" data-slide="next">
                <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </section>
    </div>

    <section id="new-prod-index">    
        <div class="container">
            <div class="page-header">
                <h1>Últimos <small>productos agregados</small></h1>
            </div>

            <?php
            include 'library/configServer.php';
            include 'library/consulSQL.php';
            
            // CONSULTA PRINCIPAL PARA OBTENER TODOS LOS PRODUCTOS
            $consultaPrincipal = ejecutarSQL::consultar("
                SELECT p.*, c.Nombre as NombreCategoria 
                FROM producto p 
                INNER JOIN categoria c ON p.CodigoCat = c.CodigoCat 
                WHERE p.Stock > 0 AND p.Estado='activo' 
                ORDER BY p.id DESC 
                LIMIT 8
            ");
            
            $totalproductos = mysqli_num_rows($consultaPrincipal);
            $consultaTotal = ejecutarSQL::consultar("SELECT COUNT(*) as total FROM producto WHERE Stock > 0 AND Estado='activo'");
            $totalGeneral = mysqli_fetch_assoc($consultaTotal)['total'];
            
            if($totalproductos > 0){
                // ALMACENAR TODOS LOS PRODUCTOS EN UN ARRAY
                $todosProductos = array();
                while($fila = mysqli_fetch_array($consultaPrincipal, MYSQLI_ASSOC)){
                    $todosProductos[] = $fila;
                }
                
                // ESTRUCTURA CORREGIDA CON 2 PRODUCTOS + VIDEO
                ?>
                <div class="products-video-row">
                    <?php
                    // MOSTRAR LOS PRIMEROS 2 PRODUCTOS
                    for($i = 0; $i < min(2, count($todosProductos)); $i++){
                        $fila = $todosProductos[$i];
                    ?>
                    <div class="product-container">
                        <div class="product-card">
                            <span class="category-badge"><?php echo $fila['NombreCategoria']; ?></span>
                            <img class="product-image" src="./assets/img-products/<?php if($fila['Imagen']!="" && is_file("./assets/img-products/".$fila['Imagen'])){ echo $fila['Imagen']; }else{ echo "default.png"; } ?>" alt="<?php echo $fila['NombreProd']; ?>">
                            
                            <h4 class="product-title"><?php echo $fila['NombreProd']; ?></h4>
                            
                            <div class="product-description">
                                <?php 
                                $descripcion = "Delicioso producto de nuestra heladería Delizia.";
                                if ($fila['CodigoCat'] == 'HELADOS') {
                                    $descripcion = "Helado cremoso y refrescante, perfecto para cualquier momento.";
                                } elseif ($fila['CodigoCat'] == 'JUGOS') {
                                    $descripcion = "Bebida refrescante con el mejor sabor natural.";
                                } elseif ($fila['CodigoCat'] == 'LACTEOS') {
                                    $descripcion = "Producto lácteo de alta calidad y frescura garantizada.";
                                }
                                echo $descripcion;
                                ?>
                            </div>
                            
                            <div class="price-section">
                                <?php 
                                if($fila['Aumento'] > 0): 
                                    $precio_final = $fila['Precio'] + ($fila['Precio'] * ($fila['Aumento']/100));
                                ?>
                                    <span class="current-price">Bs. <?php echo number_format($precio_final, 2); ?></span>
                                    <span class="original-price">Bs. <?php echo number_format($fila['Precio'], 2); ?></span>
                                <?php else: ?>
                                    <span class="current-price">Bs. <?php echo number_format($fila['Precio'], 2); ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="stock-info <?php echo ($fila['Stock'] >= 1) ? 'in-stock' : 'out-of-stock'; ?>">
                                <?php echo $fila['Stock']; ?> disponibles
                            </div>
                        </div>
                    </div>
                    <?php
                    }
                    ?>
                    
                    <!-- Video ocupando el espacio de 2 productos -->
                    <div class="video-double-width">
                        <div class="video-special-container">
                            <video width="100%" controls>
                                <source src="assets/icefruit_delizia.mp4" type="video/mp4">
                                Tu navegador no soporta la etiqueta de video.
                            </video>
                        </div>
                    </div>
                </div>

                <!-- PRODUCTOS RESTANTES (3-8) -->
                <div class="row">
                    <?php
                    $contador = 0;
                    // MOSTRAR PRODUCTOS RESTANTES (DEL 3 AL 8)
                    for($i = 2; $i < count($todosProductos); $i++){
                        $fila = $todosProductos[$i];
                        
                        if ($contador % 4 == 0) {
                            if ($contador > 0) {
                                echo '</div>';
                            }
                            echo '<div class="row">';
                        }
                    ?>
                    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
                        <div class="product-container">
                            <div class="product-card">
                                <span class="category-badge"><?php echo $fila['NombreCategoria']; ?></span>
                                <img class="product-image" src="./assets/img-products/<?php if($fila['Imagen']!="" && is_file("./assets/img-products/".$fila['Imagen'])){ echo $fila['Imagen']; }else{ echo "default.png"; } ?>" alt="<?php echo $fila['NombreProd']; ?>">
                                
                                <h4 class="product-title"><?php echo $fila['NombreProd']; ?></h4>
                                
                                <div class="product-description">
                                    <?php 
                                    $descripcion = "Delicioso producto de nuestra heladería Delizia.";
                                    if ($fila['CodigoCat'] == 'HELADOS') {
                                        $descripcion = "Helado cremoso y refrescante, perfecto para cualquier momento.";
                                    } elseif ($fila['CodigoCat'] == 'JUGOS') {
                                        $descripcion = "Bebida refrescante con el mejor sabor natural.";
                                    } elseif ($fila['CodigoCat'] == 'LACTEOS') {
                                        $descripcion = "Producto lácteo de alta calidad y frescura garantizada.";
                                    }
                                    echo $descripcion;
                                    ?>
                                </div>
                                
                                <div class="price-section">
                                    <?php 
                                    if($fila['Aumento'] > 0): 
                                        $precio_final = $fila['Precio'] + ($fila['Precio'] * ($fila['Aumento']/100));
                                    ?>
                                        <span class="current-price">Bs. <?php echo number_format($precio_final, 2); ?></span>
                                        <span class="original-price">Bs. <?php echo number_format($fila['Precio'], 2); ?></span>
                                    <?php else: ?>
                                        <span class="current-price">Bs. <?php echo number_format($fila['Precio'], 2); ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="stock-info <?php echo ($fila['Stock'] >= 1) ? 'in-stock' : 'out-of-stock'; ?>">
                                    <?php echo $fila['Stock']; ?> disponibles
                                </div>
                            </div>
                        </div>
                    </div>     
                    <?php
                        $contador++;
                    }
                    echo '</div>';
                    
                    if ($totalGeneral > 8 && $esVendedor) {
                    ?>
                    <div class="ver-mas-container">
                        <a href="product.php" class="btn btn-ver-mas btn-raised">
                            <i class="fa fa-chevron-left"></i>
                            <i class="fa fa-chevron-right"></i>
                        </a>
                        <div class="ver-mas-subtitle">Ver Más</div>
                    </div>
                    <?php
                    }
                } else {
                    echo '<div class="row"><div class="col-xs-12"><h2 style="font-size: 28px; text-align: center; padding: 40px 0;">No hay productos registrados en la tienda</h2></div></div>';
                }  
                ?>  
            </div>
        </div>
    </section>
    <?php include './inc/footer.php'; ?>
</body>
</html>