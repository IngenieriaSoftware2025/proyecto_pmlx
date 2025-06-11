import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario } from '../funciones';
import DataTable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";

const FormMarcas = document.getElementById('FormMarcas');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');

const GuardarMarca = async (event) => {
    event.preventDefault();
    BtnGuardar.disabled = true;

    if (!validarFormulario(FormMarcas, ['id_marca'])) {
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

    const body = new FormData(FormMarcas);

    const url = '/proyecto_pmlx/marcas/guardarAPI';
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
            BuscarMarcas();

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

const BuscarMarcas = async () => {
    const url = '/proyecto_pmlx/marcas/buscarAPI';
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

const datatable = new DataTable('#TableMarcas', {
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
            data: 'id_marca',
            width: '5%',
            render: (data, type, row, meta) => meta.row + 1
        },
        { 
            title: 'Nombre de la Marca', 
            data: 'nombre_marca',
            width: '20%',
            render: (data, type, row) => {
                // Determinar el color del badge según la marca
                let badgeClass = '';
                const marca = data.toLowerCase();
                
                if (marca.includes('samsung')) {
                    badgeClass = 'bg-primary';
                } else if (marca.includes('apple') || marca.includes('iphone')) {
                    badgeClass = 'bg-secondary';
                } else if (marca.includes('huawei')) {
                    badgeClass = 'bg-danger';
                } else if (marca.includes('xiaomi')) {
                    badgeClass = 'bg-warning text-dark';
                } else if (marca.includes('motorola')) {
                    badgeClass = 'bg-info';
                } else {
                    badgeClass = 'bg-success';
                }
                
                return `<span class="badge ${badgeClass} me-2">Marca</span>${data}`;
            }
        },
        { 
            title: 'Descripción', 
            data: 'descripcion',
            width: '30%',
            render: (data, type, row) => {
                return data || '<span class="text-muted">Sin descripción</span>';
            }
        },
        { 
            title: 'Modelos Registrados', 
            data: 'modelos_registrados',
            width: '10%',
            render: (data, type, row) => {
                const cantidad = parseInt(data);
                let badge = '';
                
                if (cantidad === 0) {
                    badge = '<span class="badge bg-light text-dark">Sin modelos</span>';
                } else if (cantidad === 1) {
                    badge = '<span class="badge bg-info">1 modelo</span>';
                } else {
                    badge = `<span class="badge bg-warning">${cantidad} modelos</span>`;
                }
                
                return badge;
            }
        },
        { 
            title: 'Usuario Creador', 
            data: 'usuario_creador',
            width: '15%',
            render: (data, type, row) => {
                return data || '<span class="text-muted">Sistema</span>';
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
            data: 'id_marca',
            width: '10%',
            searchable: false,
            orderable: false,
            render: (data, type, row, meta) => {
                return `
                 <div class='d-flex justify-content-center'>
                     <button class='btn btn-warning btn-sm modificar mx-1' 
                         data-id="${data}" 
                         data-nombre="${row.nombre_marca}"  
                         data-descripcion="${row.descripcion || ''}"  
                         title="Modificar marca">
                         <i class='bi bi-pencil-square me-1'></i> Modificar
                     </button>
                     <button class='btn btn-danger btn-sm eliminar mx-1' 
                         data-id="${data}"
                         data-nombre="${row.nombre_marca}"
                         data-modelos="${row.modelos_registrados}"
                         title="Eliminar marca">
                        <i class="bi bi-x-circle me-1"></i>Eliminar
                     </button>
                 </div>`;
            }
        }
    ]
});

const llenarFormulario = (event) => {
    const datos = event.currentTarget.dataset

    document.getElementById('id_marca').value = datos.id
    document.getElementById('nombre_marca').value = datos.nombre
    document.getElementById('descripcion').value = datos.descripcion

    BtnGuardar.classList.add('d-none');
    BtnModificar.classList.remove('d-none');

    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

const limpiarTodo = () => {
    FormMarcas.reset();
    BtnGuardar.classList.remove('d-none');
    BtnModificar.classList.add('d-none');
    
    const inputs = FormMarcas.querySelectorAll('input, textarea');
    inputs.forEach(input => {
        input.classList.remove('is-valid', 'is-invalid');
    });
}

const ModificarMarca = async (event) => {
    event.preventDefault();
    BtnModificar.disabled = true;

    if (!validarFormulario(FormMarcas, [''])) {
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

    const body = new FormData(FormMarcas);

    const url = '/proyecto_pmlx/marcas/modificarAPI';
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
            BuscarMarcas();

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

const EliminarMarca = async (e) => {
    const idMarca = e.currentTarget.dataset.id
    const nombreMarca = e.currentTarget.dataset.nombre

    const AlertaConfirmarEliminar = await Swal.fire({
        position: "center",
        icon: "question",
        title: "¿Desea eliminar esta marca?",
        text: `La marca "${nombreMarca}" será desactivada pero no eliminada permanentemente`,
        showConfirmButton: true,
        confirmButtonText: 'Sí, eliminar',
        confirmButtonColor: '#dc3545',
        cancelButtonText: 'No, Cancelar',
        showCancelButton: true
    });

    if (AlertaConfirmarEliminar.isConfirmed) {
        const url = `/proyecto_pmlx/marcas/eliminar?id=${idMarca}`;
        console.log('URL de eliminación:', url); // Debug
        
        const config = {
            method: 'GET'
        }

        try {
            const consulta = await fetch(url, config);
            console.log('Status de respuesta:', consulta.status); // Debug
            
            const textoRespuesta = await consulta.text();
            console.log('Respuesta cruda:', textoRespuesta); // Debug
            
            const respuesta = JSON.parse(textoRespuesta);
            console.log('Respuesta parseada:', respuesta); // Debug
            
            const { codigo, mensaje } = respuesta;

            if (codigo == 1) {
                await Swal.fire({
                    position: "center",
                    icon: "success",
                    title: "Éxito",
                    text: mensaje,
                    showConfirmButton: true,
                });

                BuscarMarcas();
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
            console.log('Error completo:', error);
            await Swal.fire({
                position: "center",
                icon: "error",
                title: "Error de conexión",
                text: "No se pudo conectar con el servidor",
                showConfirmButton: true,
            });
        }
    }
}

// Inicializar
BuscarMarcas();

// Event Listeners
datatable.on('click', '.eliminar', EliminarMarca);
datatable.on('click', '.modificar', llenarFormulario);
FormMarcas.addEventListener('submit', GuardarMarca);
BtnLimpiar.addEventListener('click', limpiarTodo);
BtnModificar.addEventListener('click', ModificarMarca);