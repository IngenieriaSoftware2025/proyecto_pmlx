<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use Model\DetalleVentaProductos;
use MVC\Router;

class DetalleVentaProductosController extends ActiveRecord {
    
    public static function renderizarPagina(Router $router) {
        $router->render('detalle_venta_productos/index', []);
    }

    // Guardar Detalle de Venta de Productos
    public static function guardarAPI() {
        try {
            // Validaciones básicas
            if (!isset($_POST['id_venta']) || empty($_POST['id_venta'])) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Debe seleccionar una venta'
                ]);
                return;
            }

            if (!isset($_POST['id_inventario']) || empty($_POST['id_inventario'])) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Debe seleccionar un producto'
                ]);
                return;
            }

            if (!isset($_POST['cantidad']) || empty($_POST['cantidad']) || (int)$_POST['cantidad'] <= 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'La cantidad debe ser mayor que 0'
                ]);
                return;
            }

            if (!isset($_POST['precio_unitario']) || empty($_POST['precio_unitario']) || floatval($_POST['precio_unitario']) <= 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El precio unitario debe ser mayor que 0'
                ]);
                return;
            }

            $id_venta = (int)$_POST['id_venta'];
            $id_inventario = (int)$_POST['id_inventario'];
            $cantidad = (int)$_POST['cantidad'];
            $precio_unitario = floatval($_POST['precio_unitario']);

            // Verificar que la venta exista
            $sql_verificar_venta = "SELECT id_venta, estado_venta FROM ventas WHERE id_venta = $id_venta";
            $venta_existe = self::fetchFirst($sql_verificar_venta);
            
            if (!$venta_existe) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'La venta seleccionada no existe'
                ]);
                return;
            }

            // Verificar que la venta no esté cancelada
            if ($venta_existe['estado_venta'] === 'N') {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se pueden agregar productos a una venta cancelada'
                ]);
                return;
            }

            // Verificar que el producto exista y esté disponible
            $sql_verificar_producto = "SELECT i.id_inventario, i.stock_cantidad, i.disponible, i.precio_venta,
                                             m.nombre_modelo, ma.nombre_marca
                                      FROM inventario i
                                      INNER JOIN modelos m ON i.id_modelo = m.id_modelo
                                      INNER JOIN marcas ma ON m.id_marca = ma.id_marca
                                      WHERE i.id_inventario = $id_inventario AND i.disponible = 'T'";
            $producto = self::fetchFirst($sql_verificar_producto);
            
            if (!$producto) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El producto seleccionado no existe o no está disponible'
                ]);
                return;
            }

            // Verificar stock disponible
            if ($producto['stock_cantidad'] < $cantidad) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => "Stock insuficiente. Disponible: {$producto['stock_cantidad']}, Solicitado: $cantidad"
                ]);
                return;
            }

            // Verificar que no esté duplicado el producto en esta venta
            $sql_verificar_duplicado = "SELECT id_detalle FROM detalle_venta_productos 
                                       WHERE id_venta = $id_venta AND id_inventario = $id_inventario";
            $detalle_existe = self::fetchFirst($sql_verificar_duplicado);
            
            if ($detalle_existe) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Este producto ya está agregado a la venta. Use la opción de modificar para cambiar la cantidad.'
                ]);
                return;
            }

            // Calcular subtotal
            $subtotal = $cantidad * $precio_unitario;

            // Crear el detalle
            $detalle = new DetalleVentaProductos([
                'id_venta' => $id_venta,
                'id_inventario' => $id_inventario,
                'cantidad' => $cantidad,
                'precio_unitario' => $precio_unitario,
                'subtotal' => $subtotal
            ]);

            $resultado = $detalle->crear();

            if ($resultado) {
                // Actualizar stock del inventario
                self::actualizarStock($id_inventario, $cantidad, 'S'); // S = Salida

                // Recalcular totales de la venta
                self::recalcularTotalesVenta($id_venta);

                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Producto agregado a la venta exitosamente'
                ]);
            } else {
                throw new Exception('Error al crear el detalle de venta');
            }

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al guardar el detalle de venta',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // Buscar Detalles de Venta de Productos
    public static function buscarAPI() {
        try {
            $sql = "SELECT dvp.id_detalle, dvp.id_venta, dvp.cantidad, dvp.precio_unitario, dvp.subtotal,
                           v.numero_factura, v.fecha_venta, v.estado_venta,
                           c.nombre as cliente_nombre,
                           i.codigo_producto, i.imei, i.precio_venta as precio_catalogo,
                           m.nombre_modelo, ma.nombre_marca,
                           CASE v.estado_venta 
                               WHEN 'C' THEN 'Completada'
                               WHEN 'P' THEN 'Pendiente'
                               WHEN 'N' THEN 'Cancelada'
                               ELSE 'Desconocido'
                           END as estado_venta_texto
                    FROM detalle_venta_productos dvp
                    INNER JOIN ventas v ON dvp.id_venta = v.id_venta
                    LEFT JOIN clientes c ON v.id_cliente = c.id_cliente
                    INNER JOIN inventario i ON dvp.id_inventario = i.id_inventario
                    INNER JOIN modelos m ON i.id_modelo = m.id_modelo
                    INNER JOIN marcas ma ON m.id_marca = ma.id_marca
                    ORDER BY dvp.id_detalle DESC";
            
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Detalles de venta obtenidos correctamente',
                'data' => $data
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener los detalles de venta',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // Modificar Detalle de Venta de Productos
    public static function modificarAPI() {
        try {
            $id = $_POST['id_detalle'];

            if (!isset($_POST['cantidad']) || empty($_POST['cantidad']) || (int)$_POST['cantidad'] <= 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'La cantidad debe ser mayor que 0'
                ]);
                return;
            }

            if (!isset($_POST['precio_unitario']) || empty($_POST['precio_unitario']) || floatval($_POST['precio_unitario']) <= 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El precio unitario debe ser mayor que 0'
                ]);
                return;
            }

            // Obtener el detalle actual
            $detalle = DetalleVentaProductos::find($id);
            if (!$detalle) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El detalle de venta no existe'
                ]);
                return;
            }

            // Obtener información del producto y venta
            $sql_info = "SELECT i.stock_cantidad, v.estado_venta, dvp.cantidad as cantidad_actual
                        FROM detalle_venta_productos dvp
                        INNER JOIN ventas v ON dvp.id_venta = v.id_venta
                        INNER JOIN inventario i ON dvp.id_inventario = i.id_inventario
                        WHERE dvp.id_detalle = $id";
            $info = self::fetchFirst($sql_info);

            if ($info['estado_venta'] === 'N') {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se pueden modificar productos de una venta cancelada'
                ]);
                return;
            }

            $nueva_cantidad = (int)$_POST['cantidad'];
            $precio_unitario = floatval($_POST['precio_unitario']);
            $cantidad_actual = $info['cantidad_actual'];
            $stock_disponible = $info['stock_cantidad'];

            // Calcular diferencia de stock
            $diferencia_cantidad = $nueva_cantidad - $cantidad_actual;

            // Verificar stock si se aumenta la cantidad
            if ($diferencia_cantidad > 0 && $stock_disponible < $diferencia_cantidad) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => "Stock insuficiente. Disponible: $stock_disponible, Necesario: $diferencia_cantidad"
                ]);
                return;
            }

            // Calcular nuevo subtotal
            $subtotal = $nueva_cantidad * $precio_unitario;

            // Actualizar el detalle
            $detalle->sincronizar([
                'cantidad' => $nueva_cantidad,
                'precio_unitario' => $precio_unitario,
                'subtotal' => $subtotal
            ]);

            $resultado = $detalle->actualizar();

            // Actualizar stock según la diferencia
            if ($diferencia_cantidad != 0) {
                $tipo_movimiento = $diferencia_cantidad > 0 ? 'S' : 'E'; // S=Salida, E=Entrada
                self::actualizarStock($detalle->id_inventario, abs($diferencia_cantidad), $tipo_movimiento);
            }

            // Recalcular totales de la venta
            self::recalcularTotalesVenta($detalle->id_venta);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Detalle de venta modificado exitosamente'
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al modificar el detalle de venta',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // Eliminar Detalle de Venta de Productos
    public static function eliminarAPI() {
        try {
            $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

            // Obtener información del detalle antes de eliminarlo
            $sql_info = "SELECT dvp.cantidad, dvp.id_inventario, dvp.id_venta,
                               v.numero_factura, v.estado_venta,
                               m.nombre_modelo, ma.nombre_marca
                        FROM detalle_venta_productos dvp
                        INNER JOIN ventas v ON dvp.id_venta = v.id_venta
                        INNER JOIN inventario i ON dvp.id_inventario = i.id_inventario
                        INNER JOIN modelos m ON i.id_modelo = m.id_modelo
                        INNER JOIN marcas ma ON m.id_marca = ma.id_marca
                        WHERE dvp.id_detalle = $id";
            $detalle_info = self::fetchFirst($sql_info);
            
            if (!$detalle_info) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El detalle de venta no existe'
                ]);
                return;
            }

            if ($detalle_info['estado_venta'] === 'N') {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se pueden eliminar productos de una venta cancelada'
                ]);
                return;
            }

            // Eliminar el detalle
            $sql_eliminar = "DELETE FROM detalle_venta_productos WHERE id_detalle = $id";
            self::SQL($sql_eliminar);

            // Devolver stock al inventario
            self::actualizarStock($detalle_info['id_inventario'], $detalle_info['cantidad'], 'E'); // E = Entrada

            // Recalcular totales de la venta
            self::recalcularTotalesVenta($detalle_info['id_venta']);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Producto eliminado de la venta correctamente',
                'detalle' => "Producto '{$detalle_info['nombre_marca']} {$detalle_info['nombre_modelo']}' eliminado de la factura '{$detalle_info['numero_factura']}'"
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al eliminar el detalle de venta',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // APIs para cargar datos de selects
    public static function ventasDisponiblesAPI() {
        try {
            $sql = "SELECT v.id_venta, v.numero_factura, v.fecha_venta, v.total, v.estado_venta,
                           c.nombre as cliente_nombre,
                           CASE v.estado_venta 
                               WHEN 'C' THEN 'Completada'
                               WHEN 'P' THEN 'Pendiente'
                               WHEN 'N' THEN 'Cancelada'
                               ELSE 'Desconocido'
                           END as estado_venta_texto
                    FROM ventas v
                    LEFT JOIN clientes c ON v.id_cliente = c.id_cliente
                    WHERE v.tipo_venta IN ('P', 'M') AND v.estado_venta != 'N'
                    ORDER BY v.fecha_venta DESC";
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Ventas disponibles obtenidas correctamente',
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener las ventas disponibles',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function productosDisponiblesAPI() {
        try {
            $sql = "SELECT i.id_inventario, i.codigo_producto, i.imei, i.precio_venta, 
                           i.stock_cantidad, i.estado_producto,
                           m.nombre_modelo, m.especificaciones,
                           ma.nombre_marca,
                           CASE i.estado_producto 
                               WHEN 'N' THEN 'Nuevo'
                               WHEN 'U' THEN 'Usado'
                               WHEN 'R' THEN 'Reacondicionado'
                               ELSE 'Desconocido'
                           END as estado_producto_texto
                    FROM inventario i
                    INNER JOIN modelos m ON i.id_modelo = m.id_modelo
                    INNER JOIN marcas ma ON m.id_marca = ma.id_marca
                    WHERE i.disponible = 'T' AND i.stock_cantidad > 0
                    ORDER BY ma.nombre_marca, m.nombre_modelo";
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Productos disponibles obtenidos correctamente',
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener los productos disponibles',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // Métodos auxiliares
    private static function actualizarStock($id_inventario, $cantidad, $tipo_movimiento) {
        try {
            if ($tipo_movimiento === 'S') { // Salida - Reducir stock
                $sql = "UPDATE inventario SET stock_cantidad = stock_cantidad - $cantidad WHERE id_inventario = $id_inventario";
            } else { // Entrada - Aumentar stock
                $sql = "UPDATE inventario SET stock_cantidad = stock_cantidad + $cantidad WHERE id_inventario = $id_inventario";
            }
            
            self::SQL($sql);

            // Registrar movimiento en historial
            $motivo = $tipo_movimiento === 'S' ? 'Venta de producto' : 'Devolución por eliminación de detalle';
            $sql_movimiento = "INSERT INTO movimientos_inventario (id_inventario, tipo_movimiento, cantidad, motivo, usuario_movimiento)
                              VALUES ($id_inventario, '$tipo_movimiento', $cantidad, '$motivo', 1)";
            self::SQL($sql_movimiento);

        } catch (Exception $e) {
            error_log("Error actualizando stock: " . $e->getMessage());
        }
    }

    private static function recalcularTotalesVenta($id_venta) {
        try {
            // Calcular nuevo subtotal basado en los detalles
            $sql_subtotal = "SELECT SUM(subtotal) as nuevo_subtotal FROM detalle_venta_productos WHERE id_venta = $id_venta";
            $resultado = self::fetchFirst($sql_subtotal);
            $nuevo_subtotal = $resultado['nuevo_subtotal'] ?? 0;

            // Obtener descuento e impuestos actuales
            $sql_venta = "SELECT descuento, impuestos FROM ventas WHERE id_venta = $id_venta";
            $venta = self::fetchFirst($sql_venta);
            $descuento = $venta['descuento'] ?? 0;
            $impuestos = $venta['impuestos'] ?? 0;

            // Calcular nuevo total
            $nuevo_total = $nuevo_subtotal - $descuento + $impuestos;

            // Actualizar la venta
            $sql_actualizar = "UPDATE ventas SET subtotal = $nuevo_subtotal, total = $nuevo_total WHERE id_venta = $id_venta";
            self::SQL($sql_actualizar);

        } catch (Exception $e) {
            error_log("Error recalculando totales de venta: " . $e->getMessage());
        }
    }

    public static function obtenerDetallesPorVenta($id_venta) {
        try {
            $sql = "SELECT dvp.*, i.codigo_producto, m.nombre_modelo, ma.nombre_marca
                    FROM detalle_venta_productos dvp
                    INNER JOIN inventario i ON dvp.id_inventario = i.id_inventario
                    INNER JOIN modelos m ON i.id_modelo = m.id_modelo
                    INNER JOIN marcas ma ON m.id_marca = ma.id_marca
                    WHERE dvp.id_venta = $id_venta
                    ORDER BY dvp.id_detalle";
            return self::fetchArray($sql);
        } catch (Exception $e) {
            return [];
        }
    }
}