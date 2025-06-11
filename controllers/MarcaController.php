<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use Model\Marcas;
use MVC\Router;

class MarcaController extends ActiveRecord{
    public static function renderizarPagina(Router $router){
        $router->render('marcas/index', []);
    }

    //Guardar Marcas
    public static function guardarAPI(){

        $_POST['nombre_marca'] = htmlspecialchars($_POST['nombre_marca']);
        $cantidad_nombre = strlen($_POST['nombre_marca']);

        if ($cantidad_nombre < 2){
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El nombre de la marca debe tener al menos 2 caracteres'
            ]);
            return;
        }

        $nombre_repetido = trim(strtoupper($_POST['nombre_marca']));
        $sql_verificar = "SELECT id_marca FROM marcas 
                         WHERE UPPER(TRIM(nombre_marca)) = " . self::$db->quote($nombre_repetido) . "
                         AND activo = 'T'";
        $nombre_existe = self::fetchFirst($sql_verificar);
        
        if ($nombre_existe) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Ya existe una marca con este nombre'
            ]);
            return;
        }

        $_POST['descripcion'] = htmlspecialchars($_POST['descripcion']);

        try {
            $data = new Marcas([
                'nombre_marca' => $_POST['nombre_marca'],
                'descripcion' => $_POST['descripcion'],
                'activo' => 'T',
                'usuario_creacion' => 1 
            ]);

            $crear = $data->crear();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'La marca ha sido registrada con éxito'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al guardar la marca',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    //Buscar Marcas
    public static function buscarAPI(){
        try {
            $sql = "SELECT m.id_marca, m.nombre_marca, m.descripcion, m.activo, 
                           m.fecha_creacion, m.usuario_creacion,
                           0 as modelos_registrados,
                           'Sistema' as usuario_creador
                    FROM marcas m 
                    WHERE m.activo = 'T'
                    ORDER BY m.nombre_marca";
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Marcas obtenidas correctamente',
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener las marcas',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    //Modificar Marcas
    public static function modificarAPI(){
        
        $id = $_POST['id_marca'];

        $_POST['nombre_marca'] = htmlspecialchars($_POST['nombre_marca']);
        $cantidad_nombre = strlen($_POST['nombre_marca']);

        if ($cantidad_nombre < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El nombre de la marca debe tener al menos 2 caracteres'
            ]);
            return;
        }

        $nombre_repetido = trim(strtoupper($_POST['nombre_marca']));
        $sql_verificar = "SELECT id_marca FROM marcas 
                         WHERE UPPER(TRIM(nombre_marca)) = " . self::$db->quote($nombre_repetido) . "
                         AND activo = 'T' 
                         AND id_marca != " . (int)$id;
        $nombre_existe = self::fetchFirst($sql_verificar);
        
        if ($nombre_existe) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Ya existe otra marca con este nombre'
            ]);
            return;
        }

        $_POST['descripcion'] = htmlspecialchars($_POST['descripcion']);

        try {
            $data = Marcas::find($id);
            $data->sincronizar([
                'nombre_marca' => $_POST['nombre_marca'],
                'descripcion' => $_POST['descripcion'],
                'activo' => 'T'
            ]);

            $data->actualizar();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'La información de la marca ha sido modificada con éxito'
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al modificar la marca',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    //Eliminar Marca
    public static function EliminarAPI(){
        try {
            $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

            $sql_verificar = "SELECT id_marca, nombre_marca FROM marcas WHERE id_marca = $id AND activo = 'T'";
            $marca = self::fetchFirst($sql_verificar);
            
            if (!$marca) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'La marca no existe o ya está inactiva'
                ]);
                return;
            }

            
            self::EliminarMarca($id, 'F');

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'La marca ha sido desactivada correctamente',
                'detalle' => "Marca '{$marca['nombre_marca']}' desactivada exitosamente"
            ]);
        
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al eliminar la marca',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function marcasDisponiblesAPI(){
        try {
            $sql = "SELECT id_marca, nombre_marca FROM marcas WHERE activo = 'T' ORDER BY nombre_marca";
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Marcas disponibles obtenidas correctamente',
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener las marcas disponibles',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function EliminarMarca($id, $situacion)
    {
        $sql = "UPDATE marcas SET activo = '$situacion' WHERE id_marca = $id";
        return self::SQL($sql);
    }

    public static function ModelosAsignadosMarca($id_marca)
    {
        $sql = "SELECT COUNT(*) as total FROM modelos WHERE id_marca = $id_marca AND activo = 'T'";
        $resultado = self::fetchFirst($sql);
        return $resultado['total'] ?? 0;
    }

    public static function OrdenesReparacionMarca($id_marca)
    {
        $sql = "SELECT COUNT(*) as total FROM ordenes_reparacion WHERE id_marca = $id_marca";
        $resultado = self::fetchFirst($sql);
        return $resultado['total'] ?? 0;
    }

    public static function ObtenerMarcasDisponibles()
    {
        $sql = "SELECT * FROM marcas WHERE activo = 'T' ORDER BY nombre_marca";
        return self::fetchArray($sql);
    }

    public static function ReactivarMarca($id)
    {
        return self::EliminarMarca($id, 'T');
    }
}