<?php

namespace Model;

class DetalleVentaProductos extends ActiveRecord {

    public static $tabla = 'detalle_venta_productos';
    public static $columnasDB = [
        'id_venta',
        'id_inventario',
        'cantidad',
        'precio_unitario',
        'subtotal'
    ];

    public static $idTabla = 'id_detalle';
    
    public $id_detalle;
    public $id_venta;
    public $id_inventario;
    public $cantidad;
    public $precio_unitario;
    public $subtotal;

    public function __construct($args = []) {
        $this->id_detalle = $args['id_detalle'] ?? null;
        $this->id_venta = $args['id_venta'] ?? null;
        $this->id_inventario = $args['id_inventario'] ?? null;
        $this->cantidad = $args['cantidad'] ?? 1;
        $this->precio_unitario = $args['precio_unitario'] ?? 0.00;
        $this->subtotal = $args['subtotal'] ?? 0.00;
    }

    public function validar() {
        if (!$this->id_venta) {
            self::$alertas['error'][] = 'La venta es obligatoria';
        }

        if (!$this->id_inventario) {
            self::$alertas['error'][] = 'El producto es obligatorio';
        }

        if (!$this->cantidad || $this->cantidad <= 0) {
            self::$alertas['error'][] = 'La cantidad debe ser mayor que 0';
        }

        if (!$this->precio_unitario || $this->precio_unitario <= 0) {
            self::$alertas['error'][] = 'El precio unitario debe ser mayor que 0';
        }

        return self::$alertas;
    }
}