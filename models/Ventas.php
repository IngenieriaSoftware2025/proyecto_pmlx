<?php

namespace Model;

class Ventas extends ActiveRecord {

    public static $tabla = 'ventas';
    public static $columnasDB = [
        'numero_factura',
        'id_cliente', 
        'tipo_venta',
        'subtotal',
        'descuento',
        'impuestos',
        'total',
        'metodo_pago',
        'estado_venta',
        'id_usuario_vendedor',
        'observaciones'
    ];

    public static $idTabla = 'id_venta';
    
    public $id_venta;
    public $numero_factura;
    public $id_cliente;
    public $tipo_venta;
    public $subtotal;
    public $descuento;
    public $impuestos;
    public $total;
    public $fecha_venta;
    public $metodo_pago;
    public $estado_venta;
    public $id_usuario_vendedor;
    public $observaciones;

    public function __construct($args = []) {
        $this->id_venta = $args['id_venta'] ?? null;
        $this->numero_factura = $args['numero_factura'] ?? '';
        $this->id_cliente = $args['id_cliente'] ?? null;
        $this->tipo_venta = $args['tipo_venta'] ?? 'P'; // P=productos, S=servicios
        $this->subtotal = $args['subtotal'] ?? 0.00;
        $this->descuento = $args['descuento'] ?? 0.00;
        $this->impuestos = $args['impuestos'] ?? 0.00;
        $this->total = $args['total'] ?? 0.00;
        $this->fecha_venta = $args['fecha_venta'] ?? null;
        $this->metodo_pago = $args['metodo_pago'] ?? 'E'; // E=efectivo, T=tarjeta, R=transferencia, C=credito
        $this->estado_venta = $args['estado_venta'] ?? 'C'; // C=completada, P=pendiente, N=cancelada
        $this->id_usuario_vendedor = $args['id_usuario_vendedor'] ?? null;
        $this->observaciones = $args['observaciones'] ?? '';
    }

    public function validar() {
        if (!$this->numero_factura) {
            self::$alertas['error'][] = 'El número de factura es obligatorio';
        }

        if (!$this->tipo_venta) {
            self::$alertas['error'][] = 'El tipo de venta es obligatorio';
        }

        if ($this->total <= 0) {
            self::$alertas['error'][] = 'El total debe ser mayor que 0';
        }

        if (!$this->id_usuario_vendedor) {
            self::$alertas['error'][] = 'El vendedor es obligatorio';
        }

        return self::$alertas;
    }
}