<?php

namespace Model;

class Roles extends ActiveRecord {

    public static $tabla = 'roles';
    public static $columnasDB = [
        'nombre_rol',
        'descripcion'
    ];

    public static $idTabla = 'id_rol';
    public $id_rol;
    public $nombre_rol;
    public $descripcion;
    public $fecha_creacion;

    public function __construct($args = []){
        $this->id_rol = $args['id_rol'] ?? null;
        $this->nombre_rol = $args['nombre_rol'] ?? '';
        $this->descripcion = $args['descripcion'] ?? '';
        $this->fecha_creacion = $args['fecha_creacion'] ?? null;
    }
}