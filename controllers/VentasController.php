<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use Model\Ventas;
use MVC\Router;

class VentasController extends ActiveRecord {
    
    public static function renderizarPagina(Router $router) {
        $router->render('ventas/index', []);
        verificarPermisos('ventas');
    }

    // Guardar Venta
    public static function guardarAPI() {
        try {
            // Validaciones básicas
            if (!isset($_POST['numero_factura']) || empty($_POST['numero_factura'])) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El número de factura es obligatorio'
                ]);
                return;
            }

            if (!isset($_POST['tipo_venta']) || empty($_POST['tipo_venta'])) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Debe especificar el tipo de venta'
                ]);
                return;
            }

            if (!isset($_POST['total']) || empty($_POST['total']) || floatval($_POST['total']) <= 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El total de la venta debe ser mayor que 0'
                ]);
                return;
            }

            // Verificar que no exista el número de factura
            $numero_factura = trim($_POST['numero_factura']);
            $sql_verificar = "SELECT id_venta FROM ventas WHERE numero_factura = '$numero_factura'";
            $venta_existe = self::fetchFirst($sql_verificar);
            
            if ($venta_existe) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El número de factura ya existe'
                ]);
                return;
            }

            // Calcular valores
            $subtotal = floatval($_POST['subtotal'] ?? 0);
            $descuento = floatval($_POST['descuento'] ?? 0);
            $impuestos = floatval($_POST['impuestos'] ?? 0);
            $total = floatval($_POST['total']);

            // Crear la venta
            $venta = new Ventas([
                'numero_factura' => $numero_factura,
                'id_cliente' => !empty($_POST['id_cliente']) ? (int)$_POST['id_cliente'] : null,
                'tipo_venta' => $_POST['tipo_venta'],
                'subtotal' => $subtotal,
                'descuento' => $descuento,
                'impuestos' => $impuestos,
                'total' => $total,
                'metodo_pago' => $_POST['metodo_pago'] ?? 'E',
                'estado_venta' => $_POST['estado_venta'] ?? 'C',
                'id_usuario_vendedor' => $_POST['id_usuario_vendedor'] ?? 1, // Usar sesión real
                'observaciones' => htmlspecialchars($_POST['observaciones'] ?? '')
            ]);

            $resultado = $venta->crear();

            if ($resultado) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Venta registrada exitosamente',
                    'id_venta' => $venta->id_venta ?? $resultado
                ]);
            } else {
                throw new Exception('Error al crear la venta');
            }

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al guardar la venta',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // Buscar Ventas
    public static function buscarAPI() {
        try {
            $sql = "SELECT v.id_venta, v.numero_factura, v.tipo_venta, v.subtotal, 
                           v.descuento, v.impuestos, v.total, v.fecha_venta,
                           v.metodo_pago, v.estado_venta, v.observaciones,
                           c.nombre as cliente_nombre,
                           u.nombre_completo as vendedor_nombre,
                           CASE v.tipo_venta 
                               WHEN 'P' THEN 'Productos'
                               WHEN 'S' THEN 'Servicios'
                               ELSE 'Mixta'
                           END as tipo_venta_texto,
                           CASE v.metodo_pago 
                               WHEN 'E' THEN 'Efectivo'
                               WHEN 'T' THEN 'Tarjeta'
                               WHEN 'R' THEN 'Transferencia'
                               WHEN 'C' THEN 'Crédito'
                               ELSE 'Otro'
                           END as metodo_pago_texto,
                           CASE v.estado_venta 
                               WHEN 'C' THEN 'Completada'
                               WHEN 'P' THEN 'Pendiente'
                               WHEN 'N' THEN 'Cancelada'
                               ELSE 'Desconocido'
                           END as estado_venta_texto
                    FROM ventas v
                    LEFT JOIN clientes c ON v.id_cliente = c.id_cliente
                    LEFT JOIN usuarios u ON v.id_usuario_vendedor = u.id_usuario
                    ORDER BY v.fecha_venta DESC";
            
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Ventas obtenidas correctamente',
                'data' => $data
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener las ventas',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // Modificar Venta
    public static function modificarAPI() {
        try {
            $id = $_POST['id_venta'];

            if (!isset($_POST['total']) || empty($_POST['total']) || floatval($_POST['total']) <= 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El total de la venta debe ser mayor que 0'
                ]);
                return;
            }

            // Verificar que la venta exista
            $venta = Ventas::find($id);
            if (!$venta) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'La venta no existe'
                ]);
                return;
            }

            // Actualizar datos
            $venta->sincronizar([
                'id_cliente' => !empty($_POST['id_cliente']) ? (int)$_POST['id_cliente'] : null,
                'tipo_venta' => $_POST['tipo_venta'],
                'subtotal' => floatval($_POST['subtotal'] ?? 0),
                'descuento' => floatval($_POST['descuento'] ?? 0),
                'impuestos' => floatval($_POST['impuestos'] ?? 0),
                'total' => floatval($_POST['total']),
                'metodo_pago' => $_POST['metodo_pago'] ?? 'E',
                'estado_venta' => $_POST['estado_venta'] ?? 'C',
                'observaciones' => htmlspecialchars($_POST['observaciones'] ?? '')
            ]);

            $resultado = $venta->actualizar();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Venta modificada exitosamente'
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al modificar la venta',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // Eliminar Venta
    public static function eliminarAPI() {
        try {
            $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

            // Verificar que la venta exista
            $sql_verificar = "SELECT numero_factura, estado_venta FROM ventas WHERE id_venta = $id";
            $venta = self::fetchFirst($sql_verificar);
            
            if (!$venta) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'La venta no existe'
                ]);
                return;
            }

            // Verificar si tiene detalles asociados
            $detalles_productos = self::contarDetallesProductos($id);
            $detalles_servicios = self::contarDetallesServicios($id);
            
            if ($detalles_productos > 0 || $detalles_servicios > 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se puede eliminar la venta porque tiene detalles asociados',
                    'detalle' => "Productos: $detalles_productos, Servicios: $detalles_servicios"
                ]);
                return;
            }

            // Eliminar la venta
            $sql_eliminar = "DELETE FROM ventas WHERE id_venta = $id";
            self::SQL($sql_eliminar);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Venta eliminada correctamente',
                'detalle' => "Factura '{$venta['numero_factura']}' eliminada exitosamente"
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al eliminar la venta',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // APIs para cargar datos de selects
    public static function clientesDisponiblesAPI() {
        try {
            $sql = "SELECT id_cliente, nombre, nit, telefono, email 
                    FROM clientes 
                    WHERE activo = 'T' 
                    ORDER BY nombre";
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

    public static function vendedoresDisponiblesAPI() {
        try {
            $sql = "SELECT u.id_usuario, u.nombre_completo, u.email 
                    FROM usuarios u 
                    WHERE u.activo = 'T' 
                    ORDER BY u.nombre_completo";
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Vendedores obtenidos correctamente',
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener los vendedores',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // Generar número de factura automático
    public static function generarNumeroFacturaAPI() {
        try {
            $sql = "SELECT COUNT(*) + 1 as siguiente FROM ventas";
            $resultado = self::fetchFirst($sql);
            $siguiente = $resultado['siguiente'] ?? 1;
            
            $numero_factura = 'FAC-' . date('Y') . '-' . str_pad($siguiente, 4, '0', STR_PAD_LEFT);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'numero_factura' => $numero_factura
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al generar número de factura',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // Métodos auxiliares
    private static function contarDetallesProductos($id_venta) {
        $sql = "SELECT COUNT(*) as total FROM detalle_venta_productos WHERE id_venta = $id_venta";
        $resultado = self::fetchFirst($sql);
        return $resultado['total'] ?? 0;
    }

    private static function contarDetallesServicios($id_venta) {
        $sql = "SELECT COUNT(*) as total FROM detalle_venta_servicios WHERE id_venta = $id_venta";
        $resultado = self::fetchFirst($sql);
        return $resultado['total'] ?? 0;
    }

    public static function obtenerDetalleVenta($id_venta) {
        try {
            // Obtener detalles de productos
            $sql_productos = "SELECT dvp.*, i.codigo_producto, m.nombre_modelo
                             FROM detalle_venta_productos dvp
                             INNER JOIN inventario i ON dvp.id_inventario = i.id_inventario
                             INNER JOIN modelos m ON i.id_modelo = m.id_modelo
                             WHERE dvp.id_venta = $id_venta";
            $productos = self::fetchArray($sql_productos);

            // Obtener detalles de servicios
            $sql_servicios = "SELECT dvs.*, o.numero_orden
                             FROM detalle_venta_servicios dvs
                             INNER JOIN ordenes_reparacion o ON dvs.id_orden = o.id_orden
                             WHERE dvs.id_venta = $id_venta";
            $servicios = self::fetchArray($sql_servicios);

            return [
                'productos' => $productos,
                'servicios' => $servicios
            ];
        } catch (Exception $e) {
            return [
                'productos' => [],
                'servicios' => []
            ];
        }
    }
}