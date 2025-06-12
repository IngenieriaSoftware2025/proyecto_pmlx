import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario } from '../funciones';
import DataTable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";

const FormServiciosOrden = document.getElementById('FormServiciosOrden');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const SelectTipoServicio = document.getElementById('id_tipo_servicio');
const InputPrecioBase = document.getElementById('precio_base_info');
const InputPrecioServicio = document.getElementById('precio_servicio');
const SelectEstado = document.getElementById('estado_servicio');
const InputFechaCompletado = document.getElementById('fecha_completado');

let datatable;

const ValidarEstadoFecha = () => {
    if (!SelectEstado || !InputFechaCompletado) return;
    
    const estado = SelectEstado.value;
    
    if (estado === 'C') { // Completado
        InputFechaCompletado.setAttribute('required', 'required');
        InputFechaCompletado.closest('.col-lg-6').querySelector('.form-text').innerHTML = 'Requerido para estado "Completado"';
        InputFechaCompletado.closest('.col-lg-6').querySelector('.form-text').classList.add('text-danger');
    } else {
        InputFechaCompletado.removeAttribute('required');
        InputFechaCompletado.closest('.col-lg-6').querySelector('.form-text').innerHTML = 'Solo cuando esté completado';
        InputFechaCompletado.closest('.col-lg-6').querySelector('.form-text').classList.remove('text-danger');
    }
}

const ActualizarPrecioBase = () => {
    if (!SelectTipoServicio || !InputPrecioBase || !InputPrecioServicio) return;
    
    const selectedOption = SelectTipoServicio.options[SelectTipoServicio.selectedIndex];
    
    if (selectedOption && selectedOption.value) {
        const precioBase = selectedOption.dataset.precioBase;
        if (precioBase) {
            InputPrecioBase.value = parseFloat(precioBase).toFixed(2);
            
            // Si el precio del servicio está vacío, sugerir el precio base
            if (!InputPrecioServicio.value) {
                InputPrecioServicio.value = parseFloat(precioBase).toFixed(2);
            }
        }
    } else {
        InputPrecioBase.value = '';
    }
}

const CargarOrdenes = async () => {
    try {
        console.log('🔄 Cargando órdenes...');
        const respuesta = await fetch('http://localhost:9002/proyecto_pmlx/servicios_orden/ordenesDisponiblesAPI');
        
        if (!respuesta.ok) {
            throw new Error(`HTTP error! status: ${respuesta.status}`);
        }
        
        const datos = await respuesta.json();
        console.log('📊 Respuesta de órdenes:', datos);
        
        const { codigo, data, mensaje } = datos;
        const selectOrden = document.getElementById('id_orden');
        
        if (!selectOrden) {
            console.error('❌ No se encontró el elemento select #id_orden');
            return;
        }

        if (codigo === 1 && data && Array.isArray(data)) {
            selectOrden.innerHTML = '<option value="">Seleccione una orden</option>';
            
            console.log(`✅ Cargando ${data.length} órdenes`);
            
            data.forEach((orden, index) => {
                const option = document.createElement('option');
                option.value = orden.id_orden;
                option.textContent = `${orden.numero_orden} - ${orden.cliente_nombre} - ${orden.motivo_ingreso}`;
                selectOrden.appendChild(option);
                console.log(`➕ Orden ${index + 1}: ${orden.numero_orden}`);
            });
            
            console.log('✅ Órdenes cargadas exitosamente');
        } else {
            console.error('❌ Error en la respuesta:', mensaje || 'Código de respuesta inválido');
            console.error('Datos recibidos:', datos);
        }
    } catch (error) {
        console.error('❌ Error cargando órdenes:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudieron cargar las órdenes disponibles'
        });
    }
}

const CargarTiposServicio = async () => {
    try {
        console.log('🔄 Cargando tipos de servicio...');
        const respuesta = await fetch('http://localhost:9002/proyecto_pmlx/servicios_orden/tiposServicioDisponiblesAPI');
        
        if (!respuesta.ok) {
            throw new Error(`HTTP error! status: ${respuesta.status}`);
        }
        
        const datos = await respuesta.json();
        console.log('📊 Respuesta de tipos de servicio:', datos);
        
        const { codigo, data, mensaje } = datos;
        const selectTipoServicio = document.getElementById('id_tipo_servicio');
        
        if (!selectTipoServicio) {
            console.error('❌ No se encontró el elemento select #id_tipo_servicio');
            return;
        }

        if (codigo === 1 && data && Array.isArray(data)) {
            selectTipoServicio.innerHTML = '<option value="">Seleccione un tipo de servicio</option>';
            
            console.log(`✅ Cargando ${data.length} tipos de servicio`);
            
            data.forEach((tipo, index) => {
                const option = document.createElement('option');
                option.value = tipo.id_tipo_servicio;
                option.dataset.precioBase = tipo.precio_base;
                option.dataset.tiempoEstimado = tipo.tiempo_estimado_horas;
                option.textContent = `${tipo.nombre_servicio} - Q${parseFloat(tipo.precio_base).toFixed(2)} (${tipo.tiempo_estimado_horas}h)`;
                selectTipoServicio.appendChild(option);
                console.log(`➕ Servicio ${index + 1}: ${tipo.nombre_servicio}`);
            });
            
            console.log('✅ Tipos de servicio cargados exitosamente');
        } else {
            console.error('❌ Error en la respuesta:', mensaje || 'Código de respuesta inválido');
            console.error('Datos recibidos:', datos);
        }
    } catch (error) {
        console.error('❌ Error cargando tipos de servicio:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudieron cargar los tipos de servicio'
        });
    }
}

const GuardarServicioOrden = async (event) => {
    event.preventDefault();
    console.log('🔄 Iniciando proceso de guardado...');
    
    BtnGuardar.disabled = true;

    // Verificar todos los campos requeridos manualmente
    const id_orden = document.getElementById('id_orden').value;
    const id_tipo_servicio = document.getElementById('id_tipo_servicio').value;
    const precio_servicio = document.getElementById('precio_servicio').value;

    console.log('📋 Datos del formulario:');
    console.log('- ID Orden:', id_orden);
    console.log('- ID Tipo Servicio:', id_tipo_servicio);
    console.log('- Precio Servicio:', precio_servicio);

    // Validaciones manuales
    if (!id_orden || id_orden === '') {
        await Swal.fire({
            position: "center",
            icon: "warning",
            title: "CAMPO REQUERIDO",
            text: "Debe seleccionar una orden de reparación",
            showConfirmButton: true,
        });
        BtnGuardar.disabled = false;
        return;
    }

    if (!id_tipo_servicio || id_tipo_servicio === '') {
        await Swal.fire({
            position: "center",
            icon: "warning",
            title: "CAMPO REQUERIDO",
            text: "Debe seleccionar un tipo de servicio",
            showConfirmButton: true,
        });
        BtnGuardar.disabled = false;
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
        BtnGuardar.disabled = false;
        return;
    }

    // Si llegamos aquí, los datos están bien
    console.log('✅ Validaciones pasadas, preparando envío...');

    const body = new FormData(FormServiciosOrden);

    // Debug: mostrar todos los datos que se enviarán
    console.log('📤 Datos a enviar:');
    for (let [key, value] of body.entries()) {
        console.log(`- ${key}: ${value}`);
    }

    try {
        console.log('🌐 Enviando petición al servidor...');
        const respuesta = await fetch('http://localhost:9002/proyecto_pmlx/servicios_orden/guardarAPI', {
            method: 'POST',
            body
        });

        console.log('📡 Respuesta del servidor:', respuesta.status, respuesta.statusText);

        if (!respuesta.ok) {
            // Si hay error HTTP, intentar leer el mensaje de error
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
            console.log('✅ Servicio guardado exitosamente');
            await Swal.fire({
                position: "center",
                icon: "success",
                title: "Éxito",
                text: mensaje,
                showConfirmButton: true,
            });

            limpiarTodo();
            BuscarServiciosOrden();
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
const BuscarServiciosOrden = async () => {
    try {
        const respuesta = await fetch('http://localhost:9002/proyecto_pmlx/servicios_orden/buscarAPI');
        const datos = await respuesta.json();
        const { codigo, mensaje, data } = datos

        if (codigo == 1) {
            if (datatable) {
                datatable.clear().draw();
                if (data && data.length > 0) {
                    datatable.rows.add(data).draw();
                }
            }
        }
    } catch (error) {
        console.log(error)
    }
}

const inicializarDataTable = () => {
    if (document.getElementById('TableServiciosOrden')) {
        datatable = new DataTable('#TableServiciosOrden', {
            language: lenguaje,
            data: [],
            columns: [
                {
                    title: 'No.',
                    data: 'id_servicio_orden',
                    render: (data, type, row, meta) => meta.row + 1
                },
                { 
                    title: 'Orden', 
                    data: 'numero_orden',
                    render: (data, type, row) => {
                        return `<strong>${data}</strong><br><small class="text-muted">${row.cliente_nombre}</small>`;
                    }
                },
                { 
                    title: 'Servicio', 
                    data: 'nombre_servicio'
                },
                { 
                    title: 'Precio', 
                    data: 'precio_servicio',
                    render: (data) => {
                        return `Q${parseFloat(data).toFixed(2)}`;
                    }
                },
                { 
                    title: 'Estado', 
                    data: 'estado_texto',
                    render: (data, type, row) => {
                        let badgeClass = 'bg-secondary';
                        switch(row.estado_servicio) {
                            case 'P': badgeClass = 'bg-warning'; break;
                            case 'E': badgeClass = 'bg-info'; break;
                            case 'C': badgeClass = 'bg-success'; break;
                        }
                        return `<span class="badge ${badgeClass}">${data}</span>`;
                    }
                },
                { 
                    title: 'Fecha Inicio', 
                    data: 'fecha_inicio',
                    render: (data) => {
                        if (data) {
                            const fecha = new Date(data);
                            return fecha.toLocaleDateString('es-GT');
                        }
                        return '<span class="text-muted">No iniciado</span>';
                    }
                },
                { 
                    title: 'Fecha Completado', 
                    data: 'fecha_completado',
                    render: (data) => {
                        if (data) {
                            const fecha = new Date(data);
                            return fecha.toLocaleDateString('es-GT');
                        }
                        return '<span class="text-muted">Pendiente</span>';
                    }
                },
                {
                    title: 'Acciones',
                    data: 'id_servicio_orden',
                    orderable: false,
                    render: (data, type, row) => {
                        // ✅ FORMATEAR FECHAS CORRECTAMENTE PARA LOS DATA ATTRIBUTES
                        let fechaInicio = '';
                        let fechaCompletado = '';
                        
                        if (row.fecha_inicio) {
                            const fechaInicioObj = new Date(row.fecha_inicio);
                            fechaInicio = fechaInicioObj.toISOString().split('T')[0]; // YYYY-MM-DD
                        }
                        
                        if (row.fecha_completado) {
                            const fechaCompletadoObj = new Date(row.fecha_completado);
                            fechaCompletado = fechaCompletadoObj.toISOString().split('T')[0]; // YYYY-MM-DD
                        }
                        
                        return `
                            <button class="btn btn-warning btn-sm modificar" 
                                data-id="${data}"
                                data-id_orden="${row.id_orden || ''}"  
                                data-id_tipo_servicio="${row.id_tipo_servicio || ''}"  
                                data-precio_servicio="${row.precio_servicio || ''}"  
                                data-estado_servicio="${row.estado_servicio || 'P'}"
                                data-fecha_inicio="${fechaInicio}"
                                data-fecha_completado="${fechaCompletado}"
                                data-observaciones="${(row.observaciones || '').replace(/"/g, '&quot;')}">
                                <i class="bi bi-pencil"></i> Modificar
                            </button>
                            <button class="btn btn-danger btn-sm eliminar" 
                                data-id="${data}"
                                data-servicio="${row.nombre_servicio}"
                                data-orden="${row.numero_orden}">
                                <i class="bi bi-trash"></i> Eliminar
                            </button>
                        `;
                    }
                }
            ]
        });
    }
}
const llenarFormulario = (event) => {
    const datos = event.currentTarget.dataset;
    
    console.log('📝 Llenando formulario con datos:', datos);

    // Llenar campos básicos
    document.getElementById('id_servicio_orden').value = datos.id || '';
    document.getElementById('precio_servicio').value = datos.precio_servicio || '';
    document.getElementById('estado_servicio').value = datos.estado_servicio || 'P';
    document.getElementById('observaciones').value = datos.observaciones || '';

    // ✅ LLENAR FECHAS CORRECTAMENTE
    const fechaInicio = datos.fecha_inicio || '';
    const fechaCompletado = datos.fecha_completado || '';
    
    document.getElementById('fecha_inicio').value = fechaInicio;
    document.getElementById('fecha_completado').value = fechaCompletado;
    
    console.log('📅 Fechas cargadas:');
    console.log('- Fecha inicio:', fechaInicio);
    console.log('- Fecha completado:', fechaCompletado);

    // ✅ CARGAR LOS SELECTS PRIMERO, LUEGO ESTABLECER LOS VALORES
    Promise.all([
        CargarOrdenes(),
        CargarTiposServicio()
    ]).then(() => {
        // Esperar un poco para que se carguen los options
        setTimeout(() => {
            // Establecer valores de los selects
            const selectOrden = document.getElementById('id_orden');
            const selectTipoServicio = document.getElementById('id_tipo_servicio');
            
            if (selectOrden && datos.id_orden) {
                selectOrden.value = datos.id_orden;
                console.log('🔄 Orden seleccionada:', datos.id_orden);
            }
            
            if (selectTipoServicio && datos.id_tipo_servicio) {
                selectTipoServicio.value = datos.id_tipo_servicio;
                console.log('🔄 Tipo de servicio seleccionado:', datos.id_tipo_servicio);
                
                // Actualizar precio base después de seleccionar
                ActualizarPrecioBase();
            }
            
            // Validar estado y fecha después de llenar todo
            ValidarEstadoFecha();
        }, 500);
    });

    // Cambiar botones
    if (BtnGuardar) BtnGuardar.classList.add('d-none');
    if (BtnModificar) BtnModificar.classList.remove('d-none');

    // Scroll hacia arriba
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}
const ModificarServicioOrden = async (event) => {
    event.preventDefault();
    BtnModificar.disabled = true;

    if (!validarFormulario(FormServiciosOrden, [''])) {
        Swal.fire({
            position: "center",
            icon: "info",
            title: "FORMULARIO INCOMPLETO",
            text: "Debe de validar todos los campos",
            showConfirmButton: true,
        });
        BtnModificar.disabled = false;
        return;
    }

    const body = new FormData(FormServiciosOrden);

    try {
        const respuesta = await fetch('http://localhost:9002/proyecto_pmlx/servicios_orden/modificarAPI', {
            method: 'POST',
            body
        });
        const datos = await respuesta.json();
        const { codigo, mensaje } = datos

        if (codigo == 1) {
            await Swal.fire({
                position: "center",
                icon: "success",
                title: "Éxito",
                text: mensaje,
                showConfirmButton: true,
            });

            limpiarTodo();
            BuscarServiciosOrden();
        } else {
            await Swal.fire({
                position: "center",
                icon: "error",
                title: "Error",
                text: mensaje,
                showConfirmButton: true,
            });
        }
    } catch (error) {
        console.log(error)
    }
    BtnModificar.disabled = false;
}

const EliminarServicioOrden = async (e) => {
    const idServicio = e.currentTarget.dataset.id
    const nombreServicio = e.currentTarget.dataset.servicio
    const numeroOrden = e.currentTarget.dataset.orden

    const AlertaConfirmarEliminar = await Swal.fire({
        position: "center",
        icon: "question",
        title: "¿Desea eliminar este servicio?",
        text: `El servicio "${nombreServicio}" de la orden "${numeroOrden}" será eliminado permanentemente`,
        showConfirmButton: true,
        confirmButtonText: 'Sí, eliminar',
        confirmButtonColor: '#dc3545',
        cancelButtonText: 'No, Cancelar',
        showCancelButton: true
    });

    if (AlertaConfirmarEliminar.isConfirmed) {
        try {
            const consulta = await fetch(`http://localhost:9002/proyecto_pmlx/servicios_orden/eliminar?id=${idServicio}`);
            const respuesta = await consulta.json();
            const { codigo, mensaje } = respuesta;

            if (codigo == 1) {
                await Swal.fire({
                    position: "center",
                    icon: "success",
                    title: "Éxito",
                    text: mensaje,
                    showConfirmButton: true,
                });

                BuscarServiciosOrden();
            } else {
                await Swal.fire({
                    position: "center",
                    icon: "error",
                    title: "Error",
                    text: mensaje,
                    showConfirmButton: true,
                });
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }
}

const limpiarTodo = () => {
    if (FormServiciosOrden) {
        FormServiciosOrden.reset();
    }
    if (BtnGuardar) BtnGuardar.classList.remove('d-none');
    if (BtnModificar) BtnModificar.classList.add('d-none');
    
    // Limpiar precio base
    if (InputPrecioBase) InputPrecioBase.value = '';
    
    // Resetear validación de fecha
    ValidarEstadoFecha();
}

// ✅ INICIALIZACIÓN CORREGIDA
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Inicializando aplicación...');
    
    // Verificar que los elementos existen
    const selectOrden = document.getElementById('id_orden');
    const selectTipoServicio = document.getElementById('id_tipo_servicio');
    
    if (!selectOrden) {
        console.error('❌ No se encontró el elemento #id_orden');
    }
    if (!selectTipoServicio) {
        console.error('❌ No se encontró el elemento #id_tipo_servicio');
    }
    
    // Inicializar DataTable
    inicializarDataTable();
    
    // ⚡ CARGAR DATOS INMEDIATAMENTE
    console.log('🔄 Cargando datos iniciales...');
    CargarOrdenes();
    CargarTiposServicio();
    
    // Esperar un poco antes de cargar servicios
    setTimeout(() => {
        BuscarServiciosOrden();
    }, 1000);
    
    // Event listeners
    if (FormServiciosOrden) {
        FormServiciosOrden.addEventListener('submit', GuardarServicioOrden);
    }

    if (SelectTipoServicio) {
        SelectTipoServicio.addEventListener('change', ActualizarPrecioBase);
    }

    if (SelectEstado) {
        SelectEstado.addEventListener('change', ValidarEstadoFecha);
    }

    if (BtnLimpiar) {
        BtnLimpiar.addEventListener('click', limpiarTodo);
    }

    if (BtnModificar) {
        BtnModificar.addEventListener('click', ModificarServicioOrden);
    }

    // Event listeners para DataTable (se agregan después de que se inicialice)
    setTimeout(() => {
        if (datatable) {
            datatable.on('click', '.eliminar', EliminarServicioOrden);
            datatable.on('click', '.modificar', llenarFormulario);
        }
    }, 1500);
    
    console.log('✅ Aplicación inicializada');
});