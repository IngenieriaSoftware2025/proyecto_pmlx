<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use Model\OrdenesReparacion;
use MVC\Router;

class OrdenReparacionController extends ActiveRecord{
    public static function renderizarPagina(Router $router){
        $router->render('ordenes_reparacion/index', []);
    }

    //Guardar Orden de Reparacion
    public static function guardarAPI(){
        // Debug temporal
        error_log("=== DATOS RECIBIDOS EN GUARDAR API ===");
        error_log(print_r($_POST, true));
        
        // Verificar que los campos obligatorios existan
        if (!isset($_POST['numero_orden']) || empty($_POST['numero_orden'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El número de orden es obligatorio'
            ]);
            return;
        }

        if (!isset($_POST['motivo_ingreso']) || empty($_POST['motivo_ingreso'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El motivo de ingreso es obligatorio'
            ]);
            return;
        }

        if (!isset($_POST['id_cliente']) || empty($_POST['id_cliente'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Debe seleccionar un cliente'
            ]);
            return;
        }

        if (!isset($_POST['id_marca']) || empty($_POST['id_marca'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Debe seleccionar una marca'
            ]);
            return;
        }
        
        // getHeadersApi(); // Comentado temporalmente

        $_POST['numero_orden'] = htmlspecialchars($_POST['numero_orden']);
        $_POST['motivo_ingreso'] = htmlspecialchars($_POST['motivo_ingreso']);
        
        $cantidad_numero_orden = strlen($_POST['numero_orden']);
        $cantidad_motivo = strlen($_POST['motivo_ingreso']);

        if ($cantidad_numero_orden < 3){
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El número de orden debe tener al menos 3 caracteres'
            ]);
            return;
        }

        if ($cantidad_motivo < 5){
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El motivo de ingreso debe tener al menos 5 caracteres'
            ]);
            return;
        }

        // Verificar que el número de orden no esté duplicado
        $numero_repetido = trim($_POST['numero_orden']);
        $sql_verificar_numero = "SELECT id_orden FROM ordenes_reparacion 
                               WHERE TRIM(numero_orden) = " . self::$db->quote($numero_repetido);
        $numero_existe = self::fetchFirst($sql_verificar_numero);
        
        if ($numero_existe) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Ya existe una orden con este número'
            ]);
            return;
        }

        // Validar que el cliente exista
        $id_cliente = (int)$_POST['id_cliente'];
        $sql_verificar_cliente = "SELECT id_cliente FROM clientes WHERE id_cliente = $id_cliente AND activo = 'T'";
        $cliente_existe = self::fetchFirst($sql_verificar_cliente);
        
        if (!$cliente_existe) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El cliente seleccionado no existe o está inactivo'
            ]);
            return;
        }

        // Validar que la marca exista
        $id_marca = (int)$_POST['id_marca'];
        $sql_verificar_marca = "SELECT id_marca FROM marcas WHERE id_marca = $id_marca AND activo = 'T'";
        $marca_existe = self::fetchFirst($sql_verificar_marca);
        
        if (!$marca_existe) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La marca seleccionada no existe o está inactiva'
            ]);
            return;
        }

        // Validar trabajador si se asigna
        if (!empty($_POST['id_trabajador_asignado'])) {
            $id_trabajador = (int)$_POST['id_trabajador_asignado'];
            $sql_verificar_trabajador = "SELECT id_trabajador FROM trabajadores WHERE id_trabajador = $id_trabajador AND activo = 'T'";
            $trabajador_existe = self::fetchFirst($sql_verificar_trabajador);
            
            if (!$trabajador_existe) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El trabajador seleccionado no existe o está inactivo'
                ]);
                return;
            }
        }

        $_POST['modelo_dispositivo'] = htmlspecialchars($_POST['modelo_dispositivo']);
        $_POST['imei_dispositivo'] = htmlspecialchars($_POST['imei_dispositivo']);
        $_POST['descripcion_problema'] = htmlspecialchars($_POST['descripcion_problema']);
        $_POST['observaciones'] = htmlspecialchars($_POST['observaciones']);

        // Convertir fechas al formato correcto para Informix
        $fecha_promesa = null;
        $fecha_entrega = null;
        
        if (!empty($_POST['fecha_promesa_entrega'])) {
            $fecha_promesa = date('Y-m-d', strtotime($_POST['fecha_promesa_entrega']));
        }
        
        if (!empty($_POST['fecha_entrega_real'])) {
            $fecha_entrega = date('Y-m-d', strtotime($_POST['fecha_entrega_real']));
        }

        try {
            $data = new OrdenesReparacion([
                'numero_orden' => $_POST['numero_orden'],
                'id_cliente' => $_POST['id_cliente'],
                'id_marca' => $_POST['id_marca'],
                'modelo_dispositivo' => $_POST['modelo_dispositivo'],
                'imei_dispositivo' => $_POST['imei_dispositivo'],
                'motivo_ingreso' => $_POST['motivo_ingreso'],
                'descripcion_problema' => $_POST['descripcion_problema'],
                'estado_orden' => $_POST['estado_orden'] ?? 'R',
                'fecha_promesa_entrega' => null, // Temporalmente deshabilitado
                'id_trabajador_asignado' => $_POST['id_trabajador_asignado'] ?: null,
                'observaciones' => $_POST['observaciones'],
                'usuario_recepcion' => 1 // Por ahora usamos el usuario admin, después se puede cambiar por sesión
            ]);

            $crear = $data->crear();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'La orden de reparación ha sido registrada con éxito'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al guardar la orden de reparación',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    //Buscar Ordenes de Reparacion
    public static function buscarAPI(){
        try {
            $sql = "SELECT o.id_orden, o.numero_orden, o.motivo_ingreso, o.descripcion_problema,
                           o.modelo_dispositivo, o.imei_dispositivo, o.estado_orden, 
                           o.fecha_recepcion, o.fecha_promesa_entrega, o.fecha_entrega_real,
                           o.observaciones,
                           c.nombre as cliente_nombre, c.telefono as cliente_telefono,
                           m.nombre_marca,
                           CASE o.estado_orden 
                               WHEN 'R' THEN 'Recibido'
                               WHEN 'P' THEN 'En Proceso'
                               WHEN 'E' THEN 'Esperando Repuestos'
                               WHEN 'T' THEN 'Terminado'
                               WHEN 'N' THEN 'Entregado'
                               WHEN 'C' THEN 'Cancelado'
                               ELSE 'Desconocido'
                           END as estado_texto,
                           t.id_trabajador,
                           u.nombre_completo as trabajador_nombre,
                           ur.nombre_completo as usuario_recepcion_nombre
                    FROM ordenes_reparacion o 
                    INNER JOIN clientes c ON o.id_cliente = c.id_cliente
                    INNER JOIN marcas m ON o.id_marca = m.id_marca
                    LEFT JOIN trabajadores t ON o.id_trabajador_asignado = t.id_trabajador
                    LEFT JOIN usuarios u ON t.id_usuario = u.id_usuario
                    INNER JOIN usuarios ur ON o.usuario_recepcion = ur.id_usuario
                    ORDER BY o.fecha_recepcion DESC";
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Órdenes de reparación obtenidas correctamente',
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener las órdenes de reparación',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    //Modificar Orden de Reparacion
    public static function modificarAPI(){
        // getHeadersApi(); // Comentado temporalmente

        $id = $_POST['id_orden'];

        $_POST['numero_orden'] = htmlspecialchars($_POST['numero_orden']);
        $_POST['motivo_ingreso'] = htmlspecialchars($_POST['motivo_ingreso']);
        
        $cantidad_numero_orden = strlen($_POST['numero_orden']);
        $cantidad_motivo = strlen($_POST['motivo_ingreso']);

        if ($cantidad_numero_orden < 3) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El número de orden debe tener al menos 3 caracteres'
            ]);
            return;
        }

        if ($cantidad_motivo < 5) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El motivo de ingreso debe tener al menos 5 caracteres'
            ]);
            return;
        }

        // Verificar que el número de orden no esté duplicado (excluyendo el actual)
        $numero_repetido = trim($_POST['numero_orden']);
        $sql_verificar_numero = "SELECT id_orden FROM ordenes_reparacion 
                               WHERE TRIM(numero_orden) = " . self::$db->quote($numero_repetido) . "
                               AND id_orden != " . (int)$id;
        $numero_existe = self::fetchFirst($sql_verificar_numero);
        
        if ($numero_existe) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Ya existe otra orden con este número'
            ]);
            return;
        }

        // Validar que el cliente exista
        $id_cliente = (int)$_POST['id_cliente'];
        $sql_verificar_cliente = "SELECT id_cliente FROM clientes WHERE id_cliente = $id_cliente AND activo = 'T'";
        $cliente_existe = self::fetchFirst($sql_verificar_cliente);
        
        if (!$cliente_existe) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El cliente seleccionado no existe o está inactivo'
            ]);
            return;
        }

        // Validar que la marca exista
        $id_marca = (int)$_POST['id_marca'];
        $sql_verificar_marca = "SELECT id_marca FROM marcas WHERE id_marca = $id_marca AND activo = 'T'";
        $marca_existe = self::fetchFirst($sql_verificar_marca);
        
        if (!$marca_existe) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La marca seleccionada no existe o está inactiva'
            ]);
            return;
        }

        // Validar trabajador si se asigna
        if (!empty($_POST['id_trabajador_asignado'])) {
            $id_trabajador = (int)$_POST['id_trabajador_asignado'];
            $sql_verificar_trabajador = "SELECT id_trabajador FROM trabajadores WHERE id_trabajador = $id_trabajador AND activo = 'T'";
            $trabajador_existe = self::fetchFirst($sql_verificar_trabajador);
            
            if (!$trabajador_existe) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El trabajador seleccionado no existe o está inactivo'
                ]);
                return;
            }
        }

        $_POST['modelo_dispositivo'] = htmlspecialchars($_POST['modelo_dispositivo']);
        $_POST['imei_dispositivo'] = htmlspecialchars($_POST['imei_dispositivo']);
        $_POST['descripcion_problema'] = htmlspecialchars($_POST['descripcion_problema']);
        $_POST['observaciones'] = htmlspecialchars($_POST['observaciones']);

        try {
            $data = OrdenesReparacion::find($id);
            $data->sincronizar([
                'numero_orden' => $_POST['numero_orden'],
                'id_cliente' => $_POST['id_cliente'],
                'id_marca' => $_POST['id_marca'],
                'modelo_dispositivo' => $_POST['modelo_dispositivo'],
                'imei_dispositivo' => $_POST['imei_dispositivo'],
                'motivo_ingreso' => $_POST['motivo_ingreso'],
                'descripcion_problema' => $_POST['descripcion_problema'],
                'estado_orden' => $_POST['estado_orden'],
                'fecha_promesa_entrega' => $_POST['fecha_promesa_entrega'] ?: null,
                'fecha_entrega_real' => $_POST['fecha_entrega_real'] ?: null,
                'id_trabajador_asignado' => $_POST['id_trabajador_asignado'] ?: null,
                'observaciones' => $_POST['observaciones']
            ]);

            $data->actualizar();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'La información de la orden ha sido modificada con éxito'
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al modificar la orden de reparación',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    //Eliminar Orden de Reparacion
    public static function EliminarAPI(){
        try {
            $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

            $sql_verificar = "SELECT id_orden, numero_orden FROM ordenes_reparacion WHERE id_orden = $id";
            $orden = self::fetchFirst($sql_verificar);
            
            if (!$orden) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'La orden de reparación no existe'
                ]);
                return;
            }

            // Verificar si tiene servicios asociados
            $servicios_asociados = self::ServiciosAsociadosOrden($id);
            
            if ($servicios_asociados > 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se puede eliminar la orden porque tiene servicios asociados',
                    'detalle' => "Hay $servicios_asociados servicio(s) registrado(s) para esta orden."
                ]);
                return;
            }

            // Verificar si está en una venta
            $ventas_asociadas = self::VentasAsociadasOrden($id);
            
            if ($ventas_asociadas > 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se puede eliminar la orden porque está asociada a una venta',
                    'detalle' => "Hay $ventas_asociadas venta(s) registrada(s) para esta orden."
                ]);
                return;
            }

            self::EliminarOrden($id);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'La orden de reparación ha sido eliminada correctamente',
                'detalle' => "Orden '{$orden['numero_orden']}' eliminada exitosamente"
            ]);
        
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al eliminar la orden de reparación',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // APIs adicionales para obtener datos de los select
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

    public static function trabajadoresDisponiblesAPI(){
        try {
            $sql = "SELECT t.id_trabajador, u.nombre_completo, t.especialidad 
                    FROM trabajadores t 
                    INNER JOIN usuarios u ON t.id_usuario = u.id_usuario 
                    WHERE t.activo = 'T' AND u.activo = 'T' 
                    ORDER BY u.nombre_completo";
            $data = self::fetchArray($sql);

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
                'mensaje' => 'Error al obtener los trabajadores disponibles',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // Métodos auxiliares
    public static function EliminarOrden($id)
    {
        $sql = "DELETE FROM ordenes_reparacion WHERE id_orden = $id";
        return self::SQL($sql);
    }

    public static function ServiciosAsociadosOrden($id_orden)
    {
        $sql = "SELECT COUNT(*) as total FROM servicios_orden WHERE id_orden = $id_orden";
        $resultado = self::fetchFirst($sql);
        return $resultado['total'] ?? 0;
    }

    public static function VentasAsociadasOrden($id_orden)
    {
        $sql = "SELECT COUNT(*) as total FROM detalle_venta_servicios WHERE id_orden = $id_orden";
        $resultado = self::fetchFirst($sql);
        return $resultado['total'] ?? 0;
    }

    public static function ObtenerOrdenesActivas()
    {
        $sql = "SELECT * FROM ordenes_reparacion ORDER BY fecha_recepcion DESC";
        return self::fetchArray($sql);
    }
}