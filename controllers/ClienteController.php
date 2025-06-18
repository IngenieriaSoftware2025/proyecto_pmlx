<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use Model\Clientes;
use MVC\Router;

class ClienteController extends ActiveRecord{
    public static function renderizarPagina(Router $router) {
    verificarPermisos('clientes');
    $router->render('clientes/index', []);
}

    //Guardar Clientes
    public static function guardarAPI(){
        // getHeadersApi(); // Comentado temporalmente

        $_POST['nombre'] = htmlspecialchars($_POST['nombre']);
        $cantidad_nombre = strlen($_POST['nombre']);

        if ($cantidad_nombre < 3){
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El nombre debe tener al menos 3 caracteres'
            ]);
            return;
        }

        // Validar email si se proporciona
        if (!empty($_POST['email'])) {
            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El formato del email no es válido'
                ]);
                return;
            }

            // Verificar que el email no esté duplicado
            $email_repetido = trim(strtolower($_POST['email']));
            $sql_verificar_email = "SELECT id_cliente FROM clientes 
                                   WHERE LOWER(TRIM(email)) = " . self::$db->quote($email_repetido) . "
                                   AND activo = 'T'";
            $email_existe = self::fetchFirst($sql_verificar_email);
            
            if ($email_existe) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Ya existe un cliente con este email'
                ]);
                return;
            }
        }

        // Validar NIT si se proporciona
        if (!empty($_POST['nit'])) {
            $_POST['nit'] = htmlspecialchars($_POST['nit']);
            
            // Verificar que el NIT no esté duplicado
            $nit_repetido = trim($_POST['nit']);
            $sql_verificar_nit = "SELECT id_cliente FROM clientes 
                                 WHERE TRIM(nit) = " . self::$db->quote($nit_repetido) . "
                                 AND activo = 'T'";
            $nit_existe = self::fetchFirst($sql_verificar_nit);
            
            if ($nit_existe) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Ya existe un cliente con este NIT'
                ]);
                return;
            }
        }

        // Validar que al menos tenga un teléfono o celular
        if (empty($_POST['telefono']) && empty($_POST['celular'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Debe proporcionar al menos un número telefónico (teléfono o celular)'
            ]);
            return;
        }

        $_POST['direccion'] = htmlspecialchars($_POST['direccion']);
        $_POST['telefono'] = htmlspecialchars($_POST['telefono']);
        $_POST['celular'] = htmlspecialchars($_POST['celular']);

        try {
            $data = new Clientes([
                'nombre' => $_POST['nombre'],
                'nit' => $_POST['nit'],
                'telefono' => $_POST['telefono'],
                'celular' => $_POST['celular'],
                'email' => $_POST['email'],
                'direccion' => $_POST['direccion'],
                'activo' => 'T',
                'usuario_registro' => 1 // Por ahora usamos el usuario admin, después se puede cambiar por sesión
            ]);

            $crear = $data->crear();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'El cliente ha sido registrado con éxito'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al guardar el cliente',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    //Buscar Clientes
    public static function buscarAPI(){
        try {
            $sql = "SELECT c.id_cliente, c.nombre, c.nit, c.telefono, c.celular, 
                           c.email, c.direccion, c.activo, c.fecha_registro,
                           u.nombre_completo as usuario_registro_nombre,
                           (SELECT COUNT(*) FROM ventas v WHERE v.id_cliente = c.id_cliente) as total_compras
                    FROM clientes c 
                    LEFT JOIN usuarios u ON c.usuario_registro = u.id_usuario
                    WHERE c.activo = 'T'
                    ORDER BY c.nombre";
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Clientes obtenidos correctamente',
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener los clientes',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    //Modificar Clientes
    public static function modificarAPI(){
        // getHeadersApi(); // Comentado temporalmente

        $id = $_POST['id_cliente'];

        $_POST['nombre'] = htmlspecialchars($_POST['nombre']);
        $cantidad_nombre = strlen($_POST['nombre']);

        if ($cantidad_nombre < 3) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El nombre debe tener al menos 3 caracteres'
            ]);
            return;
        }

        // Validar email si se proporciona
        if (!empty($_POST['email'])) {
            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El formato del email no es válido'
                ]);
                return;
            }

            // Verificar que el email no esté duplicado (excluyendo el actual)
            $email_repetido = trim(strtolower($_POST['email']));
            $sql_verificar_email = "SELECT id_cliente FROM clientes 
                                   WHERE LOWER(TRIM(email)) = " . self::$db->quote($email_repetido) . "
                                   AND activo = 'T' 
                                   AND id_cliente != " . (int)$id;
            $email_existe = self::fetchFirst($sql_verificar_email);
            
            if ($email_existe) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Ya existe otro cliente con este email'
                ]);
                return;
            }
        }

        // Validar NIT si se proporciona
        if (!empty($_POST['nit'])) {
            $_POST['nit'] = htmlspecialchars($_POST['nit']);
            
            // Verificar que el NIT no esté duplicado (excluyendo el actual)
            $nit_repetido = trim($_POST['nit']);
            $sql_verificar_nit = "SELECT id_cliente FROM clientes 
                                 WHERE TRIM(nit) = " . self::$db->quote($nit_repetido) . "
                                 AND activo = 'T' 
                                 AND id_cliente != " . (int)$id;
            $nit_existe = self::fetchFirst($sql_verificar_nit);
            
            if ($nit_existe) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Ya existe otro cliente con este NIT'
                ]);
                return;
            }
        }

        // Validar que al menos tenga un teléfono o celular
        if (empty($_POST['telefono']) && empty($_POST['celular'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Debe proporcionar al menos un número telefónico (teléfono o celular)'
            ]);
            return;
        }

        $_POST['direccion'] = htmlspecialchars($_POST['direccion']);
        $_POST['telefono'] = htmlspecialchars($_POST['telefono']);
        $_POST['celular'] = htmlspecialchars($_POST['celular']);

        try {
            $data = Clientes::find($id);
            $data->sincronizar([
                'nombre' => $_POST['nombre'],
                'nit' => $_POST['nit'],
                'telefono' => $_POST['telefono'],
                'celular' => $_POST['celular'],
                'email' => $_POST['email'],
                'direccion' => $_POST['direccion'],
                'activo' => 'T'
            ]);

            $data->actualizar();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'La información del cliente ha sido modificada con éxito'
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al modificar el cliente',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    //Eliminar Cliente
    public static function EliminarAPI(){
        try {
            $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

            $sql_verificar = "SELECT id_cliente, nombre FROM clientes WHERE id_cliente = $id AND activo = 'T'";
            $cliente = self::fetchFirst($sql_verificar);
            
            if (!$cliente) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El cliente no existe o ya está inactivo'
                ]);
                return;
            }

            // Verificar si tiene ventas asociadas (temporalmente comentado)
            /*
            $ventas_asociadas = self::VentasAsociadasCliente($id);
            
            if ($ventas_asociadas > 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se puede eliminar el cliente porque tiene ventas asociadas',
                    'detalle' => "Hay $ventas_asociadas venta(s) registrada(s) para este cliente."
                ]);
                return;
            }
            */

            self::EliminarCliente($id, 'F');

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'El cliente ha sido desactivado correctamente',
                'detalle' => "Cliente '{$cliente['nombre']}' desactivado exitosamente"
            ]);
        
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al eliminar el cliente',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function buscarPorNitAPI(){
        try {
            $nit = filter_var($_GET['nit'], FILTER_SANITIZE_STRING);
            
            if (empty($nit)) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Debe proporcionar un NIT para buscar'
                ]);
                return;
            }

            $sql = "SELECT * FROM clientes WHERE nit = " . self::$db->quote($nit) . " AND activo = 'T'";
            $cliente = self::fetchFirst($sql);

            if ($cliente) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Cliente encontrado',
                    'data' => $cliente
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se encontró ningún cliente con ese NIT'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al buscar el cliente',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function clientesDisponiblesAPI(){
        try {
            $sql = "SELECT id_cliente, nombre, nit, celular FROM clientes WHERE activo = 'T' ORDER BY nombre";
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Clientes disponibles obtenidos correctamente',
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener los clientes disponibles',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function EliminarCliente($id, $situacion)
    {
        $sql = "UPDATE clientes SET activo = '$situacion' WHERE id_cliente = $id";
        return self::SQL($sql);
    }

    public static function VentasAsociadasCliente($id_cliente)
    {
        $sql = "SELECT COUNT(*) as total FROM ventas WHERE id_cliente = $id_cliente";
        $resultado = self::fetchFirst($sql);
        return $resultado['total'] ?? 0;
    }

    public static function ObtenerClientesActivos()
    {
        $sql = "SELECT * FROM clientes WHERE activo = 'T' ORDER BY nombre";
        return self::fetchArray($sql);
    }

    public static function ReactivarCliente($id)
    {
        return self::EliminarCliente($id, 'T');
    }
}