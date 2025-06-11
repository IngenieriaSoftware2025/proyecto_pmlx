import Swal from "sweetalert2";
import { validarFormulario } from '../funciones';
import DataTable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";

const FormModelos = document.getElementById('FormModelos');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const SelectMarca = document.getElementById('id_marca');
const InputPrecio = document.getElementById('precio_referencia');

let datatable;

const ValidarPrecio = () => {
    if (!InputPrecio) return;
    
    const precio = InputPrecio.value;

    if (precio.length < 1) {
        InputPrecio.classList.remove('is-valid', 'is-invalid');
    } else {
        if (precio < 0) {
            Swal.fire({
                position: "center",
                icon: "error",
                title: "Precio inválido",
                text: "El precio de referencia no puede ser negativo",
                showConfirmButton: true,
            });

            InputPrecio.classList.remove('is-valid');
            InputPrecio.classList.add('is-invalid');
        } else {
            InputPrecio.classList.remove('is-invalid');
            InputPrecio.classList.add('is-valid');
        }
    }
}

const GuardarModelo = async (event) => {
    event.preventDefault();
    BtnGuardar.disabled = true;

    if (!validarFormulario(FormModelos, ['id_modelo'])) {
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

    const body = new FormData(FormModelos);

    const url = '/proyecto_pmlx/modelos/guardarAPI';
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
            BuscarModelos();

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

const BuscarModelos = async () => {
    const url = '/proyecto_pmlx/modelos/buscarAPI';
    const config = {
        method: 'GET'
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje, data } = datos

        if (codigo == 1) {
            if (datatable) {
                datatable.clear().draw();
                datatable.rows.add(data).draw();
            }
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

const CargarMarcas = async () => {
    if (!SelectMarca) return;

    const url = '/proyecto_pmlx/marcas/disponibles';
    
    try {
        const respuesta = await fetch(url);
        const datos = await respuesta.json();
        const { codigo, mensaje, data } = datos;

        if (codigo == 1 && data) {
            SelectMarca.innerHTML = '<option value="">Seleccione una marca</option>';
            data.forEach(marca => {
                SelectMarca.innerHTML += `<option value="${marca.id_marca}">${marca.nombre_marca}</option>`;
            });
        }

    } catch (error) {
        console.error('Error al cargar marcas:', error);
    }
}

const inicializarDataTable = () => {
    if (document.getElementById('TableModelos')) {
        datatable = new DataTable('#TableModelos', {
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
                    data: 'id_modelo',
                    width: '5%',
                    render: (data, type, row, meta) => meta.row + 1
                },
                { 
                    title: 'Marca', 
                    data: 'nombre_marca',
                    width: '15%'
                },
                { 
                    title: 'Modelo', 
                    data: 'nombre_modelo',
                    width: '20%'
                },
                { 
                    title: 'Especificaciones', 
                    data: 'especificaciones',
                    width: '25%',
                    render: (data, type, row) => {
                        if (data && data.length > 50) {
                            return data.substring(0, 50) + '...';
                        }
                        return data || '<span class="text-muted">Sin especificaciones</span>';
                    }
                },
                { 
                    title: 'Precio Referencia', 
                    data: 'precio_referencia',
                    width: '12%',
                    render: (data, type, row) => {
                        const precio = parseFloat(data);
                        if (precio > 0) {
                            return `Q. ${precio.toFixed(2)}`;
                        }
                        return '<span class="text-muted">No especificado</span>';
                    }
                },
                { 
                    title: 'Fecha Creación', 
                    data: 'fecha_creacion',
                    width: '8%',
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
                    data: 'id_modelo',
                    width: '15%',
                    searchable: false,
                    orderable: false,
                    render: (data, type, row, meta) => {
                        return `
                         <div class='d-flex justify-content-center'>
                             <button class='btn btn-warning btn-sm modificar mx-1' 
                                 data-id="${data}" 
                                 data-marca="${row.id_marca}"
                                 data-modelo="${row.nombre_modelo}"  
                                 data-especificaciones="${row.especificaciones || ''}"
                                 data-precio="${row.precio_referencia || ''}"
                                 title="Modificar modelo">
                                 <i class='bi bi-pencil-square me-1'></i> Modificar
                             </button>
                             <button class='btn btn-danger btn-sm eliminar mx-1' 
                                 data-id="${data}"
                                 data-modelo="${row.nombre_modelo}"
                                 data-marca="${row.nombre_marca}"
                                 title="Eliminar modelo">
                                <i class="bi bi-x-circle me-1"></i>Eliminar
                             </button>
                         </div>`;
                    }
                }
            ]
        });
    }
}

const llenarFormulario = (event) => {
    const datos = event.currentTarget.dataset

    document.getElementById('id_modelo').value = datos.id
    document.getElementById('nombre_modelo').value = datos.modelo
    document.getElementById('especificaciones').value = datos.especificaciones
    document.getElementById('precio_referencia').value = datos.precio

    if (SelectMarca) {
        SelectMarca.value = datos.marca;
    }

    if (BtnGuardar) BtnGuardar.classList.add('d-none');
    if (BtnModificar) BtnModificar.classList.remove('d-none');

    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

const limpiarTodo = () => {
    if (FormModelos) {
        FormModelos.reset();
    }
    if (BtnGuardar) BtnGuardar.classList.remove('d-none');
    if (BtnModificar) BtnModificar.classList.add('d-none');
    
    const inputs = document.querySelectorAll('#FormModelos input, #FormModelos textarea, #FormModelos select');
    inputs.forEach(input => {
        input.classList.remove('is-valid', 'is-invalid');
    });
}

const ModificarModelo = async (event) => {
    event.preventDefault();
    BtnModificar.disabled = true;

    if (!validarFormulario(FormModelos, [''])) {
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

    const body = new FormData(FormModelos);

    const url = '/proyecto_pmlx/modelos/modificarAPI';
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
            BuscarModelos();

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

const EliminarModelo = async (e) => {
    const idModelo = e.currentTarget.dataset.id
    const nombreModelo = e.currentTarget.dataset.modelo
    const nombreMarca = e.currentTarget.dataset.marca

    const AlertaConfirmarEliminar = await Swal.fire({
        position: "center",
        icon: "question",
        title: "¿Desea eliminar este modelo?",
        text: `El modelo "${nombreModelo}" de ${nombreMarca} será desactivado`,
        showConfirmButton: true,
        confirmButtonText: 'Sí, eliminar',
        confirmButtonColor: '#dc3545',
        cancelButtonText: 'No, Cancelar',
        showCancelButton: true
    });

    if (AlertaConfirmarEliminar.isConfirmed) {
        const url = `/proyecto_pmlx/modelos/eliminar?id=${idModelo}`;
        
        try {
            const consulta = await fetch(url);
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

                BuscarModelos();
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

// Event Listeners e inicialización
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar DataTable
    inicializarDataTable();
    
    // Cargar datos
    setTimeout(() => {
        CargarMarcas();
        BuscarModelos();
    }, 100);
    
    // Event listeners para formulario
    if (FormModelos) {
        FormModelos.addEventListener('submit', GuardarModelo);
    }

    if (InputPrecio) {
        InputPrecio.addEventListener('change', ValidarPrecio);
    }

    if (BtnLimpiar) {
        BtnLimpiar.addEventListener('click', limpiarTodo);
    }

    if (BtnModificar) {
        BtnModificar.addEventListener('click', ModificarModelo);
    }

    // Event listeners para DataTable (se agregan después de que se inicialice)
    setTimeout(() => {
        if (datatable) {
            datatable.on('click', '.eliminar', EliminarModelo);
            datatable.on('click', '.modificar', llenarFormulario);
        }
    }, 500);
});