import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario } from '../funciones';
import DataTable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";

const FormVentas = document.getElementById('FormVentas');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const BtnGenerarFactura = document.getElementById('BtnGenerarFactura');
const InputSubtotal = document.getElementById('subtotal');
const InputDescuento = document.getElementById('descuento');
const InputImpuestos = document.getElementById('impuestos');
const InputTotal = document.getElementById('total');

let datatable;

// Calcular total automáticamente
const calcularTotal = () => {
    const subtotal = parseFloat(InputSubtotal.value) || 0;
    const descuento = parseFloat(InputDescuento.value) || 0;
    const impuestos = parseFloat(InputImpuestos.value) || 0;
    
    const total = subtotal - descuento + impuestos;
    InputTotal.value = total.toFixed(2);
}

// Generar número de factura automático
const generarNumeroFactura = async () => {
    try {
        console.log('🔄 Generando número de factura...');
        const respuesta = await fetch('http://localhost:9002/proyecto_pmlx/ventas/generarNumeroFacturaAPI');
        const datos = await respuesta.json();
        
        if (datos.codigo === 1) {
            document.getElementById('numero_factura').value = datos.numero_factura;
            console.log('✅ Número de factura generado:', datos.numero_factura);
        } else {
            console.error('❌ Error generando número de factura:', datos.mensaje);
        }
    } catch (error) {
        console.error('❌ Error generando número de factura:', error);
    }
}

// Cargar clientes disponibles
const CargarClientes = async () => {
    try {
        console.log('🔄 Cargando clientes...');
        const respuesta = await fetch('http://localhost:9002/proyecto_pmlx/ventas/clientesDisponiblesAPI');
        
        if (!respuesta.ok) {
            throw new Error(`HTTP error! status: ${respuesta.status}`);
        }
        
        const datos = await respuesta.json();
        const { codigo, data, mensaje } = datos;

        const selectCliente = document.getElementById('id_cliente');
        if (!selectCliente) {
            console.error('❌ No se encontró el elemento select #id_cliente');
            return;
        }

        if (codigo === 1 && data && Array.isArray(data)) {
            selectCliente.innerHTML = '<option value="">Cliente general (opcional)</option>';
            
            data.forEach(cliente => {
                const option = document.createElement('option');
                option.value = cliente.id_cliente;
                option.textContent = `${cliente.nombre}${cliente.nit ? ' - NIT: ' + cliente.nit : ''}`;
                selectCliente.appendChild(option);
            });
            
            console.log('✅ Clientes cargados:', data.length);
        } else {
            console.error('❌ Error en la respuesta de clientes:', mensaje || 'Código de respuesta inválido');
        }
    } catch (error) {
        console.error('❌ Error cargando clientes:', error);
    }
}

// Cargar vendedores disponibles
const CargarVendedores = async () => {
    try {
        console.log('🔄 Cargando vendedores...');
        const respuesta = await fetch('http://localhost:9002/proyecto_pmlx/ventas/vendedoresDisponiblesAPI');
        
        if (!respuesta.ok) {
            throw new Error(`HTTP error! status: ${respuesta.status}`);
        }
        
        const datos = await respuesta.json();
        const { codigo, data, mensaje } = datos;

        const selectVendedor = document.getElementById('id_usuario_vendedor');
        if (!selectVendedor) {
            console.error('❌ No se encontró el elemento select #id_usuario_vendedor');
            return;
        }

        if (codigo === 1 && data && Array.isArray(data)) {
            selectVendedor.innerHTML = '<option value="">Seleccione un vendedor</option>';
            
            data.forEach(vendedor => {
                const option = document.createElement('option');
                option.value = vendedor.id_usuario;
                option.textContent = vendedor.nombre_completo;
                selectVendedor.appendChild(option);
            });
            
            console.log('✅ Vendedores cargados:', data.length);
        } else {
            console.error('❌ Error en la respuesta de vendedores:', mensaje || 'Código de respuesta inválido');
        }
    } catch (error) {
        console.error('❌ Error cargando vendedores:', error);
    }
}

// Guardar venta
const GuardarVenta = async (event) => {
    event.preventDefault();
    console.log('🔄 Iniciando proceso de guardado de venta...');
    
    BtnGuardar.disabled = true;

    // Validaciones básicas
    const numero_factura = document.getElementById('numero_factura').value;
    const tipo_venta = document.getElementById('tipo_venta').value;
    const total = document.getElementById('total').value;
    const id_usuario_vendedor = document.getElementById('id_usuario_vendedor').value;

    console.log('📋 Datos de la venta:');
    console.log('- Número factura:', numero_factura);
    console.log('- Tipo venta:', tipo_venta);
    console.log('- Total:', total);
    console.log('- Vendedor:', id_usuario_vendedor);

    if (!numero_factura || numero_factura.trim() === '') {
        await Swal.fire({
            position: "center",
            icon: "warning",
            title: "CAMPO REQUERIDO",
            text: "El número de factura es obligatorio",
            showConfirmButton: true,
        });
        BtnGuardar.disabled = false;
        return;
    }

    if (!tipo_venta) {
        await Swal.fire({
            position: "center",
            icon: "warning",
            title: "CAMPO REQUERIDO",
            text: "Debe seleccionar el tipo de venta",
            showConfirmButton: true,
        });
        BtnGuardar.disabled = false;
        return;
    }

    if (!total || parseFloat(total) <= 0) {
        await Swal.fire({
            position: "center",
            icon: "warning",
            title: "TOTAL INVÁLIDO",
            text: "El total de la venta debe ser mayor que 0",
            showConfirmButton: true,
        });
        BtnGuardar.disabled = false;
        return;
    }

    if (!id_usuario_vendedor) {
        await Swal.fire({
            position: "center",
            icon: "warning",
            title: "CAMPO REQUERIDO",
            text: "Debe seleccionar un vendedor",
            showConfirmButton: true,
        });
        BtnGuardar.disabled = false;
        return;
    }

    console.log('✅ Validaciones pasadas, preparando envío...');

    const body = new FormData(FormVentas);

    // Debug: mostrar todos los datos que se enviarán
    console.log('📤 Datos a enviar:');
    for (let [key, value] of body.entries()) {
        console.log(`- ${key}: ${value}`);
    }

    try {
        console.log('🌐 Enviando petición al servidor...');
        const respuesta = await fetch('http://localhost:9002/proyecto_pmlx/ventas/guardarAPI', {
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
            console.log('✅ Venta guardada exitosamente');
            await Swal.fire({
                position: "center",
                icon: "success",
                title: "Éxito",
                text: mensaje,
                showConfirmButton: true,
            });

            limpiarTodo();
            BuscarVentas();
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

// Buscar ventas
const BuscarVentas = async () => {
    try {
        console.log('🔍 Buscando ventas...');
        const respuesta = await fetch('http://localhost:9002/proyecto_pmlx/ventas/buscarAPI');
        const datos = await respuesta.json();
        const { codigo, mensaje, data } = datos;

        console.log('📊 Respuesta de búsqueda de ventas:', datos);

        if (codigo == 1) {
            if (datatable) {
                datatable.clear().draw();
                if (data && data.length > 0) {
                    datatable.rows.add(data).draw();
                    console.log('✅ Ventas cargadas en la tabla:', data.length);
                } else {
                    console.log('ℹ️ No hay ventas para mostrar');
                }
            }
        } else {
            console.error('❌ Error al buscar ventas:', mensaje);
        }
    } catch (error) {
        console.error('❌ Error en la búsqueda de ventas:', error);
    }
}

// Inicializar DataTable
const inicializarDataTable = () => {
    if (document.getElementById('TableVentas')) {
        console.log('🗂️ Inicializando DataTable de ventas...');
        datatable = new DataTable('#TableVentas', {
            language: lenguaje,
            data: [],
            columns: [
                {
                    title: 'No.',
                    data: 'id_venta',
                    render: (data, type, row, meta) => meta.row + 1
                },
                { 
                    title: 'No. Factura', 
                    data: 'numero_factura',
                    render: (data, type, row) => {
                        return `<strong>${data}</strong>`;
                    }
                },
                { 
                    title: 'Cliente', 
                    data: 'cliente_nombre',
                    render: (data) => {
                        return data || '<span class="text-muted">Cliente general</span>';
                    }
                },
                { 
                    title: 'Tipo', 
                    data: 'tipo_venta_texto',
                    render: (data, type, row) => {
                        let badgeClass = row.tipo_venta === 'P' ? 'bg-info' : 'bg-warning';
                        return `<span class="badge ${badgeClass}">${data}</span>`;
                    }
                },
                { 
                    title: 'Total', 
                    data: 'total',
                    render: (data) => {
                        return `Q${parseFloat(data).toFixed(2)}`;
                    }
                },
                { 
                    title: 'Método Pago', 
                    data: 'metodo_pago_texto'
                },
                { 
                    title: 'Estado', 
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
                    title: 'Fecha', 
                    data: 'fecha_venta',
                    render: (data) => {
                        if (data) {
                            const fecha = new Date(data);
                            return fecha.toLocaleDateString('es-GT') + '<br><small>' + fecha.toLocaleTimeString('es-GT') + '</small>';
                        }
                        return '';
                    }
                },
                { 
                    title: 'Vendedor', 
                    data: 'vendedor_nombre'
                },
                {
                    title: 'Acciones',
                    data: 'id_venta',
                    orderable: false,
                    render: (data, type, row) => {
                        return `
                            <button class="btn btn-warning btn-sm modificar" 
                                data-id="${data}"
                                data-numero_factura="${row.numero_factura || ''}"
                                data-id_cliente="${row.id_cliente || ''}"
                                data-tipo_venta="${row.tipo_venta || 'P'}"
                                data-subtotal="${row.subtotal || '0'}"
                                data-descuento="${row.descuento || '0'}"
                                data-impuestos="${row.impuestos || '0'}"
                                data-total="${row.total || '0'}"
                                data-metodo_pago="${row.metodo_pago || 'E'}"
                                data-estado_venta="${row.estado_venta || 'C'}"
                                data-id_usuario_vendedor="${row.id_usuario_vendedor || ''}"
                                data-observaciones="${(row.observaciones || '').replace(/"/g, '&quot;')}">
                                <i class="bi bi-pencil"></i> Modificar
                            </button>
                            <button class="btn btn-danger btn-sm eliminar" 
                                data-id="${data}"
                                data-factura="${row.numero_factura}">
                                <i class="bi bi-trash"></i> Eliminar
                            </button>
                            <button class="btn btn-info btn-sm ver-detalle" 
                                data-id="${data}"
                                data-factura="${row.numero_factura}">
                                <i class="bi bi-eye"></i> Ver Detalle
                            </button>
                        `;
                    }
                }
            ]
        });
        console.log('✅ DataTable inicializado');
    } else {
        console.error('❌ No se encontró el elemento #TableVentas');
    }
}

// Llenar formulario para edición
const llenarFormulario = (event) => {
    const datos = event.currentTarget.dataset;
    
    console.log('📝 Llenando formulario con datos de venta:', datos);

    // Llenar campos básicos
    document.getElementById('id_venta').value = datos.id || '';
    document.getElementById('numero_factura').value = datos.numero_factura || '';
    document.getElementById('tipo_venta').value = datos.tipo_venta || 'P';
    document.getElementById('subtotal').value = datos.subtotal || '0';
    document.getElementById('descuento').value = datos.descuento || '0';
    document.getElementById('impuestos').value = datos.impuestos || '0';
    document.getElementById('total').value = datos.total || '0';
    document.getElementById('metodo_pago').value = datos.metodo_pago || 'E';
    document.getElementById('estado_venta').value = datos.estado_venta || 'C';
    document.getElementById('observaciones').value = datos.observaciones || '';

    // Cargar selects y establecer valores
    Promise.all([
        CargarClientes(),
        CargarVendedores()
    ]).then(() => {
        setTimeout(() => {
            const selectCliente = document.getElementById('id_cliente');
            const selectVendedor = document.getElementById('id_usuario_vendedor');
            
            if (selectCliente && datos.id_cliente) {
                selectCliente.value = datos.id_cliente;
                console.log('🔄 Cliente seleccionado:', datos.id_cliente);
            }
            
            if (selectVendedor && datos.id_usuario_vendedor) {
                selectVendedor.value = datos.id_usuario_vendedor;
                console.log('🔄 Vendedor seleccionado:', datos.id_usuario_vendedor);
            }
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

// Modificar venta
const ModificarVenta = async (event) => {
    event.preventDefault();
    console.log('🔄 Iniciando modificación de venta...');
    
    BtnModificar.disabled = true;

    const total = document.getElementById('total').value;

    if (!total || parseFloat(total) <= 0) {
        await Swal.fire({
            position: "center",
            icon: "warning",
            title: "TOTAL INVÁLIDO",
            text: "El total de la venta debe ser mayor que 0",
            showConfirmButton: true,
        });
        BtnModificar.disabled = false;
        return;
    }

    const body = new FormData(FormVentas);

    // Debug: mostrar datos a modificar
    console.log('📤 Datos a modificar:');
    for (let [key, value] of body.entries()) {
        console.log(`- ${key}: ${value}`);
    }

    try {
        const respuesta = await fetch('http://localhost:9002/proyecto_pmlx/ventas/modificarAPI', {
            method: 'POST',
            body
        });

        const datos = await respuesta.json();
        console.log('📊 Respuesta de modificación:', datos);
        
        const { codigo, mensaje } = datos;

        if (codigo == 1) {
            console.log('✅ Venta modificada exitosamente');
            await Swal.fire({
                position: "center",
                icon: "success",
                title: "Éxito",
                text: mensaje,
                showConfirmButton: true,
            });

            limpiarTodo();
            BuscarVentas();
        } else {
            console.log('⚠️ Error al modificar venta:', mensaje);
            await Swal.fire({
                position: "center",
                icon: "error",
                title: "Error",
                text: mensaje,
                showConfirmButton: true,
            });
        }
    } catch (error) {
        console.error('❌ Error modificando venta:', error);
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

// Eliminar venta
const EliminarVenta = async (e) => {
    const idVenta = e.currentTarget.dataset.id;
    const numeroFactura = e.currentTarget.dataset.factura;

    console.log('🗑️ Intentando eliminar venta:', idVenta, numeroFactura);

    const AlertaConfirmarEliminar = await Swal.fire({
        position: "center",
        icon: "question",
        title: "¿Desea eliminar esta venta?",
        text: `La venta con factura "${numeroFactura}" será eliminada permanentemente`,
        showConfirmButton: true,
        confirmButtonText: 'Sí, eliminar',
        confirmButtonColor: '#dc3545',
        cancelButtonText: 'No, Cancelar',
        showCancelButton: true
    });

    if (AlertaConfirmarEliminar.isConfirmed) {
        try {
            console.log('🌐 Enviando petición de eliminación...');
            const consulta = await fetch(`http://localhost:9002/proyecto_pmlx/ventas/eliminar?id=${idVenta}`);
            const respuesta = await consulta.json();
            const { codigo, mensaje } = respuesta;

            console.log('📊 Respuesta de eliminación:', respuesta);

            if (codigo == 1) {
                console.log('✅ Venta eliminada exitosamente');
                await Swal.fire({
                    position: "center",
                    icon: "success",
                    title: "Éxito",
                    text: mensaje,
                    showConfirmButton: true,
                });

                BuscarVentas();
            } else {
                console.log('⚠️ Error al eliminar venta:', mensaje);
                await Swal.fire({
                    position: "center",
                    icon: "error",
                    title: "Error",
                    text: mensaje,
                    showConfirmButton: true,
                });
            }
        } catch (error) {
            console.error('❌ Error eliminando venta:', error);
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

// Ver detalle de venta
const VerDetalleVenta = async (e) => {
    const idVenta = e.currentTarget.dataset.id;
    const numeroFactura = e.currentTarget.dataset.factura;

    console.log('👁️ Viendo detalle de venta:', idVenta, numeroFactura);

    // Aquí puedes implementar la lógica para mostrar el detalle
    // Por ahora solo mostramos un mensaje
    await Swal.fire({
        position: "center",
        icon: "info",
        title: "Detalle de Venta",
        text: `Funcionalidad para ver detalle de la venta ${numeroFactura} en desarrollo`,
        showConfirmButton: true,
    });
}

// Limpiar formulario
const limpiarTodo = () => {
    console.log('🧹 Limpiando formulario...');
    
    if (FormVentas) {
        FormVentas.reset();
    }
    if (BtnGuardar) BtnGuardar.classList.remove('d-none');
    if (BtnModificar) BtnModificar.classList.add('d-none');
    
    // Generar nuevo número de factura
    generarNumeroFactura();
}

// INICIALIZACIÓN
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Inicializando aplicación de ventas...');
    
    // Verificar que los elementos principales existen
    const elementosRequeridos = [
        'FormVentas',
        'numero_factura',
        'id_cliente',
        'tipo_venta',
        'total',
        'id_usuario_vendedor',
        'TableVentas'
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
    CargarClientes();
    CargarVendedores();
    generarNumeroFactura(); // Generar número de factura inicial
    
    // Cargar ventas después de un momento
    setTimeout(() => {
        BuscarVentas();
    }, 1000);
    
    // Event listeners para cálculos automáticos
    if (InputSubtotal) {
        InputSubtotal.addEventListener('input', calcularTotal);
        console.log('✅ Event listener agregado para subtotal');
    }
    if (InputDescuento) {
        InputDescuento.addEventListener('input', calcularTotal);
        console.log('✅ Event listener agregado para descuento');
    }
    if (InputImpuestos) {
        InputImpuestos.addEventListener('input', calcularTotal);
        console.log('✅ Event listener agregado para impuestos');
    }

    // Event listeners del formulario
    if (FormVentas) {
        FormVentas.addEventListener('submit', GuardarVenta);
        console.log('✅ Event listener agregado para el formulario');
    }

    if (BtnGenerarFactura) {
        BtnGenerarFactura.addEventListener('click', generarNumeroFactura);
        console.log('✅ Event listener agregado para generar factura');
    }

    if (BtnLimpiar) {
        BtnLimpiar.addEventListener('click', limpiarTodo);
        console.log('✅ Event listener agregado para limpiar');
    }

    if (BtnModificar) {
        BtnModificar.addEventListener('click', ModificarVenta);
        console.log('✅ Event listener agregado para modificar');
    }

    // Event listeners para DataTable
    setTimeout(() => {
        if (datatable) {
            datatable.on('click', '.eliminar', EliminarVenta);
            datatable.on('click', '.modificar', llenarFormulario);
            datatable.on('click', '.ver-detalle', VerDetalleVenta);
            console.log('✅ Event listeners agregados para DataTable');
        }
    }, 1500);
    
    console.log('✅ Aplicación de ventas inicializada completamente');
});