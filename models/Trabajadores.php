<?php

namespace Model;

class Trabajadores extends ActiveRecord {

    public static $tabla = 'trabajadores';
    public static $columnasDB = [
        'id_usuario',
        'especialidad',
        'activo'
    ];

    public static $idTabla = 'id_trabajador';
    public $id_trabajador;
    public $id_usuario;
    public $especialidad;
    public $activo;
    public $fecha_registro;

    public function __construct($args = []){
        $this->id_trabajador = $args['id_trabajador'] ?? null;
        $this->id_usuario = $args['id_usuario'] ?? '';
        $this->especialidad = $args['especialidad'] ?? '';
        $this->activo = $args['activo'] ?? 'T';
        $this->fecha_registro = $args['fecha_registro'] ?? null;
    }
}