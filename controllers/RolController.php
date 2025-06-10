<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use Model\Roles;
use MVC\Router;

class RolController extends ActiveRecord{
    public static function renderizarPagina(Router $router){
        $router->render('roles/index', []);
    }

    //Guardar Roles
    public static function guardarAPI(){
        getHeadersApi();

        $_POST['nombre_rol'] = htmlspecialchars($_POST['nombre_rol']);
        $cantidad_nombre = strlen($_POST['nombre_rol']);

        if ($cantidad_nombre < 3){
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El nombre del rol debe tener al menos 3 caracteres'
            ]);
            return;
        }

        $nombre_repetido = trim(strtolower($_POST['nombre_rol']));
        $sql_verificar = "SELECT id_rol FROM roles 
                         WHERE LOWER(TRIM(nombre_rol)) = " . self::$db->quote($nombre_repetido);
        $nombre_existe = self::fetchFirst($sql_verificar);
        
        if ($nombre_existe) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Ya existe un rol con este nombre'
            ]);
            return;
        }

        $_POST['descripcion'] = htmlspecialchars($_POST['descripcion']);
        $cantidad_descripcion = strlen($_POST['descripcion']);

        if ($cantidad_descripcion < 5){
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La descripción debe tener al menos 5 caracteres'
            ]);
            return;
        }

        try {
            $data = new Roles([
                'nombre_rol' => $_POST['nombre_rol'],
                'descripcion' => $_POST['descripcion']
            ]);

            $crear = $data->crear();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'El rol ha sido registrado con éxito'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al guardar el rol',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    //Buscar Roles
    public static function buscarAPI(){
        try {
            $sql = "SELECT r.id_rol, r.nombre_rol, r.descripcion, r.fecha_creacion,
                           (SELECT COUNT(*) FROM usuarios u WHERE u.id_rol = r.id_rol AND u.activo = 'T') as usuarios_asignados
                    FROM roles r 
                    ORDER BY r.nombre_rol";
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Roles obtenidos correctamente',
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener los roles',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    //Modificar Roles
    public static function modificarAPI(){
        getHeadersApi();

        $id = $_POST['id_rol'];

        $_POST['nombre_rol'] = htmlspecialchars($_POST['nombre_rol']);
        $cantidad_nombre = strlen($_POST['nombre_rol']);

        if ($cantidad_nombre < 3) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El nombre del rol debe tener al menos 3 caracteres'
            ]);
            return;
        }

        $nombre_repetido = trim(strtolower($_POST['nombre_rol']));
        $sql_verificar = "SELECT id_rol FROM roles 
                         WHERE LOWER(TRIM(nombre_rol)) = " . self::$db->quote($nombre_repetido) . "
                         AND id_rol != " . (int)$id;
        $nombre_existe = self::fetchFirst($sql_verificar);
        
        if ($nombre_existe) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Ya existe otro rol con este nombre'
            ]);
            return;
        }

        $_POST['descripcion'] = htmlspecialchars($_POST['descripcion']);
        $cantidad_descripcion = strlen($_POST['descripcion']);

        if ($cantidad_descripcion < 5) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La descripción debe tener al menos 5 caracteres'
            ]);
            return;
        }

        try {
            $data = Roles::find($id);
            $data->sincronizar([
                'nombre_rol' => $_POST['nombre_rol'],
                'descripcion' => $_POST['descripcion']
            ]);

            $data->actualizar();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'La información del rol ha sido modificada con éxito'
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al modificar el rol',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    //Eliminar Rol
    public static function EliminarAPI(){
        try {
            $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

            $sql_verificar = "SELECT id_rol, nombre_rol FROM roles WHERE id_rol = $id";
            $rol = self::fetchFirst($sql_verificar);
            
            if (!$rol) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El rol no existe'
                ]);
                return;
            }

            // Verificar si hay usuarios asignados a este rol
            $usuarios_asignados = self::UsuariosAsignadosRol($id);
            
            if ($usuarios_asignados > 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se puede eliminar el rol porque tiene usuarios asignados',
                    'detalle' => "Hay $usuarios_asignados usuario(s) asignado(s) a este rol. Debe reasignar los usuarios antes de eliminar el rol."
                ]);
                return;
            }

            // Verificar si es un rol del sistema (Administrador, Empleado, Técnico)
            $roles_sistema = ['Administrador', 'Empleado', 'Técnico'];
            if (in_array($rol['nombre_rol'], $roles_sistema)) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se puede eliminar un rol del sistema',
                    'detalle' => "El rol '{$rol['nombre_rol']}' es un rol del sistema y no puede ser eliminado."
                ]);
                return;
            }

            self::EliminarRol($id);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'El rol ha sido eliminado correctamente',
                'detalle' => "Rol '{$rol['nombre_rol']}' eliminado exitosamente"
            ]);
        
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al eliminar el rol',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function EliminarRol($id)
    {
        $sql = "DELETE FROM roles WHERE id_rol = $id";
        return self::SQL($sql);
    }

    public static function UsuariosAsignadosRol($id_rol)
    {
        $sql = "SELECT COUNT(*) as total FROM usuarios WHERE id_rol = $id_rol AND activo = 'T'";
        $resultado = self::fetchFirst($sql);
        return $resultado['total'] ?? 0;
    }

    public static function ObtenerRolesDisponibles()
    {
        $sql = "SELECT * FROM roles ORDER BY nombre_rol";
        return self::fetchArray($sql);
    }
}