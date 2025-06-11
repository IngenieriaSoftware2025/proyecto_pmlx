import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario } from '../funciones';
import DataTable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";

const FormTiposServicio = document.getElementById('FormTiposServicio');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const InputPrecioBase = document.getElementById('precio_base');
const InputTiempoEstimado = document.getElementById('tiempo_estimado_horas');

let datatable;

const ValidarPrecio = () => {
    const precio = parseFloat(InputPrecioBase.value);

    if (InputPrecioBase.value.length < 1) {
        InputPrecioBase.classList.remove('is-valid', 'is-invalid');
    } else {
        if (precio <= 0 || isNaN(precio)) {
            Swal.fire({
                position: "center",
                icon: "error",
                title: "Precio inválido",
                text: "El precio base debe ser mayor a cero",
                showConfirmButton: true,
            });

            InputPrecioBase.classList.remove('is-valid');
            InputPrecioBase.classList.add('is-invalid');
        } else {
            InputPrecioBase.classList.remove('is-invalid');
            InputPrecioBase.classList.add('is-valid');
        }
    }
}

const ValidarTiempo = () => {
    const tiempo = parseInt(InputTiempoEstimado.value);

    if (InputTiempoEstimado.value.length < 1) {
        InputTiempoEstimado.classList.remove('is-valid', 'is-invalid');
    } else {
        if (tiempo <= 0 || isNaN(tiempo)) {
            Swal.fire({
                position: "center",
                icon: "error",
                title: "Tiempo inválido",
                text: "El tiempo estimado debe ser mayor a cero",
                showConfirmButton: true,
            });

            InputTiempoEstimado.classList.remove('is-valid');
            InputTiempoEstimado.classList.add('is-invalid');
        } else {
            InputTiempoEstimado.classList.remove('is-invalid');
            InputTiempoEstimado.classList.add('is-valid');
        }
    }
}

const GuardarTipoServicio = async (event) => {
    event.preventDefault();
    BtnGuardar.disabled = true;

    if (!validarFormulario(FormTiposServicio, ['id_tipo_servicio', 'descripcion'])) {
        Swal.fire({
            position: "center",
            icon: "info",
            title: "FORMULARIO INCOMPLETO",
            text: "Debe completar todos los campos obligatorios",
            showConfirmButton: true,
        });
        BtnGuardar.disabled = false;
        return;
    }

    const body = new FormData(FormTiposServicio);

    try {
        const respuesta = await fetch('/proyecto_pmlx/tipos_servicio/guardarAPI', {
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
            BuscarTiposServicio();
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
        await Swal.fire({
            position: "center",
            icon: "error",
            title: "Error de conexión",
            text: "No se pudo conectar con el servidor",
            showConfirmButton: true,
        });
    }
    BtnGuardar.disabled = false;
}

const BuscarTiposServicio = async () => {
    try {
        const respuesta = await fetch('/proyecto_pmlx/tipos_servicio/buscarAPI');
        const datos = await respuesta.json();
        const { codigo, mensaje, data } = datos

        if (codigo == 1) {
            if (datatable) {
                datatable.clear().draw();
                datatable.rows.add(data).draw();
            }
        } else {
            console.error('Error al buscar tipos de servicio:', mensaje);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

const inicializarDataTable = () => {
    if (document.getElementById('TableTiposServicio')) {
        datatable = new DataTable('#TableTiposServicio', {
            dom: `
                <"row mt-3 justify-content-between" 
                    <"col" l> 
                    <"col" B> 
                    <"col-3" f>
                >
                t
                <"row mt-3 justify-content-between" 
                    <"col-md-3 d-flex align-items-center" i> 
                    <"col-md-8 d-flex justify-content-end" p>
                >
            `,
            language: lenguaje,
            data: [],
            columns: [
                {
                    title: 'No.',
                    data: 'id_tipo_servicio',
                    width: '5%',
                    render: (data, type, row, meta) => meta.row + 1
                },
                { 
                    title: 'Nombre del Servicio', 
                    data: 'nombre_servicio',
                    width: '25%',
                    render: (data) => `<strong>${data}</strong>`
                },
                { 
                    title: 'Descripción', 
                    data: 'descripcion',
                    width: '30%',
                    render: (data) => data || '<span class="text-muted">Sin descripción</span>'
                },
                { 
                    title: 'Precio Base', 
                    data: 'precio_base',
                    width: '15%',
                    render: (data) => `<span class="badge bg-success">Q. ${parseFloat(data).toFixed(2)}</span>`
                },
                { 
                    title: 'Tiempo Estimado', 
                    data: 'tiempo_estimado_horas',
                    width: '15%',
                    render: (data) => {
                        const horas = parseInt(data);
                        if (horas === 1) {
                            return `<span class="badge bg-info">${horas} hora</span>`;
                        } else {
                            return `<span class="badge bg-info">${horas} horas</span>`;
                        }
                    }
                },
                {
                    title: 'Acciones',
                    data: 'id_tipo_servicio',
                    width: '10%',
                    searchable: false,
                    orderable: false,
                    render: (data, type, row) => `
                        <div class='d-flex justify-content-center'>
                            <button class='btn btn-warning btn-sm modificar mx-1' 
                                data-id="${data}" 
                                data-nombre="${row.nombre_servicio}"  
                                data-descripcion="${row.descripcion || ''}"  
                                data-precio="${row.precio_base}"  
                                data-tiempo="${row.tiempo_estimado_horas}"  
                                title="Modificar tipo de servicio">
                                <i class='bi bi-pencil-square'></i>
                            </button>
                            <button class='btn btn-danger btn-sm eliminar mx-1' 
                                data-id="${data}"
                                data-nombre="${row.nombre_servicio}"
                                title="Eliminar tipo de servicio">
                               <i class="bi bi-x-circle"></i>
                            </button>
                        </div>`
                }
            ]
        });
    }
}

const llenarFormulario = (event) => {
    const datos = event.currentTarget.dataset;

    document.getElementById('id_tipo_servicio').value = datos.id;
    document.getElementById('nombre_servicio').value = datos.nombre;
    document.getElementById('descripcion').value = datos.descripcion;
    document.getElementById('precio_base').value = datos.precio;
    document.getElementById('tiempo_estimado_horas').value = datos.tiempo;

    BtnGuardar.classList.add('d-none');
    BtnModificar.classList.remove('d-none');

    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

const limpiarTodo = () => {
    if (FormTiposServicio) {
        FormTiposServicio.reset();
    }
    if (BtnGuardar) BtnGuardar.classList.remove('d-none');
    if (BtnModificar) BtnModificar.classList.add('d-none');
    
    // Limpiar clases de validación
    const inputs = FormTiposServicio.querySelectorAll('input');
    inputs.forEach(input => {
        input.classList.remove('is-valid', 'is-invalid');
    });
}

const ModificarTipoServicio = async (event) => {
    event.preventDefault();
    BtnModificar.disabled = true;

    if (!validarFormulario(FormTiposServicio, ['descripcion'])) {
        Swal.fire({
            position: "center",
            icon: "info",
            title: "FORMULARIO INCOMPLETO",
            text: "Debe completar todos los campos obligatorios",
            showConfirmButton: true,
        });
        BtnModificar.disabled = false;
        return;
    }

    const body = new FormData(FormTiposServicio);

    try {
        const respuesta = await fetch('/proyecto_pmlx/tipos_servicio/modificarAPI', {
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
            BuscarTiposServicio();
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
    BtnModificar.disabled = false;
}

const EliminarTipoServicio = async (e) => {
    const idTipoServicio = e.currentTarget.dataset.id;
    const nombreServicio = e.currentTarget.dataset.nombre;

    const AlertaConfirmarEliminar = await Swal.fire({
        position: "center",
        icon: "question",
        title: "¿Desea eliminar este tipo de servicio?",
        text: `El servicio "${nombreServicio}" será desactivado del sistema`,
        showConfirmButton: true,
        confirmButtonText: 'Sí, eliminar',
        confirmButtonColor: '#dc3545',
        cancelButtonText: 'No, Cancelar',
        showCancelButton: true
    });

    if (AlertaConfirmarEliminar.isConfirmed) {
        try {
            const consulta = await fetch(`/proyecto_pmlx/tipos_servicio/eliminar?id=${idTipoServicio}`);
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

                BuscarTiposServicio();
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

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Iniciando aplicación de Tipos de Servicio...');
    
    inicializarDataTable();
    
    setTimeout(() => {
        BuscarTiposServicio();
    }, 100);
    
    if (FormTiposServicio) {
        FormTiposServicio.addEventListener('submit', GuardarTipoServicio);
    }

    if (InputPrecioBase) {
        InputPrecioBase.addEventListener('change', ValidarPrecio);
    }

    if (InputTiempoEstimado) {
        InputTiempoEstimado.addEventListener('change', ValidarTiempo);
    }

    if (BtnLimpiar) {
        BtnLimpiar.addEventListener('click', limpiarTodo);
    }

    if (BtnModificar) {
        BtnModificar.addEventListener('click', ModificarTipoServicio);
    }

    // Event listeners para DataTable
    setTimeout(() => {
        if (datatable) {
            datatable.on('click', '.eliminar', EliminarTipoServicio);
            datatable.on('click', '.modificar', llenarFormulario);
            console.log('✅ Event listeners del DataTable configurados');
        }
    }, 500);
    
    console.log('🎉 Inicialización completada');
});