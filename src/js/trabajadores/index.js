import Swal from "sweetalert2";
import { validarFormulario } from '../funciones';
import DataTable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";

const FormTrabajadores = document.getElementById('FormTrabajadores');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const SelectUsuario = document.getElementById('id_usuario');
const InputEspecialidad = document.getElementById('especialidad');

let datatable;
let esModificacion = false;

const CargarUsuarios = async (paraModificacion = false) => {
    if (!SelectUsuario) {
        console.error('❌ SelectUsuario no encontrado');
        return;
    }

    console.log('🔄 Cargando usuarios... Para modificación:', paraModificacion);

    try {
        const endpoint = paraModificacion 
            ? '/proyecto_pmlx/trabajadores/todosUsuarios' 
            : '/proyecto_pmlx/trabajadores/usuariosDisponibles';
        
        console.log('📡 Endpoint:', endpoint);
        
        const respuesta = await fetch(endpoint);
        console.log('📡 Respuesta usuarios - Status:', respuesta.status);
        
        if (!respuesta.ok) {
            throw new Error(`HTTP error! status: ${respuesta.status}`);
        }
        
        const datos = await respuesta.json();
        console.log('📦 Datos usuarios recibidos:', datos);
        
        const { codigo, mensaje, data } = datos;

        if (codigo == 1 && data && Array.isArray(data)) {
            SelectUsuario.innerHTML = '<option value="">Seleccione un usuario</option>';
            
            if (data.length > 0) {
                data.forEach(usuario => {
                    console.log('➕ Agregando usuario:', usuario);
                    const infoAdicional = usuario.email ? ` (${usuario.email})` : '';
                    SelectUsuario.innerHTML += `<option value="${usuario.id_usuario}">${usuario.nombre_completo}${infoAdicional}</option>`;
                });
                console.log('✅ Usuarios cargados exitosamente:', data.length);
            } else {
                SelectUsuario.innerHTML += '<option value="" disabled>No hay usuarios disponibles</option>';
                console.log('ℹ️ No hay usuarios disponibles');
                
                if (!paraModificacion) {
                    await Swal.fire({
                        position: "center",
                        icon: "warning",
                        title: "Sin usuarios disponibles",
                        text: "Todos los usuarios activos ya están registrados como trabajadores.",
                        showConfirmButton: true,
                    });
                }
            }
        } else {
            console.warn('⚠️ Error en los datos de usuarios:', mensaje);
            SelectUsuario.innerHTML = '<option value="">Error al cargar usuarios</option>';
        }
    } catch (error) {
        console.error('❌ Error al cargar usuarios:', error);
        SelectUsuario.innerHTML = '<option value="">Error de conexión</option>';
    }
}

const ValidarEspecialidad = () => {
    const especialidad = InputEspecialidad.value.trim();

    if (especialidad.length < 1) {
        InputEspecialidad.classList.remove('is-valid', 'is-invalid');
    } else {
        if (especialidad.length < 3) {
            Swal.fire({
                position: "center",
                icon: "error",
                title: "Especialidad inválida",
                text: "La especialidad debe tener al menos 3 caracteres",
                showConfirmButton: true,
            });

            InputEspecialidad.classList.remove('is-valid');
            InputEspecialidad.classList.add('is-invalid');
        } else {
            InputEspecialidad.classList.remove('is-invalid');
            InputEspecialidad.classList.add('is-valid');
        }
    }
}

const GuardarTrabajador = async (event) => {
    event.preventDefault();
    BtnGuardar.disabled = true;

    if (!validarFormulario(FormTrabajadores, ['id_trabajador'])) {
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

    const body = new FormData(FormTrabajadores);

    try {
        const respuesta = await fetch('/proyecto_pmlx/trabajadores/guardarAPI', {
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
            BuscarTrabajadores();
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

const BuscarTrabajadores = async () => {
    try {
        const respuesta = await fetch('/proyecto_pmlx/trabajadores/buscarAPI');
        const datos = await respuesta.json();
        const { codigo, mensaje, data } = datos

        if (codigo == 1) {
            if (datatable) {
                datatable.clear().draw();
                datatable.rows.add(data).draw();
            }
        } else {
            console.error('Error al buscar trabajadores:', mensaje);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

const inicializarDataTable = () => {
    if (document.getElementById('TableTrabajadores')) {
        datatable = new DataTable('#TableTrabajadores', {
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
                    data: 'id_trabajador',
                    width: '5%',
                    render: (data, type, row, meta) => meta.row + 1
                },
                { 
                    title: 'Trabajador', 
                    data: 'nombre_completo',
                    width: '20%',
                    render: (data, type, row) => {
                        const telefono = row.telefono ? `<br><small class="text-muted"><i class="bi bi-telephone"></i> ${row.telefono}</small>` : '';
                        return `<strong>${data}</strong><br><small class="text-muted"><i class="bi bi-envelope"></i> ${row.email}</small>${telefono}`;
                    }
                },
                { 
                    title: 'Especialidad', 
                    data: 'especialidad',
                    width: '25%',
                    render: (data) => `<span class="badge bg-primary">${data}</span>`
                },
                { 
                    title: 'Fecha Registro', 
                    data: 'fecha_registro',
                    width: '10%',
                    render: (data) => {
                        if (data) {
                            const fecha = new Date(data);
                            return fecha.toLocaleDateString('es-GT');
                        }
                        return '<span class="text-muted">Sin fecha</span>';
                    }
                },
                { 
                    title: 'Estado', 
                    data: 'activo',
                    width: '10%',
                    render: (data) => {
                        return data === 'T' 
                            ? '<span class="badge bg-success">Activo</span>'
                            : '<span class="badge bg-danger">Inactivo</span>';
                    }
                },
                {
                    title: 'Acciones',
                    data: 'id_trabajador',
                    width: '20%',
                    searchable: false,
                    orderable: false,
                    render: (data, type, row) => `
                        <div class='d-flex justify-content-center flex-wrap gap-1'>
                            <button class='btn btn-warning btn-sm modificar' 
                                data-id="${data}" 
                                data-usuario="${row.id_usuario}"  
                                data-nombre="${row.nombre_completo}"  
                                data-especialidad="${row.especialidad}"  
                                title="Modificar trabajador">
                                <i class='bi bi-pencil-square me-1'></i>Modificar
                            </button>
                            <button class='btn btn-danger btn-sm eliminar' 
                                data-id="${data}"
                                data-nombre="${row.nombre_completo}"
                                data-especialidad="${row.especialidad}"
                                title="Eliminar trabajador">
                               <i class="bi bi-x-circle me-1"></i>Eliminar
                            </button>
                        </div>`
                }
            ]
        });
        console.log('✅ DataTable inicializado');
    }
}

const llenarFormulario = async (event) => {
    console.log('📝 Iniciando llenado de formulario para modificar...');
    
    const datos = event.currentTarget.dataset;
    esModificacion = true;

    console.log('📦 Datos del trabajador a modificar:', datos);

    // Cargar todos los usuarios para modificación
    await CargarUsuarios(true);

    // Llenar los campos del formulario
    document.getElementById('id_trabajador').value = datos.id;
    document.getElementById('especialidad').value = datos.especialidad;
    
    // Esperar un poco para que se carguen los usuarios y luego seleccionar
    setTimeout(() => {
        document.getElementById('id_usuario').value = datos.usuario;
    }, 200);

    BtnGuardar.classList.add('d-none');
    BtnModificar.classList.remove('d-none');

    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

const limpiarTodo = () => {
    esModificacion = false;
    
    if (FormTrabajadores) {
        FormTrabajadores.reset();
    }
    if (BtnGuardar) BtnGuardar.classList.remove('d-none');
    if (BtnModificar) BtnModificar.classList.add('d-none');
    
    // Limpiar clases de validación
    const inputs = FormTrabajadores.querySelectorAll('input, select');
    inputs.forEach(input => {
        input.classList.remove('is-valid', 'is-invalid');
    });

    // Recargar usuarios disponibles para nuevo registro
    CargarUsuarios(false);
}

const ModificarTrabajador = async (event) => {
    event.preventDefault();
    BtnModificar.disabled = true;

    if (!validarFormulario(FormTrabajadores, [])) {
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

    const body = new FormData(FormTrabajadores);

    try {
        const respuesta = await fetch('/proyecto_pmlx/trabajadores/modificarAPI', {
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
            BuscarTrabajadores();
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

const EliminarTrabajador = async (e) => {
    const idTrabajador = e.currentTarget.dataset.id;
    const nombreTrabajador = e.currentTarget.dataset.nombre;
    const especialidad = e.currentTarget.dataset.especialidad;

    const AlertaConfirmarEliminar = await Swal.fire({
        position: "center",
        icon: "question",
        title: "¿Desea eliminar este trabajador?",
        text: `${nombreTrabajador} (${especialidad}) será desactivado del sistema`,
        showConfirmButton: true,
        confirmButtonText: 'Sí, eliminar',
        confirmButtonColor: '#dc3545',
        cancelButtonText: 'No, Cancelar',
        showCancelButton: true
    });

    if (AlertaConfirmarEliminar.isConfirmed) {
        try {
            const consulta = await fetch(`/proyecto_pmlx/trabajadores/eliminar?id=${idTrabajador}`);
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

                BuscarTrabajadores();
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
    console.log('🚀 Iniciando aplicación de Trabajadores...');
    console.log('🔍 Verificando elementos:');
    console.log('  - SelectUsuario:', SelectUsuario ? '✅' : '❌');
    console.log('  - FormTrabajadores:', FormTrabajadores ? '✅' : '❌');
    
    inicializarDataTable();
    
    setTimeout(() => {
        CargarUsuarios(false); // Cargar usuarios disponibles inicialmente
        BuscarTrabajadores();
    }, 100);
    
    if (FormTrabajadores) {
        FormTrabajadores.addEventListener('submit', GuardarTrabajador);
    }

    if (InputEspecialidad) {
        InputEspecialidad.addEventListener('change', ValidarEspecialidad);
        InputEspecialidad.addEventListener('blur', ValidarEspecialidad);
    }

    if (BtnLimpiar) {
        BtnLimpiar.addEventListener('click', limpiarTodo);
    }

    if (BtnModificar) {
        BtnModificar.addEventListener('click', ModificarTrabajador);
    }

    // Event listeners para DataTable
    setTimeout(() => {
        if (datatable) {
            datatable.on('click', '.eliminar', EliminarTrabajador);
            datatable.on('click', '.modificar', llenarFormulario);
            console.log('✅ Event listeners del DataTable configurados');
        }
    }, 500);
    
    console.log('🎉 Inicialización completada');
});