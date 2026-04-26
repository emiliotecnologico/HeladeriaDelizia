<?php
session_start(); 
include './library/configServer.php';
include './library/consulSQL.php';
include './process/check_cart_expiry.php';

// Inicializar carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = array();
}

// Obtener TODOS los productos activos sin filtrar por categoría
$categoria = isset($_GET['categ']) ? consultasSQL::clean_string($_GET['categ']) : "";

// Manejar búsqueda
$search = "";
$is_search = false;
if(isset($_GET['term']) && $_GET['term'] != ""){
    $search = consultasSQL::clean_string($_GET['term']);
    $is_search = true;
}

// Obtener todas las categorías para el dropdown
$categorias = ejecutarSQL::consultar("SELECT * FROM categoria WHERE Estado='activa'");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Venta de Productos</title>
    <?php include './inc/link.php'; ?>
    <style>
        /* Estilos optimizados - MENOS EXTENSOS */
        .floating-cart {
            position: fixed;
            top: 90px;
            right: 25px;
            background: #2196F3;
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            z-index: 1000;
            cursor: pointer;
            transition: all 0.3s ease;
            min-width: 150px;
            text-align: center;
            font-weight: bold;
            font-size: 16px;
        }
        
        .floating-cart:hover {
            background: #1976D2;
            transform: translateY(-2px);
        }
        
        .cart-count {
            background: #FF5722;
            color: white;
            border-radius: 50%;
            padding: 5px 10px;
            font-size: 14px;
            margin-left: 8px;
            font-weight: bold;
        }
        
        .cart-preview {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            min-width: 350px;
            max-height: 500px;
            overflow-y: auto;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            display: none;
            z-index: 1001;
            color: #333;
        }
        
        .floating-cart:hover .cart-preview {
            display: block;
        }
        
        .cart-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
            align-items: center;
            font-size: 14px;
        }
        
        .cart-total {
            font-weight: bold;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 2px solid #2196F3;
            font-size: 16px;
        }
        
        .btn-pedido {
            margin-top: 12px;
            background: #4CAF50;
            color: white;
            border: none;
            padding: 12px 15px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-weight: bold;
            font-size: 16px;
        }

        /* ESTILOS UNIFICADOS PARA PRODUCTOS */
        .product-container {
            margin-bottom: 20px;
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .product-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.08);
            padding: 18px;
            height: 100%;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
            border: 1px solid #e0e0e0;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 18px rgba(0,0,0,0.12);
        }
        
        .product-image {
            width: 100%;
            height: 160px;
            object-fit: cover;
            border-radius: 6px;
            margin-bottom: 12px;
        }
        
        .product-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #333;
            line-height: 1.3;
            min-height: 42px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .product-description {
            color: #666;
            font-size: 13px;
            margin-bottom: 12px;
            flex-grow: 1;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .current-price {
            font-size: 18px;
            font-weight: bold;
            color: #28a745;
        }
        
        .quantity-input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            text-align: center;
            min-width: 70px;
            font-weight: bold;
        }
        
        .add-to-cart-btn {
            flex: 2;
            padding: 10px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease;
            white-space: nowrap;
        }
        
        .add-to-cart-btn:hover {
            background: #218838;
        }
        
        .stock-info {
            font-size: 13px;
            padding: 5px 10px;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        
        .in-stock {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .out-of-stock {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .sold-out {
            background: rgba(255, 0, 0, 0.85);
            color: white;
            padding: 12px 15px;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
            margin-top: 8px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }
        
        .login-required-btn {
            width: 100%;
            padding: 12px;
            background: #6c757d;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
            cursor: not-allowed;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .category-badge {
            background: #17a2b8;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            margin-bottom: 8px;
            display: inline-block;
            font-weight: bold;
        }

        .category-section {
            margin-bottom: 30px;
        }
        
        .category-title {
            font-size: 22px;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #2196F3;
            font-weight: bold;
        }

        /* Toast y utilidades */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 8px;
            color: white;
            font-weight: bold;
            z-index: 9999;
            opacity: 0;
            transition: opacity 0.3s ease;
            font-size: 15px;
        }
        
        .toast.success { background: #28a745; }
        .toast.error { background: #dc3545; }
        .toast.show { opacity: 1; }

        /* Responsive */
        @media (max-width: 768px) {
            .floating-cart {
                top: 70px;
                right: 12px;
                padding: 12px 15px;
                font-size: 14px;
                min-width: 130px;
            }
            
            .cart-preview {
                min-width: 300px;
                right: -40px;
            }
        }
    </style>
</head>
<body style="background: url('assets/img/font-index.jpg') fixed; background-size: cover;">
    <?php include './inc/navbar.php'; ?>
    
    <!-- Toast para mensajes -->
    <div id="toast" class="toast"></div>
    
    <!-- Carrito flotante -->
    <div class="floating-cart">
        <i class="fa fa-shopping-cart"></i> Carrito
        <span class="cart-count" id="cart-count">
            <?php 
                $totalItems = 0;
                if (isset($_SESSION['carrito']) && is_array($_SESSION['carrito'])) {
                    foreach ($_SESSION['carrito'] as $cantidad) {
                        $totalItems += $cantidad;
                    }
                }
                echo $totalItems;
            ?>
        </span>
        
        <div class="cart-preview" id="cart-preview">
            <h5 style="margin-top: 0; position: sticky; top: 0; background: white; padding: 6px 0; z-index: 1; font-size: 16px;">Detalles del Carrito</h5>
            <div id="cart-content">
                <?php
                $suma = 0;
                if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
                    foreach($_SESSION['carrito'] as $codigoProd => $cantidad) {
                        $consulta = ejecutarSQL::consultar("SELECT * FROM producto WHERE CodigoProd='$codigoProd'");
                        if ($consulta && mysqli_num_rows($consulta) > 0) {
                            $fila = mysqli_fetch_array($consulta, MYSQLI_ASSOC);
                            $precioFinal = $fila['Precio'];
                            $subtotal = $precioFinal * $cantidad;
                            $suma += $subtotal;
                            echo '<div class="cart-item">
                                    <div>'.$fila['NombreProd'].'</div>
                                    <div>'.$cantidad.' x Bs. '.number_format($precioFinal, 2).'</div>
                                    <div><strong>Bs. '.number_format($subtotal, 2).'</strong></div>
                                  </div>';
                            mysqli_free_result($consulta);
                        }
                    }
                    echo '<div class="cart-total">Total: Bs. '.number_format($suma, 2).'</div>';
                    echo '<button class="btn-pedido" data-toggle="modal" data-target="#modalPedido">Realizar Venta</button>';
                    echo '<button class="btn btn-sm btn-warning btn-block mt-2" onclick="vaciarCarrito()" style="font-size: 14px; padding: 8px;">Vaciar Carrito</button>';
                } else {
                    echo '<p style="font-size: 14px; text-align: center; padding: 15px;">Tu carrito está vacío</p>';
                }
                ?>
            </div>
        </div>
    </div>

    <section id="store">
        <br>
        <div class="container">
            <div class="page-header">
                <h1>Productos <img src="assets/img/black-delizia1.jpg" alt="Logo Delizia" style="height: 50px; margin-bottom: 10px;"></h1>
            </div>

            <!-- Sección de categorías y búsqueda -->
            <?php if(mysqli_num_rows($categorias) >= 1): ?>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-xs-12 col-md-4">
                            <div class="dropdown">
                                <button class="btn btn-primary btn-raised dropdown-toggle" type="button" id="drpdowncategory" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    Seleccione una categoría &nbsp;
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="drpdowncategory">
                                    <li><a href="product.php">Todos los Productos</a></li>
                                    <li role="separator" class="divider"></li>
                                    <?php 
                                    while($cate = mysqli_fetch_array($categorias, MYSQLI_ASSOC)) {
                                        echo '<li><a href="product.php?categ='.$cate['CodigoCat'].'">'.$cate['Nombre'].'</a></li>
                                              <li role="separator" class="divider"></li>';
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4 col-md-offset-4">
                            <form action="product.php" method="GET" id="search-form">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-search" aria-hidden="true"></i></span>
                                        <input type="text" id="search-input" class="form-control" name="term" 
                                               value="<?php echo htmlspecialchars($search); ?>" 
                                               placeholder="Buscar productos...">
                                        <span class="input-group-btn">
                                            <button class="btn btn-info btn-raised" type="submit">Buscar</button>
                                        </span>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Mostrar productos -->
                <div class="row" style="margin-top: 20px;">
                    <?php
                    // Determinar qué consulta usar
                    if($is_search && $search != "") {
                        // Búsqueda de productos
                        $consultar_productos = ejecutarSQL::consultar("SELECT p.*, c.Nombre as CategoriaNombre 
                                                                      FROM producto p 
                                                                      JOIN categoria c ON p.CodigoCat = c.CodigoCat 
                                                                      WHERE p.NombreProd LIKE '%$search%' AND p.Estado='activo' 
                                                                      ORDER BY c.Nombre, p.id DESC");
                        $titulo = 'Resultados de búsqueda para: <strong>"'.$search.'"</strong>';
                        $mostrarCategorias = false;
                    } else if($categoria != "") {
                        // Productos por categoría específica
                        $selCat = ejecutarSQL::consultar("SELECT * FROM categoria WHERE CodigoCat='$categoria'");
                        $datCat = mysqli_fetch_array($selCat, MYSQLI_ASSOC);
                        $consultar_productos = ejecutarSQL::consultar("SELECT p.*, c.Nombre as CategoriaNombre 
                                                                      FROM producto p 
                                                                      JOIN categoria c ON p.CodigoCat = c.CodigoCat 
                                                                      WHERE p.CodigoCat='$categoria' AND p.Estado='activo' 
                                                                      ORDER BY p.id DESC");
                        $titulo = 'Productos de: <strong>"'.$datCat['Nombre'].'"</strong>';
                        $mostrarCategorias = false;
                    } else {
                        // TODOS los productos agrupados por categoría
                        $consultar_productos = ejecutarSQL::consultar("SELECT p.*, c.Nombre as CategoriaNombre 
                                                                      FROM producto p 
                                                                      JOIN categoria c ON p.CodigoCat = c.CodigoCat 
                                                                      WHERE p.Estado='activo' 
                                                                      ORDER BY c.Nombre, p.id DESC");
                        $titulo = 'Todos los Productos';
                        $mostrarCategorias = true;
                    }
                    
                    if(mysqli_num_rows($consultar_productos) >= 1) {
                        if(!$mostrarCategorias) {
                            echo '<div class="col-12"><h3 class="text-center" style="margin-bottom: 15px;">'.$titulo.'</h3><br></div>';
                        }
                        
                        $categoria_actual = "";
                        $primera_categoria = true;
                        
                        while($prod = mysqli_fetch_array($consultar_productos, MYSQLI_ASSOC)) {
                            // Si estamos mostrando por categorías, crear secciones
                            if($mostrarCategorias && $prod['CategoriaNombre'] != $categoria_actual) {
                                $categoria_actual = $prod['CategoriaNombre'];
                                
                                if(!$primera_categoria) {
                                    echo '</div>'; // Cerrar row anterior
                                }
                                
                                echo '<div class="category-section">';
                                echo '<h3 class="category-title">'.$categoria_actual.'</h3>';
                                echo '<div class="row">';
                                
                                $primera_categoria = false;
                            }
                            
                            $precioFinal = $prod['Precio'];
                            $stockDisponible = $prod['Stock'];
                            
                            // Calcular stock reservado en carrito
                            $stockReservado = 0;
                            if (isset($_SESSION['carrito']) && isset($_SESSION['carrito'][$prod['CodigoProd']])) {
                                $stockReservado = $_SESSION['carrito'][$prod['CodigoProd']];
                            }
                            $stockVisual = max(0, $stockDisponible - $stockReservado);
                    ?>
                            <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
                                <div class="product-container">
                                    <div class="product-card">
                                        <?php if(!$mostrarCategorias): ?>
                                            <span class="category-badge"><?php echo $prod['CategoriaNombre']; ?></span>
                                        <?php endif; ?>
                                        
                                        <img class="product-image" src="./assets/img-products/<?php echo ($prod['Imagen']!="" && is_file("./assets/img-products/".$prod['Imagen'])) ? $prod['Imagen'] : "default.png"; ?>" alt="<?php echo $prod['NombreProd']; ?>">
                                        
                                        <h4 class="product-title"><?php echo $prod['NombreProd']; ?></h4>
                                        
                                        <div class="product-description">
                                            <?php echo !empty($prod['Descripcion']) ? $prod['Descripcion'] : 'Delicioso producto de nuestra heladería.'; ?>
                                        </div>
                                        
                                        <div class="price-section">
                                            <span class="current-price">Bs. <?php echo number_format($precioFinal, 2); ?></span>
                                        </div>
                                        
                                        <div class="stock-info <?php echo ($stockVisual >= 1) ? 'in-stock' : 'out-of-stock'; ?>" 
                                             id="stock-info-<?php echo $prod['CodigoProd']; ?>" 
                                             data-stock="<?php echo $stockVisual; ?>" 
                                             data-stock-original="<?php echo $stockDisponible; ?>">
                                            <?php echo $stockVisual; ?> disponibles
                                        </div>
                                        
                                        <?php if($stockVisual >= 1 && (isset($_SESSION['nombreUser']) || isset($_SESSION['nombreAdmin']))): ?> 
                                            <div class="quantity-section" id="quantity-section-<?php echo $prod['CodigoProd']; ?>">
                                                <input type="number" class="quantity-input" id="quantity-<?php echo $prod['CodigoProd']; ?>" 
                                                       placeholder="Cant" min="1" max="<?php echo $stockVisual; ?>" value="">
                                                <button type="button" class="add-to-cart-btn" onclick="agregarAlCarrito('<?php echo $prod['CodigoProd']; ?>')">
                                                    <i class="fa fa-cart-plus"></i> Agregar
                                                </button>
                                            </div>
                                        <?php elseif($stockVisual <= 0): ?>
                                            <div class="sold-out" id="sold-out-<?php echo $prod['CodigoProd']; ?>">AGOTADO</div>
                                        <?php else: ?>
                                            <div class="login-required-btn">
                                                <i class="fa fa-lock"></i> Inicia sesión
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                    <?php
                        }
                        
                        // Cerrar la última sección de categoría si estamos mostrando por categorías
                        if($mostrarCategorias && !$primera_categoria) {
                            echo '</div></div>'; // Cerrar row y category-section
                        }
                    } else {
                        if($is_search) {
                            echo '<div class="col-12"><h2 class="text-center" style="padding: 30px 0;">No se encontraron productos con el nombre <strong>"'.$search.'"</strong></h2>';
                            echo '<p class="text-center"><a href="product.php" class="btn btn-info btn-raised">Ver todos los productos</a></p></div>';
                        } else {
                            echo '<div class="col-12"><h2 class="text-center" style="padding: 30px 0;">No hay productos disponibles</h2></div>';
                        }
                    }
                    if(isset($consultar_productos)) mysqli_free_result($consultar_productos);
                    ?>
                </div>
            <?php else: ?>
                <h2 class="text-center" style="padding: 30px 0;">No hay categorías disponibles</h2>
            <?php endif; ?>
        </div>
    </section>

    <?php
    include './inc/modal-pedido.php';
    include './process/modal-registro-cliente.php';
    include './process/modal-busqueda-avanzada.php';
    include './inc/footer.php';
    ?>

    <script>
    // Agregar al carrito con AJAX
    function agregarAlCarrito(codigoProducto) {
        const cantidadInput = document.getElementById('quantity-' + codigoProducto);
        let cantidad = parseInt(cantidadInput.value);
        const boton = cantidadInput.parentElement.querySelector('.add-to-cart-btn');
        const stockInfo = document.getElementById('stock-info-' + codigoProducto);
        const stockActual = parseInt(stockInfo.getAttribute('data-stock'));
        
        if (isNaN(cantidad) || cantidad < 1) {
            showToast('Por favor ingresa una cantidad válida (mínimo 1)', 'error');
            cantidadInput.focus();
            return;
        }

        if (cantidad > stockActual) {
            showToast('No hay suficiente stock disponible. Máximo disponible: ' + stockActual, 'error');
            cantidadInput.value = "";
            cantidadInput.focus();
            return;
        }

        const originalText = boton.innerHTML;
        boton.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Agregando...';
        boton.disabled = true;

        const formData = new FormData();
        formData.append('codigo', codigoProducto);
        formData.append('cantidad', cantidad);

        fetch('process/add_to_cart_ajax.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('cart-count').textContent = data.cart_count;
                updateCartPreview();
                updateStockDisplay(codigoProducto, cantidad);
                cantidadInput.value = "";
                showToast('Producto agregado al carrito', 'success');
            } else {
                showToast(data.message || 'Error al agregar producto', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error de conexión', 'error');
        })
        .finally(() => {
            boton.innerHTML = originalText;
            boton.disabled = false;
        });
    }

    function vaciarCarrito() {
        swal({
            title: "¿Vaciar carrito?",
            text: "Esta acción no se puede deshacer",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Aceptar",
            cancelButtonText: "Cancelar",
            closeOnConfirm: false,
            closeOnCancel: false
        }, function(isConfirm) {
            if (isConfirm) {
                fetch('process/vaciar_carrito_ajax.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            swal({
                                title: "Carrito vaciado",
                                text: "El carrito se ha vaciado exitosamente",
                                type: "success",
                                showCancelButton: true,
                                confirmButtonClass: "btn-danger",
                                confirmButtonText: "Aceptar",
                                cancelButtonText: "Cancelar",
                                closeOnConfirm: false,
                                closeOnCancel: false
                            }, function(isConfirm) {
                                location.reload();
                            });
                        } else {
                            swal("ERROR", data.message || "Error al vaciar el carrito", "error");
                        }
                    })
                    .catch(error => {
                        swal("ERROR", "Error de conexión", "error");
                    });
            } else {
                swal("Cancelado", "El carrito no se ha vaciado", "error");
            }
        });
    }

    function updateStockDisplay(codigoProducto, cantidad) {
        const stockInfo = document.getElementById('stock-info-' + codigoProducto);
        const quantitySection = document.getElementById('quantity-section-' + codigoProducto);
        const quantityInput = document.getElementById('quantity-' + codigoProducto);
        
        if (!stockInfo) return;
        
        let currentStock = parseInt(stockInfo.getAttribute('data-stock'));
        if (isNaN(currentStock)) return;

        const newStock = currentStock - cantidad;
        
        stockInfo.setAttribute('data-stock', newStock);
        stockInfo.textContent = newStock + ' disponibles';
        
        if (quantityInput) {
            quantityInput.max = newStock;
        }
        
        if (newStock <= 0) {
            stockInfo.className = 'stock-info out-of-stock';
            if (quantitySection) {
                quantitySection.innerHTML = '<div class="sold-out" id="sold-out-' + codigoProducto + '">AGOTADO</div>';
            }
        } else {
            stockInfo.className = 'stock-info in-stock';
        }
    }

    function updateCartPreview() {
        fetch('process/get_cart_preview.php')
            .then(response => response.text())
            .then(data => {
                document.getElementById('cart-content').innerHTML = data;
            })
            .catch(error => {
                console.error('Error al actualizar vista previa:', error);
            });
    }

    function showToast(message, type) {
        const toast = document.getElementById('toast');
        toast.textContent = message;
        toast.className = `toast ${type} show`;
        
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }

    // Validar entrada en tiempo real
    document.addEventListener('DOMContentLoaded', function() {
        const quantityInputs = document.querySelectorAll('.quantity-input');
        quantityInputs.forEach(input => {
            input.addEventListener('input', function() {
                const max = parseInt(this.max);
                const value = parseInt(this.value);
                
                if (value > max) {
                    this.value = max;
                    showToast('No puedes exceder el stock disponible: ' + max, 'error');
                }
                
                if (value < 0) {
                    this.value = "";
                }
            });
        });
    });

    document.getElementById('search-input').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('search-form').submit();
        }
    });
    </script>
</body>
</html>