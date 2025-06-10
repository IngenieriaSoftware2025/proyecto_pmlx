import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario } from '../funciones';
import DataTable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";

const FormUsuarios = document.getElementById('FormUsuarios');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const InputEmail = document.getElementById('email');
const SelectRol = document.getElementById('id_rol');

const ValidarEmail = () => {
    const email = InputEmail.value;
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (email.length < 1) {
        InputEmail.classList.remove('is-valid', 'is-invalid');
    } else {
        if (!emailPattern.test(email)) {
            Swal.fire({
                position: "center",
                icon: "error",
                title: "Email inválido",
                text: "El formato del email no es válido",
                showConfirmButton: true,
            });

            InputEmail.classList.remove('is-valid');
            InputEmail.classList.add('is-invalid');
        } else {
            InputEmail.classList.remove('is-invalid');
            InputEmail.classList.add('is-valid');
        }
    }
}

const GuardarUsuario = async (event) => {
    event.preventDefault();
    BtnGuardar.disabled = true;

    if (!validarFormulario(FormUsuarios, ['id_usuario'])) {
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

    const body = new FormData(FormUsuarios);

    const url = '/proyecto_pmlx/usuarios/guardarAPI';
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
            BuscarUsuarios();

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

const BuscarUsuarios = async () => {
    const url = '/proyecto_pmlx/usuarios/buscarAPI';
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

const CargarRoles = async () => {
    const url = '/proyecto_pmlx/usuarios/rolesAPI';
    const config = {
        method: 'GET'
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje, data } = datos

        if (codigo == 1) {
            SelectRol.innerHTML = '<option value="">Seleccione un rol</option>';
            data.forEach(rol => {
                SelectRol.innerHTML += `<option value="${rol.id_rol}">${rol.nombre_rol}</option>`;
            });
        } else {
            console.log('Error al cargar roles:', mensaje);
        }

    } catch (error) {
        console.log(error)
    }
}

const datatable = new DataTable('#TableUsuarios', {
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
            data: 'id_usuario',
            width: '5%',
            render: (data, type, row, meta) => meta.row + 1
        },
        { 
            title: 'Usuario', 
            data: 'nombre_usuario',
            width: '15%'
        },
        { 
            title: 'Nombre Completo', 
            data: 'nombre_completo',
            width: '25%'
        },
        { 
            title: 'Email', 
            data: 'email',
            width: '20%',
            render: (data, type, row) => {
                return data || '<span class="text-muted">No especificado</span>';
            }
        },
        { 
            title: 'Teléfono', 
            data: 'telefono',
            width: '12%',
            render: (data, type, row) => {
                return data || '<span class="text-muted">No especificado</span>';
            }
        },
        { 
            title: 'Rol', 
            data: 'nombre_rol',
            width: '13%',
            render: (data, type, row) => {
                let badge = '';
                
                if (data === 'Administrador') {
                    badge = '<span class="badge bg-danger">Administrador</span>';
                } else if (data === 'Empleado') {
                    badge = '<span class="badge bg-primary">Empleado</span>';
                } else if (data === 'Técnico') {
                    badge = '<span class="badge bg-success">Técnico</span>';
                } else {
                    badge = `<span class="badge bg-secondary">${data}</span>`;
                }
                
                return badge;
            }
        },
        {
            title: 'Acciones',
            data: 'id_usuario',
            width: '10%',
            searchable: false,
            orderable: false,
            render: (data, type, row, meta) => {
                return `
                 <div class='d-flex justify-content-center'>
                     <button class='btn btn-warning btn-sm modificar mx-1' 
                         data-id="${data}" 
                         data-usuario="${row.nombre_usuario}"  
                         data-nombre="${row.nombre_completo}"  
                         data-email="${row.email || ''}"  
                         data-telefono="${row.telefono || ''}"  
                         data-rol="${row.nombre_rol}"
                         title="Modificar usuario">
                         <i class='bi bi-pencil-square me-1'></i> Modificar
                     </button>
                     <button class='btn btn-danger btn-sm eliminar mx-1' 
                         data-id="${data}"
                         data-nombre="${row.nombre_completo}"
                         title="Eliminar usuario">
                        <i class="bi bi-x-circle me-1"></i>Eliminar
                     </button>
                 </div>`;
            }
        }
    ]
});

const llenarFormulario = async (event) => {
    const datos = event.currentTarget.dataset

    document.getElementById('id_usuario').value = datos.id
    document.getElementById('nombre_usuario').value = datos.usuario
    document.getElementById('nombre_completo').value = datos.nombre
    document.getElementById('email').value = datos.email
    document.getElementById('telefono').value = datos.telefono
    
    // Buscar el rol por nombre y seleccionarlo
    const opciones = SelectRol.querySelectorAll('option');
    opciones.forEach(opcion => {
        if (opcion.textContent === datos.rol) {
            opcion.selected = true;
        }
    });

    // Limpiar el campo de password
    document.getElementById('password').value = '';
    
    // Cambiar el placeholder del password
    document.getElementById('password').placeholder = 'Dejar vacío para mantener la actual';

    BtnGuardar.classList.add('d-none');
    BtnModificar.classList.remove('d-none');

    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

const limpiarTodo = () => {
    FormUsuarios.reset();
    BtnGuardar.classList.remove('d-none');
    BtnModificar.classList.add('d-none');
    
    // Restaurar placeholder original del password
    document.getElementById('password').placeholder = 'Contraseña';
    
    const inputs = FormUsuarios.querySelectorAll('input, select');
    inputs.forEach(input => {
        input.classList.remove('is-valid', 'is-invalid');
    });
}

const ModificarUsuario = async (event) => {
    event.preventDefault();
    BtnModificar.disabled = true;

    if (!validarFormulario(FormUsuarios, ['password'])) {
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

    const body = new FormData(FormUsuarios);

    const url = '/proyecto_pmlx/usuarios/modificarAPI';
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
            BuscarUsuarios();

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

const EliminarUsuario = async (e) => {
    const idUsuario = e.currentTarget.dataset.id
    const nombreUsuario = e.currentTarget.dataset.nombre

    const AlertaConfirmarEliminar = await Swal.fire({
        position: "center",
        icon: "question",
        title: "¿Desea eliminar este usuario?",
        text: `El usuario "${nombreUsuario}" será desactivado pero no eliminado permanentemente`,
        showConfirmButton: true,
        confirmButtonText: 'Sí, eliminar',
        confirmButtonColor: '#dc3545',
        cancelButtonText: 'No, Cancelar',
        showCancelButton: true
    });

    if (AlertaConfirmarEliminar.isConfirmed) {
        const url = `/proyecto_pmlx/usuarios/eliminar?id=${idUsuario}`;
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

                BuscarUsuarios();
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
CargarRoles();
BuscarUsuarios();

// Event Listeners
datatable.on('click', '.eliminar', EliminarUsuario);
datatable.on('click', '.modificar', llenarFormulario);
FormUsuarios.addEventListener('submit', GuardarUsuario);
InputEmail.addEventListener('change', ValidarEmail);
BtnLimpiar.addEventListener('click', limpiarTodo);
BtnModificar.addEventListener('click', ModificarUsuario);