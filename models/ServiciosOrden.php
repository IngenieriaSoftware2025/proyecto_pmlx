<?php

namespace Model;

class ServiciosOrden extends ActiveRecord {

    public static $tabla = 'servicios_orden';
    public static $columnasDB = [
        'id_orden',
        'id_tipo_servicio',
        'precio_servicio',
        'estado_servicio',
        'fecha_inicio',
        'fecha_completado',
        'observaciones'
    ];

    public static $idTabla = 'id_servicio_orden';
    public $id_servicio_orden;
    public $id_orden;
    public $id_tipo_servicio;
    public $precio_servicio;
    public $estado_servicio;
    public $fecha_inicio;
    public $fecha_completado;
    public $observaciones;

    public function __construct($args = []){
        $this->id_servicio_orden = $args['id_servicio_orden'] ?? null;
        $this->id_orden = $args['id_orden'] ?? null;
        $this->id_tipo_servicio = $args['id_tipo_servicio'] ?? null;
        $this->precio_servicio = $args['precio_servicio'] ?? 0.00;
        $this->estado_servicio = $args['estado_servicio'] ?? 'P';
        $this->fecha_inicio = $args['fecha_inicio'] ?? null;
        $this->fecha_completado = $args['fecha_completado'] ?? null;
        $this->observaciones = $args['observaciones'] ?? '';
    }
}