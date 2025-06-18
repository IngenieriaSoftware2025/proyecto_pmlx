<?php 
require_once __DIR__ . '/../includes/app.php';


use MVC\Router;
use Controllers\AppController;
use Controllers\MarcaController;
use Controllers\RolController;
use Controllers\UsuarioController;
use Controllers\ModeloController;
use Controllers\ClienteController;
use Controllers\DetalleVentaProductosController;
use Controllers\DetalleVentaServiciosController;
use Controllers\EstadisticasController;
use Controllers\InventarioController;
use Controllers\MovimientosInventarioController;
use Controllers\TiposServicioController;
use Controllers\TrabajadoresController;
use Controllers\OrdenReparacionController;
use Controllers\ServicioOrdenController;
use Controllers\VentasController;

$router = new Router();
$router->setBaseURL('/' . $_ENV['APP_NAME']);

$router->get('/', [AppController::class,'index']);
$router->get('/inicio', [AppController::class,'inicio']);

$router->get('/usuarios', [UsuarioController::class, 'renderizarPAgina']);
$router->post('/usuarios/guardarAPI', [UsuarioController::class, 'guardarAPI']);
$router->get('/usuarios/buscarAPI', [UsuarioController::class, 'buscarAPI']);
$router->post('/usuarios/modificarAPI', [UsuarioController::class, 'modificarAPI']);
$router->get('/usuarios/eliminarAPI', [UsuarioController::class, 'eliminarAPI']);
$router->get('/usuarios/rolesAPI', [UsuarioController::class, 'rolesAPI']);

$router->get('/roles', [RolController::class, 'renderizarPAgina']);
$router->post('/roles/guardarAPI', [RolController::class, 'guardarAPI']);
$router->get('/roles/buscarAPI', [RolController::class, 'buscarAPI']);
$router->post('/roles/modificarAPI', [RolController::class, 'modificarAPI']);
$router->get('/roles/eliminarAPI', [RolController::class, 'eliminarAPI']);
$router->get('/roles/eliminarRol', [RolController::class, 'EliminarRol']);

$router->get('/marcas', [MarcaController::class, 'renderizarPAgina']);
$router->post('/marcas/guardarAPI', [MarcaController::class, 'guardarAPI']);
$router->get('/marcas/buscarAPI', [MarcaController::class, 'buscarAPI']);
$router->post('/marcas/modificarAPI', [MarcaController::class, 'modificarAPI']);
$router->get('/marcas/eliminar', [MarcaController::class, 'EliminarAPI']);
$router->get('/marcas/EliminarMarca', [MarcaController::class, 'EliminarMarca']);

$router->get('/modelos', [ModeloController::class, 'renderizarPagina']);
$router->post('/modelos/guardarAPI', [ModeloController::class, 'guardarAPI']);
$router->get('/modelos/buscarAPI', [ModeloController::class, 'buscarAPI']);
$router->post('/modelos/modificarAPI', [ModeloController::class, 'modificarAPI']);
$router->get('/modelos/eliminar', [ModeloController::class, 'EliminarAPI']);
$router->get('/modelos/porMarca', [ModeloController::class, 'modelosPorMarcaAPI']);
$router->get('/marcas/disponibles', [MarcaController::class, 'marcasDisponiblesAPI']);

$router->get('/clientes', [ClienteController::class, 'renderizarPagina']);
$router->post('/clientes/guardarAPI', [ClienteController::class, 'guardarAPI']);
$router->get('/clientes/buscarAPI', [ClienteController::class, 'buscarAPI']);
$router->post('/clientes/modificarAPI', [ClienteController::class, 'modificarAPI']);
$router->get('/clientes/eliminar', [ClienteController::class, 'EliminarAPI']);
$router->get('/clientes/buscarPorNit', [ClienteController::class, 'buscarPorNitAPI']);
$router->get('/clientes/disponibles', [ClienteController::class, 'clientesDisponiblesAPI']);

$router->get('/inventario', [InventarioController::class, 'renderizarPagina']);
$router->post('/inventario/guardarAPI', [InventarioController::class, 'guardarAPI']);
$router->get('/inventario/buscarAPI', [InventarioController::class, 'buscarAPI']);
$router->post('/inventario/modificarAPI', [InventarioController::class, 'modificarAPI']);
$router->get('/inventario/eliminar', [InventarioController::class, 'EliminarAPI']);
$router->get('/inventario/modelosPorMarca', [InventarioController::class, 'modelosPorMarcaAPI']);
$router->get('/inventario/disponible', [InventarioController::class, 'inventarioDisponibleAPI']);

$router->get('/marcas/disponibles', [InventarioController::class, 'marcasDisponiblesAPI']);
$router->get('/modelos/buscarAPI', [InventarioController::class, 'buscarModelosAPI']);

$router->get('/tipos_servicio', [TiposServicioController::class, 'renderizarPagina']);
$router->post('/tipos_servicio/guardarAPI', [TiposServicioController::class, 'guardarAPI']);
$router->get('/tipos_servicio/buscarAPI', [TiposServicioController::class, 'buscarAPI']);
$router->post('/tipos_servicio/modificarAPI', [TiposServicioController::class, 'modificarAPI']);
$router->get('/tipos_servicio/eliminar', [TiposServicioController::class, 'EliminarAPI']);
$router->get('/tipos_servicio/disponibles', [TiposServicioController::class, 'serviciosDisponiblesAPI']);

$router->get('/trabajadores', [TrabajadoresController::class, 'renderizarPagina']);
$router->post('/trabajadores/guardarAPI', [TrabajadoresController::class, 'guardarAPI']);
$router->get('/trabajadores/buscarAPI', [TrabajadoresController::class, 'buscarAPI']);
$router->post('/trabajadores/modificarAPI', [TrabajadoresController::class, 'modificarAPI']);
$router->get('/trabajadores/eliminar', [TrabajadoresController::class, 'EliminarAPI']);
$router->get('/trabajadores/usuariosDisponibles', [TrabajadoresController::class, 'usuariosDisponiblesAPI']);
$router->get('/trabajadores/todosUsuarios', [TrabajadoresController::class, 'todosUsuariosAPI']);
$router->get('/trabajadores/disponibles', [TrabajadoresController::class, 'trabajadoresDisponiblesAPI']);

$router->get('/ordenes_reparacion', [OrdenReparacionController::class, 'renderizarPagina']);
$router->post('/ordenes_reparacion/guardarAPI', [OrdenReparacionController::class, 'guardarAPI']);
$router->get('/ordenes_reparacion/buscarAPI', [OrdenReparacionController::class, 'buscarAPI']);
$router->post('/ordenes_reparacion/modificarAPI', [OrdenReparacionController::class, 'modificarAPI']);
$router->get('/ordenes_reparacion/eliminar', [OrdenReparacionController::class, 'EliminarAPI']);
$router->get('/ordenes_reparacion/clientesDisponiblesAPI', [OrdenReparacionController::class, 'clientesDisponiblesAPI']);
$router->get('/ordenes_reparacion/marcasDisponiblesAPI', [OrdenReparacionController::class, 'marcasDisponiblesAPI']);
$router->get('/ordenes_reparacion/trabajadoresDisponiblesAPI', [OrdenReparacionController::class, 'trabajadoresDisponiblesAPI']);

$router->get('/servicios_orden', [ServicioOrdenController::class, 'renderizarPagina']);
$router->post('/servicios_orden/guardarAPI', [ServicioOrdenController::class, 'guardarAPI']);
$router->get('/servicios_orden/buscarAPI', [ServicioOrdenController::class, 'buscarAPI']);
$router->post('/servicios_orden/modificarAPI', [ServicioOrdenController::class, 'modificarAPI']);
$router->get('/servicios_orden/eliminar', [ServicioOrdenController::class, 'EliminarAPI']);
$router->get('/servicios_orden/VentasAsociadasServicio', [ServicioOrdenController::class, 'VentasAsociadasServicio']);
$router->get('/servicios_orden/ObtenerServiciosPorOrden', [ServicioOrdenController::class, 'ObtenerServiciosPorOrden']);
$router->get('/servicios_orden/EliminarServicio', [ServicioOrdenController::class, 'EliminarServicio']);
$router->get('/servicios_orden/ordenesDisponiblesAPI', [ServicioOrdenController::class, 'ordenesDisponiblesAPI']);
$router->get('/servicios_orden/tiposServicioDisponiblesAPI', [ServicioOrdenController::class, 'tiposServicioDisponiblesAPI']);

$router->get('/ventas', [VentasController::class, 'renderizarPagina']);

$router->post('/ventas/guardarAPI', [VentasController::class, 'guardarAPI']);
$router->get('/ventas/buscarAPI', [VentasController::class, 'buscarAPI']);
$router->post('/ventas/modificarAPI', [VentasController::class, 'modificarAPI']);
$router->get('/ventas/eliminar', [VentasController::class, 'eliminarAPI']);

$router->get('/ventas/clientesDisponiblesAPI', [VentasController::class, 'clientesDisponiblesAPI']);
$router->get('/ventas/vendedoresDisponiblesAPI', [VentasController::class, 'vendedoresDisponiblesAPI']);
$router->get('/ventas/generarNumeroFacturaAPI', [VentasController::class, 'generarNumeroFacturaAPI']);

$router->get('/detalle_venta_productos', [DetalleVentaProductosController::class, 'renderizarPagina']);

$router->post('/detalle_venta_productos/guardarAPI', [DetalleVentaProductosController::class, 'guardarAPI']);
$router->get('/detalle_venta_productos/buscarAPI', [DetalleVentaProductosController::class, 'buscarAPI']);
$router->post('/detalle_venta_productos/modificarAPI', [DetalleVentaProductosController::class, 'modificarAPI']);
$router->get('/detalle_venta_productos/eliminar', [DetalleVentaProductosController::class, 'eliminarAPI']);

$router->get('/detalle_venta_productos/ventasDisponiblesAPI', [DetalleVentaProductosController::class, 'ventasDisponiblesAPI']);
$router->get('/detalle_venta_productos/productosDisponiblesAPI', [DetalleVentaProductosController::class, 'productosDisponiblesAPI']);
$router->get('/detalle_venta_productos/obtenerDetallesPorVenta', [DetalleVentaProductosController::class, 'obtenerDetallesPorVenta']);

$router->get('/detalle_venta_servicios', [DetalleVentaServiciosController::class, 'renderizarPagina']);

$router->post('/detalle_venta_servicios/guardarAPI', [DetalleVentaServiciosController::class, 'guardarAPI']);
$router->get('/detalle_venta_servicios/buscarAPI', [DetalleVentaServiciosController::class, 'buscarAPI']);
$router->post('/detalle_venta_servicios/modificarAPI', [DetalleVentaServiciosController::class, 'modificarAPI']);
$router->get('/detalle_venta_servicios/eliminar', [DetalleVentaServiciosController::class, 'eliminarAPI']);

$router->get('/detalle_venta_servicios/ventasDisponiblesAPI', [DetalleVentaServiciosController::class, 'ventasDisponiblesAPI']);
$router->get('/detalle_venta_servicios/ordenesDisponiblesAPI', [DetalleVentaServiciosController::class, 'ordenesDisponiblesAPI']);

$router->get('/movimientos_inventario', [MovimientosInventarioController::class, 'renderizarPagina']);

$router->post('/movimientos_inventario/guardarAPI', [MovimientosInventarioController::class, 'guardarAPI']);
$router->get('/movimientos_inventario/buscarAPI', [MovimientosInventarioController::class, 'buscarAPI']);
$router->post('/movimientos_inventario/modificarAPI', [MovimientosInventarioController::class, 'modificarAPI']);
$router->get('/movimientos_inventario/eliminar', [MovimientosInventarioController::class, 'eliminarAPI']);

$router->get('/movimientos_inventario/productosInventarioAPI', [MovimientosInventarioController::class, 'productosInventarioAPI']);
$router->get('/movimientos_inventario/usuariosDisponiblesAPI', [MovimientosInventarioController::class, 'usuariosDisponiblesAPI']);

$router->get('/movimientos_inventario/resumenPorProductoAPI', [MovimientosInventarioController::class, 'resumenPorProductoAPI']);
$router->get('/movimientos_inventario/movimientosPorProductoAPI', [MovimientosInventarioController::class, 'movimientosPorProductoAPI']);

$router->get('/login', [AppController::class,'index']);
$router->get('/logout', [AppController::class,'logout']);
$router->post('/API/login', [AppController::class,'login']);
$router->get('/API/logout', [AppController::class,'logout']);
$router->post('/hashear', [AppController::class, 'hashearPassword']);
$router->post('/actualizarPasswordsExistentes', [AppController::class, 'actualizarPasswordsExistentes']);

//estadisticas 

$router->get('/estadisticas', [EstadisticasController::class, 'renderizarPagina']);
$router->get('/estadisticas/buscarAPI', [EstadisticasController::class, 'buscarAPI']);
$router->get('/estadisticas/buscarClientesAPI', [EstadisticasController::class, 'buscarClientesAPI']);
$router->get('/estadisticas/buscarVentasMesAPI', [EstadisticasController::class, 'buscarVentasMesAPI']);
$router->get('/estadisticas/buscarMarcasAPI', [EstadisticasController::class, 'buscarMarcasAPI']);
$router->get('/estadisticas/buscarTrabajadoresAPI', [EstadisticasController::class, 'buscarTrabajadoresAPI']);
$router->get('/estadisticas/buscarUsuariosAPI', [EstadisticasController::class, 'buscarUsuariosAPI']);


$router->comprobarRutas();