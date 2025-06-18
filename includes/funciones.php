<?php

function debuguear($variable) {
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
    exit;
}

// Escapa / Sanitizar el HTML
function s($html) {
    $s = htmlspecialchars($html);
    return $s;
}

// Función que revisa que el usuario este autenticado
function isAuth() {
    session_start();
    if(!isset($_SESSION['login'])) {
        header('Location: /');
    }
}
function isAuthApi() {
    getHeadersApi();
    session_start();
    if(!isset($_SESSION['auth_user'])) {
        echo json_encode([    
            "mensaje" => "No esta autenticado",

            "codigo" => 4,
        ]);
        exit;
    }
}

function isNotAuth(){
    session_start();
    if(isset($_SESSION['auth'])) {
        header('Location: /auth/');
    }
}


function hasPermission(array $permisos){

    $comprobaciones = [];
    foreach ($permisos as $permiso) {

        $comprobaciones[] = !isset($_SESSION[$permiso]) ? false : true;
      
    }

    if(array_search(true, $comprobaciones) !== false){}else{
        header('Location: /');
    }
}

function hasPermissionApi(array $permisos){
    getHeadersApi();
    $comprobaciones = [];
    foreach ($permisos as $permiso) {

        $comprobaciones[] = !isset($_SESSION[$permiso]) ? false : true;
      
    }

    if(array_search(true, $comprobaciones) !== false){}else{
        echo json_encode([     
            "mensaje" => "No tiene permisos",

            "codigo" => 4,
        ]);
        exit;
    }
}

function getHeadersApi(){
    return header("Content-type:application/json; charset=utf-8");
}

function asset($ruta){
    return "/". $_ENV['APP_NAME']."/public/" . $ruta;
}

function verificarAutenticacion() {
    session_start();
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: /proyecto_pmlx/login');
        exit;
    }
}

function verificarLogin() {
    session_start();
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: /proyecto_pmlx/login');
        exit;
    }
}

function obtenerRolUsuario() {
    session_start();
    
    if (!isset($_SESSION['usuario_id'])) {
        return null;
    }
    
    // Obtener el rol del usuario desde la base de datos
    $sql = "SELECT r.nombre_rol 
            FROM usuarios u 
            INNER JOIN roles r ON u.id_rol = r.id_rol 
            WHERE u.id_usuario = " . $_SESSION['usuario_id'];
    
    $resultado = \Model\ActiveRecord::fetchArray($sql);
    
    if ($resultado && count($resultado) > 0) {
        return $resultado[0]['nombre_rol'];
    }
    
    return null;
}

function verificarPermisos($modulo) {
    verificarLogin(); 
    
    $rol = obtenerRolUsuario();
  
    $permisos = [
        'Administrador' => [
            'usuarios', 'roles', 'marcas', 'modelos', 'clientes', 
            'inventario', 'ventas', 'ordenes_reparacion', 'servicios_orden', 
            'trabajadores', 'tipos_servicio', 'estadisticas', 
            'detalle_venta_productos', 'detalle_venta_servicios', 
            'movimientos_inventario'
        ],
        'Empleado' => [
            'marcas', 'modelos', 'clientes', 'ventas', 'ordenes_reparacion', 
            'servicios_orden', 'trabajadores', 'tipos_servicio', 
            'detalle_venta_productos'
            
         
        ],
        'Técnico' => [
            'tipos_servicio', 'ordenes_reparacion', 'servicios_orden'
           
        ]
    ];
    
 
    if (!isset($permisos[$rol]) || !in_array($modulo, $permisos[$rol])) {
        header('Location: /proyecto_pmlx/sin-permisos');
        exit;
    }
    
    return true;
}