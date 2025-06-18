<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use MVC\Router;

class EstadisticasController extends ActiveRecord{
    
    public static function renderizarPagina(Router $router){
        $router->render('estadisticas/index', []);
    }

    // 1. GRÁFICA DE VENTAS (Productos más vendidos)
    public static function buscarAPI(){
        try {
            $sql = "SELECT 
                        m.nombre_marca || ' ' || mo.nombre_modelo as producto, 
                        mo.id_modelo as pro_id,
                        COUNT(dvp.id_detalle) as cantidad
                    FROM detalle_venta_productos dvp
                    INNER JOIN inventario i ON dvp.id_inventario = i.id_inventario
                    INNER JOIN modelos mo ON i.id_modelo = mo.id_modelo
                    INNER JOIN marcas m ON mo.id_marca = m.id_marca
                    INNER JOIN ventas v ON dvp.id_venta = v.id_venta
                    WHERE v.estado_venta = 'C'
                    GROUP BY mo.id_modelo, m.nombre_marca, mo.nombre_modelo
                    ORDER BY cantidad ASC";
            
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Productos vendidos obtenidos correctamente',
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener los productos vendidos',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // 2. GRÁFICA DE MARCAS (Marcas más populares en reparaciones)
    public static function buscarMarcasAPI(){
        try {
            $sql = "SELECT 
                        m.nombre_marca as marca, 
                        m.id_marca as marca_id, 
                        COUNT(o.id_orden) as total_reparaciones
                    FROM marcas m
                    INNER JOIN ordenes_reparacion o ON m.id_marca = o.id_marca
                    WHERE m.activo = 'T'
                    GROUP BY m.id_marca, m.nombre_marca
                    ORDER BY total_reparaciones DESC
                    LIMIT 10";
            
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
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // 3. GRÁFICA DE TRABAJADORES (Trabajadores con más órdenes completadas)
    public static function buscarTrabajadoresAPI(){
        try {
            $sql = "SELECT 
                        u.nombre_completo as trabajador, 
                        t.id_trabajador as trabajador_id, 
                        COUNT(o.id_orden) as total_ordenes
                    FROM trabajadores t
                    INNER JOIN usuarios u ON t.id_usuario = u.id_usuario
                    LEFT JOIN ordenes_reparacion o ON t.id_trabajador = o.id_trabajador_asignado 
                        AND o.estado_orden = 'N'
                    WHERE t.activo = 'T' AND u.activo = 'T'
                    GROUP BY t.id_trabajador, u.nombre_completo
                    ORDER BY total_ordenes DESC
                    LIMIT 10";
            
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

    // 4. GRÁFICA DE USUARIOS (Usuarios con más ventas realizadas)
    public static function buscarUsuariosAPI(){
        try {
            $sql = "SELECT 
                        u.nombre_completo as usuario, 
                        u.id_usuario as usuario_id, 
                        COUNT(v.id_venta) as total_ventas
                    FROM usuarios u
                    LEFT JOIN ventas v ON u.id_usuario = v.id_usuario_vendedor 
                        AND v.estado_venta = 'C'
                    WHERE u.activo = 'T'
                    GROUP BY u.id_usuario, u.nombre_completo
                    ORDER BY total_ventas DESC
                    LIMIT 10";
            
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
}