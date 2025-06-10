import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario } from '../funciones';
import DataTable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";

const FormRoles = document.getElementById('FormRoles');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');

const GuardarRol = async (event) => {
    event.preventDefault();
    BtnGuardar.disabled = true;

    if (!validarFormulario(FormRoles, ['id_rol'])) {
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

    const body = new FormData(FormRoles);

    const url = '/proyecto_pmlx/roles/guardarAPI';
    const config = {
        method: 'POST',
        body
    }

    try {
        const respuesta = await fetch(url, config);
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
            BuscarRoles();

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

const BuscarRoles = async () => {
    const url = '/proyecto_pmlx/roles/buscarAPI';
    const config = {
        method: 'GET'
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje, data } = datos

        if (codigo == 1) {
            datatable.clear().draw();
            datatable.rows.add(data).draw();
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
}

const datatable = new DataTable('#TableRoles', {
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
            data: 'id_rol',
            width: '5%',
            render: (data, type, row, meta) => meta.row + 1
        },
        { 
            title: 'Nombre del Rol', 
            data: 'nombre_rol',
            width: '20%',
            render: (data, type, row) => {
                let badge = '';
                
                if (data === 'Administrador') {
                    badge = '<span class="badge bg-danger me-2">Sistema</span>';
                } else if (data === 'Empleado') {
                    badge = '<span class="badge bg-primary me-2">Sistema</span>';
                } else if (data === 'Técnico') {
                    badge = '<span class="badge bg-success me-2">Sistema</span>';
                } else {
                    badge = '<span class="badge bg-secondary me-2">Personalizado</span>';
                }
                
                return badge + data;
            }
        },
        { 
            title: 'Descripción', 
            data: 'descripcion',
            width: '40%'
        },
        { 
            title: 'Usuarios Asignados', 
            data: 'usuarios_asignados',
            width: '15%',
            render: (data, type, row) => {
                const cantidad = parseInt(data);
                let badge = '';
                
                if (cantidad === 0) {
                    badge = '<span class="badge bg-light text-dark">Sin usuarios</span>';
                } else if (cantidad === 1) {
                    badge = '<span class="badge bg-info">1 usuario</span>';
                } else {
                    badge = `<span class="badge bg-warning">${cantidad} usuarios</span>`;
                }
                
                return badge;
            }
        },
        { 
            title: 'Fecha Creación', 
            data: 'fecha_creacion',
            width: '10%',
            render: (data, type, row) => {
                if (data) {
                    const fecha = new Date(data);
                    return fecha.toLocaleDateString('es-GT');
                }
                return '<span class="text-muted">N/A</span>';
            }
        },
        {
            title: 'Acciones',
            data: 'id_rol',
            width: '10%',
            searchable: false,
            orderable: false,
            render: (data, type, row, meta) => {
                const rolesDelSistema = ['Administrador', 'Empleado', 'Técnico'];
                const esRolDelSistema = rolesDelSistema.includes(row.nombre_rol);
                
                let botones = `
                 <div class='d-flex justify-content-center'>
                     <button class='btn btn-warning btn-sm modificar mx-1' 
                         data-id="${data}" 
                         data-nombre="${row.nombre_rol}"  
                         data-descripcion="${row.descripcion}"  
                         title="Modificar rol">
                         <i class='bi bi-pencil-square me-1'></i> Modificar
                     </button>`;
                
                if (!esRolDelSistema) {
                    botones += `
                     <button class='btn btn-danger btn-sm eliminar mx-1' 
                         data-id="${data}"
                         data-nombre="${row.nombre_rol}"
                         data-usuarios="${row.usuarios_asignados}"
                         title="Eliminar rol">
                        <i class="bi bi-x-circle me-1"></i>Eliminar
                     </button>`;
                } else {
                    botones += `
                     <button class='btn btn-secondary btn-sm disabled mx-1' 
                         title="No se puede eliminar un rol del sistema">
                        <i class="bi bi-lock-fill me-1"></i>Protegido
                     </button>`;
                }
                
                botones += ` </div>`;
                
                return botones;
            }
        }
    ]
});

const llenarFormulario = (event) => {
    const datos = event.currentTarget.dataset

    document.getElementById('id_rol').value = datos.id
    document.getElementById('nombre_rol').value = datos.nombre
    document.getElementById('descripcion').value = datos.descripcion

    BtnGuardar.classList.add('d-none');
    BtnModificar.classList.remove('d-none');

    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

const limpiarTodo = () => {
    FormRoles.reset();
    BtnGuardar.classList.remove('d-none');
    BtnModificar.classList.add('d-none');
    
    const inputs = FormRoles.querySelectorAll('input, textarea');
    inputs.forEach(input => {
        input.classList.remove('is-valid', 'is-invalid');
    });
}

const ModificarRol = async (event) => {
    event.preventDefault();
    BtnModificar.disabled = true;

    if (!validarFormulario(FormRoles, [''])) {
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

    const body = new FormData(FormRoles);

    const url = '/proyecto_pmlx/roles/modificarAPI';
    const config = {
        method: 'POST',
        body
    }

    try {
        const respuesta = await fetch(url, config);
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
            BuscarRoles();

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

const EliminarRol = async (e) => {
    const idRol = e.currentTarget.dataset.id
    const nombreRol = e.currentTarget.dataset.nombre
    const usuariosAsignados = e.currentTarget.dataset.usuarios

    if (parseInt(usuariosAsignados) > 0) {
        await Swal.fire({
            position: "center",
            icon: "warning",
            title: "No se puede eliminar",
            text: `El rol "${nombreRol}" tiene ${usuariosAsignados} usuario(s) asignado(s). Debe reasignar los usuarios antes de eliminar el rol.`,
            showConfirmButton: true,
        });
        return;
    }

    const AlertaConfirmarEliminar = await Swal.fire({
        position: "center",
        icon: "question",
        title: "¿Desea eliminar este rol?",
        text: `El rol "${nombreRol}" será eliminado permanentemente`,
        showConfirmButton: true,
        confirmButtonText: 'Sí, eliminar',
        confirmButtonColor: '#dc3545',
        cancelButtonText: 'No, Cancelar',
        showCancelButton: true
    });

    if (AlertaConfirmarEliminar.isConfirmed) {
        const url = `/proyecto_pmlx/roles/eliminar?id=${idRol}`;
        const config = {
            method: 'GET'
        }

        try {
            const consulta = await fetch(url, config);
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

                BuscarRoles();
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
    }
}

// Inicializar
BuscarRoles();

// Event Listeners
datatable.on('click', '.eliminar', EliminarRol);
datatable.on('click', '.modificar', llenarFormulario);
FormRoles.addEventListener('submit', GuardarRol);
BtnLimpiar.addEventListener('click', limpiarTodo);
BtnModificar.addEventListener('click', ModificarRol);