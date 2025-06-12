<?php 
require_once __DIR__ . '/../includes/app.php';


use MVC\Router;
use Controllers\AppController;
use Controllers\MarcaController;
use Controllers\RolController;
use Controllers\UsuarioController;
use Controllers\ModeloController;
use Controllers\ClienteController;
use Controllers\InventarioController;
use Controllers\TiposServicioController;
use Controllers\TrabajadoresController;
use Controllers\OrdenReparacionController;
use Controllers\ServicioOrdenController;

$router = new Router();
$router->setBaseURL('/' . $_ENV['APP_NAME']);

$router->get('/', [AppController::class,'index']);

//rutas de mis usuarios 
$router->get('/usuarios', [UsuarioController::class, 'renderizarPAgina']);
$router->post('/usuarios/guardarAPI', [UsuarioController::class, 'guardarAPI']);
$router->get('/usuarios/buscarAPI', [UsuarioController::class, 'buscarAPI']);
$router->post('/usuarios/modificarAPI', [UsuarioController::class, 'modificarAPI']);
$router->get('/usuarios/eliminarAPI', [UsuarioController::class, 'eliminarAPI']);
// En tu archivo de rutas (probablemente Router.php o routes.php)
$router->get('/usuarios/rolesAPI', [UsuarioController::class, 'rolesAPI']);

//rutas de mis Roles 
$router->get('/roles', [RolController::class, 'renderizarPAgina']);
$router->post('/roles/guardarAPI', [RolController::class, 'guardarAPI']);
$router->get('/roles/buscarAPI', [RolController::class, 'buscarAPI']);
$router->post('/roles/modificarAPI', [RolController::class, 'modificarAPI']);
$router->get('/roles/eliminarAPI', [RolController::class, 'eliminarAPI']);
$router->get('/roles/eliminarRol', [RolController::class, 'EliminarRol']);


//rutas de mis Marcas 

$router->get('/marcas', [MarcaController::class, 'renderizarPAgina']);
$router->post('/marcas/guardarAPI', [MarcaController::class, 'guardarAPI']);
$router->get('/marcas/buscarAPI', [MarcaController::class, 'buscarAPI']);
$router->post('/marcas/modificarAPI', [MarcaController::class, 'modificarAPI']);
$router->get('/marcas/eliminar', [MarcaController::class, 'EliminarAPI']);
$router->get('/marcas/EliminarMarca', [MarcaController::class, 'EliminarMarca']);



// RUTAS DE MODELOS
$router->get('/modelos', [ModeloController::class, 'renderizarPagina']);
$router->post('/modelos/guardarAPI', [ModeloController::class, 'guardarAPI']);
$router->get('/modelos/buscarAPI', [ModeloController::class, 'buscarAPI']);
$router->post('/modelos/modificarAPI', [ModeloController::class, 'modificarAPI']);
$router->get('/modelos/eliminar', [ModeloController::class, 'EliminarAPI']);
$router->get('/modelos/porMarca', [ModeloController::class, 'modelosPorMarcaAPI']);
$router->get('/marcas/disponibles', [MarcaController::class, 'marcasDisponiblesAPI']);

// RUTAS DE CLIENTES
$router->get('/clientes', [ClienteController::class, 'renderizarPagina']);
$router->post('/clientes/guardarAPI', [ClienteController::class, 'guardarAPI']);
$router->get('/clientes/buscarAPI', [ClienteController::class, 'buscarAPI']);
$router->post('/clientes/modificarAPI', [ClienteController::class, 'modificarAPI']);
$router->get('/clientes/eliminar', [ClienteController::class, 'EliminarAPI']);
$router->get('/clientes/buscarPorNit', [ClienteController::class, 'buscarPorNitAPI']);
$router->get('/clientes/disponibles', [ClienteController::class, 'clientesDisponiblesAPI']);


// RUTAS DE INVENTARIO - CORREGIDAS
$router->get('/inventario', [InventarioController::class, 'renderizarPagina']);
$router->post('/inventario/guardarAPI', [InventarioController::class, 'guardarAPI']);
$router->get('/inventario/buscarAPI', [InventarioController::class, 'buscarAPI']);
$router->post('/inventario/modificarAPI', [InventarioController::class, 'modificarAPI']);
$router->get('/inventario/eliminar', [InventarioController::class, 'EliminarAPI']);
$router->get('/inventario/modelosPorMarca', [InventarioController::class, 'modelosPorMarcaAPI']);
$router->get('/inventario/disponible', [InventarioController::class, 'inventarioDisponibleAPI']);

// NUEVAS RUTAS PARA MARCAS Y MODELOS DESDE INVENTARIO
$router->get('/marcas/disponibles', [InventarioController::class, 'marcasDisponiblesAPI']);
$router->get('/modelos/buscarAPI', [InventarioController::class, 'buscarModelosAPI']);


// RUTAS DE TIPOS DE SERVICIO
$router->get('/tipos_servicio', [TiposServicioController::class, 'renderizarPagina']);
$router->post('/tipos_servicio/guardarAPI', [TiposServicioController::class, 'guardarAPI']);
$router->get('/tipos_servicio/buscarAPI', [TiposServicioController::class, 'buscarAPI']);
$router->post('/tipos_servicio/modificarAPI', [TiposServicioController::class, 'modificarAPI']);
$router->get('/tipos_servicio/eliminar', [TiposServicioController::class, 'EliminarAPI']);
$router->get('/tipos_servicio/disponibles', [TiposServicioController::class, 'serviciosDisponiblesAPI']);


// RUTAS DE TRABAJADORES
$router->get('/trabajadores', [TrabajadoresController::class, 'renderizarPagina']);
$router->post('/trabajadores/guardarAPI', [TrabajadoresController::class, 'guardarAPI']);
$router->get('/trabajadores/buscarAPI', [TrabajadoresController::class, 'buscarAPI']);
$router->post('/trabajadores/modificarAPI', [TrabajadoresController::class, 'modificarAPI']);
$router->get('/trabajadores/eliminar', [TrabajadoresController::class, 'EliminarAPI']);
$router->get('/trabajadores/usuariosDisponibles', [TrabajadoresController::class, 'usuariosDisponiblesAPI']);
$router->get('/trabajadores/todosUsuarios', [TrabajadoresController::class, 'todosUsuariosAPI']);
$router->get('/trabajadores/disponibles', [TrabajadoresController::class, 'trabajadoresDisponiblesAPI']);


// RUTAS DE ORDEN REPARACION 
// RUTAS DE ÓRDENES DE REPARACIÓN

$router->get('/ordenes_reparacion', [OrdenReparacionController::class, 'renderizarPagina']);
$router->post('/ordenes_reparacion/guardarAPI', [OrdenReparacionController::class, 'guardarAPI']);
$router->get('/ordenes_reparacion/buscarAPI', [OrdenReparacionController::class, 'buscarAPI']);
$router->post('/ordenes_reparacion/modificarAPI', [OrdenReparacionController::class, 'modificarAPI']);
$router->get('/ordenes_reparacion/eliminar', [OrdenReparacionController::class, 'EliminarAPI']);
$router->get('/ordenes_reparacion/clientesDisponiblesAPI', [OrdenReparacionController::class, 'clientesDisponiblesAPI']);
$router->get('/ordenes_reparacion/marcasDisponiblesAPI', [OrdenReparacionController::class, 'marcasDisponiblesAPI']);
$router->get('/ordenes_reparacion/trabajadoresDisponiblesAPI', [OrdenReparacionController::class, 'trabajadoresDisponiblesAPI']);

// RUTAS DE SERVICIOS ÓRDENES 

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



// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();
