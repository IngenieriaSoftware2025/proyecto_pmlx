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

// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();
