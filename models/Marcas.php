<?php

namespace Model;

class Marcas extends ActiveRecord {

    public static $tabla = 'marcas';
    public static $columnasDB = [
        'nombre_marca',
        'descripcion',
        'activo',
        'usuario_creacion'
    ];

    public static $idTabla = 'id_marca';
    public $id_marca;
    public $nombre_marca;
    public $descripcion;
    public $activo;
    public $fecha_creacion;
    public $usuario_creacion;

    public function __construct($args = []){
        $this->id_marca = $args['id_marca'] ?? null;
        $this->nombre_marca = $args['nombre_marca'] ?? '';
        $this->descripcion = $args['descripcion'] ?? '';
        $this->activo = $args['activo'] ?? 'T';
        $this->fecha_creacion = $args['fecha_creacion'] ?? null;
        $this->usuario_creacion = $args['usuario_creacion'] ?? null;
    }
}