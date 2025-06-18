<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use Model\DetalleVentaServicios;
use MVC\Router;

class DetalleVentaServiciosController extends ActiveRecord {
    
    public static function renderizarPagina(Router $router) {
        $router->render('detalle_venta_servicios/index', [] );
    }

    // Guardar Detalle de Venta de Servicios
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

            if (!isset($_POST['id_orden']) || empty($_POST['id_orden'])) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Debe seleccionar una orden de reparación'
                ]);
                return;
            }

            if (!isset($_POST['precio_servicio']) || empty($_POST['precio_servicio']) || floatval($_POST['precio_servicio']) <= 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El precio del servicio debe ser mayor que 0'
                ]);
                return;
            }

            $id_venta = (int)$_POST['id_venta'];
            $id_orden = (int)$_POST['id_orden'];
            $precio_servicio = floatval($_POST['precio_servicio']);

            // Verificar que la venta exista y sea válida
            $sql_verificar_venta = "SELECT id_venta, estado_venta, tipo_venta FROM ventas WHERE id_venta = $id_venta";
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
                    'mensaje' => 'No se pueden agregar servicios a una venta cancelada'
                ]);
                return;
            }

            // Verificar que sea una venta de servicios o mixta
            if ($venta_existe['tipo_venta'] === 'P') {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se pueden agregar servicios a una venta de productos. Cambie el tipo de venta a "Servicios".'
                ]);
                return;
            }

            // Verificar que la orden exista y esté disponible
            $sql_verificar_orden = "SELECT o.id_orden, o.numero_orden, o.estado_orden, o.motivo_ingreso,
                                          c.nombre as cliente_nombre
                                   FROM ordenes_reparacion o
                                   INNER JOIN clientes c ON o.id_cliente = c.id_cliente
                                   WHERE o.id_orden = $id_orden";
            $orden = self::fetchFirst($sql_verificar_orden);
            
            if (!$orden) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'La orden de reparación seleccionada no existe'
                ]);
                return;
            }

            // Verificar que la orden no esté ya facturada
            $sql_verificar_duplicado = "SELECT id_detalle_servicio FROM detalle_venta_servicios 
                                       WHERE id_venta = $id_venta AND id_orden = $id_orden";
            $servicio_existe = self::fetchFirst($sql_verificar_duplicado);
            
            if ($servicio_existe) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Esta orden ya está facturada en esta venta'
                ]);
                return;
            }

            // Verificar que la orden no esté facturada en otra venta
            $sql_verificar_otra_venta = "SELECT v.numero_factura FROM detalle_venta_servicios dvs
                                        INNER JOIN ventas v ON dvs.id_venta = v.id_venta
                                        WHERE dvs.id_orden = $id_orden AND v.estado_venta != 'N'";
            $facturada_en = self::fetchFirst($sql_verificar_otra_venta);
            
            if ($facturada_en) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => "Esta orden ya está facturada en la venta: {$facturada_en['numero_factura']}"
                ]);
                return;
            }

            // Crear el detalle
            $detalle = new DetalleVentaServicios([
                'id_venta' => $id_venta,
                'id_orden' => $id_orden,
                'descripcion_servicio' => $_POST['descripcion_servicio'] ?? $orden['motivo_ingreso'],
                'precio_servicio' => $precio_servicio
            ]);

            $resultado = $detalle->crear();

            if ($resultado) {
                // Recalcular totales de la venta
                self::recalcularTotalesVenta($id_venta);

                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Servicio agregado a la venta exitosamente'
                ]);
            } else {
                throw new Exception('Error al crear el detalle de venta de servicios');
            }

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al guardar el detalle de venta de servicios',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // Buscar Detalles de Venta de Servicios
    public static function buscarAPI() {
        try {
            $sql = "SELECT dvs.id_detalle_servicio, dvs.id_venta, dvs.id_orden, 
                           dvs.descripcion_servicio, dvs.precio_servicio,
                           v.numero_factura, v.fecha_venta, v.estado_venta,
                           c.nombre as cliente_nombre,
                           o.numero_orden, o.motivo_ingreso, o.estado_orden,
                           CASE v.estado_venta 
                               WHEN 'C' THEN 'Completada'
                               WHEN 'P' THEN 'Pendiente'
                               WHEN 'N' THEN 'Cancelada'
                               ELSE 'Desconocido'
                           END as estado_venta_texto,
                           CASE o.estado_orden 
                               WHEN 'R' THEN 'Recibido'
                               WHEN 'P' THEN 'En Proceso'
                               WHEN 'E' THEN 'Esperando Repuestos'
                               WHEN 'T' THEN 'Terminado'
                               WHEN 'N' THEN 'Entregado'
                               WHEN 'C' THEN 'Cancelado'
                               ELSE 'Desconocido'
                           END as estado_orden_texto
                    FROM detalle_venta_servicios dvs
                    INNER JOIN ventas v ON dvs.id_venta = v.id_venta
                    LEFT JOIN clientes c ON v.id_cliente = c.id_cliente
                    INNER JOIN ordenes_reparacion o ON dvs.id_orden = o.id_orden
                    ORDER BY dvs.id_detalle_servicio DESC";
            
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Detalles de venta de servicios obtenidos correctamente',
                'data' => $data
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener los detalles de venta de servicios',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // Modificar Detalle de Venta de Servicios
    public static function modificarAPI() {
        try {
            $id = $_POST['id_detalle_servicio'];

            if (!isset($_POST['precio_servicio']) || empty($_POST['precio_servicio']) || floatval($_POST['precio_servicio']) <= 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El precio del servicio debe ser mayor que 0'
                ]);
                return;
            }

            // Obtener el detalle actual
            $detalle = DetalleVentaServicios::find($id);
            if (!$detalle) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El detalle de venta de servicios no existe'
                ]);
                return;
            }

            // Verificar que la venta no esté cancelada
            $sql_verificar = "SELECT v.estado_venta FROM ventas v 
                             INNER JOIN detalle_venta_servicios dvs ON v.id_venta = dvs.id_venta
                             WHERE dvs.id_detalle_servicio = $id";
            $info = self::fetchFirst($sql_verificar);

            if ($info['estado_venta'] === 'N') {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se pueden modificar servicios de una venta cancelada'
                ]);
                return;
            }

            // Actualizar el detalle
            $detalle->sincronizar([
                'descripcion_servicio' => $_POST['descripcion_servicio'] ?? $detalle->descripcion_servicio,
                'precio_servicio' => floatval($_POST['precio_servicio'])
            ]);

            $resultado = $detalle->actualizar();

            // Recalcular totales de la venta
            self::recalcularTotalesVenta($detalle->id_venta);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Detalle de venta de servicios modificado exitosamente'
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al modificar el detalle de venta de servicios',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // Eliminar Detalle de Venta de Servicios
    public static function eliminarAPI() {
        try {
            $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

            // Obtener información del detalle antes de eliminarlo
            $sql_info = "SELECT dvs.id_venta, dvs.precio_servicio,
                               v.numero_factura, v.estado_venta,
                               o.numero_orden
                        FROM detalle_venta_servicios dvs
                        INNER JOIN ventas v ON dvs.id_venta = v.id_venta
                        INNER JOIN ordenes_reparacion o ON dvs.id_orden = o.id_orden
                        WHERE dvs.id_detalle_servicio = $id";
            $detalle_info = self::fetchFirst($sql_info);
            
            if (!$detalle_info) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El detalle de venta de servicios no existe'
                ]);
                return;
            }

            if ($detalle_info['estado_venta'] === 'N') {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se pueden eliminar servicios de una venta cancelada'
                ]);
                return;
            }

            // Eliminar el detalle
            $sql_eliminar = "DELETE FROM detalle_venta_servicios WHERE id_detalle_servicio = $id";
            self::SQL($sql_eliminar);

            // Recalcular totales de la venta
            self::recalcularTotalesVenta($detalle_info['id_venta']);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Servicio eliminado de la venta correctamente',
                'detalle' => "Orden '{$detalle_info['numero_orden']}' eliminada de la factura '{$detalle_info['numero_factura']}'"
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al eliminar el detalle de venta de servicios',
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
                    WHERE v.tipo_venta IN ('S') AND v.estado_venta != 'N'
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

    public static function ordenesDisponiblesAPI() {
        try {
            $sql = "SELECT o.id_orden, o.numero_orden, o.motivo_ingreso, o.estado_orden,
                           o.descripcion_problema, o.fecha_recepcion,
                           c.nombre as cliente_nombre, c.telefono as cliente_telefono,
                           m.nombre_marca, o.modelo_dispositivo,
                           -- Calcular precio total de servicios de la orden
                           (SELECT SUM(so.precio_servicio) 
                            FROM servicios_orden so 
                            WHERE so.id_orden = o.id_orden) as precio_total_servicios,
                           CASE o.estado_orden 
                               WHEN 'R' THEN 'Recibido'
                               WHEN 'P' THEN 'En Proceso'
                               WHEN 'E' THEN 'Esperando Repuestos'
                               WHEN 'T' THEN 'Terminado'
                               WHEN 'N' THEN 'Entregado'
                               WHEN 'C' THEN 'Cancelado'
                               ELSE 'Desconocido'
                           END as estado_orden_texto
                    FROM ordenes_reparacion o
                    INNER JOIN clientes c ON o.id_cliente = c.id_cliente
                    INNER JOIN marcas m ON o.id_marca = m.id_marca
                    WHERE o.estado_orden IN ('T', 'N') -- Solo órdenes terminadas o entregadas
                      AND o.id_orden NOT IN (
                          SELECT DISTINCT dvs.id_orden 
                          FROM detalle_venta_servicios dvs
                          INNER JOIN ventas v ON dvs.id_venta = v.id_venta
                          WHERE v.estado_venta != 'N'
                      )
                    ORDER BY o.fecha_recepcion DESC";
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Órdenes disponibles obtenidas correctamente',
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener las órdenes disponibles',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // Método auxiliar para recalcular totales
    private static function recalcularTotalesVenta($id_venta) {
        try {
            // Calcular nuevo subtotal basado en los detalles de servicios
            $sql_subtotal = "SELECT SUM(precio_servicio) as nuevo_subtotal FROM detalle_venta_servicios WHERE id_venta = $id_venta";
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
            error_log("Error recalculando totales de venta de servicios: " . $e->getMessage());
        }
    }

    public static function obtenerDetallesPorVenta($id_venta) {
        try {
            $sql = "SELECT dvs.*, o.numero_orden, o.motivo_ingreso, c.nombre as cliente_nombre
                    FROM detalle_venta_servicios dvs
                    INNER JOIN ordenes_reparacion o ON dvs.id_orden = o.id_orden
                    INNER JOIN clientes c ON o.id_cliente = c.id_cliente
                    WHERE dvs.id_venta = $id_venta
                    ORDER BY dvs.id_detalle_servicio";
            return self::fetchArray($sql);
        } catch (Exception $e) {
            return [];
        }
    }
}