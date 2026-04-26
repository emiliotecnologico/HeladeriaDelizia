-- Base de Datos Sistema de Información de Ventas - Heladería Delizia
-- Ubicación: Oruro, Bolivia
-- Estructura MODIFICADA: 
-- 1. Cambiado Descuento por Aumento en productos
-- 2. Eliminado campo Nombre de la tabla clientes
-- 3. Ruta de imágenes: C:\laragon\www\HeladeriaDelizia\assets\img-products\

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;

-- --------------------------------------------------------
-- Estructura de tabla para la tabla `administrador`
-- --------------------------------------------------------

CREATE TABLE `administrador` (
  `id` int NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(30) NOT NULL,
  `Clave` varchar(255) NOT NULL,
  `Estado` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `ultimo_acceso` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Nombre` (`Nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------
-- Volcado de datos para la tabla `administrador`
-- --------------------------------------------------------

INSERT INTO `administrador` (`id`, `Nombre`, `Clave`, `Estado`, `fecha_creacion`, `ultimo_acceso`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'activo', NOW(), NULL),
(2, 'maria_gonzalez', '5f4dcc3b5aa765d61d8327deb882cf99', 'activo', NOW(), NULL);

-- --------------------------------------------------------
-- Estructura de tabla para la tabla `categoria`
-- --------------------------------------------------------

CREATE TABLE `categoria` (
  `CodigoCat` varchar(30) NOT NULL,
  `Nombre` varchar(30) NOT NULL,
  `Descripcion` text NOT NULL,
  `Estado` enum('activa','inactiva') DEFAULT 'activa',
  PRIMARY KEY (`CodigoCat`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------
-- Volcado de datos para la tabla `categoria`
-- --------------------------------------------------------

INSERT INTO `categoria` (`CodigoCat`, `Nombre`, `Descripcion`, `Estado`) VALUES
('HELADOS', 'Helados', 'Deliziosos helados de diferentes sabores', 'activa'),
('JUGOS', 'Jugos', 'Liquidos de dulzes sabores', 'activa'),
('LACTEOS', 'Lácteos', 'Cremosos y deliziosos lácteos', 'activa');

-- --------------------------------------------------------
-- Estructura de tabla para la tabla `vendedores`
-- --------------------------------------------------------

CREATE TABLE `vendedores` (
  `NIT` varchar(30) NOT NULL,
  `Nombre` varchar(30) NOT NULL,
  `NombreCompleto` varchar(70) NOT NULL,
  `Apellido` varchar(70) NOT NULL,
  `Clave` varchar(255) NOT NULL,
  `Direccion` varchar(200) NOT NULL,
  `Telefono` varchar(20) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Estado` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_registro` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`NIT`),
  UNIQUE KEY `Email` (`Email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------
-- Volcado de datos para la tabla `vendedores`
-- --------------------------------------------------------

INSERT INTO `vendedores` (`NIT`, `Nombre`, `NombreCompleto`, `Apellido`, `Clave`, `Direccion`, `Telefono`, `Email`, `Estado`, `fecha_registro`) VALUES
('123456789', 'juan_perez', 'Juan Carlos', 'Perez Mamani', '5f4dcc3b5aa765d61d8327deb882cf99', 'Av. 6 de Agosto #123, Oruro', '59171234567', 'juan.perez@email.com', 'activo', NOW()),
('987654321', 'ana_rodriguez', 'Ana Maria', 'Rodriguez Lopez', '5f4dcc3b5aa765d61d8327deb882cf99', 'Calle Bolivar #456, Oruro', '59171234568', 'ana.rodriguez@email.com', 'activo', NOW()),
('134567', 'genezio', 'Genezio Alfredo', 'Barros Mamani', '5f4dcc3b5aa765d61d8327deb882cf99', 'Calle La Plata #56', '7297042', 'genezio1112@gmail.com', 'activo', NOW()),
('456789123', 'luis_martinez', 'Luis Alberto', 'Martinez Flores', '5f4dcc3b5aa765d61d8327deb882cf99', 'Av. Dehene #234, Oruro', '59171234572', 'luis.martinez@email.com', 'activo', NOW()),
('789123456', 'carla_torrez', 'Carla Patricia', 'Torrez Mendoza', '5f4dcc3b5aa765d61d8327deb882cf99', 'Calle Junín #567, Oruro', '59171234573', 'carla.torrez@email.com', 'activo', NOW()),
('321654987', 'miguel_aguilar', 'Miguel Angel', 'Aguilar Rojas', '5f4dcc3b5aa765d61d8327deb882cf99', 'Av. Ejército #890, Oruro', '59171234574', 'miguel.aguilar@email.com', 'activo', NOW());

-- --------------------------------------------------------
-- Estructura MODIFICADA de tabla para la tabla `clientes` (ELIMINADO campo Nombre)
-- --------------------------------------------------------

CREATE TABLE `clientes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `NIT` varchar(30) NOT NULL,
  `NombreCompleto` varchar(70) NOT NULL,
  `Apellido` varchar(70) NOT NULL,
  `Direccion` varchar(200) NOT NULL,
  `Telefono` varchar(20) NOT NULL,
  `Estado` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_registro` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `NIT` (`NIT`),
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------
-- Volcado de datos MODIFICADO para la tabla `clientes` (ELIMINADO campo Nombre)
-- --------------------------------------------------------

INSERT INTO `clientes` (`id`, `NIT`, `NombreCompleto`, `Apellido`, `Direccion`, `Telefono`, `Estado`, `fecha_registro`) VALUES
(1, '111222333', 'Carlos Andres', 'Silva Fernandez', 'Av. Ejercito #789, Oruro', '59171234569', 'activo', NOW()),
(2, '444555666', 'Laura Patricia', 'Mendez Castro', 'Calle Washington #321, Oruro', '59171234570', 'activo', NOW()),
(3, '777888999', 'Roberto Jose', 'Vega Rios', 'Av. España #654, Oruro', '59171234571', 'activo', NOW()),
(4, '222333444', 'Maria Fernanda', 'Lopez Gutierrez', 'Av. Brasil #987, Oruro', '59171234575', 'activo', NOW()),
(5, '555666777', 'Jorge Antonio', 'Castro Morales', 'Calle Ayacucho #654, Oruro', '59171234576', 'activo', NOW()),
(6, '888999000', 'Sofia Alejandra', 'Vargas Paredes', 'Av. San Felipe #321, Oruro', '59171234577', 'activo', NOW());

-- --------------------------------------------------------
-- Estructura de tabla para la tabla `producto` (MODIFICADA: Descuento por Aumento)
-- --------------------------------------------------------

CREATE TABLE `producto` (
  `id` int NOT NULL AUTO_INCREMENT,
  `CodigoProd` varchar(30) NOT NULL,
  `NombreProd` varchar(100) NOT NULL,
  `CodigoCat` varchar(30) NOT NULL,
  `Precio` decimal(30,2) NOT NULL,
  `Aumento` int NOT NULL,
  `Stock` int NOT NULL,
  `Imagen` varchar(150) NOT NULL,
  `Estado` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `id_administrador` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `CodigoProd` (`CodigoProd`),
  KEY `CodigoCat` (`CodigoCat`),
  KEY `id_administrador` (`id_administrador`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------
-- Volcado de datos para la tabla `producto` (ACTUALIZADO: Descuento por Aumento)
-- Ruta de imágenes: C:\laragon\www\HeladeriaDelizia\assets\img-products\
-- --------------------------------------------------------

INSERT INTO `producto` (`id`, `CodigoProd`, `NombreProd`, `CodigoCat`, `Precio`, `Aumento`, `Stock`, `Imagen`, `Estado`, `fecha_creacion`, `id_administrador`) VALUES
(1, '0001', 'Negrito', 'HELADOS', 2.50, 0, 500, 'negrito.jpg', 'activo', NOW(), 1),
(2, '0002', 'Delizaurio', 'HELADOS', 2.00, 5, 450, 'delizaurio.png', 'activo', NOW(), 1),
(3, '0003', 'Delivasito', 'HELADOS', 1.50, 0, 350, 'delivasito.png', 'activo', NOW(), 1),
(4, '0004', 'Delipop', 'HELADOS', 1.00, 10, 250, 'delipop.png', 'activo', NOW(), 1),
(5, '0005', 'Bolo Vaquita', 'HELADOS', 1.00, 0, 300, 'bolovaquita.png', 'activo', NOW(), 2),
(6, '0006', 'Gemelos', 'HELADOS', 2.00, 5, 200, 'gemelos.png', 'activo', NOW(), 2),
(7, '0007', 'Alfredo Canela', 'HELADOS', 1.50, 0, 400, 'alfredo-canela.png', 'activo', NOW(), 1),
(8, '0008', 'Waferito', 'HELADOS', 1.50, 0, 150, 'waferito.png', 'activo', NOW(), 2),
(9, '0009', 'Tentación', 'HELADOS', 6.00, 0, 500, 'tentacion.png', 'activo', NOW(), 1),
(10, '0010', 'Chantilly Tradicional', 'HELADOS', 2.50, 0, 500, 'chantilly-trad.png', 'activo', NOW(), 1),
(11, '0011', 'Rocky Ruso', 'HELADOS', 3.50, 5, 450, 'rockyruso.png', 'activo', NOW(), 1),
(12, '0012', 'Ice Fruit 2l', 'JUGOS', 13.50, 0, 350, 'ice-fruit-citrus2L.png', 'activo', NOW(), 1),
(13, '0013', 'Tampico 2l', 'JUGOS', 11.00, 10, 250, 'tampico-2L.png', 'activo', NOW(), 1),
(14, '0014', 'Nectar Frush 600ml', 'JUGOS', 12.00, 0, 300, 'nectar-frush-2L.png', 'activo', NOW(), 2),
(15, '0015', 'Agua Glaciar 2.5l', 'JUGOS', 10.00, 5, 200, 'agua-glaciar-25l.png', 'activo', NOW(), 2),
(16, '0016', 'Yogurt Familiar 2kg', 'LACTEOS', 14.00, 0, 400, 'Yogurt-familiar-2kg.png', 'activo', NOW(), 1),
(17, '0017', 'Yogurt Griego 170g', 'LACTEOS', 9.00, 0, 150, 'yogurt-griego-170g.png', 'activo', NOW(), 2),
(18, '0018', 'Leche Fresca 946ml', 'LACTEOS', 6.50, 0, 500, 'Leche-Fresca-946ml.png', 'activo', NOW(), 1),
(19, '0019', 'Base Soft 946ml', 'LACTEOS', 10.50, 0, 500, 'base-soft-946ml.png', 'activo', NOW(), 1),
(20, '0020', 'Protein Shake 13.8g', 'LACTEOS', 6.00, 0, 500, 'protein-shake-220g.png', 'activo', NOW(), 1);

-- --------------------------------------------------------
-- Estructura ACTUALIZADA para la tabla `venta`
-- --------------------------------------------------------

CREATE TABLE `venta` (
  `NumPedido` int NOT NULL AUTO_INCREMENT,
  `Fecha` date NOT NULL,
  `id_cliente` int NOT NULL,
  `id_vendedor` varchar(30) DEFAULT NULL,
  `TotalPagar` decimal(30,2) NOT NULL,
  `Estado` enum('pendiente','confirmado','enviado','entregado','cancelado') DEFAULT 'pendiente',
  `TipoEnvio` varchar(37) NOT NULL,
  `Adjunto` varchar(255) DEFAULT NULL,
  `id_administrador` int DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`NumPedido`),
  KEY `id_cliente` (`id_cliente`),
  KEY `id_vendedor` (`id_vendedor`),
  KEY `id_administrador` (`id_administrador`),
  KEY `Fecha` (`Fecha`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------
-- Volcado de datos para la tabla `venta`
-- --------------------------------------------------------

INSERT INTO `venta` (`NumPedido`, `Fecha`, `id_cliente`, `id_vendedor`, `TotalPagar`, `Estado`, `TipoEnvio`, `Adjunto`, `id_administrador`, `fecha_actualizacion`) VALUES
(1, '2024-01-15', 1, '123456789', 45.50, 'entregado', 'Recojo en tienda', 'comprobante_1.jpg', 1, NOW()),
(2, '2024-01-16', 2, '987654321', 60.00, 'confirmado', 'Recojo en tienda', 'comprobante_2.jpg', 2, NOW()),
(3, '2024-01-17', 3, '134567', 32.50, 'pendiente', 'Recojo en tienda', NULL, NULL, NOW()),
(4, '2024-01-18', 4, '456789123', 28.75, 'enviado', 'Recojo en tienda', 'comprobante_4.jpg', 1, NOW()),
(5, '2024-01-19', 5, '789123456', 52.30, 'confirmado', 'Recojo en tienda', 'comprobante_5.jpg', 2, NOW()),
(6, '2024-01-20', 6, '321654987', 41.80, 'pendiente', 'Recojo en tienda', NULL, NULL, NOW());

-- --------------------------------------------------------
-- Estructura de tabla para la tabla `detalle`
-- --------------------------------------------------------

CREATE TABLE `detalle` (
  `NumPedido` int NOT NULL,
  `CodigoProd` varchar(30) NOT NULL,
  `CantidadProductos` int NOT NULL,
  `PrecioProd` decimal(30,2) NOT NULL,
  PRIMARY KEY (`NumPedido`,`CodigoProd`),
  KEY `CodigoProd` (`CodigoProd`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------
-- Volcado de datos para la tabla `detalle`
-- --------------------------------------------------------

INSERT INTO `detalle` (`NumPedido`, `CodigoProd`, `CantidadProductos`, `PrecioProd`) VALUES
(1, '0001', 2, 15.50),
(1, '0002', 1, 18.00),
(2, '0005', 1, 25.00),
(2, '0003', 2, 16.50),
(3, '0007', 1, 12.00),
(3, '0008', 1, 22.00),
(4, '0010', 3, 7.50),
(4, '0011', 1, 3.50),
(5, '0012', 2, 27.00),
(5, '0016', 1, 14.00),
(6, '0019', 1, 10.50),
(6, '0020', 2, 12.00);

-- --------------------------------------------------------
-- Restricciones para tablas volcadas
-- --------------------------------------------------------

-- Restricciones para la tabla `producto`
ALTER TABLE `producto`
  ADD CONSTRAINT `producto_ibfk_admin` FOREIGN KEY (`id_administrador`) REFERENCES `administrador` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `producto_ibfk_categoria` FOREIGN KEY (`CodigoCat`) REFERENCES `categoria` (`CodigoCat`) ON UPDATE CASCADE;

-- Restricciones para la tabla `venta`
ALTER TABLE `venta`
  ADD CONSTRAINT `venta_ibfk_admin` FOREIGN KEY (`id_administrador`) REFERENCES `administrador` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `venta_ibfk_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `venta_ibfk_vendedor` FOREIGN KEY (`id_vendedor`) REFERENCES `vendedores` (`NIT`) ON UPDATE CASCADE;

-- Restricciones para la tabla `detalle`
ALTER TABLE `detalle`
  ADD CONSTRAINT `detalle_ibfk_producto` FOREIGN KEY (`CodigoProd`) REFERENCES `producto` (`CodigoProd`) ON UPDATE CASCADE,
  ADD CONSTRAINT `detalle_ibfk_venta` FOREIGN KEY (`NumPedido`) REFERENCES `venta` (`NumPedido`) ON DELETE CASCADE ON UPDATE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;