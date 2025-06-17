import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario } from '../funciones';
import DataTable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";

const FormDetalleVentaProductos = document.getElementById('FormDetalleVentaProductos');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const SelectProducto = document.getElementById('id_inventario');
const InputCantidad = document.getElementById('cantidad');
const InputPrecioUnitario = document.getElementById('precio_unitario');
const InputStockDisponible = document.getElementById('stock_disponible');
const InputPrecioCatalogo = document.getElementById('precio_catalogo');
const InputSubtotalCalculado = document.getElementById('subtotal_calculado');
const InfoProducto = document.getElementById('info_producto');

let datatable;

// Calcular subtotal automáticamente
const calcularSubtotal = () => {
    const cantidad = parseFloat(InputCantidad.value) || 0;
    const precioUnitario = parseFloat(InputPrecioUnitario.value) || 0;
    
    const subtotal = cantidad * precioUnitario;
    InputSubtotalCalculado.value = subtotal.toFixed(2);
}

// Actualizar información del producto seleccionado
const actualizarInfoProducto = () => {
    if (!SelectProducto || !InputStockDisponible || !InputPrecioCatalogo || !InfoProducto) return;
    
    const selectedOption = SelectProducto.options[SelectProducto.selectedIndex];
    
    if (selectedOption && selectedOption.value) {
        const stock = selectedOption.dataset.stock;
        const precio = selectedOption.dataset.precio;
        const marca = selectedOption.dataset.marca;
        const modelo = selectedOption.dataset.modelo;
        const codigo = selectedOption.dataset.codigo;
        const imei = selectedOption.dataset.imei;
        const estado = selectedOption.dataset.estado;
        
        // Actualizar campos informativos
        InputStockDisponible.value = stock || '0';
        InputPrecioCatalogo.value = precio ? parseFloat(precio).toFixed(2) : '0.00';
        
        // Sugerir precio del catálogo si el campo está vacío
        if (!InputPrecioUnitario.value && precio) {
            InputPrecioUnitario.value = parseFloat(precio).toFixed(2);
        }
        
        // Mostrar información del producto
        InfoProducto.innerHTML = `
            <div class="row">
                <div class="col-6">
                    <strong>Marca:</strong> ${marca}<br>
                    <strong>Modelo:</strong> ${modelo}<br>
                    <strong>Estado:</strong> ${estado}
                </div>
                <div class="col-6">
                    <strong>Código:</strong> ${codigo}<br>
                    ${imei ? `<strong>IMEI:</strong> ${imei}<br>` : ''}
                    <strong>Stock:</strong> ${stock} unidades
                </div>
            </div>
        `;
        
        // Recalcular subtotal
        calcularSubtotal();
    } else {
        // Limpiar campos informativos
        InputStockDisponible.value = '';
        InputPrecioCatalogo.value = '';
        InfoProducto.innerHTML = '<i class="bi bi-info-circle"></i> Seleccione un producto para ver su información';
    }
}

// Cargar ventas disponibles
const CargarVentas = async () => {
    try {
        console.log('🔄 Cargando ventas disponibles...');
        const respuesta = await fetch('http://localhost:9002/proyecto_pmlx/detalle_venta_productos/ventasDisponiblesAPI');
        
        if (!respuesta.ok) {
            throw new Error(`HTTP error! status: ${respuesta.status}`);
        }
        
        const datos = await respuesta.json();
        const { codigo, data, mensaje } = datos;

        const selectVenta = document.getElementById('id_venta');
        if (!selectVenta) {
            console.error('❌ No se encontró el elemento select #id_venta');
            return;
        }

        if (codigo === 1 && data && Array.isArray(data)) {
            selectVenta.innerHTML = '<option value="">Seleccione una venta</option>';
            
            data.forEach(venta => {
                const option = document.createElement('option');
                option.value = venta.id_venta;
                const cliente = venta.cliente_nombre || 'Cliente general';
                const fecha = new Date(venta.fecha_venta).toLocaleDateString('es-GT');
                option.textContent = `${venta.numero_factura} - ${cliente} - Q${parseFloat(venta.total).toFixed(2)} (${fecha})`;
                selectVenta.appendChild(option);
            });
            
            console.log('✅ Ventas cargadas:', data.length);
        } else {
            console.error('❌ Error en la respuesta de ventas:', mensaje || 'Código de respuesta inválido');
        }
    } catch (error) {
        console.error('❌ Error cargando ventas:', error);
    }
}

// Cargar productos disponibles
const CargarProductos = async () => {
    try {
        console.log('🔄 Cargando productos disponibles...');
        const respuesta = await fetch('http://localhost:9002/proyecto_pmlx/detalle_venta_productos/productosDisponiblesAPI');
        
        if (!respuesta.ok) {
            throw new Error(`HTTP error! status: ${respuesta.status}`);
        }
        
        const datos = await respuesta.json();
        const { codigo, data, mensaje } = datos;

        const selectProducto = document.getElementById('id_inventario');
        if (!selectProducto) {
            console.error('❌ No se encontró el elemento select #id_inventario');
            return;
        }

        if (codigo === 1 && data && Array.isArray(data)) {
            selectProducto.innerHTML = '<option value="">Seleccione un producto</option>';
            
            data.forEach(producto => {
                const option = document.createElement('option');
                option.value = producto.id_inventario;
                
                // Agregar datos como data attributes
                option.dataset.stock = producto.stock_cantidad;
                option.dataset.precio = producto.precio_venta;
                option.dataset.marca = producto.nombre_marca;
                option.dataset.modelo = producto.nombre_modelo;
                option.dataset.codigo = producto.codigo_producto;
                option.dataset.imei = producto.imei || '';
                option.dataset.estado = producto.estado_producto_texto;
                
                option.textContent = `${producto.nombre_marca} ${producto.nombre_modelo} - Q${parseFloat(producto.precio_venta).toFixed(2)} (Stock: ${producto.stock_cantidad})`;
                selectProducto.appendChild(option);
            });
            
            console.log('✅ Productos cargados:', data.length);
        } else {
            console.error('❌ Error en la respuesta de productos:', mensaje || 'Código de respuesta inválido');
        }
    } catch (error) {
        console.error('❌ Error cargando productos:', error);
    }
}

// Guardar detalle de venta
const GuardarDetalleVenta = async (event) => {
    event.preventDefault();
    console.log('🔄 Iniciando proceso de guardado...');
    
    BtnGuardar.disabled = true;

    // Validaciones básicas
    const id_venta = document.getElementById('id_venta').value;
    const id_inventario = document.getElementById('id_inventario').value;
    const cantidad = document.getElementById('cantidad').value;
    const precio_unitario = document.getElementById('precio_unitario').value;

    console.log('📋 Datos del detalle:');
    console.log('- ID Venta:', id_venta);
    console.log('- ID Producto:', id_inventario);
    console.log('- Cantidad:', cantidad);
    console.log('- Precio unitario:', precio_unitario);

    if (!id_venta || id_venta === '') {
        await Swal.fire({
            position: "center",
            icon: "warning",
            title: "CAMPO REQUERIDO",
            text: "Debe seleccionar una venta",
            showConfirmButton: true,
        });
        BtnGuardar.disabled = false;
        return;
    }

    if (!id_inventario || id_inventario === '') {
        await Swal.fire({
            position: "center",
            icon: "warning",
            title: "CAMPO REQUERIDO",
            text: "Debe seleccionar un producto",
            showConfirmButton: true,
        });
        BtnGuardar.disabled = false;
        return;
    }

    if (!cantidad || cantidad === '' || parseInt(cantidad) <= 0) {
        await Swal.fire({
            position: "center",
            icon: "warning",
            title: "CANTIDAD INVÁLIDA",
            text: "La cantidad debe ser mayor que 0",
            showConfirmButton: true,
        });
        BtnGuardar.disabled = false;
        return;
    }

    if (!precio_unitario || precio_unitario === '' || parseFloat(precio_unitario) <= 0) {
        await Swal.fire({
            position: "center",
            icon: "warning",
            title: "PRECIO INVÁLIDO",
            text: "El precio unitario debe ser mayor que 0",
            showConfirmButton: true,
        });
        BtnGuardar.disabled = false;
        return;
    }

    // Validar stock disponible
    const stockDisponible = parseInt(InputStockDisponible.value) || 0;
    const cantidadSolicitada = parseInt(cantidad);
    
    if (cantidadSolicitada > stockDisponible) {
        await Swal.fire({
            position: "center",
            icon: "warning",
            title: "STOCK INSUFICIENTE",
            text: `Stock disponible: ${stockDisponible}, Cantidad solicitada: ${cantidadSolicitada}`,
            showConfirmButton: true,
        });
        BtnGuardar.disabled = false;
        return;
    }

    console.log('✅ Validaciones pasadas, preparando envío...');

    const body = new FormData(FormDetalleVentaProductos);

    // Debug: mostrar todos los datos que se enviarán
    console.log('📤 Datos a enviar:');
    for (let [key, value] of body.entries()) {
        console.log(`- ${key}: ${value}`);
    }

    try {
        console.log('🌐 Enviando petición al servidor...');
        const respuesta = await fetch('http://localhost:9002/proyecto_pmlx/detalle_venta_productos/guardarAPI', {
            method: 'POST',
            body
        });

        console.log('📡 Respuesta del servidor:', respuesta.status, respuesta.statusText);

        if (!respuesta.ok) {
            try {
                const errorData = await respuesta.json();
                console.error('❌ Error del servidor:', errorData);
                
                await Swal.fire({
                    position: "center",
                    icon: "error",
                    title: "Error del Servidor",
                    text: errorData.mensaje || `Error ${respuesta.status}: ${respuesta.statusText}`,
                    showConfirmButton: true,
                });
            } catch (e) {
                console.error('❌ Error sin respuesta JSON:', e);
                await Swal.fire({
                    position: "center",
                    icon: "error",
                    title: "Error de Comunicación",
                    text: `Error ${respuesta.status}: ${respuesta.statusText}`,
                    showConfirmButton: true,
                });
            }
            BtnGuardar.disabled = false;
            return;
        }

        const datos = await respuesta.json();
        console.log('📊 Respuesta procesada:', datos);
        
        const { codigo, mensaje } = datos;

        if (codigo == 1) {
            console.log('✅ Detalle guardado exitosamente');
            await Swal.fire({
                position: "center",
                icon: "success",
                title: "Éxito",
                text: mensaje,
                showConfirmButton: true,
            });

            limpiarTodo();
            BuscarDetallesVenta();
            
            // Recargar productos para actualizar stock
            CargarProductos();
        } else {
            console.log('⚠️ Error en la lógica de negocio:', mensaje);
            await Swal.fire({
                position: "center",
                icon: "error",
                title: "Error",
                text: mensaje,
                showConfirmButton: true,
            });
        }
    } catch (error) {
        console.error('💥 Error de red o excepción:', error);
        await Swal.fire({
            position: "center",
            icon: "error",
            title: "Error de Conexión",
            text: "No se pudo conectar con el servidor. Verifique su conexión.",
            showConfirmButton: true,
        });
    }
    
    BtnGuardar.disabled = false;
}

// Buscar detalles de venta
const BuscarDetallesVenta = async () => {
    try {
        console.log('🔍 Buscando detalles de venta...');
        const respuesta = await fetch('http://localhost:9002/proyecto_pmlx/detalle_venta_productos/buscarAPI');
        const datos = await respuesta.json();
        const { codigo, mensaje, data } = datos;

        console.log('📊 Respuesta de búsqueda:', datos);

        if (codigo == 1) {
            if (datatable) {
                datatable.clear().draw();
                if (data && data.length > 0) {
                    datatable.rows.add(data).draw();
                    console.log('✅ Detalles cargados en la tabla:', data.length);
                } else {
                    console.log('ℹ️ No hay detalles para mostrar');
                }
            }
        } else {
            console.error('❌ Error al buscar detalles:', mensaje);
        }
    } catch (error) {
        console.error('❌ Error en la búsqueda:', error);
    }
}

// Inicializar DataTable
const inicializarDataTable = () => {
    if (document.getElementById('TableDetalleVentaProductos')) {
        console.log('🗂️ Inicializando DataTable...');
        datatable = new DataTable('#TableDetalleVentaProductos', {
            language: lenguaje,
            data: [],
            columns: [
                {
                    title: 'No.',
                    data: 'id_detalle',
                    render: (data, type, row, meta) => meta.row + 1
                },
                { 
                    title: 'Factura', 
                    data: 'numero_factura',
                    render: (data, type, row) => {
                        const cliente = row.cliente_nombre || 'Cliente general';
                        return `<strong>${data}</strong><br><small class="text-muted">${cliente}</small>`;
                    }
                },
                { 
                    title: 'Producto', 
                    data: 'nombre_marca',
                    render: (data, type, row) => {
                        return `<strong>${data} ${row.nombre_modelo}</strong><br><small class="text-muted">Código: ${row.codigo_producto}</small>`;
                    }
                },
                { 
                    title: 'Cantidad', 
                    data: 'cantidad',
                    render: (data) => {
                        return `<span class="badge bg-primary">${data}</span>`;
                    }
                },
                { 
                    title: 'Precio Unit.', 
                    data: 'precio_unitario',
                    render: (data) => {
                        return `Q${parseFloat(data).toFixed(2)}`;
                    }
                },
                { 
                    title: 'Subtotal', 
                    data: 'subtotal',
                    render: (data) => {
                        return `<strong>Q${parseFloat(data).toFixed(2)}</strong>`;
                    }
                },
                { 
                    title: 'Estado Venta', 
                    data: 'estado_venta_texto',
                    render: (data, type, row) => {
                        let badgeClass = 'bg-secondary';
                        switch(row.estado_venta) {
                            case 'C': badgeClass = 'bg-success'; break;
                            case 'P': badgeClass = 'bg-warning'; break;
                            case 'N': badgeClass = 'bg-danger'; break;
                        }
                        return `<span class="badge ${badgeClass}">${data}</span>`;
                    }
                },
                { 
                    title: 'Fecha Venta', 
                    data: 'fecha_venta',
                    render: (data) => {
                        if (data) {
                            const fecha = new Date(data);
                            return fecha.toLocaleDateString('es-GT');
                        }
                        return '';
                    }
                },
                {
                    title: 'Acciones',
                    data: 'id_detalle',
                    orderable: false,
                    render: (data, type, row) => {
                        // Solo mostrar acciones si la venta no está cancelada
                        if (row.estado_venta === 'N') {
                            return '<span class="text-muted">Venta cancelada</span>';
                        }
                        
                        return `
                            <button class="btn btn-warning btn-sm modificar" 
                                data-id="${data}"
                                data-id_venta="${row.id_venta || ''}"
                                data-id_inventario="${row.id_inventario || ''}"
                                data-cantidad="${row.cantidad || '1'}"
                                data-precio_unitario="${row.precio_unitario || '0'}"
                                data-subtotal="${row.subtotal || '0'}">
                                <i class="bi bi-pencil"></i> Modificar
                            </button>
                            <button class="btn btn-danger btn-sm eliminar" 
                                data-id="${data}"
                                data-producto="${row.nombre_marca} ${row.nombre_modelo}"
                                data-factura="${row.numero_factura}">
                                <i class="bi bi-trash"></i> Eliminar
                            </button>
                        `;
                    }
                }
            ]
        });
        console.log('✅ DataTable inicializado');
    } else {
        console.error('❌ No se encontró el elemento #TableDetalleVentaProductos');
    }
}

// Llenar formulario para edición
const llenarFormulario = (event) => {
    const datos = event.currentTarget.dataset;
    
    console.log('📝 Llenando formulario con datos:', datos);

    // Llenar campos básicos
    document.getElementById('id_detalle').value = datos.id || '';
    document.getElementById('cantidad').value = datos.cantidad || '1';
    document.getElementById('precio_unitario').value = datos.precio_unitario || '0';

    // Cargar selects y establecer valores
    Promise.all([
        CargarVentas(),
        CargarProductos()
    ]).then(() => {
        setTimeout(() => {
            const selectVenta = document.getElementById('id_venta');
            const selectProducto = document.getElementById('id_inventario');
            
            if (selectVenta && datos.id_venta) {
                selectVenta.value = datos.id_venta;
                console.log('🔄 Venta seleccionada:', datos.id_venta);
            }
            
            if (selectProducto && datos.id_inventario) {
                selectProducto.value = datos.id_inventario;
                console.log('🔄 Producto seleccionado:', datos.id_inventario);
                
                // Actualizar información del producto después de seleccionar
                actualizarInfoProducto();
            }
            
            // Recalcular subtotal
            calcularSubtotal();
        }, 500);
    });

    // Cambiar botones
    if (BtnGuardar) BtnGuardar.classList.add('d-none');
    if (BtnModificar) BtnModificar.classList.remove('d-none');

    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Modificar detalle de venta
const ModificarDetalleVenta = async (event) => {
    event.preventDefault();
    console.log('🔄 Iniciando modificación...');
    
    BtnModificar.disabled = true;

    const cantidad = document.getElementById('cantidad').value;
    const precio_unitario = document.getElementById('precio_unitario').value;

    if (!cantidad || cantidad === '' || parseInt(cantidad) <= 0) {
        await Swal.fire({
            position: "center",
            icon: "warning",
            title: "CANTIDAD INVÁLIDA",
            text: "La cantidad debe ser mayor que 0",
            showConfirmButton: true,
        });
        BtnModificar.disabled = false;
        return;
    }

    if (!precio_unitario || precio_unitario === '' || parseFloat(precio_unitario) <= 0) {
        await Swal.fire({
            position: "center",
            icon: "warning",
            title: "PRECIO INVÁLIDO",
            text: "El precio unitario debe ser mayor que 0",
            showConfirmButton: true,
        });
        BtnModificar.disabled = false;
        return;
    }

    const body = new FormData(FormDetalleVentaProductos);

    // Debug: mostrar datos a modificar
    console.log('📤 Datos a modificar:');
    for (let [key, value] of body.entries()) {
        console.log(`- ${key}: ${value}`);
    }

    try {
        const respuesta = await fetch('http://localhost:9002/proyecto_pmlx/detalle_venta_productos/modificarAPI', {
            method: 'POST',
            body
        });

        const datos = await respuesta.json();
        console.log('📊 Respuesta de modificación:', datos);
        
        const { codigo, mensaje } = datos;

        if (codigo == 1) {
            console.log('✅ Detalle modificado exitosamente');
            await Swal.fire({
                position: "center",
                icon: "success",
                title: "Éxito",
                text: mensaje,
                showConfirmButton: true,
            });

            limpiarTodo();
            BuscarDetallesVenta();
            CargarProductos(); // Recargar para actualizar stock
        } else {
            console.log('⚠️ Error al modificar:', mensaje);
            await Swal.fire({
                position: "center",
                icon: "error",
                title: "Error",
                text: mensaje,
                showConfirmButton: true,
            });
        }
    } catch (error) {
        console.error('❌ Error modificando:', error);
        await Swal.fire({
            position: "center",
            icon: "error",
            title: "Error de Conexión",
            text: "No se pudo conectar con el servidor.",
            showConfirmButton: true,
        });
    }
    
    BtnModificar.disabled = false;
}

// Eliminar detalle de venta
const EliminarDetalleVenta = async (e) => {
    const idDetalle = e.currentTarget.dataset.id;
    const nombreProducto = e.currentTarget.dataset.producto;
    const numeroFactura = e.currentTarget.dataset.factura;

    console.log('🗑️ Intentando eliminar detalle:', idDetalle, nombreProducto, numeroFactura);

    const AlertaConfirmarEliminar = await Swal.fire({
        position: "center",
        icon: "question",
        title: "¿Desea eliminar este producto?",
        text: `El producto "${nombreProducto}" será eliminado de la factura "${numeroFactura}"`,
        showConfirmButton: true,
        confirmButtonText: 'Sí, eliminar',
        confirmButtonColor: '#dc3545',
        cancelButtonText: 'No, Cancelar',
        showCancelButton: true
    });

    if (AlertaConfirmarEliminar.isConfirmed) {
        try {
            console.log('🌐 Enviando petición de eliminación...');
            const consulta = await fetch(`http://localhost:9002/proyecto_pmlx/detalle_venta_productos/eliminar?id=${idDetalle}`);
            const respuesta = await consulta.json();
            const { codigo, mensaje } = respuesta;

            console.log('📊 Respuesta de eliminación:', respuesta);

            if (codigo == 1) {
                console.log('✅ Detalle eliminado exitosamente');
                await Swal.fire({
                    position: "center",
                    icon: "success",
                    title: "Éxito",
                    text: mensaje,
                    showConfirmButton: true,
                });

                BuscarDetallesVenta();
                CargarProductos(); // Recargar para actualizar stock
            } else {
                console.log('⚠️ Error al eliminar:', mensaje);
                await Swal.fire({
                    position: "center",
                    icon: "error",
                    title: "Error",
                    text: mensaje,
                    showConfirmButton: true,
                });
            }
        } catch (error) {
            console.error('❌ Error eliminando:', error);
            await Swal.fire({
                position: "center",
                icon: "error",
                title: "Error de Conexión",
                text: "No se pudo conectar con el servidor.",
                showConfirmButton: true,
            });
        }
    }
}

// Limpiar formulario
const limpiarTodo = () => {
    console.log('🧹 Limpiando formulario...');
    
    if (FormDetalleVentaProductos) {
        FormDetalleVentaProductos.reset();
    }
    if (BtnGuardar) BtnGuardar.classList.remove('d-none');
    if (BtnModificar) BtnModificar.classList.add('d-none');
    
    // Limpiar campos informativos
    if (InputStockDisponible) InputStockDisponible.value = '';
    if (InputPrecioCatalogo) InputPrecioCatalogo.value = '';
    if (InputSubtotalCalculado) InputSubtotalCalculado.value = '';
    if (InfoProducto) InfoProducto.innerHTML = '<i class="bi bi-info-circle"></i> Seleccione un producto para ver su información';
}

// INICIALIZACIÓN
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Inicializando aplicación de detalle de ventas...');
    
    // Verificar que los elementos principales existen
    const elementosRequeridos = [
        'FormDetalleVentaProductos',
        'id_venta',
        'id_inventario',
        'cantidad',
        'precio_unitario',
        'TableDetalleVentaProductos'
    ];
    
    elementosRequeridos.forEach(id => {
        const elemento = document.getElementById(id);
        if (!elemento) {
            console.error(`❌ No se encontró el elemento #${id}`);
        }
    });
    
    // Inicializar DataTable
    inicializarDataTable();
    
    // Cargar datos iniciales
    console.log('🔄 Cargando datos iniciales...');
    CargarVentas();
    CargarProductos();
    
    // Cargar detalles después de un momento
    setTimeout(() => {
        BuscarDetallesVenta();
    }, 1000);
    
    // Event listeners para cálculos automáticos
    if (InputCantidad) {
        InputCantidad.addEventListener('input', calcularSubtotal);
        console.log('✅ Event listener agregado para cantidad');
    }
    if (InputPrecioUnitario) {
        InputPrecioUnitario.addEventListener('input', calcularSubtotal);
        console.log('✅ Event listener agregado para precio unitario');
    }

    // Event listener para actualizar info del producto
    if (SelectProducto) {
        SelectProducto.addEventListener('change', actualizarInfoProducto);
        console.log('✅ Event listener agregado para selección de producto');
    }

    // Event listeners del formulario
    if (FormDetalleVentaProductos) {
        FormDetalleVentaProductos.addEventListener('submit', GuardarDetalleVenta);
        console.log('✅ Event listener agregado para el formulario');
    }

    if (BtnLimpiar) {
        BtnLimpiar.addEventListener('click', limpiarTodo);
        console.log('✅ Event listener agregado para limpiar');
    }

    if (BtnModificar) {
        BtnModificar.addEventListener('click', ModificarDetalleVenta);
        console.log('✅ Event listener agregado para modificar');
    }

    // Event listeners para DataTable
    setTimeout(() => {
        if (datatable) {
            datatable.on('click', '.eliminar', EliminarDetalleVenta);
            datatable.on('click', '.modificar', llenarFormulario);
            console.log('✅ Event listeners agregados para DataTable');
        }
    }, 1500);
    
    console.log('✅ Aplicación de detalle de ventas inicializada completamente');
});