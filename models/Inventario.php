<?php

namespace Model;

class Inventario extends ActiveRecord {

    public static $tabla = 'inventario';
    public static $columnasDB = [
        'id_modelo',
        'codigo_producto',
        'imei',
        'estado_producto',
        'precio_compra',
        'precio_venta',
        'stock_cantidad',
        'ubicacion',
        'disponible',
        'usuario_registro'
    ];

    public static $idTabla = 'id_inventario';
    public $id_inventario;
    public $id_modelo;
    public $codigo_producto;
    public $imei;
    public $estado_producto;
    public $precio_compra;
    public $precio_venta;
    public $stock_cantidad;
    public $ubicacion;
    public $fecha_ingreso;
    public $disponible;
    public $usuario_registro;

    public function __construct($args = []){
        $this->id_inventario = $args['id_inventario'] ?? null;
        $this->id_modelo = $args['id_modelo'] ?? '';
        $this->codigo_producto = $args['codigo_producto'] ?? '';
        $this->imei = $args['imei'] ?? '';
        $this->estado_producto = $args['estado_producto'] ?? 'N';
        $this->precio_compra = $args['precio_compra'] ?? 0;
        $this->precio_venta = $args['precio_venta'] ?? 0;
        $this->stock_cantidad = $args['stock_cantidad'] ?? 1;
        $this->ubicacion = $args['ubicacion'] ?? '';
        $this->fecha_ingreso = $args['fecha_ingreso'] ?? null;
        $this->disponible = $args['disponible'] ?? 'T';
        $this->usuario_registro = $args['usuario_registro'] ?? null;
    }
}