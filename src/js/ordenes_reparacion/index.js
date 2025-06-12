import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario } from '../funciones';
import DataTable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";

const FormOrdenesReparacion = document.getElementById('FormOrdenesReparacion');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const SelectEstado = document.getElementById('estado_orden');
const InputFechaEntrega = document.getElementById('fecha_entrega_real');

let datatable;

const ValidarEstadoFecha = () => {
    if (!SelectEstado || !InputFechaEntrega) return;
    
    const estado = SelectEstado.value;
    
    if (estado === 'N') { // Entregado
        InputFechaEntrega.setAttribute('required', 'required');
        InputFechaEntrega.closest('.col-lg-4').querySelector('.form-text').innerHTML = 'Requerido para estado "Entregado"';
        InputFechaEntrega.closest('.col-lg-4').querySelector('.form-text').classList.add('text-danger');
    } else {
        InputFechaEntrega.removeAttribute('required');
        InputFechaEntrega.closest('.col-lg-4').querySelector('.form-text').innerHTML = 'Solo cuando esté entregado';
        InputFechaEntrega.closest('.col-lg-4').querySelector('.form-text').classList.remove('text-danger');
    }
}

const CargarClientes = async () => {
    try {
        const respuesta = await fetch('http://localhost:9002/proyecto_pmlx/ordenes_reparacion/clientesDisponiblesAPI');
        const datos = await respuesta.json();
        const { codigo, data } = datos;

        const selectCliente = document.getElementById('id_cliente');
        if (selectCliente && codigo === 1) {
            selectCliente.innerHTML = '<option value="">Seleccione un cliente</option>';
            data.forEach(cliente => {
                const option = document.createElement('option');
                option.value = cliente.id_cliente;
                option.textContent = `${cliente.nombre} ${cliente.nit ? '- NIT: ' + cliente.nit : ''} ${cliente.celular ? '- Tel: ' + cliente.celular : ''}`;
                selectCliente.appendChild(option);
            });
        }
    } catch (error) {
        console.log('Error cargando clientes:', error);
    }
}

const CargarMarcas = async () => {
    try {
        const respuesta = await fetch('http://localhost:9002/proyecto_pmlx/ordenes_reparacion/marcasDisponiblesAPI');
        const datos = await respuesta.json();
        const { codigo, data } = datos;

        const selectMarca = document.getElementById('id_marca');
        if (selectMarca && codigo === 1) {
            selectMarca.innerHTML = '<option value="">Seleccione una marca</option>';
            data.forEach(marca => {
                const option = document.createElement('option');
                option.value = marca.id_marca;
                option.textContent = marca.nombre_marca;
                selectMarca.appendChild(option);
            });
        }
    } catch (error) {
        console.log('Error cargando marcas:', error);
    }
}

const CargarTrabajadores = async () => {
    try {
        const respuesta = await fetch('http://localhost:9002/proyecto_pmlx/ordenes_reparacion/trabajadoresDisponiblesAPI');
        const datos = await respuesta.json();
        const { codigo, data } = datos;

        const selectTrabajador = document.getElementById('id_trabajador_asignado');
        if (selectTrabajador && codigo === 1) {
            selectTrabajador.innerHTML = '<option value="">Sin asignar</option>';
            data.forEach(trabajador => {
                const option = document.createElement('option');
                option.value = trabajador.id_trabajador;
                option.textContent = `${trabajador.nombre_completo} ${trabajador.especialidad ? '- ' + trabajador.especialidad : ''}`;
                selectTrabajador.appendChild(option);
            });
        }
    } catch (error) {
        console.log('Error cargando trabajadores:', error);
    }
}

const GuardarOrdenReparacion = async (event) => {
    event.preventDefault();
    BtnGuardar.disabled = true;

    if (!validarFormulario(FormOrdenesReparacion, ['id_orden'])) {
        Swal.fire({
            position: "center",
            icon: "info",
            title: "FORMULARIO INCOMPLETO",
            text: "Debe de validar todos los campos",
            showConfirmButton: true,
        });
        BtnGuardar.disabled = false;
        return;
    }

    const body = new FormData(FormOrdenesReparacion);

    try {
        const respuesta = await fetch('http://localhost:9002/proyecto_pmlx/ordenes_reparacion/guardarAPI', {
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
            BuscarOrdenesReparacion();
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
    BtnGuardar.disabled = false;
}

const BuscarOrdenesReparacion = async () => {
    try {
        const respuesta = await fetch('http://localhost:9002/proyecto_pmlx/ordenes_reparacion/buscarAPI');
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
    if (document.getElementById('TableOrdenesReparacion')) {
        datatable = new DataTable('#TableOrdenesReparacion', {
            language: lenguaje,
            data: [],
            columns: [
                {
                    title: 'No.',
                    data: 'id_orden',
                    render: (data, type, row, meta) => meta.row + 1
                },
                { 
                    title: 'N° Orden', 
                    data: 'numero_orden'
                },
                { 
                    title: 'Cliente', 
                    data: 'cliente_nombre'
                },
                { 
                    title: 'Marca/Modelo', 
                    data: 'nombre_marca',
                    render: (data, type, row) => {
                        let info = data;
                        if (row.modelo_dispositivo) {
                            info += `<br><small class="text-muted">${row.modelo_dispositivo}</small>`;
                        }
                        return info;
                    }
                },
                { 
                    title: 'Motivo', 
                    data: 'motivo_ingreso',
                    render: (data) => {
                        return data.length > 30 ? data.substring(0, 30) + '...' : data;
                    }
                },
                { 
                    title: 'Estado', 
                    data: 'estado_texto',
                    render: (data, type, row) => {
                        let badgeClass = 'bg-secondary';
                        switch(row.estado_orden) {
                            case 'R': badgeClass = 'bg-primary'; break;
                            case 'P': badgeClass = 'bg-warning'; break;
                            case 'E': badgeClass = 'bg-info'; break;
                            case 'T': badgeClass = 'bg-success'; break;
                            case 'N': badgeClass = 'bg-dark'; break;
                            case 'C': badgeClass = 'bg-danger'; break;
                        }
                        return `<span class="badge ${badgeClass}">${data}</span>`;
                    }
                },
                { 
                    title: 'Técnico', 
                    data: 'trabajador_nombre',
                    render: (data) => data || '<span class="text-muted">Sin asignar</span>'
                },
                { 
                    title: 'Fecha Recepción', 
                    data: 'fecha_recepcion',
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
                    data: 'id_orden',
                    orderable: false,
                    render: (data, type, row) => `
                        <button class="btn btn-warning btn-sm modificar" 
                            data-id="${data}"
                            data-numero_orden="${row.numero_orden}"  
                            data-id_cliente="${row.id_cliente || ''}"  
                            data-id_marca="${row.id_marca || ''}"  
                            data-modelo_dispositivo="${row.modelo_dispositivo || ''}"  
                            data-imei_dispositivo="${row.imei_dispositivo || ''}"  
                            data-motivo_ingreso="${row.motivo_ingreso || ''}"
                            data-descripcion_problema="${row.descripcion_problema || ''}"
                            data-estado_orden="${row.estado_orden || 'R'}"
                            data-fecha_promesa_entrega="${row.fecha_promesa_entrega || ''}"
                            data-fecha_entrega_real="${row.fecha_entrega_real || ''}"
                            data-id_trabajador_asignado="${row.id_trabajador || ''}"
                            data-observaciones="${row.observaciones || ''}">
                            <i class="bi bi-pencil"></i> Modificar
                        </button>
                        <button class="btn btn-danger btn-sm eliminar" 
                            data-id="${data}"
                            data-numero="${row.numero_orden}">
                            <i class="bi bi-trash"></i> Eliminar
                        </button>
                    `
                }
            ]
        });
    }
}

const llenarFormulario = (event) => {
    const datos = event.currentTarget.dataset

    document.getElementById('id_orden').value = datos.id
    document.getElementById('numero_orden').value = datos.numero_orden
    document.getElementById('id_cliente').value = datos.id_cliente
    document.getElementById('id_marca').value = datos.id_marca
    document.getElementById('modelo_dispositivo').value = datos.modelo_dispositivo
    document.getElementById('imei_dispositivo').value = datos.imei_dispositivo
    document.getElementById('motivo_ingreso').value = datos.motivo_ingreso
    document.getElementById('descripcion_problema').value = datos.descripcion_problema
    document.getElementById('estado_orden').value = datos.estado_orden
    document.getElementById('fecha_promesa_entrega').value = datos.fecha_promesa_entrega
    document.getElementById('fecha_entrega_real').value = datos.fecha_entrega_real
    document.getElementById('id_trabajador_asignado').value = datos.id_trabajador_asignado
    document.getElementById('observaciones').value = datos.observaciones

    // Validar estado y fecha después de llenar
    ValidarEstadoFecha();

    if (BtnGuardar) BtnGuardar.classList.add('d-none');
    if (BtnModificar) BtnModificar.classList.remove('d-none');

    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

const ModificarOrdenReparacion = async (event) => {
    event.preventDefault();
    BtnModificar.disabled = true;

    if (!validarFormulario(FormOrdenesReparacion, [''])) {
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

    const body = new FormData(FormOrdenesReparacion);

    try {
        const respuesta = await fetch('/proyecto_pmlx/ordenes_reparacion/modificarAPI', {
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
            BuscarOrdenesReparacion();
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

const EliminarOrdenReparacion = async (e) => {
    const idOrden = e.currentTarget.dataset.id
    const numeroOrden = e.currentTarget.dataset.numero

    const AlertaConfirmarEliminar = await Swal.fire({
        position: "center",
        icon: "question",
        title: "¿Desea eliminar esta orden de reparación?",
        text: `La orden "${numeroOrden}" será eliminada permanentemente`,
        showConfirmButton: true,
        confirmButtonText: 'Sí, eliminar',
        confirmButtonColor: '#dc3545',
        cancelButtonText: 'No, Cancelar',
        showCancelButton: true
    });

    if (AlertaConfirmarEliminar.isConfirmed) {
        try {
            const consulta = await fetch(`/proyecto_pmlx/ordenes_reparacion/eliminar?id=${idOrden}`);
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

                BuscarOrdenesReparacion();
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
    if (FormOrdenesReparacion) {
        FormOrdenesReparacion.reset();
    }
    if (BtnGuardar) BtnGuardar.classList.remove('d-none');
    if (BtnModificar) BtnModificar.classList.add('d-none');
    
    // Resetear validación de fecha
    ValidarEstadoFecha();
}

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    inicializarDataTable();
    
    // Cargar datos para los selects
    CargarClientes();
    CargarMarcas();
    CargarTrabajadores();
    
    setTimeout(() => {
        BuscarOrdenesReparacion();
    }, 500);
    
    if (FormOrdenesReparacion) {
        FormOrdenesReparacion.addEventListener('submit', GuardarOrdenReparacion);
    }

    if (SelectEstado) {
        SelectEstado.addEventListener('change', ValidarEstadoFecha);
    }

    if (BtnLimpiar) {
        BtnLimpiar.addEventListener('click', limpiarTodo);
    }

    if (BtnModificar) {
        BtnModificar.addEventListener('click', ModificarOrdenReparacion);
    }

    // Event listeners para DataTable (se agregan después de que se inicialice)
    setTimeout(() => {
        if (datatable) {
            datatable.on('click', '.eliminar', EliminarOrdenReparacion);
            datatable.on('click', '.modificar', llenarFormulario);
        }
    }, 1000);
});