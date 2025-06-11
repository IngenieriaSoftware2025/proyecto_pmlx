<?php

namespace Model;

class Clientes extends ActiveRecord {

    public static $tabla = 'clientes';
    public static $columnasDB = [
        'nombre',
        'nit',
        'telefono',
        'celular',
        'email',
        'direccion',
        'activo',
        'usuario_registro'
    ];

    public static $idTabla = 'id_cliente';
    public $id_cliente;
    public $nombre;
    public $nit;
    public $telefono;
    public $celular;
    public $email;
    public $direccion;
    public $fecha_registro;
    public $activo;
    public $usuario_registro;

    public function __construct($args = []){
        $this->id_cliente = $args['id_cliente'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->nit = $args['nit'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->celular = $args['celular'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->direccion = $args['direccion'] ?? '';
        $this->fecha_registro = $args['fecha_registro'] ?? null;
        $this->activo = $args['activo'] ?? 'T';
        $this->usuario_registro = $args['usuario_registro'] ?? null;
    }
}