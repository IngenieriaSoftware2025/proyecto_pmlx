import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario } from '../funciones';
import DataTable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";

const FormClientes = document.getElementById('FormClientes');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const InputEmail = document.getElementById('email');

let datatable;

const ValidarEmail = () => {
    if (!InputEmail) return;
    
    const email = InputEmail.value;
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (email.length < 1) {
        InputEmail.classList.remove('is-valid', 'is-invalid');
    } else {
        if (!emailPattern.test(email)) {
            InputEmail.classList.remove('is-valid');
            InputEmail.classList.add('is-invalid');
        } else {
            InputEmail.classList.remove('is-invalid');
            InputEmail.classList.add('is-valid');
        }
    }
}

const GuardarCliente = async (event) => {
    event.preventDefault();
    BtnGuardar.disabled = true;

    if (!validarFormulario(FormClientes, ['id_cliente'])) {
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

    const body = new FormData(FormClientes);

    try {
        const respuesta = await fetch('/proyecto_pmlx/clientes/guardarAPI', {
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
            BuscarClientes();
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

const BuscarClientes = async () => {
    try {
        const respuesta = await fetch('/proyecto_pmlx/clientes/buscarAPI');
        const datos = await respuesta.json();
        const { codigo, mensaje, data } = datos

        if (codigo == 1) {
            if (datatable) {
                datatable.clear().draw();
                datatable.rows.add(data).draw();
            }
        }
    } catch (error) {
        console.log(error)
    }
}

const inicializarDataTable = () => {
    if (document.getElementById('TableClientes')) {
        datatable = new DataTable('#TableClientes', {
            language: lenguaje,
            data: [],
            columns: [
                {
                    title: 'No.',
                    data: 'id_cliente',
                    render: (data, type, row, meta) => meta.row + 1
                },
                { 
                    title: 'Nombre', 
                    data: 'nombre'
                },
                { 
                    title: 'NIT', 
                    data: 'nit',
                    render: (data) => data || '<span class="text-muted">No especificado</span>'
                },
                { 
                    title: 'Teléfonos', 
                    data: 'telefono',
                    render: (data, type, row) => {
                        let telefonos = [];
                        if (data) telefonos.push(`Tel: ${data}`);
                        if (row.celular) telefonos.push(`Cel: ${row.celular}`);
                        return telefonos.length > 0 ? telefonos.join('<br>') : '<span class="text-muted">No especificado</span>';
                    }
                },
                { 
                    title: 'Email', 
                    data: 'email',
                    render: (data) => data || '<span class="text-muted">No especificado</span>'
                },
                {
                    title: 'Acciones',
                    data: 'id_cliente',
                    orderable: false,
                    render: (data, type, row) => `
                        <button class="btn btn-warning btn-sm modificar" 
                            data-id="${data}"
                            data-nombre="${row.nombre}"  
                            data-nit="${row.nit || ''}"  
                            data-telefono="${row.telefono || ''}"  
                            data-celular="${row.celular || ''}"  
                            data-email="${row.email || ''}"  
                            data-direccion="${row.direccion || ''}">
                            <i class="bi bi-pencil"></i> Modificar
                        </button>
                        <button class="btn btn-danger btn-sm eliminar" 
                            data-id="${data}"
                            data-nombre="${row.nombre}">
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

    document.getElementById('id_cliente').value = datos.id
    document.getElementById('nombre').value = datos.nombre
    document.getElementById('nit').value = datos.nit
    document.getElementById('telefono').value = datos.telefono
    document.getElementById('celular').value = datos.celular
    document.getElementById('email').value = datos.email
    document.getElementById('direccion').value = datos.direccion

    if (BtnGuardar) BtnGuardar.classList.add('d-none');
    if (BtnModificar) BtnModificar.classList.remove('d-none');

    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

const ModificarCliente = async (event) => {
    event.preventDefault();
    BtnModificar.disabled = true;

    if (!validarFormulario(FormClientes, [''])) {
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

    const body = new FormData(FormClientes);

    try {
        const respuesta = await fetch('/proyecto_pmlx/clientes/modificarAPI', {
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
            BuscarClientes();
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

const EliminarCliente = async (e) => {
    const idCliente = e.currentTarget.dataset.id
    const nombreCliente = e.currentTarget.dataset.nombre

    const AlertaConfirmarEliminar = await Swal.fire({
        position: "center",
        icon: "question",
        title: "¿Desea eliminar este cliente?",
        text: `El cliente "${nombreCliente}" será desactivado`,
        showConfirmButton: true,
        confirmButtonText: 'Sí, eliminar',
        confirmButtonColor: '#dc3545',
        cancelButtonText: 'No, Cancelar',
        showCancelButton: true
    });

    if (AlertaConfirmarEliminar.isConfirmed) {
        try {
            const consulta = await fetch(`/proyecto_pmlx/clientes/eliminar?id=${idCliente}`);
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

                BuscarClientes();
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
    if (FormClientes) {
        FormClientes.reset();
    }
    if (BtnGuardar) BtnGuardar.classList.remove('d-none');
    if (BtnModificar) BtnModificar.classList.add('d-none');
}

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    inicializarDataTable();
    
    setTimeout(() => {
        BuscarClientes();
    }, 100);
    
    if (FormClientes) {
        FormClientes.addEventListener('submit', GuardarCliente);
    }

    if (InputEmail) {
        InputEmail.addEventListener('change', ValidarEmail);
    }

    if (BtnLimpiar) {
        BtnLimpiar.addEventListener('click', limpiarTodo);
    }

    if (BtnModificar) {
        BtnModificar.addEventListener('click', ModificarCliente);
    }

    // Event listeners para DataTable (se agregan después de que se inicialice)
    setTimeout(() => {
        if (datatable) {
            datatable.on('click', '.eliminar', EliminarCliente);
            datatable.on('click', '.modificar', llenarFormulario);
        }
    }, 500);
});