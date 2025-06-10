<?php 
require_once __DIR__ . '/../includes/app.php';


use MVC\Router;
use Controllers\AppController;
use Controllers\MarcaController;
use Controllers\RolController;
use Controllers\UsuarioController;

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
$router->get('/marcas/eliminarAPI', [MarcaController::class, 'eliminarAPI']);
$router->get('/marcas/EliminarMarca', [MarcaController::class, 'EliminarMarca']);


$router->get('/usuarios/rolesAPI', [UsuarioController::class, 'rolesAPI']);


// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();
