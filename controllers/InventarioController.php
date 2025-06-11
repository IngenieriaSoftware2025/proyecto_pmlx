<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use Model\Inventario;
use MVC\Router;

class InventarioController extends ActiveRecord{
    public static function renderizarPagina(Router $router){
        $router->render('inventario/index', []);
    }

    //Guardar Inventario
    public static function guardarAPI(){
        // getHeadersApi(); // Comentado temporalmente

        $modelo_validado = filter_var($_POST['id_modelo'], FILTER_VALIDATE_INT);
        if ($modelo_validado === false || $modelo_validado <= 0){
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Debe seleccionar un modelo válido'
            ]);
            return;
        }

        // Verificar que el modelo existe
        $sql_verificar_modelo = "SELECT id_modelo FROM modelos WHERE id_modelo = $modelo_validado AND activo = 'T'";
        $modelo_existe = self::fetchFirst($sql_verificar_modelo);
        
        if (!$modelo_existe) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El modelo seleccionado no existe o está inactivo'
            ]);
            return;
        }

        // Validar código de producto si se proporciona
        if (!empty($_POST['codigo_producto'])) {
            $_POST['codigo_producto'] = htmlspecialchars($_POST['codigo_producto']);
            
            // Verificar que el código no esté duplicado
            $codigo_repetido = trim($_POST['codigo_producto']);
            $sql_verificar_codigo = "SELECT id_inventario FROM inventario 
                                    WHERE TRIM(codigo_producto) = " . self::$db->quote($codigo_repetido);
            $codigo_existe = self::fetchFirst($sql_verificar_codigo);
            
            if ($codigo_existe) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Ya existe un producto con este código'
                ]);
                return;
            }
        }

        // Validar IMEI si se proporciona
        if (!empty($_POST['imei'])) {
            $_POST['imei'] = htmlspecialchars($_POST['imei']);
            
            // Verificar que el IMEI no esté duplicado
            $imei_repetido = trim($_POST['imei']);
            $sql_verificar_imei = "SELECT id_inventario FROM inventario 
                                  WHERE TRIM(imei) = " . self::$db->quote($imei_repetido);
            $imei_existe = self::fetchFirst($sql_verificar_imei);
            
            if ($imei_existe) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Ya existe un producto con este IMEI'
                ]);
                return;
            }
        }

        // Validar precios
        $precio_compra = filter_var($_POST['precio_compra'], FILTER_VALIDATE_FLOAT);
        if ($precio_compra === false || $precio_compra < 0){
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El precio de compra debe ser un número válido y no negativo'
            ]);
            return;
        }

        $precio_venta = filter_var($_POST['precio_venta'], FILTER_VALIDATE_FLOAT);
        if ($precio_venta === false || $precio_venta <= 0){
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El precio de venta debe ser un número válido y mayor a cero'
            ]);
            return;
        }

        // Validar cantidad
        $stock_cantidad = filter_var($_POST['stock_cantidad'], FILTER_VALIDATE_INT);
        if ($stock_cantidad === false || $stock_cantidad < 1){
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La cantidad debe ser un número válido y mayor a cero'
            ]);
            return;
        }

        $_POST['ubicacion'] = htmlspecialchars($_POST['ubicacion']);

        try {
            $data = new Inventario([
                'id_modelo' => $modelo_validado,
                'codigo_producto' => $_POST['codigo_producto'],
                'imei' => $_POST['imei'],
                'estado_producto' => $_POST['estado_producto'],
                'precio_compra' => $precio_compra,
                'precio_venta' => $precio_venta,
                'stock_cantidad' => $stock_cantidad,
                'ubicacion' => $_POST['ubicacion'],
                'disponible' => 'T',
                'usuario_registro' => 1 // Por ahora usamos el usuario admin
            ]);

            $crear = $data->crear();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'El producto ha sido agregado al inventario con éxito'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al guardar el producto en inventario',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    //Buscar Inventario
    public static function buscarAPI(){
        try {
            $sql = "SELECT i.id_inventario, i.codigo_producto, i.imei, i.estado_producto, 
                           i.precio_compra, i.precio_venta, i.stock_cantidad, i.ubicacion, 
                           i.fecha_ingreso, i.disponible,
                           m.nombre_modelo, ma.nombre_marca, m.id_modelo,
                           u.nombre_completo as usuario_registro_nombre
                    FROM inventario i 
                    INNER JOIN modelos m ON i.id_modelo = m.id_modelo
                    INNER JOIN marcas ma ON m.id_marca = ma.id_marca
                    LEFT JOIN usuarios u ON i.usuario_registro = u.id_usuario
                    WHERE i.disponible = 'T'
                    ORDER BY ma.nombre_marca, m.nombre_modelo, i.fecha_ingreso DESC";
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Inventario obtenido correctamente',
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener el inventario',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    //Modificar Inventario
    public static function modificarAPI(){
        // getHeadersApi(); // Comentado temporalmente

        $id = $_POST['id_inventario'];

        $modelo_validado = filter_var($_POST['id_modelo'], FILTER_VALIDATE_INT);
        if ($modelo_validado === false || $modelo_validado <= 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Debe seleccionar un modelo válido'
            ]);
            return;
        }

        // Verificar que el modelo existe
        $sql_verificar_modelo = "SELECT id_modelo FROM modelos WHERE id_modelo = $modelo_validado AND activo = 'T'";
        $modelo_existe = self::fetchFirst($sql_verificar_modelo);
        
        if (!$modelo_existe) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El modelo seleccionado no existe o está inactivo'
            ]);
            return;
        }

        // Validar código de producto si se proporciona (excluyendo el actual)
        if (!empty($_POST['codigo_producto'])) {
            $_POST['codigo_producto'] = htmlspecialchars($_POST['codigo_producto']);
            
            $codigo_repetido = trim($_POST['codigo_producto']);
            $sql_verificar_codigo = "SELECT id_inventario FROM inventario 
                                    WHERE TRIM(codigo_producto) = " . self::$db->quote($codigo_repetido) . "
                                    AND id_inventario != " . (int)$id;
            $codigo_existe = self::fetchFirst($sql_verificar_codigo);
            
            if ($codigo_existe) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Ya existe otro producto con este código'
                ]);
                return;
            }
        }

        // Validar IMEI si se proporciona (excluyendo el actual)
        if (!empty($_POST['imei'])) {
            $_POST['imei'] = htmlspecialchars($_POST['imei']);
            
            $imei_repetido = trim($_POST['imei']);
            $sql_verificar_imei = "SELECT id_inventario FROM inventario 
                                  WHERE TRIM(imei) = " . self::$db->quote($imei_repetido) . "
                                  AND id_inventario != " . (int)$id;
            $imei_existe = self::fetchFirst($sql_verificar_imei);
            
            if ($imei_existe) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Ya existe otro producto con este IMEI'
                ]);
                return;
            }
        }

        // Validar precios
        $precio_compra = filter_var($_POST['precio_compra'], FILTER_VALIDATE_FLOAT);
        if ($precio_compra === false || $precio_compra < 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El precio de compra debe ser un número válido y no negativo'
            ]);
            return;
        }

        $precio_venta = filter_var($_POST['precio_venta'], FILTER_VALIDATE_FLOAT);
        if ($precio_venta === false || $precio_venta <= 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El precio de venta debe ser un número válido y mayor a cero'
            ]);
            return;
        }

        // Validar cantidad
        $stock_cantidad = filter_var($_POST['stock_cantidad'], FILTER_VALIDATE_INT);
        if ($stock_cantidad === false || $stock_cantidad < 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La cantidad debe ser un número válido y no negativa'
            ]);
            return;
        }

        $_POST['ubicacion'] = htmlspecialchars($_POST['ubicacion']);

        try {
            $data = Inventario::find($id);
            $data->sincronizar([
                'id_modelo' => $modelo_validado,
                'codigo_producto' => $_POST['codigo_producto'],
                'imei' => $_POST['imei'],
                'estado_producto' => $_POST['estado_producto'],
                'precio_compra' => $precio_compra,
                'precio_venta' => $precio_venta,
                'stock_cantidad' => $stock_cantidad,
                'ubicacion' => $_POST['ubicacion'],
                'disponible' => 'T'
            ]);

            $data->actualizar();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'La información del producto ha sido modificada con éxito'
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al modificar el producto',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    //Eliminar Producto del Inventario
    public static function EliminarAPI(){
        try {
            $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

            $sql_verificar = "SELECT i.id_inventario, m.nombre_modelo, ma.nombre_marca, i.codigo_producto
                             FROM inventario i 
                             INNER JOIN modelos m ON i.id_modelo = m.id_modelo
                             INNER JOIN marcas ma ON m.id_marca = ma.id_marca
                             WHERE i.id_inventario = $id AND i.disponible = 'T'";
            $producto = self::fetchFirst($sql_verificar);
            
            if (!$producto) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El producto no existe o ya está inactivo'
                ]);
                return;
            }

            self::EliminarProducto($id, 'F');

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'El producto ha sido retirado del inventario correctamente',
                'detalle' => "Producto '{$producto['nombre_modelo']}' de {$producto['nombre_marca']} retirado exitosamente"
            ]);
        
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al eliminar el producto',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // MÉTODO CORREGIDO - Modelos por marca
   public static function modelosPorMarcaAPI(){
    try {
        // DEBUG: Imprimir lo que recibimos
        error_log("DEBUG - Parámetros recibidos: " . print_r($_GET, true));
        
        $id_marca = filter_var($_GET['id_marca'] ?? '', FILTER_SANITIZE_NUMBER_INT);
        
        // DEBUG: Imprimir ID procesado
        error_log("DEBUG - ID marca procesado: " . $id_marca);
        
        // Validar que se recibió el parámetro
        if (!$id_marca || $id_marca <= 0) {
            error_log("DEBUG - ID marca no válido");
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de marca no válido o no proporcionado',
                'data' => [],
                'debug' => "ID recibido: " . ($_GET['id_marca'] ?? 'null')
            ]);
            return;
        }
        
        // Verificar que la marca existe y está activa
        $sql_verificar_marca = "SELECT id_marca FROM marcas WHERE id_marca = $id_marca AND activo = 'T'";
        error_log("DEBUG - SQL verificar marca: " . $sql_verificar_marca);
        
        $marca_existe = self::fetchFirst($sql_verificar_marca);
        error_log("DEBUG - Marca existe: " . print_r($marca_existe, true));
        
        if (!$marca_existe) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La marca seleccionada no existe o está inactiva',
                'data' => [],
                'debug' => "Marca ID $id_marca no encontrada"
            ]);
            return;
        }
        
        $sql = "SELECT id_modelo, nombre_modelo 
                FROM modelos 
                WHERE id_marca = $id_marca AND activo = 'T' 
                ORDER BY nombre_modelo";
        
        error_log("DEBUG - SQL modelos: " . $sql);
        
        $data = self::fetchArray($sql);
        
        error_log("DEBUG - Modelos encontrados: " . print_r($data, true));

        http_response_code(200);
        echo json_encode([
            'codigo' => 1,
            'mensaje' => 'Modelos obtenidos correctamente',
            'data' => $data,
            'debug' => [
                'id_marca' => $id_marca,
                'sql' => $sql,
                'total_modelos' => count($data)
            ]
        ]);
    } catch (Exception $e) {
        error_log("DEBUG - Error: " . $e->getMessage());
        http_response_code(400);
        echo json_encode([
            'codigo' => 0,
            'mensaje' => 'Error al obtener los modelos',
            'detalle' => $e->getMessage(),
            'data' => []
        ]);
    }
}

    // NUEVO MÉTODO - Obtener marcas disponibles desde Inventario
    public static function marcasDisponiblesAPI(){
        try {
            $sql = "SELECT id_marca, nombre_marca 
                    FROM marcas 
                    WHERE activo = 'T' 
                    ORDER BY nombre_marca";
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
                'mensaje' => 'Error al obtener las marcas',
                'detalle' => $e->getMessage(),
                'data' => []
            ]);
        }
    }

    // NUEVO MÉTODO - Buscar modelos (para modificación)
    public static function buscarModelosAPI(){
        try {
            $sql = "SELECT m.id_modelo, m.nombre_modelo, m.id_marca, ma.nombre_marca
                    FROM modelos m
                    INNER JOIN marcas ma ON m.id_marca = ma.id_marca
                    WHERE m.activo = 'T' AND ma.activo = 'T'
                    ORDER BY ma.nombre_marca, m.nombre_modelo";
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Modelos encontrados correctamente',
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al buscar modelos',
                'detalle' => $e->getMessage(),
                'data' => []
            ]);
        }
    }

    public static function inventarioDisponibleAPI(){
        try {
            $sql = "SELECT i.id_inventario, i.codigo_producto, i.precio_venta, i.stock_cantidad,
                           m.nombre_modelo, ma.nombre_marca
                    FROM inventario i 
                    INNER JOIN modelos m ON i.id_modelo = m.id_modelo
                    INNER JOIN marcas ma ON m.id_marca = ma.id_marca
                    WHERE i.disponible = 'T' AND i.stock_cantidad > 0
                    ORDER BY ma.nombre_marca, m.nombre_modelo";
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Inventario disponible obtenido correctamente',
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener el inventario disponible',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function EliminarProducto($id, $situacion)
    {
        $sql = "UPDATE inventario SET disponible = '$situacion' WHERE id_inventario = $id";
        return self::SQL($sql);
    }

    public static function ActualizarStock($id_inventario, $cantidad_vendida)
    {
        $sql = "UPDATE inventario SET stock_cantidad = stock_cantidad - $cantidad_vendida 
                WHERE id_inventario = $id_inventario";
        return self::SQL($sql);
    }

    public static function ObtenerInventarioDisponible()
    {
        $sql = "SELECT i.*, m.nombre_modelo, ma.nombre_marca 
                FROM inventario i 
                INNER JOIN modelos m ON i.id_modelo = m.id_modelo
                INNER JOIN marcas ma ON m.id_marca = ma.id_marca
                WHERE i.disponible = 'T' AND i.stock_cantidad > 0 
                ORDER BY ma.nombre_marca, m.nombre_modelo";
        return self::fetchArray($sql);
    }

    public static function ReactivarProducto($id)
    {
        return self::EliminarProducto($id, 'T');
    }
}