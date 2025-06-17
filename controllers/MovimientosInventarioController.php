<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use Model\MovimientosInventario;
use MVC\Router;

class MovimientosInventarioController extends ActiveRecord {
    
    public static function renderizarPagina(Router $router) {
        $router->render('movimientos_inventario/index', []);
    }

    // Guardar Movimiento de Inventario
    public static function guardarAPI() {
        try {
            // Validaciones básicas
            if (!isset($_POST['id_inventario']) || empty($_POST['id_inventario'])) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Debe seleccionar un producto del inventario'
                ]);
                return;
            }

            if (!isset($_POST['tipo_movimiento']) || empty($_POST['tipo_movimiento'])) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Debe especificar el tipo de movimiento'
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

            if (!isset($_POST['motivo']) || empty($_POST['motivo'])) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El motivo del movimiento es obligatorio'
                ]);
                return;
            }

            $id_inventario = (int)$_POST['id_inventario'];
            $tipo_movimiento = $_POST['tipo_movimiento'];
            $cantidad = (int)$_POST['cantidad'];
            $motivo = trim($_POST['motivo']);

            // Verificar que el producto del inventario exista
            $sql_verificar_producto = "SELECT i.id_inventario, i.stock_cantidad, i.disponible,
                                             m.nombre_modelo, ma.nombre_marca, i.codigo_producto
                                      FROM inventario i
                                      INNER JOIN modelos m ON i.id_modelo = m.id_modelo
                                      INNER JOIN marcas ma ON m.id_marca = ma.id_marca
                                      WHERE i.id_inventario = $id_inventario";
            $producto = self::fetchFirst($sql_verificar_producto);
            
            if (!$producto) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El producto seleccionado no existe'
                ]);
                return;
            }

            if ($producto['disponible'] !== 'T') {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se pueden realizar movimientos en productos inactivos'
                ]);
                return;
            }

            // Validar stock disponible para salidas
            if ($tipo_movimiento === 'S') {
                if ($producto['stock_cantidad'] < $cantidad) {
                    http_response_code(400);
                    echo json_encode([
                        'codigo' => 0,
                        'mensaje' => "Stock insuficiente. Disponible: {$producto['stock_cantidad']}, Solicitado: $cantidad"
                    ]);
                    return;
                }
            }

            // Obtener usuario actual (aquí deberías usar la sesión real)
            $usuario_movimiento = $_POST['usuario_movimiento'] ?? 1;

            // Crear el movimiento
            $movimiento = new MovimientosInventario([
                'id_inventario' => $id_inventario,
                'tipo_movimiento' => $tipo_movimiento,
                'cantidad' => $cantidad,
                'motivo' => htmlspecialchars($motivo),
                'referencia_documento' => htmlspecialchars($_POST['referencia_documento'] ?? ''),
                'usuario_movimiento' => $usuario_movimiento,
                'observaciones' => htmlspecialchars($_POST['observaciones'] ?? '')
            ]);

            $resultado = $movimiento->crear();

            if ($resultado) {
                // Actualizar stock del inventario
                self::actualizarStockInventario($id_inventario, $cantidad, $tipo_movimiento);

                $tipo_texto = $tipo_movimiento === 'E' ? 'Entrada' : ($tipo_movimiento === 'S' ? 'Salida' : 'Ajuste');

                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => "Movimiento de $tipo_texto registrado exitosamente",
                    'detalle' => "Producto: {$producto['nombre_marca']} {$producto['nombre_modelo']} - Cantidad: $cantidad"
                ]);
            } else {
                throw new Exception('Error al crear el movimiento de inventario');
            }

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al guardar el movimiento de inventario',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // Buscar Movimientos de Inventario
    public static function buscarAPI() {
        try {
            $sql = "SELECT mi.id_movimiento, mi.tipo_movimiento, mi.cantidad, mi.motivo,
                           mi.referencia_documento, mi.fecha_movimiento, mi.observaciones,
                           i.codigo_producto, i.stock_cantidad as stock_actual,
                           m.nombre_modelo, ma.nombre_marca,
                           u.nombre_completo as usuario_nombre,
                           CASE mi.tipo_movimiento 
                               WHEN 'E' THEN 'Entrada'
                               WHEN 'S' THEN 'Salida'
                               WHEN 'A' THEN 'Ajuste'
                               ELSE 'Desconocido'
                           END as tipo_movimiento_texto
                    FROM movimientos_inventario mi
                    INNER JOIN inventario i ON mi.id_inventario = i.id_inventario
                    INNER JOIN modelos m ON i.id_modelo = m.id_modelo
                    INNER JOIN marcas ma ON m.id_marca = ma.id_marca
                    INNER JOIN usuarios u ON mi.usuario_movimiento = u.id_usuario
                    ORDER BY mi.fecha_movimiento DESC, mi.id_movimiento DESC";
            
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Movimientos de inventario obtenidos correctamente',
                'data' => $data
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener los movimientos de inventario',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // Modificar Movimiento de Inventario
    public static function modificarAPI() {
        try {
            $id = $_POST['id_movimiento'];

            if (!isset($_POST['cantidad']) || empty($_POST['cantidad']) || (int)$_POST['cantidad'] <= 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'La cantidad debe ser mayor que 0'
                ]);
                return;
            }

            if (!isset($_POST['motivo']) || empty($_POST['motivo'])) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El motivo del movimiento es obligatorio'
                ]);
                return;
            }

            // Obtener el movimiento actual
            $movimiento = MovimientosInventario::find($id);
            if (!$movimiento) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El movimiento de inventario no existe'
                ]);
                return;
            }

            // Verificar que no sea un movimiento automático del sistema
            if (in_array(strtolower($movimiento->motivo), ['venta de producto', 'devolución por eliminación de detalle'])) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se pueden modificar movimientos automáticos del sistema'
                ]);
                return;
            }

            $nueva_cantidad = (int)$_POST['cantidad'];
            $cantidad_anterior = $movimiento->cantidad;
            $diferencia = $nueva_cantidad - $cantidad_anterior;

            // Si hay diferencia en cantidad, validar stock para salidas
            if ($diferencia != 0 && $movimiento->tipo_movimiento === 'S') {
                $sql_stock = "SELECT stock_cantidad FROM inventario WHERE id_inventario = {$movimiento->id_inventario}";
                $stock_info = self::fetchFirst($sql_stock);
                
                if ($diferencia > 0 && $stock_info['stock_cantidad'] < $diferencia) {
                    http_response_code(400);
                    echo json_encode([
                        'codigo' => 0,
                        'mensaje' => "Stock insuficiente para el ajuste. Disponible: {$stock_info['stock_cantidad']}"
                    ]);
                    return;
                }
            }

            // Actualizar el movimiento
            $movimiento->sincronizar([
                'cantidad' => $nueva_cantidad,
                'motivo' => htmlspecialchars($_POST['motivo']),
                'referencia_documento' => htmlspecialchars($_POST['referencia_documento'] ?? ''),
                'observaciones' => htmlspecialchars($_POST['observaciones'] ?? '')
            ]);

            $resultado = $movimiento->actualizar();

            // Ajustar stock si hay diferencia
            if ($diferencia != 0) {
                $tipo_ajuste = ($movimiento->tipo_movimiento === 'E') ? 'E' : 'S';
                if ($diferencia < 0) {
                    $tipo_ajuste = ($tipo_ajuste === 'E') ? 'S' : 'E';
                }
                self::actualizarStockInventario($movimiento->id_inventario, abs($diferencia), $tipo_ajuste);
            }

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Movimiento de inventario modificado exitosamente'
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al modificar el movimiento de inventario',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // Eliminar Movimiento de Inventario
    public static function eliminarAPI() {
        try {
            $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

            // Obtener información del movimiento antes de eliminarlo
            $sql_info = "SELECT mi.cantidad, mi.tipo_movimiento, mi.motivo, mi.id_inventario,
                               m.nombre_modelo, ma.nombre_marca, i.codigo_producto
                        FROM movimientos_inventario mi
                        INNER JOIN inventario i ON mi.id_inventario = i.id_inventario
                        INNER JOIN modelos m ON i.id_modelo = m.id_modelo
                        INNER JOIN marcas ma ON m.id_marca = ma.id_marca
                        WHERE mi.id_movimiento = $id";
            $movimiento_info = self::fetchFirst($sql_info);
            
            if (!$movimiento_info) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El movimiento de inventario no existe'
                ]);
                return;
            }

            // Verificar que no sea un movimiento automático del sistema
            if (in_array(strtolower($movimiento_info['motivo']), ['venta de producto', 'devolución por eliminación de detalle'])) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se pueden eliminar movimientos automáticos del sistema'
                ]);
                return;
            }

            // Reversar el movimiento en el stock
            $tipo_reverso = $movimiento_info['tipo_movimiento'] === 'E' ? 'S' : 'E';
            self::actualizarStockInventario($movimiento_info['id_inventario'], $movimiento_info['cantidad'], $tipo_reverso);

            // Eliminar el movimiento
            $sql_eliminar = "DELETE FROM movimientos_inventario WHERE id_movimiento = $id";
            self::SQL($sql_eliminar);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Movimiento de inventario eliminado correctamente',
                'detalle' => "Movimiento de '{$movimiento_info['nombre_marca']} {$movimiento_info['nombre_modelo']}' eliminado y stock ajustado"
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al eliminar el movimiento de inventario',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // APIs para cargar datos de selects
    public static function productosInventarioAPI() {
        try {
            $sql = "SELECT i.id_inventario, i.codigo_producto, i.imei, i.stock_cantidad, 
                           i.estado_producto, i.disponible,
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
                    WHERE i.disponible = 'T'
                    ORDER BY ma.nombre_marca, m.nombre_modelo";
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Productos de inventario obtenidos correctamente',
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener los productos de inventario',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function usuariosDisponiblesAPI() {
        try {
            $sql = "SELECT u.id_usuario, u.nombre_completo, u.email 
                    FROM usuarios u 
                    WHERE u.activo = 'T' 
                    ORDER BY u.nombre_completo";
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Usuarios obtenidos correctamente',
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener los usuarios',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // Obtener resumen de movimientos por producto
    public static function resumenPorProductoAPI() {
        try {
            $id_inventario = $_GET['id_inventario'] ?? null;
            
            if (!$id_inventario) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Debe especificar el ID del producto'
                ]);
                return;
            }

            $sql = "SELECT 
                        SUM(CASE WHEN tipo_movimiento = 'E' THEN cantidad ELSE 0 END) as total_entradas,
                        SUM(CASE WHEN tipo_movimiento = 'S' THEN cantidad ELSE 0 END) as total_salidas,
                        SUM(CASE WHEN tipo_movimiento = 'A' THEN cantidad ELSE 0 END) as total_ajustes,
                        COUNT(*) as total_movimientos,
                        i.stock_cantidad as stock_actual
                    FROM movimientos_inventario mi
                    INNER JOIN inventario i ON mi.id_inventario = i.id_inventario
                    WHERE mi.id_inventario = $id_inventario";
            
            $resumen = self::fetchFirst($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Resumen obtenido correctamente',
                'data' => $resumen
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener el resumen',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // Método auxiliar para actualizar stock
    private static function actualizarStockInventario($id_inventario, $cantidad, $tipo_movimiento) {
        try {
            if ($tipo_movimiento === 'E') { // Entrada - Aumentar stock
                $sql = "UPDATE inventario SET stock_cantidad = stock_cantidad + $cantidad WHERE id_inventario = $id_inventario";
            } else { // Salida - Reducir stock
                $sql = "UPDATE inventario SET stock_cantidad = stock_cantidad - $cantidad WHERE id_inventario = $id_inventario";
            }
            
            self::SQL($sql);

        } catch (Exception $e) {
            error_log("Error actualizando stock: " . $e->getMessage());
            throw $e;
        }
    }

    // Obtener movimientos por producto específico
    public static function movimientosPorProductoAPI() {
        try {
            $id_inventario = $_GET['id_inventario'] ?? null;
            
            if (!$id_inventario) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Debe especificar el ID del producto'
                ]);
                return;
            }

            $sql = "SELECT mi.*, u.nombre_completo as usuario_nombre,
                           CASE mi.tipo_movimiento 
                               WHEN 'E' THEN 'Entrada'
                               WHEN 'S' THEN 'Salida'
                               WHEN 'A' THEN 'Ajuste'
                               ELSE 'Desconocido'
                           END as tipo_movimiento_texto
                    FROM movimientos_inventario mi
                    INNER JOIN usuarios u ON mi.usuario_movimiento = u.id_usuario
                    WHERE mi.id_inventario = $id_inventario
                    ORDER BY mi.fecha_movimiento DESC";
            
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Movimientos del producto obtenidos correctamente',
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener los movimientos del producto',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}