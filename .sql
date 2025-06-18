database lopez03

-- 1. TABLA DE ROLES DE USUARIOS
CREATE TABLE roles (
    id_rol SERIAL PRIMARY KEY,
    nombre_rol VARCHAR(50) NOT NULL UNIQUE,
    descripcion VARCHAR(200),
    fecha_creacion DATETIME YEAR TO SECOND DEFAULT CURRENT YEAR TO SECOND
);

-- 2. TABLA DE USUARIOS
CREATE TABLE usuarios (
    id_usuario SERIAL PRIMARY KEY,
    nombre_usuario VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nombre_completo VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    telefono VARCHAR(20),
    id_rol INT NOT NULL,
    activo CHAR(1) DEFAULT 'T',
    fecha_creacion DATETIME YEAR TO SECOND DEFAULT CURRENT YEAR TO SECOND,
    ultimo_acceso DATETIME YEAR TO SECOND,
    FOREIGN KEY (id_rol) REFERENCES roles(id_rol)
);


-- 3. TABLA DE MARCAS DE CELULARES

CREATE TABLE marcas (
    id_marca SERIAL PRIMARY KEY,
    nombre_marca VARCHAR(50) NOT NULL UNIQUE,
    descripcion VARCHAR(200),
    activo CHAR(1) DEFAULT 'T',
    fecha_creacion DATETIME YEAR TO SECOND DEFAULT CURRENT YEAR TO SECOND,
    usuario_creacion INT,
    FOREIGN KEY (usuario_creacion) REFERENCES usuarios(id_usuario)
);

-- 4. TABLA DE MODELOS DE CELULARES

CREATE TABLE modelos (
    id_modelo SERIAL PRIMARY KEY,
    id_marca INT NOT NULL,
    nombre_modelo VARCHAR(100) NOT NULL,
    especificaciones VARCHAR(100),
    precio_referencia DECIMAL(10,2),
    activo CHAR(1) DEFAULT 'T',
    fecha_creacion DATETIME YEAR TO SECOND DEFAULT CURRENT YEAR TO SECOND,
    FOREIGN KEY (id_marca) REFERENCES marcas(id_marca),
    UNIQUE (id_marca, nombre_modelo)
);

-- 5. TABLA DE CLIENTES

CREATE TABLE clientes (
    id_cliente SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    nit VARCHAR(20),
    telefono VARCHAR(20),
    celular VARCHAR(20),
    email VARCHAR(100),
    direccion VARCHAR(200),
    fecha_registro DATETIME YEAR TO SECOND DEFAULT CURRENT YEAR TO SECOND,
    activo CHAR(1) DEFAULT 'T',
    usuario_registro INT,
    FOREIGN KEY (usuario_registro) REFERENCES usuarios(id_usuario)
);

-- 6. TABLA DE INVENTARIO DE CELULARES

CREATE TABLE inventario (
    id_inventario SERIAL PRIMARY KEY,
    id_modelo INT NOT NULL,
    codigo_producto VARCHAR(50) UNIQUE,
    imei VARCHAR(20) UNIQUE,
    estado_producto CHAR(1) DEFAULT 'N', -- N=nuevo, U=usado, R=reacondicionado
    precio_compra DECIMAL(10,2),
    precio_venta DECIMAL(10,2) NOT NULL,
    stock_cantidad INT DEFAULT 1,
    ubicacion VARCHAR(100),
    fecha_ingreso DATETIME YEAR TO SECOND DEFAULT CURRENT YEAR TO SECOND,
    disponible CHAR(1) DEFAULT 'T',
    usuario_registro INT,
    FOREIGN KEY (id_modelo) REFERENCES modelos(id_modelo),
    FOREIGN KEY (usuario_registro) REFERENCES usuarios(id_usuario)
);

-- 7. TABLA DE TIPOS DE SERVICIOS DE REPARACIÓN
CREATE TABLE tipos_servicio (
    id_tipo_servicio SERIAL PRIMARY KEY,
    nombre_servicio VARCHAR(100) NOT NULL UNIQUE,
    descripcion VARCHAR(200),
    precio_base DECIMAL(10,2),
    tiempo_estimado_horas INT,
    activo CHAR(1) DEFAULT 'T',
    fecha_creacion DATETIME YEAR TO SECOND DEFAULT CURRENT YEAR TO SECOND
);

-- 8. TABLA DE TRABAJADORES/TÉCNICOS
CREATE TABLE trabajadores (
    id_trabajador SERIAL PRIMARY KEY,
    id_usuario INT NOT NULL,
    especialidad VARCHAR(100),
    activo CHAR(1) DEFAULT 'T',
    fecha_registro DATETIME YEAR TO SECOND DEFAULT CURRENT YEAR TO SECOND,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

-- 9. TABLA DE ÓRDENES DE REPARACIÓN
CREATE TABLE ordenes_reparacion (
    id_orden SERIAL PRIMARY KEY,
    numero_orden VARCHAR(20) UNIQUE NOT NULL,
    id_cliente INT NOT NULL,
    id_marca INT NOT NULL,
    modelo_dispositivo VARCHAR(100),
    imei_dispositivo VARCHAR(20),
    motivo_ingreso VARCHAR(100) NOT NULL,
    descripcion_problema VARCHAR(100),
    estado_orden CHAR(1) DEFAULT 'R', -- R=recibido, P=en_proceso, E=esperando_repuestos, T=terminado, N=entregado, C=cancelado
    fecha_recepcion DATETIME YEAR TO SECOND DEFAULT CURRENT YEAR TO SECOND,
    fecha_promesa_entrega DATE,
    fecha_entrega_real DATETIME YEAR TO SECOND,
    id_trabajador_asignado INT,
    observaciones VARCHAR(100),
    usuario_recepcion INT NOT NULL,
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente),
    FOREIGN KEY (id_marca) REFERENCES marcas(id_marca),
    FOREIGN KEY (id_trabajador_asignado) REFERENCES trabajadores(id_trabajador),
    FOREIGN KEY (usuario_recepcion) REFERENCES usuarios(id_usuario)
);

-- 10. TABLA DE SERVICIOS POR ORDEN (Detalle de servicios a realizar)
CREATE TABLE servicios_orden (
    id_servicio_orden SERIAL PRIMARY KEY,
    id_orden INT NOT NULL,
    id_tipo_servicio INT NOT NULL,
    precio_servicio DECIMAL(10,2) NOT NULL,
    estado_servicio CHAR(1) DEFAULT 'P', -- P=pendiente, E=en_proceso, C=completado
    fecha_inicio DATETIME YEAR TO SECOND,
    fecha_completado DATETIME YEAR TO SECOND,
    observaciones VARCHAR(100),
    FOREIGN KEY (id_orden) REFERENCES ordenes_reparacion(id_orden),
    FOREIGN KEY (id_tipo_servicio) REFERENCES tipos_servicio(id_tipo_servicio)
);

-- 11. TABLA DE VENTAS
CREATE TABLE ventas (
    id_venta SERIAL PRIMARY KEY,
    numero_factura VARCHAR(20) UNIQUE NOT NULL,
    id_cliente INT,
    tipo_venta CHAR(1) NOT NULL, -- P=producto, S=servicio
    subtotal DECIMAL(10,2) NOT NULL,
    descuento DECIMAL(10,2) DEFAULT 0,
    impuestos DECIMAL(10,2) DEFAULT 0,
    total DECIMAL(10,2) NOT NULL,
    fecha_venta DATETIME YEAR TO SECOND DEFAULT CURRENT YEAR TO SECOND,
    metodo_pago CHAR(1) DEFAULT 'E', -- E=efectivo, T=tarjeta, R=transferencia, C=credito
    estado_venta CHAR(1) DEFAULT 'C', -- C=completada, P=pendiente, N=cancelada
    id_usuario_vendedor INT NOT NULL,
    observaciones VARCHAR(100),
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente),
    FOREIGN KEY (id_usuario_vendedor) REFERENCES usuarios(id_usuario)
);

-- 12. TABLA DE DETALLE DE VENTAS DE PRODUCTOS
CREATE TABLE detalle_venta_productos (
    id_detalle SERIAL PRIMARY KEY,
    id_venta INT NOT NULL,
    id_inventario INT NOT NULL,
    cantidad INT NOT NULL DEFAULT 1,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_venta) REFERENCES ventas(id_venta),
    FOREIGN KEY (id_inventario) REFERENCES inventario(id_inventario)
);

-- 13. TABLA DE DETALLE DE VENTAS DE SERVICIOS (Reparaciones cobradas)
CREATE TABLE detalle_venta_servicios (
    id_detalle_servicio SERIAL PRIMARY KEY,
    id_venta INT NOT NULL,
    id_orden INT NOT NULL,
    descripcion_servicio VARCHAR(100),
    precio_servicio DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_venta) REFERENCES ventas(id_venta),
    FOREIGN KEY (id_orden) REFERENCES ordenes_reparacion(id_orden)
);

-- 14. TABLA DE HISTORIAL DE MOVIMIENTOS DE INVENTARIO
CREATE TABLE movimientos_inventario (
    id_movimiento SERIAL PRIMARY KEY,
    id_inventario INT NOT NULL,
    tipo_movimiento CHAR(1) NOT NULL, -- E=entrada, S=salida, A=ajuste
    cantidad INT NOT NULL,
    motivo VARCHAR(100) NOT NULL,
    referencia_documento VARCHAR(50), -- número de venta, compra, etc.
    fecha_movimiento DATETIME YEAR TO SECOND DEFAULT CURRENT YEAR TO SECOND,
    usuario_movimiento INT NOT NULL,
    observaciones VARCHAR(100),
    FOREIGN KEY (id_inventario) REFERENCES inventario(id_inventario),
    FOREIGN KEY (usuario_movimiento) REFERENCES usuarios(id_usuario)
);

-- 15. TABLA DE CONFIGURACIÓN DEL SISTEMA
CREATE TABLE configuracion_sistema (
    id_config SERIAL PRIMARY KEY,
    clave_config VARCHAR(50) NOT NULL UNIQUE,
    valor_config VARCHAR(100),
    descripcion VARCHAR(100),
    fecha_modificacion DATETIME YEAR TO SECOND DEFAULT CURRENT YEAR TO SECOND,
    usuario_modificacion INT,
    FOREIGN KEY (usuario_modificacion) REFERENCES usuarios(id_usuario)
);

-- INSERCIÓN DE DATOS BÁSICOS

-- Insertar roles básicos
INSERT INTO roles (nombre_rol, descripcion) VALUES 
('Administrador', 'Acceso completo a todos los módulos del sistema');
INSERT INTO roles (nombre_rol, descripcion) VALUES 
('Empleado', 'Acceso a ventas, reparaciones e inventario');
INSERT INTO roles (nombre_rol, descripcion) VALUES 
('Técnico', 'Acceso principalmente a módulo de reparaciones');


-- Insertar algunas marcas populares
INSERT INTO marcas (nombre_marca, descripcion, usuario_creacion) VALUES 
('Samsung', 'Marca surcoreana de electrónicos', 1);
INSERT INTO marcas (nombre_marca, descripcion, usuario_creacion) VALUES 
('Apple', 'Marca estadounidense de tecnología', 1);
INSERT INTO marcas (nombre_marca, descripcion, usuario_creacion) VALUES 
('Huawei', 'Marca china de telecomunicaciones', 1);
INSERT INTO marcas (nombre_marca, descripcion, usuario_creacion) VALUES 
('Xiaomi', 'Marca china de electrónicos', 1);
INSERT INTO marcas (nombre_marca, descripcion, usuario_creacion) VALUES 
('Motorola', 'Marca de telecomunicaciones', 1);

-- Insertar tipos de servicios comunes
INSERT INTO tipos_servicio (nombre_servicio, descripcion, precio_base, tiempo_estimado_horas) VALUES 
('Cambio de Pantalla', 'Reemplazo de pantalla táctil', 150.00, 2);
INSERT INTO tipos_servicio (nombre_servicio, descripcion, precio_base, tiempo_estimado_horas) VALUES 
('Formateo', 'Restauración de fábrica del dispositivo', 50.00, 1);
INSERT INTO tipos_servicio (nombre_servicio, descripcion, precio_base, tiempo_estimado_horas) VALUES 
('Cambio de Batería', 'Reemplazo de batería', 80.00, 1);
INSERT INTO tipos_servicio (nombre_servicio, descripcion, precio_base, tiempo_estimado_horas) VALUES 
('Reparación de Carga', 'Reparación del puerto de carga', 100.00, 3);
INSERT INTO tipos_servicio (nombre_servicio, descripcion, precio_base, tiempo_estimado_horas) VALUES 
('Liberación', 'Liberación de operadora', 75.00, 1);
INSERT INTO tipos_servicio (nombre_servicio, descripcion, precio_base, tiempo_estimado_horas) VALUES 
('Cambio de Carcasa', 'Reemplazo de carcasa trasera', 120.00, 2);

-- Insertar configuraciones básicas
INSERT INTO configuracion_sistema (clave_config, valor_config, descripcion, usuario_modificacion) VALUES 
('empresa_nombre', 'TechCell Repairs', 'Nombre de la empresa', 1);
INSERT INTO configuracion_sistema (clave_config, valor_config, descripcion, usuario_modificacion) VALUES 
('empresa_direccion', 'Zona 1, Guatemala City', 'Dirección de la empresa', 1);
INSERT INTO configuracion_sistema (clave_config, valor_config, descripcion, usuario_modificacion) VALUES 
('empresa_telefono', '2234-5678', 'Teléfono de la empresa', 1);
INSERT INTO configuracion_sistema (clave_config, valor_config, descripcion, usuario_modificacion) VALUES 
('impuesto_iva', '12', 'Porcentaje de IVA', 1);
INSERT INTO configuracion_sistema (clave_config, valor_config, descripcion, usuario_modificacion) VALUES 
('moneda_simbolo', 'Q', 'Símbolo de la moneda local', 1);


-----iniciar sesion
 
 CREATE TABLE usuario_login2025 (
    usu_id SERIAL PRIMARY KEY,
    usu_nombre VARCHAR(50),
    usu_codigo INTEGER,
    usu_password VARCHAR(150),
    usu_situacion SMALLINT DEFAULT 1
);

CREATE TABLE rol_login2025 (
    rol_id SERIAL PRIMARY KEY,
    rol_nombre VARCHAR(75),
    rol_nombre_ct VARCHAR(25),
    rol_situacion SMALLINT DEFAULT 1
);

CREATE TABLE permiso_login2025 (
    permiso_id SERIAL PRIMARY KEY,
    permiso_usuario INTEGER,
    permiso_rol INTEGER,
    permiso_situacion SMALLINT DEFAULT 1,
    FOREIGN KEY (permiso_usuario) REFERENCES usuario_login2025 (usu_id),
    FOREIGN KEY (permiso_rol) REFERENCES rol_login2025 (rol_id)
);
