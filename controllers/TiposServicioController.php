<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use Model\TiposServicio;
use MVC\Router;

class TiposServicioController extends ActiveRecord{
    public static function renderizarPagina(Router $router) {
    verificarPermisos('tipos_servicio');
    $router->render('tipos_servicio/index', []);
}

    //Guardar Tipos de Servicio
    public static function guardarAPI(){
        // getHeadersApi(); // Descomenta si usas validación de headers

        $_POST['nombre_servicio'] = htmlspecialchars($_POST['nombre_servicio']);
        $cantidad_nombre = strlen($_POST['nombre_servicio']);

        if ($cantidad_nombre < 3){
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El nombre del servicio debe tener al menos 3 caracteres'
            ]);
            return;
        }

        // Verificar que el nombre no esté duplicado
        $nombre_repetido = trim(strtoupper($_POST['nombre_servicio']));
        $sql_verificar = "SELECT id_tipo_servicio FROM tipos_servicio 
                         WHERE UPPER(TRIM(nombre_servicio)) = " . self::$db->quote($nombre_repetido) . "
                         AND activo = 'T'";
        $nombre_existe = self::fetchFirst($sql_verificar);
        
        if ($nombre_existe) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Ya existe un servicio con este nombre'
            ]);
            return;
        }

        // Validar precio base
        $precio_validado = filter_var($_POST['precio_base'], FILTER_VALIDATE_FLOAT);
        if ($precio_validado === false || $precio_validado <= 0){
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El precio base debe ser mayor a cero y ser un número válido'
            ]);
            return;
        }

        // Validar tiempo estimado
        $tiempo_validado = filter_var($_POST['tiempo_estimado_horas'], FILTER_VALIDATE_INT);
        if ($tiempo_validado === false || $tiempo_validado <= 0){
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El tiempo estimado debe ser mayor a cero y ser un número válido'
            ]);
            return;
        }

        $_POST['descripcion'] = htmlspecialchars($_POST['descripcion']);

        try {
            $data = new TiposServicio([
                'nombre_servicio' => $_POST['nombre_servicio'],
                'descripcion' => $_POST['descripcion'],
                'precio_base' => $precio_validado,
                'tiempo_estimado_horas' => $tiempo_validado,
                'activo' => 'T'
            ]);

            $crear = $data->crear();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'El tipo de servicio ha sido registrado con éxito'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al guardar el tipo de servicio',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    //Buscar Tipos de Servicio
    public static function buscarAPI(){
        try {
            $sql = "SELECT id_tipo_servicio, nombre_servicio, descripcion, precio_base, 
                           tiempo_estimado_horas, activo, fecha_creacion 
                    FROM tipos_servicio 
                    WHERE activo = 'T' 
                    ORDER BY nombre_servicio";
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Tipos de servicio obtenidos correctamente',
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener los tipos de servicio',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    //Modificar Tipos de Servicio
    public static function modificarAPI(){
        // getHeadersApi(); // Descomenta si usas validación de headers

        $id = $_POST['id_tipo_servicio'];

        $_POST['nombre_servicio'] = htmlspecialchars($_POST['nombre_servicio']);
        $cantidad_nombre = strlen($_POST['nombre_servicio']);

        if ($cantidad_nombre < 3) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El nombre del servicio debe tener al menos 3 caracteres'
            ]);
            return;
        }

        // Verificar que el nombre no esté duplicado (excluyendo el actual)
        $nombre_repetido = trim(strtoupper($_POST['nombre_servicio']));
        $sql_verificar = "SELECT id_tipo_servicio FROM tipos_servicio 
                         WHERE UPPER(TRIM(nombre_servicio)) = " . self::$db->quote($nombre_repetido) . "
                         AND activo = 'T' 
                         AND id_tipo_servicio != " . (int)$id;
        $nombre_existe = self::fetchFirst($sql_verificar);
        
        if ($nombre_existe) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Ya existe otro servicio con este nombre'
            ]);
            return;
        }

        // Validar precio base
        $precio_validado = filter_var($_POST['precio_base'], FILTER_VALIDATE_FLOAT);
        if ($precio_validado === false || $precio_validado <= 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El precio base debe ser mayor a cero y ser un número válido'
            ]);
            return;
        }

        // Validar tiempo estimado
        $tiempo_validado = filter_var($_POST['tiempo_estimado_horas'], FILTER_VALIDATE_INT);
        if ($tiempo_validado === false || $tiempo_validado <= 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El tiempo estimado debe ser mayor a cero y ser un número válido'
            ]);
            return;
        }

        $_POST['descripcion'] = htmlspecialchars($_POST['descripcion']);

        try {
            $data = TiposServicio::find($id);
            $data->sincronizar([
                'nombre_servicio' => $_POST['nombre_servicio'],
                'descripcion' => $_POST['descripcion'],
                'precio_base' => $precio_validado,
                'tiempo_estimado_horas' => $tiempo_validado,
                'activo' => 'T'
            ]);

            $data->actualizar();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'La información del tipo de servicio ha sido modificada con éxito'
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al modificar el tipo de servicio',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    //Eliminar Tipo de Servicio
    public static function EliminarAPI(){
        try {
            $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

            // Verificar si el servicio está siendo usado (puedes agregar validaciones adicionales)
            $sql_verificar = "SELECT id_tipo_servicio, nombre_servicio FROM tipos_servicio 
                             WHERE id_tipo_servicio = $id AND activo = 'T'";
            $servicio = self::fetchFirst($sql_verificar);
            
            if (!$servicio) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El tipo de servicio no existe o ya está inactivo'
                ]);
                return;
            }

            self::EliminarTipoServicio($id, 'F');

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'El tipo de servicio ha sido desactivado correctamente',
                'detalle' => "Servicio '{$servicio['nombre_servicio']}' desactivado exitosamente"
            ]);
        
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al eliminar el tipo de servicio',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // Obtener servicios disponibles para otros módulos
    public static function serviciosDisponiblesAPI(){
        try {
            $data = self::ObtenerServiciosDisponibles();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Servicios disponibles obtenidos correctamente',
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener los servicios disponibles',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // Métodos auxiliares
    public static function EliminarTipoServicio($id, $situacion)
    {
        $sql = "UPDATE tipos_servicio SET activo = '$situacion' WHERE id_tipo_servicio = $id";
        return self::SQL($sql);
    }

    public static function ObtenerServiciosDisponibles()
    {
        $sql = "SELECT * FROM tipos_servicio WHERE activo = 'T' ORDER BY nombre_servicio";
        return self::fetchArray($sql);
    }

    public static function ReactivarTipoServicio($id)
    {
        return self::EliminarTipoServicio($id, 'T');
    }

    public static function ObtenerPrecioServicio($id)
    {
        $sql = "SELECT precio_base FROM tipos_servicio WHERE id_tipo_servicio = $id AND activo = 'T'";
        $resultado = self::fetchFirst($sql);
        return $resultado['precio_base'] ?? 0;
    }

    public static function ObtenerTiempoEstimado($id)
    {
        $sql = "SELECT tiempo_estimado_horas FROM tipos_servicio WHERE id_tipo_servicio = $id AND activo = 'T'";
        $resultado = self::fetchFirst($sql);
        return $resultado['tiempo_estimado_horas'] ?? 0;
    }
}