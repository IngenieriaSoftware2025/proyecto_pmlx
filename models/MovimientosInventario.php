<?php

namespace Model;

class MovimientosInventario extends ActiveRecord {

    public static $tabla = 'movimientos_inventario';
    public static $columnasDB = [
        'id_inventario',
        'tipo_movimiento',
        'cantidad',
        'motivo',
        'referencia_documento',
        'usuario_movimiento',
        'observaciones'
    ];

    public static $idTabla = 'id_movimiento';
    
    public $id_movimiento;
    public $id_inventario;
    public $tipo_movimiento;
    public $cantidad;
    public $motivo;
    public $referencia_documento;
    public $fecha_movimiento;
    public $usuario_movimiento;
    public $observaciones;

    public function __construct($args = []) {
        $this->id_movimiento = $args['id_movimiento'] ?? null;
        $this->id_inventario = $args['id_inventario'] ?? null;
        $this->tipo_movimiento = $args['tipo_movimiento'] ?? 'E'; // E=entrada, S=salida, A=ajuste
        $this->cantidad = $args['cantidad'] ?? 0;
        $this->motivo = $args['motivo'] ?? '';
        $this->referencia_documento = $args['referencia_documento'] ?? '';
        $this->fecha_movimiento = $args['fecha_movimiento'] ?? null;
        $this->usuario_movimiento = $args['usuario_movimiento'] ?? null;
        $this->observaciones = $args['observaciones'] ?? '';
    }

    public function validar() {
        if (!$this->id_inventario) {
            self::$alertas['error'][] = 'El producto de inventario es obligatorio';
        }

        if (!$this->tipo_movimiento || !in_array($this->tipo_movimiento, ['E', 'S', 'A'])) {
            self::$alertas['error'][] = 'El tipo de movimiento debe ser E (Entrada), S (Salida) o A (Ajuste)';
        }

        if (!$this->cantidad || $this->cantidad <= 0) {
            self::$alertas['error'][] = 'La cantidad debe ser mayor que 0';
        }

        if (!$this->motivo) {
            self::$alertas['error'][] = 'El motivo del movimiento es obligatorio';
        }

        if (!$this->usuario_movimiento) {
            self::$alertas['error'][] = 'El usuario que realiza el movimiento es obligatorio';
        }

        return self::$alertas;
    }
}