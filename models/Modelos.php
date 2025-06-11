<?php

namespace Model;

class Modelos extends ActiveRecord {

    public static $tabla = 'modelos';
    public static $columnasDB = [
        'id_marca',
        'nombre_modelo',
        'especificaciones',
        'precio_referencia',
        'activo'
    ];

    public static $idTabla = 'id_modelo';
    public $id_modelo;
    public $id_marca;
    public $nombre_modelo;
    public $especificaciones;
    public $precio_referencia;
    public $activo;
    public $fecha_creacion;

    public function __construct($args = []){
        $this->id_modelo = $args['id_modelo'] ?? null;
        $this->id_marca = $args['id_marca'] ?? '';
        $this->nombre_modelo = $args['nombre_modelo'] ?? '';
        $this->especificaciones = $args['especificaciones'] ?? '';
        $this->precio_referencia = $args['precio_referencia'] ?? 0;
        $this->activo = $args['activo'] ?? 'T';
        $this->fecha_creacion = $args['fecha_creacion'] ?? null;
    }
}