<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use Model\ServiciosOrden;
use MVC\Router;

class ServicioOrdenController extends ActiveRecord{
    public static function renderizarPagina(Router $router){
        $router->render('servicios_orden/index', []);
    }

    //Guardar Servicio de Orden
    public static function guardarAPI(){
        // getHeadersApi(); // Comentado temporalmente

        // Verificar que los campos obligatorios existan
        if (!isset($_POST['id_orden']) || empty($_POST['id_orden'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Debe seleccionar una orden de reparación'
            ]);
            return;
        }

        if (!isset($_POST['id_tipo_servicio']) || empty($_POST['id_tipo_servicio'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Debe seleccionar un tipo de servicio'
            ]);
            return;
        }

        if (!isset($_POST['precio_servicio']) || empty($_POST['precio_servicio'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El precio del servicio es obligatorio'
            ]);
            return;
        }

        // Validar que el precio sea numérico y mayor que 0
        $precio = floatval($_POST['precio_servicio']);
        if ($precio <= 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El precio del servicio debe ser mayor que 0'
            ]);
            return;
        }

        // Validar que la orden exista
        $id_orden = (int)$_POST['id_orden'];
        $sql_verificar_orden = "SELECT id_orden FROM ordenes_reparacion WHERE id_orden = $id_orden";
        $orden_existe = self::fetchFirst($sql_verificar_orden);
        
        if (!$orden_existe) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La orden de reparación seleccionada no existe'
            ]);
            return;
        }

        // Validar que el tipo de servicio exista
        $id_tipo_servicio = (int)$_POST['id_tipo_servicio'];
        $sql_verificar_tipo = "SELECT id_tipo_servicio FROM tipos_servicio WHERE id_tipo_servicio = $id_tipo_servicio AND activo = 'T'";
        $tipo_existe = self::fetchFirst($sql_verificar_tipo);
        
        if (!$tipo_existe) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El tipo de servicio seleccionado no existe o está inactivo'
            ]);
            return;
        }

        // Verificar que no esté duplicado el servicio para esta orden
        $sql_verificar_duplicado = "SELECT id_servicio_orden FROM servicios_orden 
                                   WHERE id_orden = $id_orden AND id_tipo_servicio = $id_tipo_servicio";
        $servicio_existe = self::fetchFirst($sql_verificar_duplicado);
        
        if ($servicio_existe) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Este tipo de servicio ya está agregado a la orden'
            ]);
            return;
        }

        $_POST['observaciones'] = htmlspecialchars($_POST['observaciones'] ?? '');

        // ✅ CORREGIR FORMATO DE FECHAS PARA INFORMIX
        $fecha_inicio = null;
        $fecha_completado = null;
        
        if (!empty($_POST['fecha_inicio'])) {
            // Convertir al formato que Informix acepta: YYYY-MM-DD HH:MM:SS
            $fecha_inicio = date('Y-m-d H:i:s', strtotime($_POST['fecha_inicio'] . ' 00:00:00'));
        }
        
        if (!empty($_POST['fecha_completado'])) {
            // Convertir al formato que Informix acepta: YYYY-MM-DD HH:MM:SS
            $fecha_completado = date('Y-m-d H:i:s', strtotime($_POST['fecha_completado'] . ' 23:59:59'));
        }

        try {
            $data = new ServiciosOrden([
                'id_orden' => $_POST['id_orden'],
                'id_tipo_servicio' => $_POST['id_tipo_servicio'],
                'precio_servicio' => $precio,
                'estado_servicio' => $_POST['estado_servicio'] ?? 'P',
                'fecha_inicio' => $fecha_inicio,
                'fecha_completado' => $fecha_completado,
                'observaciones' => $_POST['observaciones']
            ]);

            $crear = $data->crear();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'El servicio ha sido agregado a la orden con éxito'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al guardar el servicio',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    //Modificar Servicio de Orden
    public static function modificarAPI(){
        // getHeadersApi(); // Comentado temporalmente

        $id = $_POST['id_servicio_orden'];

        // Verificar que los campos obligatorios existan
        if (!isset($_POST['precio_servicio']) || empty($_POST['precio_servicio'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El precio del servicio es obligatorio'
            ]);
            return;
        }

        // Validar que el precio sea numérico y mayor que 0
        $precio = floatval($_POST['precio_servicio']);
        if ($precio <= 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El precio del servicio debe ser mayor que 0'
            ]);
            return;
        }

        $_POST['observaciones'] = htmlspecialchars($_POST['observaciones'] ?? '');

        // ✅ CORREGIR FORMATO DE FECHAS PARA INFORMIX
        $fecha_inicio = null;
        $fecha_completado = null;
        
        if (!empty($_POST['fecha_inicio'])) {
            $fecha_inicio = date('Y-m-d H:i:s', strtotime($_POST['fecha_inicio'] . ' 00:00:00'));
        }
        
        if (!empty($_POST['fecha_completado'])) {
            $fecha_completado = date('Y-m-d H:i:s', strtotime($_POST['fecha_completado'] . ' 23:59:59'));
        }

        try {
            $data = ServiciosOrden::find($id);
            $data->sincronizar([
                'precio_servicio' => $precio,
                'estado_servicio' => $_POST['estado_servicio'],
                'fecha_inicio' => $fecha_inicio,
                'fecha_completado' => $fecha_completado,
                'observaciones' => $_POST['observaciones']
            ]);

            $data->actualizar();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'El servicio ha sido modificado con éxito'
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al modificar el servicio',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // ... resto de las funciones sin cambios
   //Buscar Servicios de Orden - VERSIÓN CORREGIDA
public static function buscarAPI(){
    try {
        $sql = "SELECT so.id_servicio_orden, so.id_orden, so.id_tipo_servicio, 
                       so.precio_servicio, so.estado_servicio,
                       so.fecha_inicio, so.fecha_completado, so.observaciones,
                       o.numero_orden, o.motivo_ingreso,
                       c.nombre as cliente_nombre,
                       ts.nombre_servicio, ts.precio_base, ts.tiempo_estimado_horas,
                       CASE so.estado_servicio 
                           WHEN 'P' THEN 'Pendiente'
                           WHEN 'E' THEN 'En Proceso'
                           WHEN 'C' THEN 'Completado'
                           ELSE 'Desconocido'
                       END as estado_texto
                FROM servicios_orden so
                INNER JOIN ordenes_reparacion o ON so.id_orden = o.id_orden
                INNER JOIN clientes c ON o.id_cliente = c.id_cliente
                INNER JOIN tipos_servicio ts ON so.id_tipo_servicio = ts.id_tipo_servicio
                ORDER BY so.id_servicio_orden DESC";
        $data = self::fetchArray($sql);

        http_response_code(200);
        echo json_encode([
            'codigo' => 1,
            'mensaje' => 'Servicios de orden obtenidos correctamente',
            'data' => $data
        ]);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'codigo' => 0,
            'mensaje' => 'Error al obtener los servicios de orden',
            'detalle' => $e->getMessage()
        ]);
    }
}

    public static function EliminarAPI(){
        try {
            $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

            $sql_verificar = "SELECT so.id_servicio_orden, ts.nombre_servicio, o.numero_orden
                             FROM servicios_orden so
                             INNER JOIN tipos_servicio ts ON so.id_tipo_servicio = ts.id_tipo_servicio
                             INNER JOIN ordenes_reparacion o ON so.id_orden = o.id_orden
                             WHERE so.id_servicio_orden = $id";
            $servicio = self::fetchFirst($sql_verificar);
            
            if (!$servicio) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El servicio no existe'
                ]);
                return;
            }

            // Verificar si está en una venta
            $ventas_asociadas = self::VentasAsociadasServicio($id);
            
            if ($ventas_asociadas > 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se puede eliminar el servicio porque está asociado a una venta',
                    'detalle' => "Hay $ventas_asociadas venta(s) registrada(s) para este servicio."
                ]);
                return;
            }

            self::EliminarServicio($id);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'El servicio ha sido eliminado correctamente',
                'detalle' => "Servicio '{$servicio['nombre_servicio']}' de la orden '{$servicio['numero_orden']}' eliminado exitosamente"
            ]);
        
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al eliminar el servicio',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function ordenesDisponiblesAPI(){
        try {
            $sql = "SELECT o.id_orden, o.numero_orden, o.motivo_ingreso, c.nombre as cliente_nombre
                    FROM ordenes_reparacion o 
                    INNER JOIN clientes c ON o.id_cliente = c.id_cliente
                    WHERE o.estado_orden IN ('R', 'P', 'E') 
                    ORDER BY o.numero_orden";
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

    public static function tiposServicioDisponiblesAPI(){
        try {
            $sql = "SELECT id_tipo_servicio, nombre_servicio, precio_base, tiempo_estimado_horas
                    FROM tipos_servicio 
                    WHERE activo = 'T' 
                    ORDER BY nombre_servicio";
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Tipos de servicio disponibles obtenidos correctamente',
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener los tipos de servicio disponibles',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // Métodos auxiliares
    public static function EliminarServicio($id)
    {
        $sql = "DELETE FROM servicios_orden WHERE id_servicio_orden = $id";
        return self::SQL($sql);
    }

    public static function VentasAsociadasServicio($id_servicio_orden)
    {
        $sql = "SELECT COUNT(*) as total FROM detalle_venta_servicios dvs
                INNER JOIN servicios_orden so ON dvs.id_orden = so.id_orden
                WHERE so.id_servicio_orden = $id_servicio_orden";
        $resultado = self::fetchFirst($sql);
        return $resultado['total'] ?? 0;
    }

    public static function ObtenerServiciosPorOrden($id_orden)
    {
        $sql = "SELECT so.*, ts.nombre_servicio, ts.precio_base
                FROM servicios_orden so
                INNER JOIN tipos_servicio ts ON so.id_tipo_servicio = ts.id_tipo_servicio
                WHERE so.id_orden = $id_orden
                ORDER BY so.id_servicio_orden";
        return self::fetchArray($sql);
    }
}