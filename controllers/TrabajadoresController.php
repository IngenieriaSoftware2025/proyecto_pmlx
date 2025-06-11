<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use Model\Trabajadores;
use MVC\Router;

class TrabajadoresController extends ActiveRecord{
    public static function renderizarPagina(Router $router){
        $router->render('trabajadores/index', []);
    }

    // Obtener usuarios disponibles (que no son trabajadores) - CORREGIDO
    public static function usuariosDisponiblesAPI(){
        try {
            // Consulta corregida - verificar si la tabla trabajadores tiene datos
            $sql = "SELECT u.id_usuario, u.nombre_completo, u.email, u.telefono
                    FROM usuarios u 
                    WHERE u.activo = 'T' 
                    AND u.id_usuario NOT IN (
                        SELECT COALESCE(t.id_usuario, 0) 
                        FROM trabajadores t 
                        WHERE t.activo = 'T'
                    )
                    ORDER BY u.nombre_completo";
            
            error_log("DEBUG - SQL usuarios disponibles: " . $sql);
            $data = self::fetchArray($sql);
            error_log("DEBUG - Usuarios disponibles encontrados: " . count($data));
            error_log("DEBUG - Datos: " . print_r($data, true));

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Usuarios disponibles obtenidos correctamente',
                'data' => $data,
                'debug' => [
                    'sql' => $sql,
                    'total_encontrados' => count($data)
                ]
            ]);
        } catch (Exception $e) {
            error_log("ERROR en usuariosDisponiblesAPI: " . $e->getMessage());
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener usuarios disponibles',
                'detalle' => $e->getMessage(),
                'data' => []
            ]);
        }
    }

    // Obtener todos los usuarios (para modificación) - CORREGIDO
    public static function todosUsuariosAPI(){
        try {
            $sql = "SELECT id_usuario, nombre_completo, email, telefono
                    FROM usuarios 
                    WHERE activo = 'T'
                    ORDER BY nombre_completo";
            
            error_log("DEBUG - SQL todos usuarios: " . $sql);
            $data = self::fetchArray($sql);
            error_log("DEBUG - Todos usuarios encontrados: " . count($data));

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Usuarios obtenidos correctamente',
                'data' => $data,
                'debug' => [
                    'sql' => $sql,
                    'total_encontrados' => count($data)
                ]
            ]);
        } catch (Exception $e) {
            error_log("ERROR en todosUsuariosAPI: " . $e->getMessage());
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener usuarios',
                'detalle' => $e->getMessage(),
                'data' => []
            ]);
        }
    }

    //Guardar Trabajadores
    public static function guardarAPI(){
        // getHeadersApi(); // Descomenta si usas validación de headers

        // Validar usuario seleccionado
        $usuario_validado = filter_var($_POST['id_usuario'], FILTER_VALIDATE_INT);
        if ($usuario_validado === false || $usuario_validado <= 0){
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Debe seleccionar un usuario válido'
            ]);
            return;
        }

        // Verificar que el usuario existe y está activo
        $sql_verificar_usuario = "SELECT id_usuario FROM usuarios WHERE id_usuario = $usuario_validado AND activo = 'T'";
        $usuario_existe = self::fetchFirst($sql_verificar_usuario);
        
        if (!$usuario_existe) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El usuario seleccionado no existe o está inactivo'
            ]);
            return;
        }

        // Verificar que el usuario no esté ya registrado como trabajador
        $sql_verificar_trabajador = "SELECT id_trabajador FROM trabajadores 
                                    WHERE id_usuario = $usuario_validado AND activo = 'T'";
        $trabajador_existe = self::fetchFirst($sql_verificar_trabajador);
        
        if ($trabajador_existe) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Este usuario ya está registrado como trabajador'
            ]);
            return;
        }

        // Validar especialidad
        $_POST['especialidad'] = htmlspecialchars($_POST['especialidad']);
        $cantidad_especialidad = strlen($_POST['especialidad']);

        if ($cantidad_especialidad < 3){
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La especialidad debe tener al menos 3 caracteres'
            ]);
            return;
        }

        try {
            $data = new Trabajadores([
                'id_usuario' => $usuario_validado,
                'especialidad' => $_POST['especialidad'],
                'activo' => 'T'
            ]);

            $crear = $data->crear();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'El trabajador ha sido registrado con éxito'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al guardar el trabajador',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    //Buscar Trabajadores
    public static function buscarAPI(){
        try {
            $sql = "SELECT t.id_trabajador, t.id_usuario, t.especialidad, t.activo, 
                           t.fecha_registro, u.nombre_completo, u.email, u.telefono
                    FROM trabajadores t 
                    INNER JOIN usuarios u ON t.id_usuario = u.id_usuario
                    WHERE t.activo = 'T' AND u.activo = 'T'
                    ORDER BY u.nombre_completo";
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Trabajadores obtenidos correctamente',
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener los trabajadores',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    //Modificar Trabajadores
    public static function modificarAPI(){
        // getHeadersApi(); // Descomenta si usas validación de headers

        $id = $_POST['id_trabajador'];

        // Validar usuario seleccionado
        $usuario_validado = filter_var($_POST['id_usuario'], FILTER_VALIDATE_INT);
        if ($usuario_validado === false || $usuario_validado <= 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Debe seleccionar un usuario válido'
            ]);
            return;
        }

        // Verificar que el usuario existe y está activo
        $sql_verificar_usuario = "SELECT id_usuario FROM usuarios WHERE id_usuario = $usuario_validado AND activo = 'T'";
        $usuario_existe = self::fetchFirst($sql_verificar_usuario);
        
        if (!$usuario_existe) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El usuario seleccionado no existe o está inactivo'
            ]);
            return;
        }

        // Verificar que el usuario no esté ya registrado como trabajador (excluyendo el actual)
        $sql_verificar_trabajador = "SELECT id_trabajador FROM trabajadores 
                                    WHERE id_usuario = $usuario_validado AND activo = 'T' 
                                    AND id_trabajador != " . (int)$id;
        $trabajador_existe = self::fetchFirst($sql_verificar_trabajador);
        
        if ($trabajador_existe) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Este usuario ya está registrado como otro trabajador'
            ]);
            return;
        }

        // Validar especialidad
        $_POST['especialidad'] = htmlspecialchars($_POST['especialidad']);
        $cantidad_especialidad = strlen($_POST['especialidad']);

        if ($cantidad_especialidad < 3) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La especialidad debe tener al menos 3 caracteres'
            ]);
            return;
        }

        try {
            $data = Trabajadores::find($id);
            $data->sincronizar([
                'id_usuario' => $usuario_validado,
                'especialidad' => $_POST['especialidad'],
                'activo' => 'T'
            ]);

            $data->actualizar();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'La información del trabajador ha sido modificada con éxito'
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al modificar el trabajador',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    //Eliminar Trabajador
    public static function EliminarAPI(){
        try {
            $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

            // Verificar que el trabajador existe
            $sql_verificar = "SELECT t.id_trabajador, u.nombre_completo, t.especialidad
                             FROM trabajadores t 
                             INNER JOIN usuarios u ON t.id_usuario = u.id_usuario
                             WHERE t.id_trabajador = $id AND t.activo = 'T'";
            $trabajador = self::fetchFirst($sql_verificar);
            
            if (!$trabajador) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El trabajador no existe o ya está inactivo'
                ]);
                return;
            }

            self::EliminarTrabajador($id, 'F');

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'El trabajador ha sido desactivado correctamente',
                'detalle' => "Trabajador '{$trabajador['nombre_completo']}' ({$trabajador['especialidad']}) desactivado exitosamente"
            ]);
        
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al eliminar el trabajador',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // Obtener trabajadores disponibles para otros módulos
    public static function trabajadoresDisponiblesAPI(){
        try {
            $data = self::ObtenerTrabajadoresDisponibles();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Trabajadores disponibles obtenidos correctamente',
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener trabajadores disponibles',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // Métodos auxiliares
    public static function EliminarTrabajador($id, $situacion)
    {
        $sql = "UPDATE trabajadores SET activo = '$situacion' WHERE id_trabajador = $id";
        return self::SQL($sql);
    }

    public static function ObtenerTrabajadoresDisponibles()
    {
        $sql = "SELECT t.id_trabajador, t.especialidad, u.nombre_completo, u.email, u.telefono
                FROM trabajadores t 
                INNER JOIN usuarios u ON t.id_usuario = u.id_usuario
                WHERE t.activo = 'T' AND u.activo = 'T'
                ORDER BY u.nombre_completo";
        return self::fetchArray($sql);
    }

    public static function ReactivarTrabajador($id)
    {
        return self::EliminarTrabajador($id, 'T');
    }

    public static function ObtenerTrabajadorPorUsuario($id_usuario)
    {
        $sql = "SELECT t.*, u.nombre_completo, u.email, u.telefono
                FROM trabajadores t 
                INNER JOIN usuarios u ON t.id_usuario = u.id_usuario
                WHERE t.id_usuario = $id_usuario AND t.activo = 'T'";
        return self::fetchFirst($sql);
    }

    public static function ValidarDisponibilidadTrabajador($id_trabajador, $fecha_hora)
    {
        // Método para validar si un trabajador está disponible en una fecha/hora específica
        return true;
    }
}