<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use Model\OrdenesReparacion;
use MVC\Router;

class OrdenesReparacionController extends ActiveRecord{
    public static function renderizarPagina(Router $router){
        $router->render('ordenes_reparacion/index', []);
    }

    //Guardar Orden de Reparación
    public static function guardarAPI(){
        // getHeadersApi(); // Descomenta si usas validación de headers

        // Generar número de orden único
        $numero_orden = self::generarNumeroOrden();

        // Validar cliente
        $cliente_validado = filter_var($_POST['id_cliente'], FILTER_VALIDATE_INT);
        if ($cliente_validado === false || $cliente_validado <= 0){
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Debe seleccionar un cliente válido'
            ]);
            return;
        }

        // Verificar que el cliente existe
        $sql_verificar_cliente = "SELECT id_cliente FROM clientes WHERE id_cliente = $cliente_validado AND activo = 'T'";
        $cliente_existe = self::fetchFirst($sql_verificar_cliente);
        
        if (!$cliente_existe) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El cliente seleccionado no existe o está inactivo'
            ]);
            return;
        }

        // Validar marca
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

        // Validar trabajador asignado (opcional)
        $trabajador_validado = null;
        if (!empty($_POST['id_trabajador_asignado'])) {
            $trabajador_validado = filter_var($_POST['id_trabajador_asignado'], FILTER_VALIDATE_INT);
            if ($trabajador_validado === false || $trabajador_validado <= 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El trabajador asignado no es válido'
                ]);
                return;
            }

            // Verificar que el trabajador existe
            $sql_verificar_trabajador = "SELECT id_trabajador FROM trabajadores WHERE id_trabajador = $trabajador_validado AND activo = 'T'";
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

        // Validar campos requeridos
        $_POST['modelo_dispositivo'] = htmlspecialchars($_POST['modelo_dispositivo']);
        $_POST['motivo_ingreso'] = htmlspecialchars($_POST['motivo_ingreso']);
        $_POST['descripcion_problema'] = htmlspecialchars($_POST['descripcion_problema']);
        $_POST['observaciones'] = htmlspecialchars($_POST['observaciones']);
        $_POST['imei_dispositivo'] = htmlspecialchars($_POST['imei_dispositivo']);

        if (strlen($_POST['motivo_ingreso']) < 5) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El motivo de ingreso debe tener al menos 5 caracteres'
            ]);
            return;
        }

        // Validar fecha promesa (opcional)
        $fecha_promesa = null;
        if (!empty($_POST['fecha_promesa_entrega'])) {
            $fecha_promesa = $_POST['fecha_promesa_entrega'];
            // Verificar que la fecha sea futura
            if (strtotime($fecha_promesa) < strtotime(date('Y-m-d'))) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'La fecha promesa de entrega debe ser futura'
                ]);
                return;
            }
        }

        try {
            $data = new OrdenesReparacion([
                'numero_orden' => $numero_orden,
                'id_cliente' => $cliente_validado,
                'id_marca' => $marca_validada,
                'modelo_dispositivo' => $_POST['modelo_dispositivo'],
                'imei_dispositivo' => $_POST['imei_dispositivo'],
                'motivo_ingreso' => $_POST['motivo_ingreso'],
                'descripcion_problema' => $_POST['descripcion_problema'],
                'estado_orden' => 'R', // Recibido por defecto
                'fecha_promesa_entrega' => $fecha_promesa,
                'id_trabajador_asignado' => $trabajador_validado,
                'observaciones' => $_POST['observaciones'],
                'usuario_recepcion' => 1 // Por ahora usamos usuario fijo
            ]);

            $crear = $data->crear();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => "Orden de reparación creada con éxito. Número: $numero_orden",
                'numero_orden' => $numero_orden
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

    //Buscar Órdenes de Reparación
    public static function buscarAPI(){
        try {
            $sql = "SELECT o.*, c.nombre as cliente_nombre, c.telefono as cliente_telefono,
                           m.nombre_marca, t.especialidad as trabajador_especialidad,
                           u.nombre_completo as trabajador_nombre, ur.nombre_completo as usuario_recepcion_nombre
                    FROM ordenes_reparacion o 
                    INNER JOIN clientes c ON o.id_cliente = c.id_cliente
                    INNER JOIN marcas m ON o.id_marca = m.id_marca
                    LEFT JOIN trabajadores t ON o.id_trabajador_asignado = t.id_trabajador
                    LEFT JOIN usuarios u ON t.id_usuario = u.id_usuario
                    LEFT JOIN usuarios ur ON o.usuario_recepcion = ur.id_usuario
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

    //Modificar Orden de Reparación
    public static function modificarAPI(){
        // getHeadersApi(); // Descomenta si usas validación de headers

        $id = $_POST['id_orden'];

        // Validar cliente
        $cliente_validado = filter_var($_POST['id_cliente'], FILTER_VALIDATE_INT);
        if ($cliente_validado === false || $cliente_validado <= 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Debe seleccionar un cliente válido'
            ]);
            return;
        }

        // Validar marca
        $marca_validada = filter_var($_POST['id_marca'], FILTER_VALIDATE_INT);
        if ($marca_validada === false || $marca_validada <= 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Debe seleccionar una marca válida'
            ]);
            return;
        }

        // Validar trabajador asignado (opcional)
        $trabajador_validado = null;
        if (!empty($_POST['id_trabajador_asignado'])) {
            $trabajador_validado = filter_var($_POST['id_trabajador_asignado'], FILTER_VALIDATE_INT);
        }

        // Validar campos
        $_POST['modelo_dispositivo'] = htmlspecialchars($_POST['modelo_dispositivo']);
        $_POST['motivo_ingreso'] = htmlspecialchars($_POST['motivo_ingreso']);
        $_POST['descripcion_problema'] = htmlspecialchars($_POST['descripcion_problema']);
        $_POST['observaciones'] = htmlspecialchars($_POST['observaciones']);
        $_POST['imei_dispositivo'] = htmlspecialchars($_POST['imei_dispositivo']);

        if (strlen($_POST['motivo_ingreso']) < 5) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El motivo de ingreso debe tener al menos 5 caracteres'
            ]);
            return;
        }

        // Validar fecha promesa
        $fecha_promesa = !empty($_POST['fecha_promesa_entrega']) ? $_POST['fecha_promesa_entrega'] : null;

        try {
            $data = OrdenesReparacion::find($id);
            $data->sincronizar([
                'id_cliente' => $cliente_validado,
                'id_marca' => $marca_validada,
                'modelo_dispositivo' => $_POST['modelo_dispositivo'],
                'imei_dispositivo' => $_POST['imei_dispositivo'],
                'motivo_ingreso' => $_POST['motivo_ingreso'],
                'descripcion_problema' => $_POST['descripcion_problema'],
                'estado_orden' => $_POST['estado_orden'],
                'fecha_promesa_entrega' => $fecha_promesa,
                'id_trabajador_asignado' => $trabajador_validado,
                'observaciones' => $_POST['observaciones']
            ]);

            $data->actualizar();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'La orden de reparación ha sido modificada con éxito'
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

    //Cambiar Estado de Orden
    public static function cambiarEstadoAPI(){
        try {
            $id = filter_var($_POST['id_orden'], FILTER_VALIDATE_INT);
            $nuevo_estado = $_POST['nuevo_estado'];

            // Validar estado
            $estados_validos = ['R', 'P', 'E', 'T', 'N', 'C'];
            if (!in_array($nuevo_estado, $estados_validos)) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Estado no válido'
                ]);
                return;
            }

            $data = OrdenesReparacion::find($id);
            $data->estado_orden = $nuevo_estado;

            // Si el estado es "entregado", establecer fecha de entrega
            if ($nuevo_estado === 'N') {
                $data->fecha_entrega_real = date('Y-m-d H:i:s');
            }

            $data->actualizar();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Estado de la orden actualizado correctamente'
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al cambiar el estado',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // Obtener clientes disponibles
    public static function clientesDisponiblesAPI(){
        try {
            $sql = "SELECT id_cliente, nombre, telefono, email FROM clientes WHERE activo = 'T' ORDER BY nombre";
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
                'mensaje' => 'Error al obtener clientes',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // Obtener marcas disponibles
    public static function marcasDisponiblesAPI(){
        try {
            $sql = "SELECT id_marca, nombre_marca FROM marcas WHERE activo = 'T' ORDER BY nombre_marca";
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Marcas obtenidas correctamente',
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener marcas',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // Obtener trabajadores disponibles
    public static function trabajadoresDisponiblesAPI(){
        try {
            $sql = "SELECT t.id_trabajador, t.especialidad, u.nombre_completo
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
                'mensaje' => 'Error al obtener trabajadores',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // Método para generar número de orden único
    private static function generarNumeroOrden() {
        $prefijo = 'ORD-';
        $fecha = date('Y');
        
        // Obtener el último número de orden del año actual
        $sql = "SELECT numero_orden FROM ordenes_reparacion 
                WHERE numero_orden LIKE '$prefijo$fecha%' 
                ORDER BY numero_orden DESC LIMIT 1";
        $ultimo = self::fetchFirst($sql);
        
        if ($ultimo) {
            // Extraer el número secuencial y aumentarlo
            $numero = intval(substr($ultimo['numero_orden'], -4)) + 1;
        } else {
            $numero = 1;
        }
        
        return $prefijo . $fecha . '-' . str_pad($numero, 4, '0', STR_PAD_LEFT);
    }

    // Métodos auxiliares
    public static function ObtenerEstadosOrden() {
        return [
            'R' => 'Recibido',
            'P' => 'En Proceso',
            'E' => 'Esperando Repuestos',
            'T' => 'Terminado',
            'N' => 'Entregado',
            'C' => 'Cancelado'
        ];
    }

    public static function ObtenerOrdenesPorEstado($estado) {
        $sql = "SELECT o.*, c.nombre as cliente_nombre 
                FROM ordenes_reparacion o 
                INNER JOIN clientes c ON o.id_cliente = c.id_cliente
                WHERE o.estado_orden = '$estado' 
                ORDER BY o.fecha_recepcion DESC";
        return self::fetchArray($sql);
    }

    public static function ObtenerOrdenPorNumero($numero_orden) {
        $sql = "SELECT o.*, c.nombre as cliente_nombre, c.telefono as cliente_telefono,
                       m.nombre_marca, u.nombre_completo as trabajador_nombre
                FROM ordenes_reparacion o 
                INNER JOIN clientes c ON o.id_cliente = c.id_cliente
                INNER JOIN marcas m ON o.id_marca = m.id_marca
                LEFT JOIN trabajadores t ON o.id_trabajador_asignado = t.id_trabajador
                LEFT JOIN usuarios u ON t.id_usuario = u.id_usuario
                WHERE o.numero_orden = '$numero_orden'";
        return self::fetchFirst($sql);
    }
}