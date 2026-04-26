<?php
/* 
 * Clase para ejecutar las consultas a la Base de Datos
 * Usa el archivo configServer.php para la configuración
 */

// Verificar si ya se incluyó configServer.php antes de incluirlo
if (!defined('USER')) {
    include __DIR__ . "/configServer.php";
}

class ejecutarSQL {
    private static $conexion = null;
    
    public static function conectar(){
        if(self::$conexion === null) {
            self::$conexion = mysqli_connect(SERVER, USER, PASS, BD);
            
            if(!self::$conexion) {
                die("Error en el servidor, verifique sus datos: " . mysqli_connect_error());
            }
            
            /* Codificar la información de la base de datos a UTF8 */
            mysqli_set_charset(self::$conexion, 'utf8');
        }
        return self::$conexion;  
    }
    
    public static function consultar($query) {
        $conexion = self::conectar();
        if (!$consul = mysqli_query($conexion, $query)) {
            error_log('Error en la consulta SQL: ' . mysqli_error($conexion) . ' - Consulta: ' . $query);
            return false;
        }
        return $consul;
    }
    
    public static function cerrarConexion() {
        if(self::$conexion !== null) {
            mysqli_close(self::$conexion);
            self::$conexion = null;
        }
    }
    
    // Función para obtener el último ID insertado
    public static function lastInsertId() {
        $conexion = self::conectar();
        return mysqli_insert_id($conexion);
    }
}

/* Clase para hacer las consultas Insertar, Eliminar y Actualizar */
class consultasSQL{
    public static function InsertSQL($tabla, $campos, $valores) {
        if (!$consul = ejecutarSQL::consultar("INSERT INTO $tabla ($campos) VALUES($valores)")) {
            error_log("Error al insertar datos en la tabla $tabla: " . mysqli_error(ejecutarSQL::conectar()));
            return false;
        }
        return $consul;
    }
    
    public static function DeleteSQL($tabla, $condicion) {
        if (!$consul = ejecutarSQL::consultar("DELETE FROM $tabla WHERE $condicion")) {
            error_log("Error al eliminar registros en la tabla $tabla: " . mysqli_error(ejecutarSQL::conectar()));
            return false;
        }
        return $consul;
    }
    
    public static function UpdateSQL($tabla, $campos, $condicion) {
        if (!$consul = ejecutarSQL::consultar("UPDATE $tabla SET $campos WHERE $condicion")) {
            error_log("Error al actualizar datos en la tabla $tabla: " . mysqli_error(ejecutarSQL::conectar()));
            return false;
        }
        return $consul;
    }
    
    public static function clean_string($val){
        $conexion = ejecutarSQL::conectar();
        // Verificar si el valor es null antes de usar trim()
        $val = ($val === null) ? '' : trim($val);
        $val = stripslashes($val);
        $val = mysqli_real_escape_string($conexion, $val);
        $val = str_ireplace("<script>", "", $val);
        $val = str_ireplace("</script>", "", $val);
        $val = str_ireplace("<script src", "", $val);
        $val = str_ireplace("<script type=", "", $val);
        $val = str_ireplace("SELECT * FROM", "", $val);
        $val = str_ireplace("DELETE FROM", "", $val);
        $val = str_ireplace("INSERT INTO", "", $val);
        $val = str_ireplace("--", "", $val);
        $val = str_ireplace("^", "", $val);
        $val = str_ireplace("[", "", $val);
        $val = str_ireplace("]", "", $val);
        $val = str_ireplace("==", "", $val);
        $val = str_ireplace(";", "", $val);
        return $val;
    }

    // Función para obtener el número de clientes
    public static function obtenerNumeroClientes() {
        $consultaClientes = ejecutarSQL::consultar("SELECT COUNT(*) as count FROM cliente");
        if($consultaClientes) {
            $resultado = mysqli_fetch_assoc($consultaClientes);
            return $resultado ? $resultado['count'] : 0;
        }
        return 0;
    }

    // Función para obtener el número de proveedores
    public static function obtenerNumeroProveedores() {
        $consultaProveedores = ejecutarSQL::consultar("SELECT COUNT(*) as count FROM proveedor");
        if($consultaProveedores) {
            $resultado = mysqli_fetch_assoc($consultaProveedores);
            return $resultado ? $resultado['count'] : 0;
        }
        return 0;
    }

    // Función para obtener el número de administradores
    public static function obtenerNumeroAdministradores() {
        $consultaAdministradores = ejecutarSQL::consultar("SELECT COUNT(*) as count FROM administrador");
        if($consultaAdministradores) {
            $resultado = mysqli_fetch_assoc($consultaAdministradores);
            return $resultado ? $resultado['count'] : 0;
        }
        return 0;
    }
    
    // Función para obtener las direcciones de los proveedores
    public static function obtenerDireccionesProveedores() {
        $consultaProveedores = ejecutarSQL::consultar("SELECT NITProveedor, NombreProveedor, Direccion FROM proveedor");
        $proveedores = [];
        if($consultaProveedores) {
            while ($row = mysqli_fetch_assoc($consultaProveedores)) {
                $proveedores[] = $row;
            }
        }
        return $proveedores;
    }
    
    // Función para obtener las direcciones de los clientes
    public static function obtenerDireccionesClientes() {
        $consultaClientes = ejecutarSQL::consultar("SELECT NIT, Nombre, Direccion FROM cliente");
        $clientes = [];
        if($consultaClientes) {
            while ($row = mysqli_fetch_assoc($consultaClientes)) {
                $clientes[] = $row;
            }
        }
        return $clientes;
    }
    
    // Función para obtener los detalles de un pedido específico - CORREGIDA
    public static function obtenerDetallesPedido($numPedido) {
        $consulta = ejecutarSQL::consultar("
            SELECT d.*, p.NombreProd, p.Precio 
            FROM detalle d 
            INNER JOIN producto p ON d.CodigoProd = p.CodigoProd 
            WHERE d.NumPedido = '$numPedido'
        ");
        
        $detalles = [];
        if($consulta) {
            while ($row = mysqli_fetch_assoc($consulta)) {
                $detalles[] = $row;
            }
        }
        return $detalles;
    }
    
    // Función para obtener todos los pedidos
    public static function obtenerTodosPedidos() {
        $consulta = ejecutarSQL::consultar("
            SELECT v.*, c.Nombre as NombreCliente 
            FROM venta v 
            LEFT JOIN cliente c ON v.NIT = c.NIT 
            ORDER BY v.Fecha DESC
        ");
        
        $pedidos = [];
        if($consulta) {
            while ($row = mysqli_fetch_assoc($consulta)) {
                $pedidos[] = $row;
            }
        }
        return $pedidos;
    }
    
    // Función para verificar la conexión a la base de datos
    public static function verificarConexion() {
        try {
            $conexion = ejecutarSQL::conectar();
            return $conexion !== false;
        } catch (Exception $e) {
            error_log("Error de conexión: " . $e->getMessage());
            return false;
        }
    }
    
    // Función para obtener estadísticas de ventas
    public static function obtenerEstadisticasVentas() {
        $estadisticas = [];
        
        // Total de ventas
        $consultaTotal = ejecutarSQL::consultar("SELECT SUM(TotalPagar) as total FROM venta WHERE Estado = 'entregado'");
        if($consultaTotal) {
            $row = mysqli_fetch_assoc($consultaTotal);
            $estadisticas['total_ventas'] = $row['total'] ? $row['total'] : 0;
        }
        
        // Pedidos pendientes
        $consultaPendientes = ejecutarSQL::consultar("SELECT COUNT(*) as count FROM venta WHERE Estado = 'pendiente'");
        if($consultaPendientes) {
            $row = mysqli_fetch_assoc($consultaPendientes);
            $estadisticas['pedidos_pendientes'] = $row['count'];
        }
        
        // Pedidos entregados
        $consultaEntregados = ejecutarSQL::consultar("SELECT COUNT(*) as count FROM venta WHERE Estado = 'entregado'");
        if($consultaEntregados) {
            $row = mysqli_fetch_assoc($consultaEntregados);
            $estadisticas['pedidos_entregados'] = $row['count'];
        }
        
        return $estadisticas;
    }

    // Función para obtener productos más vendidos - CORREGIDA
    public static function obtenerProductosMasVendidos($limite = 5) {
        $consulta = ejecutarSQL::consultar("
            SELECT p.CodigoProd, p.NombreProd, SUM(d.CantidadProductos) as total_vendido
            FROM detalle d
            INNER JOIN producto p ON d.CodigoProd = p.CodigoProd
            INNER JOIN venta v ON d.NumPedido = v.NumPedido
            WHERE v.Estado = 'entregado'
            GROUP BY p.CodigoProd, p.NombreProd
            ORDER BY total_vendido DESC
            LIMIT $limite
        ");
        
        $productos = [];
        if($consulta) {
            while ($row = mysqli_fetch_assoc($consulta)) {
                $productos[] = $row;
            }
        }
        return $productos;
    }

    // Función para buscar productos - CORREGIDA
    public static function buscarProductos($termino) {
        $termino = self::clean_string($termino);
        $consulta = ejecutarSQL::consultar("
            SELECT * FROM producto 
            WHERE NombreProd LIKE '%$termino%' 
            OR CodigoProd LIKE '%$termino%'
            ORDER BY NombreProd
        ");
        
        $productos = [];
        if($consulta) {
            while ($row = mysqli_fetch_assoc($consulta)) {
                $productos[] = $row;
            }
        }
        return $productos;
    }

    // Función para obtener información de un cliente específico
    public static function obtenerClientePorNIT($nit) {
        $nit = self::clean_string($nit);
        $consulta = ejecutarSQL::consultar("SELECT * FROM cliente WHERE NIT = '$nit'");
        if($consulta && mysqli_num_rows($consulta) > 0) {
            return mysqli_fetch_assoc($consulta);
        }
        return false;
    }

    // Función para obtener información de un administrador específico
    public static function obtenerAdministradorPorId($id) {
        $id = self::clean_string($id);
        $consulta = ejecutarSQL::consultar("SELECT * FROM administrador WHERE id = '$id'");
        if($consulta && mysqli_num_rows($consulta) > 0) {
            return mysqli_fetch_assoc($consulta);
        }
        return false;
    }

    // Función para obtener el stock de productos - CORREGIDA
    public static function obtenerStockProductos() {
        $consulta = ejecutarSQL::consultar("
            SELECT CodigoProd, NombreProd, Precio, Stock 
            FROM producto 
            ORDER BY Stock ASC, NombreProd
        ");
        
        $productos = [];
        if($consulta) {
            while ($row = mysqli_fetch_assoc($consulta)) {
                $productos[] = $row;
            }
        }
        return $productos;
    }

    // Función para obtener productos con stock bajo - CORREGIDA
    public static function obtenerProductosStockBajo($limiteStock = 10) {
        $consulta = ejecutarSQL::consultar("
            SELECT CodigoProd, NombreProd, Precio, Stock 
            FROM producto 
            WHERE Stock <= $limiteStock
            ORDER BY Stock ASC
        ");
        
        $productos = [];
        if($consulta) {
            while ($row = mysqli_fetch_assoc($consulta)) {
                $productos[] = $row;
            }
        }
        return $productos;
    }

    // Función para obtener ventas por mes
    public static function obtenerVentasPorMes($anio = null) {
        if($anio === null) {
            $anio = date('Y');
        }
        $consulta = ejecutarSQL::consultar("
            SELECT 
                MONTH(Fecha) as mes,
                COUNT(*) as total_ventas,
                SUM(TotalPagar) as total_ingresos
            FROM venta 
            WHERE YEAR(Fecha) = '$anio' AND Estado = 'entregado'
            GROUP BY MONTH(Fecha)
            ORDER BY mes
        ");
        
        $ventas = [];
        if($consulta) {
            while ($row = mysqli_fetch_assoc($consulta)) {
                $ventas[] = $row;
            }
        }
        return $ventas;
    }

    // Función para obtener los últimos pedidos
    public static function obtenerUltimosPedidos($limite = 10) {
        $consulta = ejecutarSQL::consultar("
            SELECT v.*, c.Nombre as NombreCliente 
            FROM venta v 
            LEFT JOIN cliente c ON v.NIT = c.NIT 
            ORDER BY v.Fecha DESC 
            LIMIT $limite
        ");
        
        $pedidos = [];
        if($consulta) {
            while ($row = mysqli_fetch_assoc($consulta)) {
                $pedidos[] = $row;
            }
        }
        return $pedidos;
    }

    // Función para obtener productos por categoría
    public static function obtenerProductosPorCategoria($categoria) {
        $categoria = self::clean_string($categoria);
        $consulta = ejecutarSQL::consultar("
            SELECT * FROM producto 
            WHERE CodigoCat = '$categoria' 
            ORDER BY NombreProd
        ");
        
        $productos = [];
        if($consulta) {
            while ($row = mysqli_fetch_assoc($consulta)) {
                $productos[] = $row;
            }
        }
        return $productos;
    }

    // Función para actualizar stock de producto
    public static function actualizarStockProducto($codigoProd, $nuevoStock) {
        $codigoProd = self::clean_string($codigoProd);
        $nuevoStock = intval($nuevoStock);
        return self::UpdateSQL('producto', "Stock = $nuevoStock", "CodigoProd = '$codigoProd'");
    }

    // Función para verificar credenciales de administrador
    public static function verificarAdmin($usuario, $password) {
        $usuario = self::clean_string($usuario);
        $password = md5(self::clean_string($password));
        $consulta = ejecutarSQL::consultar("SELECT * FROM administrador WHERE Nombre = '$usuario' AND Clave = '$password'");
        return ($consulta && mysqli_num_rows($consulta) > 0);
    }

    // Función para verificar credenciales de cliente
    public static function verificarCliente($usuario, $password) {
        $usuario = self::clean_string($usuario);
        $password = md5(self::clean_string($password));
        $consulta = ejecutarSQL::consultar("SELECT * FROM cliente WHERE Nombre = '$usuario' AND Clave = '$password'");
        return ($consulta && mysqli_num_rows($consulta) > 0);
    }

    // Nueva función para obtener productos por administrador
    public static function obtenerProductosPorAdministrador($idAdmin) {
        $idAdmin = self::clean_string($idAdmin);
        $consulta = ejecutarSQL::consultar("
            SELECT * FROM producto 
            WHERE id_administrador = '$idAdmin' 
            ORDER BY fecha_creacion DESC
        ");
        
        $productos = [];
        if($consulta) {
            while ($row = mysqli_fetch_assoc($consulta)) {
                $productos[] = $row;
            }
        }
        return $productos;
    }

    // Nueva función para obtener ventas por administrador
    public static function obtenerVentasPorAdministrador($idAdmin) {
        $idAdmin = self::clean_string($idAdmin);
        $consulta = ejecutarSQL::consultar("
            SELECT v.*, c.Nombre as NombreCliente 
            FROM venta v 
            LEFT JOIN cliente c ON v.NIT = c.NIT 
            WHERE v.id_administrador = '$idAdmin'
            ORDER BY v.Fecha DESC
        ");
        
        $ventas = [];
        if($consulta) {
            while ($row = mysqli_fetch_assoc($consulta)) {
                $ventas[] = $row;
            }
        }
        return $ventas;
    }

    // Función para obtener categorías activas
    public static function obtenerCategoriasActivas() {
        $consulta = ejecutarSQL::consultar("
            SELECT * FROM categoria 
            WHERE Estado = 'activa' 
            ORDER BY Nombre
        ");
        
        $categorias = [];
        if($consulta) {
            while ($row = mysqli_fetch_assoc($consulta)) {
                $categorias[] = $row;
            }
        }
        return $categorias;
    }

    // Función para obtener proveedores activos
    public static function obtenerProveedoresActivos() {
        $consulta = ejecutarSQL::consultar("
            SELECT * FROM proveedor 
            WHERE Estado = 'activo' 
            ORDER BY NombreProveedor
        ");
        
        $proveedores = [];
        if($consulta) {
            while ($row = mysqli_fetch_assoc($consulta)) {
                $proveedores[] = $row;
            }
        }
        return $proveedores;
    }
}

// Registrar función para cerrar conexión al finalizar el script
register_shutdown_function(['ejecutarSQL', 'cerrarConexion']);
?>