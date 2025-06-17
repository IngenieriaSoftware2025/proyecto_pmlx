import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario } from '../funciones';
import DataTable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";

const FormDetalleVentaServicios = document.getElementById('FormDetalleVentaServicios');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const SelectOrden = document.getElementById('id_orden');
const InputPrecioServicio = document.getElementById('precio_servicio');
const InputPrecioSugerido = document.getElementById('precio_sugerido');
const InputEstadoOrden = document.getElementById('estado_orden');
const InputDescripcionServicio = document.getElementById('descripcion_servicio');
const InfoOrden = document.getElementById('info_orden');

let datatable;

// Actualizar información de la orden seleccionada
const actualizarInfoOrden = () => {
    if (!SelectOrden) return;
    
    const selectedOption = SelectOrden.options[SelectOrden.selectedIndex];
    
    if (selectedOption && selectedOption.value) {
        const numeroOrden = selectedOption.dataset.numeroOrden;
        const clienteNombre = selectedOption.dataset.clienteNombre;
        const clienteTelefono = selectedOption.dataset.clienteTelefono;
        const marca = selectedOption.dataset.marca;
        const modelo = selectedOption.dataset.modelo;
        const motivoIngreso = selectedOption.dataset.motivoIngreso;
        const descripcionProblema = selectedOption.dataset.descripcionProblema;
        const fechaRecepcion = selectedOption.dataset.fechaRecepcion;
        const estadoOrden = selectedOption.dataset.estadoOrden;
        const precioSugerido = selectedOption.dataset.precioSugerido;
        
        // Actualizar campos informativos
        if (InputPrecioSugerido) {
            InputPrecioSugerido.value = precioSugerido ? parseFloat(precioSugerido).toFixed(2) : '0.00';
        }
        
        if (InputEstadoOrden) {
            InputEstadoOrden.value = estadoOrden || '';
        }
        
        // Sugerir precio si el campo está vacío
        if (InputPrecioServicio && !InputPrecioServicio.value && precioSugerido) {
            InputPrecioServicio.value = parseFloat(precioSugerido).toFixed(2);
        }
        
        // Autocompletar descripción con el motivo de ingreso
        if (InputDescripcionServicio && !InputDescripcionServicio.value && motivoIngreso) {
            InputDescripcionServicio.value = motivoIngreso;
        }
        
        // Mostrar información de la orden
        if (InfoOrden) {
            const fecha = fechaRecepcion ? new Date(fechaRecepcion).toLocaleDateString('es-GT') : 'N/A';
            InfoOrden.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <strong>Orden:</strong> ${numeroOrden}<br>
                        <strong>Cliente:</strong> ${clienteNombre}<br>
                        <strong>Teléfono:</strong> ${clienteTelefono || 'N/A'}<br>
                        <strong>Fecha:</strong> ${fecha}
                    </div>
                    <div class="col-md-6">
                        <strong>Dispositivo:</strong> ${marca} ${modelo || ''}<br>
                        <strong>Problema:</strong> ${motivoIngreso}<br>
                        ${descripcionProblema ? `<strong>Detalle:</strong> ${descripcionProblema}<br>` : ''}
                        <strong>Estado:</strong> <span class="badge bg-success">${estadoOrden}</span>
                    </div>
                </div>
            `;
        }
    } else {
        // Limpiar campos informativos
        if (InputPrecioSugerido) InputPrecioSugerido.value = '';
        if (InputEstadoOrden) InputEstadoOrden.value = '';
        if (InfoOrden) InfoOrden.innerHTML = '<i class="bi bi-info-circle"></i> Seleccione una orden para ver su información';
    }
}

// Cargar ventas disponibles
const CargarVentas = async () => {
    try {
        console.log('🔄 Cargando ventas de servicios disponibles...');
        const url = 'http://localhost:9002/proyecto_pmlx/detalle_venta_servicios/ventasDisponiblesAPI';
        console.log('📡 URL:', url);
        
        const respuesta = await fetch(url);
        console.log('📡 Status:', respuesta.status);
        
        if (!respuesta.ok) {
            throw new Error(`HTTP error! status: ${respuesta.status}`);
        }
        
        const datos = await respuesta.json();
        console.log('📊 Respuesta completa:', datos);
        
        const { codigo, data, mensaje } = datos;

        const selectVenta = document.getElementById('id_venta');
        if (!selectVenta) {
            console.error('❌ No se encontró el elemento select #id_venta');
            return;
        }

        selectVenta.innerHTML = '<option value="">Seleccione una venta</option>';

        if (codigo === 1) {
            if (data && Array.isArray(data) && data.length > 0) {
                data.forEach(venta => {
                    const option = document.createElement('option');
                    option.value = venta.id_venta;
                    const cliente = venta.cliente_nombre || 'Cliente general';
                    const fecha = new Date(venta.fecha_venta).toLocaleDateString('es-GT');
                    option.textContent = `${venta.numero_factura} - ${cliente} - Q${parseFloat(venta.total).toFixed(2)} (${fecha})`;
                    selectVenta.appendChild(option);
                });
                
                console.log('✅ Ventas de servicios cargadas:', data.length);
            } else {
                console.log('ℹ️ No hay ventas de servicios disponibles');
                const option = document.createElement('option');
                option.value = '';
                option.textContent = 'No hay ventas de servicios disponibles';
                option.disabled = true;
                selectVenta.appendChild(option);
                
                await Swal.fire({
                    icon: 'info',
                    title: 'Sin ventas disponibles',
                    text: 'No hay ventas de servicios activas. Debe crear una venta de servicios primero.',
                    showConfirmButton: true,
                });
            }
        } else {
            console.error('❌ Error en la respuesta de ventas:', mensaje);
            await Swal.fire({
                icon: 'error',
                title: 'Error cargando ventas',
                text: mensaje || 'Error desconocido al cargar ventas'
            });
        }
    } catch (error) {
        console.error('❌ Error cargando ventas:', error);
        await Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: 'No se pudo conectar con el servidor para cargar las ventas'
        });
    }
}

// Cargar órdenes disponibles
const CargarOrdenes = async () => {
    try {
        console.log('🔄 Cargando órdenes disponibles...');
        const url = 'http://localhost:9002/proyecto_pmlx/detalle_venta_servicios/ordenesDisponiblesAPI';
        console.log('📡 URL:', url);
        
        const respuesta = await fetch(url);
        console.log('📡 Status:', respuesta.status);
        
        if (!respuesta.ok) {
            throw new Error(`HTTP error! status: ${respuesta.status}`);
        }
        
        const datos = await respuesta.json();
        console.log('📊 Respuesta completa:', datos);
        
        const { codigo, data, mensaje } = datos;

        const selectOrden = document.getElementById('id_orden');
        if (!selectOrden) {
            console.error('❌ No se encontró el elemento select #id_orden');
            return;
        }

        selectOrden.innerHTML = '<option value="">Seleccione una orden</option>';

        if (codigo === 1) {
            if (data && Array.isArray(data) && data.length > 0) {
                data.forEach(orden => {
                    const option = document.createElement('option');
                    option.value = orden.id_orden;
                    
                    // Agregar datos como data attributes
                    option.dataset.numeroOrden = orden.numero_orden;
                    option.dataset.clienteNombre = orden.cliente_nombre;
                    option.dataset.clienteTelefono = orden.cliente_telefono || '';
                    option.dataset.marca = orden.nombre_marca;
                    option.dataset.modelo = orden.modelo_dispositivo || '';
                    option.dataset.motivoIngreso = orden.motivo_ingreso;
                    option.dataset.descripcionProblema = orden.descripcion_problema || '';
                    option.dataset.fechaRecepcion = orden.fecha_recepcion;
                    option.dataset.estadoOrden = orden.estado_orden_texto;
                    option.dataset.precioSugerido = orden.precio_total_servicios || '0';
                    
                    const precioTexto = orden.precio_total_servicios ? ` - Q${parseFloat(orden.precio_total_servicios).toFixed(2)}` : '';
                    option.textContent = `${orden.numero_orden} - ${orden.cliente_nombre} - ${orden.motivo_ingreso}${precioTexto}`;
                    selectOrden.appendChild(option);
                });
                
                console.log('✅ Órdenes cargadas:', data.length);
            } else {
                console.log('ℹ️ No hay órdenes disponibles');
                const option = document.createElement('option');
                option.value = '';
                option.textContent = 'No hay órdenes terminadas disponibles';
                option.disabled = true;
                selectOrden.appendChild(option);
                
                await Swal.fire({
                    icon: 'info',
                    title: 'Sin órdenes disponibles',
                    text: 'No hay órdenes terminadas sin facturar disponibles.',
                    showConfirmButton: true,
                });
            }
        } else {
            console.error('❌ Error en la respuesta de órdenes:', mensaje);
            await Swal.fire({
                icon: 'error',
                title: 'Error cargando órdenes',
                text: mensaje || 'Error desconocido al cargar órdenes'
            });
        }
    } catch (error) {
        console.error('❌ Error cargando órdenes:', error);
        await Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: 'No se pudo conectar con el servidor para cargar las órdenes'
        });
    }
}

// Guardar detalle de venta de servicios
const GuardarDetalleVentaServicios = async (event) => {
    event.preventDefault();
    console.log('🔄 Iniciando proceso de guardado...');
    
    if (BtnGuardar) BtnGuardar.disabled = true;

    // Validaciones básicas
    const id_venta = document.getElementById('id_venta')?.value;
    const id_orden = document.getElementById('id_orden')?.value;
    const precio_servicio = document.getElementById('precio_servicio')?.value;

    console.log('📋 Datos del detalle:');
    console.log('- ID Venta:', id_venta);
    console.log('- ID Orden:', id_orden);
    console.log('- Precio servicio:', precio_servicio);

    if (!id_venta || id_venta === '') {
        await Swal.fire({
            position: "center",
            icon: "warning",
            title: "CAMPO REQUERIDO",
            text: "Debe seleccionar una venta",
            showConfirmButton: true,
        });
        if (BtnGuardar) BtnGuardar.disabled = false;
        return;
    }

    if (!id_orden || id_orden === '') {
        await Swal.fire({
            position: "center",
            icon: "warning",
            title: "CAMPO REQUERIDO",
            text: "Debe seleccionar una orden de reparación",
            showConfirmButton: true,
        });
        if (BtnGuardar) BtnGuardar.disabled = false;
        return;
    }

    if (!precio_servicio || precio_servicio === '' || parseFloat(precio_servicio) <= 0) {
        await Swal.fire({
            position: "center",
            icon: "warning",
            title: "PRECIO INVÁLIDO",
            text: "El precio del servicio debe ser mayor que 0",
            showConfirmButton: true,
        });
        if (BtnGuardar) BtnGuardar.disabled = false;
        return;
    }

    console.log('✅ Validaciones pasadas, preparando envío...');

    const body = new FormData(FormDetalleVentaServicios);

    // Debug: mostrar todos los datos que se enviarán
    console.log('📤 Datos a enviar:');
    for (let [key, value] of body.entries()) {
        console.log(`- ${key}: ${value}`);
    }

    try {
        console.log('🌐 Enviando petición al servidor...');
        const respuesta = await fetch('http://localhost:9002/proyecto_pmlx/detalle_venta_servicios/guardarAPI', {
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
            if (BtnGuardar) BtnGuardar.disabled = false;
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
            BuscarDetallesVentaServicios();
            
            // Recargar órdenes para actualizar disponibilidad
            CargarOrdenes();
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
    
    if (BtnGuardar) BtnGuardar.disabled = false;
}

// Buscar detalles de venta de servicios
const BuscarDetallesVentaServicios = async () => {
    try {
        console.log('🔍 Buscando detalles de venta de servicios...');
        const respuesta = await fetch('http://localhost:9002/proyecto_pmlx/detalle_venta_servicios/buscarAPI');
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
    if (document.getElementById('TableDetalleVentaServicios')) {
        console.log('🗂️ Inicializando DataTable...');
        datatable = new DataTable('#TableDetalleVentaServicios', {
            language: lenguaje,
            data: [],
            columns: [
                {
                    title: 'No.',
                    data: 'id_detalle_servicio',
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
                    title: 'Orden', 
                    data: 'numero_orden',
                    render: (data, type, row) => {
                        return `<strong>${data}</strong><br><small class="text-muted">${row.motivo_ingreso}</small>`;
                    }
                },
                { 
                    title: 'Descripción', 
                    data: 'descripcion_servicio',
                    render: (data) => {
                        if (data && data.length > 50) {
                            return data.substring(0, 50) + '...';
                        }
                        return data || 'Sin descripción';
                    }
                },
                { 
                    title: 'Precio', 
                    data: 'precio_servicio',
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
                    title: 'Estado Orden', 
                    data: 'estado_orden_texto',
                    render: (data, type, row) => {
                        let badgeClass = 'bg-info';
                        switch(row.estado_orden) {
                            case 'T': badgeClass = 'bg-success'; break;
                            case 'N': badgeClass = 'bg-primary'; break;
                            case 'C': badgeClass = 'bg-danger'; break;
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
                    data: 'id_detalle_servicio',
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
                                data-id_orden="${row.id_orden || ''}"
                                data-descripcion_servicio="${(row.descripcion_servicio || '').replace(/"/g, '&quot;')}"
                                data-precio_servicio="${row.precio_servicio || '0'}">
                                <i class="bi bi-pencil"></i> Modificar
                            </button>
                            <button class="btn btn-danger btn-sm eliminar" 
                                data-id="${data}"
                                data-orden="${row.numero_orden}"
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
        console.error('❌ No se encontró el elemento #TableDetalleVentaServicios');
    }
}

// Llenar formulario para edición
const llenarFormulario = (event) => {
    const datos = event.currentTarget.dataset;
    
    console.log('📝 Llenando formulario con datos:', datos);

    // Llenar campos básicos
    document.getElementById('id_detalle_servicio').value = datos.id || '';
    document.getElementById('descripcion_servicio').value = datos.descripcion_servicio || '';
    document.getElementById('precio_servicio').value = datos.precio_servicio || '0';

    // Cargar selects y establecer valores
    Promise.all([
        CargarVentas(),
        CargarOrdenes()
    ]).then(() => {
        setTimeout(() => {
            const selectVenta = document.getElementById('id_venta');
            const selectOrden = document.getElementById('id_orden');
            
            if (selectVenta && datos.id_venta) {
                selectVenta.value = datos.id_venta;
                console.log('🔄 Venta seleccionada:', datos.id_venta);
            }
            
            if (selectOrden && datos.id_orden) {
                selectOrden.value = datos.id_orden;
                console.log('🔄 Orden seleccionada:', datos.id_orden);
                
                // Actualizar información de la orden después de seleccionar
                actualizarInfoOrden();
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

// Modificar detalle de venta de servicios
const ModificarDetalleVentaServicios = async (event) => {
    event.preventDefault();
    console.log('🔄 Iniciando modificación...');
    
    if (BtnModificar) BtnModificar.disabled = true;

    const precio_servicio = document.getElementById('precio_servicio')?.value;

    if (!precio_servicio || precio_servicio === '' || parseFloat(precio_servicio) <= 0) {
        await Swal.fire({
            position: "center",
            icon: "warning",
            title: "PRECIO INVÁLIDO",
            text: "El precio del servicio debe ser mayor que 0",
            showConfirmButton: true,
        });
        if (BtnModificar) BtnModificar.disabled = false;
        return;
    }

    const body = new FormData(FormDetalleVentaServicios);

    // Debug: mostrar datos a modificar
    console.log('📤 Datos a modificar:');
    for (let [key, value] of body.entries()) {
        console.log(`- ${key}: ${value}`);
    }

    try {
        const respuesta = await fetch('http://localhost:9002/proyecto_pmlx/detalle_venta_servicios/modificarAPI', {
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
            BuscarDetallesVentaServicios();
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
    
    if (BtnModificar) BtnModificar.disabled = false;
}

// Eliminar detalle de venta de servicios
const EliminarDetalleVentaServicios = async (e) => {
    const idDetalle = e.currentTarget.dataset.id;
    const numeroOrden = e.currentTarget.dataset.orden;
    const numeroFactura = e.currentTarget.dataset.factura;

    console.log('🗑️ Intentando eliminar detalle:', idDetalle, numeroOrden, numeroFactura);

    const AlertaConfirmarEliminar = await Swal.fire({
        position: "center",
        icon: "question",
        title: "¿Desea eliminar este servicio?",
        text: `La orden "${numeroOrden}" será eliminada de la factura "${numeroFactura}"`,
        showConfirmButton: true,
        confirmButtonText: 'Sí, eliminar',
        confirmButtonColor: '#dc3545',
        cancelButtonText: 'No, Cancelar',
        showCancelButton: true
    });

    if (AlertaConfirmarEliminar.isConfirmed) {
        try {
            console.log('🌐 Enviando petición de eliminación...');
            const consulta = await fetch(`http://localhost:9002/proyecto_pmlx/detalle_venta_servicios/eliminar?id=${idDetalle}`);
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

                BuscarDetallesVentaServicios();
                CargarOrdenes(); // Recargar para que la orden vuelva a estar disponible
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
    
    if (FormDetalleVentaServicios) {
        FormDetalleVentaServicios.reset();
    }
    if (BtnGuardar) BtnGuardar.classList.remove('d-none');
    if (BtnModificar) BtnModificar.classList.add('d-none');
    
    // Limpiar campos informativos
    if (InputPrecioSugerido) InputPrecioSugerido.value = '';
    if (InputEstadoOrden) InputEstadoOrden.value = '';
    if (InfoOrden) InfoOrden.innerHTML = '<i class="bi bi-info-circle"></i> Seleccione una orden para ver su información';
}

// INICIALIZACIÓN
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Inicializando aplicación de detalle de ventas de servicios...');
    
    // Verificar que los elementos principales existen
    const elementosRequeridos = [
        'FormDetalleVentaServicios',
        'id_venta',
        'id_orden',
        'precio_servicio',
        'TableDetalleVentaServicios'
    ];
    
    elementosRequeridos.forEach(id => {
        const elemento = document.getElementById(id);
        if (!elemento) {
            console.error(`❌ No se encontró el elemento #${id}`);
        } else {
            console.log(`✅ Elemento encontrado: ${id}`);
        }
    });
    
    // Inicializar DataTable
    inicializarDataTable();
    
    // Cargar datos iniciales con delay
    console.log('🔄 Cargando datos iniciales...');
    setTimeout(() => {
        CargarVentas();
    }, 500);
    
    setTimeout(() => {
        CargarOrdenes();
    }, 1000);
    
    // Cargar detalles después de un momento
    setTimeout(() => {
        BuscarDetallesVentaServicios();
    }, 1500);
    
    // Event listener para actualizar info de la orden
    if (SelectOrden) {
        SelectOrden.addEventListener('change', actualizarInfoOrden);
        console.log('✅ Event listener agregado para selección de orden');
    }

    // Event listeners del formulario
    if (FormDetalleVentaServicios) {
        FormDetalleVentaServicios.addEventListener('submit', GuardarDetalleVentaServicios);
        console.log('✅ Event listener agregado para el formulario');
    }

    if (BtnLimpiar) {
        BtnLimpiar.addEventListener('click', limpiarTodo);
        console.log('✅ Event listener agregado para limpiar');
    }

    if (BtnModificar) {
        BtnModificar.addEventListener('click', ModificarDetalleVentaServicios);
        console.log('✅ Event listener agregado para modificar');
    }

    // Event listeners para DataTable
    setTimeout(() => {
        if (datatable) {
            datatable.on('click', '.eliminar', EliminarDetalleVentaServicios);
            datatable.on('click', '.modificar', llenarFormulario);
            console.log('✅ Event listeners agregados para DataTable');
        }
    }, 2000);
    
    console.log('✅ Aplicación de detalle de ventas de servicios inicializada completamente');
});