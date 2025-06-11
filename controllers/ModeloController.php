<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use Model\Modelos;
use MVC\Router;

class ModeloController extends ActiveRecord{
    public static function renderizarPagina(Router $router){
        $router->render('modelos/index', []);
    }

    //Guardar Modelos
    public static function guardarAPI(){
        // getHeadersApi(); // Comentado temporalmente

        $_POST['nombre_modelo'] = htmlspecialchars($_POST['nombre_modelo']);
        $cantidad_nombre = strlen($_POST['nombre_modelo']);

        if ($cantidad_nombre < 2){
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El nombre del modelo debe tener al menos 2 caracteres'
            ]);
            return;
        }

        $marca_validada = filter_var($_POST['id_marca'], FILTER_VALIDATE_INT);
        if ($marca_validada === false || $marca_validada <= 0){
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Debe seleccionar una marca válida'
            ]);
            return;
        }

        // Verificar que la marca existe
        $sql_verificar_marca = "SELECT id_marca FROM marcas WHERE id_marca = $marca_validada AND activo = 'T'";
        $marca_existe = self::fetchFirst($sql_verificar_marca);
        
        if (!$marca_existe) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La marca seleccionada no existe o está inactiva'
            ]);
            return;
        }

        // Verificar que no exista el mismo modelo para la misma marca
        $nombre_repetido = trim(strtoupper($_POST['nombre_modelo']));
        $sql_verificar = "SELECT id_modelo FROM modelos 
                         WHERE UPPER(TRIM(nombre_modelo)) = " . self::$db->quote($nombre_repetido) . "
                         AND id_marca = $marca_validada
                         AND activo = 'T'";
        $nombre_existe = self::fetchFirst($sql_verificar);
        
        if ($nombre_existe) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Ya existe un modelo con este nombre para la marca seleccionada'
            ]);
            return;
        }

        $_POST['especificaciones'] = htmlspecialchars($_POST['especificaciones']);

        // Validar precio de referencia si se proporciona
        if (!empty($_POST['precio_referencia'])) {
            $precio_validado = filter_var($_POST['precio_referencia'], FILTER_VALIDATE_FLOAT);
            if ($precio_validado === false || $precio_validado < 0){
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El precio de referencia debe ser un número válido y no negativo'
                ]);
                return;
            }
            $_POST['precio_referencia'] = $precio_validado;
        } else {
            $_POST['precio_referencia'] = 0;
        }

        try {
            $data = new Modelos([
                'id_marca' => $marca_validada,
                'nombre_modelo' => $_POST['nombre_modelo'],
                'especificaciones' => $_POST['especificaciones'],
                'precio_referencia' => $_POST['precio_referencia'],
                'activo' => 'T'
            ]);

            $crear = $data->crear();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'El modelo ha sido registrado con éxito'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al guardar el modelo',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    //Buscar Modelos
    public static function buscarAPI(){
        try {
            $sql = "SELECT m.id_modelo, m.nombre_modelo, m.especificaciones, 
                           m.precio_referencia, m.activo, m.fecha_creacion,
                           ma.nombre_marca, ma.id_marca,
                           (SELECT COUNT(*) FROM inventario i WHERE i.id_modelo = m.id_modelo AND i.disponible = 'T') as inventario_disponible
                    FROM modelos m 
                    INNER JOIN marcas ma ON m.id_marca = ma.id_marca
                    WHERE m.activo = 'T'
                    ORDER BY ma.nombre_marca, m.nombre_modelo";
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Modelos obtenidos correctamente',
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener los modelos',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    //Modificar Modelos
    public static function modificarAPI(){
        // getHeadersApi(); // Comentado temporalmente

        $id = $_POST['id_modelo'];

        $_POST['nombre_modelo'] = htmlspecialchars($_POST['nombre_modelo']);
        $cantidad_nombre = strlen($_POST['nombre_modelo']);

        if ($cantidad_nombre < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El nombre del modelo debe tener al menos 2 caracteres'
            ]);
            return;
        }

        $marca_validada = filter_var($_POST['id_marca'], FILTER_VALIDATE_INT);
        if ($marca_validada === false || $marca_validada <= 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Debe seleccionar una marca válida'
            ]);
            return;
        }

        // Verificar que la marca existe
        $sql_verificar_marca = "SELECT id_marca FROM marcas WHERE id_marca = $marca_validada AND activo = 'T'";
        $marca_existe = self::fetchFirst($sql_verificar_marca);
        
        if (!$marca_existe) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La marca seleccionada no existe o está inactiva'
            ]);
            return;
        }

        // Verificar que no exista el mismo modelo para la misma marca (excluyendo el actual)
        $nombre_repetido = trim(strtoupper($_POST['nombre_modelo']));
        $sql_verificar = "SELECT id_modelo FROM modelos 
                         WHERE UPPER(TRIM(nombre_modelo)) = " . self::$db->quote($nombre_repetido) . "
                         AND id_marca = $marca_validada
                         AND activo = 'T' 
                         AND id_modelo != " . (int)$id;
        $nombre_existe = self::fetchFirst($sql_verificar);
        
        if ($nombre_existe) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Ya existe otro modelo con este nombre para la marca seleccionada'
            ]);
            return;
        }

        $_POST['especificaciones'] = htmlspecialchars($_POST['especificaciones']);

        // Validar precio de referencia si se proporciona
        if (!empty($_POST['precio_referencia'])) {
            $precio_validado = filter_var($_POST['precio_referencia'], FILTER_VALIDATE_FLOAT);
            if ($precio_validado === false || $precio_validado < 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El precio de referencia debe ser un número válido y no negativo'
                ]);
                return;
            }
            $_POST['precio_referencia'] = $precio_validado;
        } else {
            $_POST['precio_referencia'] = 0;
        }

        try {
            $data = Modelos::find($id);
            $data->sincronizar([
                'id_marca' => $marca_validada,
                'nombre_modelo' => $_POST['nombre_modelo'],
                'especificaciones' => $_POST['especificaciones'],
                'precio_referencia' => $_POST['precio_referencia'],
                'activo' => 'T'
            ]);

            $data->actualizar();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'La información del modelo ha sido modificada con éxito'
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al modificar el modelo',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    //Eliminar Modelo
    public static function EliminarAPI(){
        try {
            $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

            $sql_verificar = "SELECT m.id_modelo, m.nombre_modelo, ma.nombre_marca 
                             FROM modelos m 
                             INNER JOIN marcas ma ON m.id_marca = ma.id_marca
                             WHERE m.id_modelo = $id AND m.activo = 'T'";
            $modelo = self::fetchFirst($sql_verificar);
            
            if (!$modelo) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El modelo no existe o ya está inactivo'
                ]);
                return;
            }

            // Verificar si hay inventario con este modelo (temporalmente comentado)
            /*
            $inventario_asignado = self::InventarioAsignadoModelo($id);
            
            if ($inventario_asignado > 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se puede eliminar el modelo porque tiene inventario registrado',
                    'detalle' => "Hay $inventario_asignado producto(s) en inventario con este modelo."
                ]);
                return;
            }
            */

            self::EliminarModelo($id, 'F');

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'El modelo ha sido desactivado correctamente',
                'detalle' => "Modelo '{$modelo['nombre_modelo']}' de {$modelo['nombre_marca']} desactivado exitosamente"
            ]);
        
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al eliminar el modelo',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function modelosPorMarcaAPI(){
        try {
            $id_marca = filter_var($_GET['id_marca'], FILTER_SANITIZE_NUMBER_INT);
            
            $sql = "SELECT id_modelo, nombre_modelo, precio_referencia 
                    FROM modelos 
                    WHERE id_marca = $id_marca AND activo = 'T' 
                    ORDER BY nombre_modelo";
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Modelos obtenidos correctamente',
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener los modelos',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function EliminarModelo($id, $situacion)
    {
        $sql = "UPDATE modelos SET activo = '$situacion' WHERE id_modelo = $id";
        return self::SQL($sql);
    }

    public static function InventarioAsignadoModelo($id_modelo)
    {
        $sql = "SELECT COUNT(*) as total FROM inventario WHERE id_modelo = $id_modelo AND disponible = 'T'";
        $resultado = self::fetchFirst($sql);
        return $resultado['total'] ?? 0;
    }

    public static function ObtenerModelosPorMarca($id_marca)
    {
        $sql = "SELECT * FROM modelos WHERE id_marca = $id_marca AND activo = 'T' ORDER BY nombre_modelo";
        return self::fetchArray($sql);
    }

    public static function ObtenerModelosDisponibles()
    {
        $sql = "SELECT m.*, ma.nombre_marca 
                FROM modelos m 
                INNER JOIN marcas ma ON m.id_marca = ma.id_marca
                WHERE m.activo = 'T' 
                ORDER BY ma.nombre_marca, m.nombre_modelo";
        return self::fetchArray($sql);
    }

    public static function ReactivarModelo($id)
    {
        return self::EliminarModelo($id, 'T');
    }
}