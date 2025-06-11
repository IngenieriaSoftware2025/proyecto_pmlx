<?php

namespace Model;

class TiposServicio extends ActiveRecord {

    public static $tabla = 'tipos_servicio';
    public static $columnasDB = [
        'nombre_servicio',
        'descripcion',
        'precio_base',
        'tiempo_estimado_horas',
        'activo'
    ];

    public static $idTabla = 'id_tipo_servicio';
    public $id_tipo_servicio;
    public $nombre_servicio;
    public $descripcion;
    public $precio_base;
    public $tiempo_estimado_horas;
    public $activo;
    public $fecha_creacion;

    public function __construct($args = []){
        $this->id_tipo_servicio = $args['id_tipo_servicio'] ?? null;
        $this->nombre_servicio = $args['nombre_servicio'] ?? '';
        $this->descripcion = $args['descripcion'] ?? '';
        $this->precio_base = $args['precio_base'] ?? 0.00;
        $this->tiempo_estimado_horas = $args['tiempo_estimado_horas'] ?? 1;
        $this->activo = $args['activo'] ?? 'T';
        $this->fecha_creacion = $args['fecha_creacion'] ?? null;
    }
}