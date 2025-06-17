<?php

namespace Model;

class DetalleVentaServicios extends ActiveRecord {

    public static $tabla = 'detalle_venta_servicios';
    public static $columnasDB = [
        'id_venta',
        'id_orden',
        'descripcion_servicio',
        'precio_servicio'
    ];

    public static $idTabla = 'id_detalle_servicio';
    
    public $id_detalle_servicio;
    public $id_venta;
    public $id_orden;
    public $descripcion_servicio;
    public $precio_servicio;

    public function __construct($args = []) {
        $this->id_detalle_servicio = $args['id_detalle_servicio'] ?? null;
        $this->id_venta = $args['id_venta'] ?? null;
        $this->id_orden = $args['id_orden'] ?? null;
        $this->descripcion_servicio = $args['descripcion_servicio'] ?? '';
        $this->precio_servicio = $args['precio_servicio'] ?? 0.00;
    }

    public function validar() {
        if (!$this->id_venta) {
            self::$alertas['error'][] = 'La venta es obligatoria';
        }

        if (!$this->id_orden) {
            self::$alertas['error'][] = 'La orden de reparación es obligatoria';
        }

        if (!$this->precio_servicio || $this->precio_servicio <= 0) {
            self::$alertas['error'][] = 'El precio del servicio debe ser mayor que 0';
        }

        return self::$alertas;
    }
}