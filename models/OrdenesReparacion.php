<?php

namespace Model;

class OrdenesReparacion extends ActiveRecord {

    public static $tabla = 'ordenes_reparacion';
    public static $columnasDB = [
        'numero_orden',
        'id_cliente',
        'id_marca',
        'modelo_dispositivo',
        'imei_dispositivo',
        'motivo_ingreso',
        'descripcion_problema',
        'estado_orden',
        'id_trabajador_asignado',
        'observaciones',
        'usuario_recepcion'
    ];

    public static $idTabla = 'id_orden';
    public $id_orden;
    public $numero_orden;
    public $id_cliente;
    public $id_marca;
    public $modelo_dispositivo;
    public $imei_dispositivo;
    public $motivo_ingreso;
    public $descripcion_problema;
    public $estado_orden;
    public $fecha_recepcion;
    public $fecha_promesa_entrega;
    public $fecha_entrega_real;
    public $id_trabajador_asignado;
    public $observaciones;
    public $usuario_recepcion;

    public function __construct($args = []){
        $this->id_orden = $args['id_orden'] ?? null;
        $this->numero_orden = $args['numero_orden'] ?? '';
        $this->id_cliente = $args['id_cliente'] ?? null;
        $this->id_marca = $args['id_marca'] ?? null;
        $this->modelo_dispositivo = $args['modelo_dispositivo'] ?? '';
        $this->imei_dispositivo = $args['imei_dispositivo'] ?? '';
        $this->motivo_ingreso = $args['motivo_ingreso'] ?? '';
        $this->descripcion_problema = $args['descripcion_problema'] ?? '';
        $this->estado_orden = $args['estado_orden'] ?? 'R';
        $this->fecha_recepcion = $args['fecha_recepcion'] ?? null;
        $this->fecha_promesa_entrega = $args['fecha_promesa_entrega'] ?? null;
        $this->fecha_entrega_real = $args['fecha_entrega_real'] ?? null;
        $this->id_trabajador_asignado = $args['id_trabajador_asignado'] ?? null;
        $this->observaciones = $args['observaciones'] ?? '';
        $this->usuario_recepcion = $args['usuario_recepcion'] ?? null;
    }
}