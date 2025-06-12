import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario } from '../funciones';
import DataTable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";

const FormInventario = document.getElementById('FormInventario');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const SelectMarca = document.getElementById('id_marca');
const SelectModelo = document.getElementById('id_modelo');
const InputPrecioCompra = document.getElementById('precio_compra');
const InputPrecioVenta = document.getElementById('precio_venta');
const InputCantidad = document.getElementById('stock_cantidad');

let datatable;

const ValidarPrecios = () => {
    const precioCompra = parseFloat(InputPrecioCompra?.value || 0);
    const precioVenta = parseFloat(InputPrecioVenta?.value || 0);

    if (precioCompra > 0 && precioVenta > 0) {
        if (precioCompra >= precioVenta) {
            Swal.fire({
                position: "center",
                icon: "warning",
                title: "Precios inconsistentes",
                text: "El precio de venta debería ser mayor al precio de compra",
                showConfirmButton: true,
            });
        }
    }
}

const GuardarInventario = async (event) => {
    event.preventDefault();
    BtnGuardar.disabled = true;

    if (!validarFormulario(FormInventario, ['id_inventario', 'codigo_producto', 'imei', 'ubicacion'])) {
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

    const body = new FormData(FormInventario);

    try {
        const respuesta = await fetch('/proyecto_pmlx/inventario/guardarAPI', {
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
            BuscarInventario();
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

const BuscarInventario = async () => {
    try {
        const respuesta = await fetch('/proyecto_pmlx/inventario/buscarAPI');
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

const CargarMarcas = async () => {
    if (!SelectMarca) {
        console.error('❌ SelectMarca no encontrado');
        return;
    }

    console.log('🔄 Iniciando carga de marcas...');

    try {
        const respuesta = await fetch('/proyecto_pmlx/marcas/disponibles');
        console.log('📡 Respuesta marcas - Status:', respuesta.status);
        
        const datos = await respuesta.json();
        console.log('📦 Datos marcas recibidos:', datos);
        
        const { codigo, mensaje, data } = datos;

        if (codigo == 1 && data) {
            SelectMarca.innerHTML = '<option value="">Seleccione una marca</option>';
            data.forEach(marca => {
                console.log('➕ Agregando marca:', marca);
                SelectMarca.innerHTML += `<option value="${marca.id_marca}">${marca.nombre_marca}</option>`;
            });
            console.log('✅ Marcas cargadas exitosamente:', data.length);
        } else {
            console.warn('⚠️ No se pudieron cargar las marcas:', mensaje);
        }
    } catch (error) {
        console.error('❌ Error al cargar marcas:', error);
        // Fallback: cargar marcas desde un endpoint alternativo
        try {
            console.log('🔄 Intentando endpoint alternativo...');
            const respuestaAlternativa = await fetch('/proyecto_pmlx/marcas/buscarAPI');
            const datosAlternativos = await respuestaAlternativa.json();
            if (datosAlternativos.codigo == 1 && datosAlternativos.data) {
                SelectMarca.innerHTML = '<option value="">Seleccione una marca</option>';
                datosAlternativos.data.forEach(marca => {
                    SelectMarca.innerHTML += `<option value="${marca.id_marca}">${marca.nombre_marca}</option>`;
                });
                console.log('✅ Marcas cargadas desde endpoint alternativo');
            }
        } catch (errorAlternativo) {
            console.error('❌ Error al cargar marcas (alternativo):', errorAlternativo);
        }
    }
}

const CargarModelos = async (id_marca) => {
    if (!SelectModelo) {
        console.error('❌ SelectModelo no encontrado');
        return;
    }
    
    if (!id_marca) {
        console.log('ℹ️ No hay marca seleccionada');
        SelectModelo.innerHTML = '<option value="">Primero seleccione una marca</option>';
        return;
    }

    console.log('🔄 Cargando modelos para marca ID:', id_marca);

    try {
        const url = `/proyecto_pmlx/inventario/modelosPorMarca?id_marca=${id_marca}`;
        console.log('📡 URL petición modelos:', url);
        
        const respuesta = await fetch(url);
        console.log('📡 Respuesta modelos - Status:', respuesta.status);
        
        const datos = await respuesta.json();
        console.log('📦 Datos modelos recibidos:', datos);
        
        const { codigo, mensaje, data } = datos;

        if (codigo == 1 && data) {
            SelectModelo.innerHTML = '<option value="">Seleccione un modelo</option>';
            data.forEach(modelo => {
                console.log('➕ Agregando modelo:', modelo);
                SelectModelo.innerHTML += `<option value="${modelo.id_modelo}">${modelo.nombre_modelo}</option>`;
            });
            console.log('✅ Modelos cargados exitosamente:', data.length);
        } else {
            console.warn('⚠️ No hay modelos disponibles:', mensaje);
            SelectModelo.innerHTML = '<option value="">No hay modelos disponibles</option>';
        }
    } catch (error) {
        console.error('❌ Error al cargar modelos:', error);
        SelectModelo.innerHTML = '<option value="">Error al cargar modelos</option>';
    }
}

const inicializarDataTable = () => {
    if (document.getElementById('TableInventario')) {
        datatable = new DataTable('#TableInventario', {
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
                    data: 'id_inventario',
                    width: '5%',
                    render: (data, type, row, meta) => meta.row + 1
                },
                { 
                    title: 'Producto', 
                    data: 'nombre_marca',
                    width: '20%',
                    render: (data, type, row) => {
                        return `<strong>${data}</strong><br><small class="text-muted">${row.nombre_modelo}</small>`;
                    }
                },
                { 
                    title: 'Código', 
                    data: 'codigo_producto',
                    width: '10%',
                    render: (data) => data || '<span class="text-muted">Sin código</span>'
                },
                { 
                    title: 'IMEI', 
                    data: 'imei',
                    width: '12%',
                    render: (data) => data || '<span class="text-muted">Sin IMEI</span>'
                },
                { 
                    title: 'Estado', 
                    data: 'estado_producto',
                    width: '8%',
                    render: (data) => {
                        const estados = {
                            'N': '<span class="badge bg-success">Nuevo</span>',
                            'U': '<span class="badge bg-warning">Usado</span>',
                            'R': '<span class="badge bg-info">Reacondicionado</span>'
                        };
                        return estados[data] || '<span class="badge bg-secondary">N/A</span>';
                    }
                },
                { 
                    title: 'Precios', 
                    data: 'precio_compra',
                    width: '15%',
                    render: (data, type, row) => {
                        return `Compra: Q. ${parseFloat(data).toFixed(2)}<br>Venta: Q. ${parseFloat(row.precio_venta).toFixed(2)}`;
                    }
                },
                { 
                    title: 'Stock', 
                    data: 'stock_cantidad',
                    width: '8%',
                    render: (data) => {
                        const cantidad = parseInt(data);
                        let badge = '';
                        
                        if (cantidad === 0) {
                            badge = '<span class="badge bg-danger">Agotado</span>';
                        } else if (cantidad <= 5) {
                            badge = `<span class="badge bg-warning">${cantidad}</span>`;
                        } else {
                            badge = `<span class="badge bg-success">${cantidad}</span>`;
                        }
                        
                        return badge;
                    }
                },
                { 
                    title: 'Ubicación', 
                    data: 'ubicacion',
                    width: '10%',
                    render: (data) => data || '<span class="text-muted">Sin ubicación</span>'
                },
                {
                    title: 'Acciones',
                    data: 'id_inventario',
                    width: '12%',
                    searchable: false,
                    orderable: false,
                    render: (data, type, row) => `
                        <button class="btn btn-warning btn-sm modificar" 
                            data-id="${data}"
                            data-modelo="${row.id_modelo}"
                            data-codigo="${row.codigo_producto || ''}"
                            data-imei="${row.imei || ''}"
                            data-estado="${row.estado_producto}"
                            data-precio-compra="${row.precio_compra}"
                            data-precio-venta="${row.precio_venta}"
                            data-cantidad="${row.stock_cantidad}"
                            data-ubicacion="${row.ubicacion || ''}">
                            <i class="bi bi-pencil"></i> Modificar
                        </button>
                        <button class="btn btn-danger btn-sm eliminar" 
                            data-id="${data}"
                            data-producto="${row.nombre_marca} ${row.nombre_modelo}"
                            data-codigo="${row.codigo_producto || 'Sin código'}">
                            <i class="bi bi-trash"></i> Eliminar
                        </button>
                    `
                }
            ]
        });
        console.log('✅ DataTable inicializado');
    }
}

// FUNCIÓN CORREGIDA - Esta era la que estaba incompleta
const llenarFormulario = async (e) => {
    console.log('📝 Iniciando llenado de formulario para modificar...');
    
    const idInventario = e.currentTarget.dataset.id;
    const datos = {
        id: idInventario,
        modelo: e.currentTarget.dataset.modelo,
        codigo: e.currentTarget.dataset.codigo,
        imei: e.currentTarget.dataset.imei,
        estado: e.currentTarget.dataset.estado,
        precioCompra: e.currentTarget.dataset.precioCompra,
        precioVenta: e.currentTarget.dataset.precioVenta,
        cantidad: e.currentTarget.dataset.cantidad,
        ubicacion: e.currentTarget.dataset.ubicacion
    };

    console.log('📦 Datos del producto a modificar:', datos);

    // Llenar los campos del formulario
    document.getElementById('id_inventario').value = datos.id;
    document.getElementById('codigo_producto').value = datos.codigo;
    document.getElementById('imei').value = datos.imei;
    document.getElementById('estado_producto').value = datos.estado;
    document.getElementById('precio_compra').value = datos.precioCompra;
    document.getElementById('precio_venta').value = datos.precioVenta;
    document.getElementById('stock_cantidad').value = datos.cantidad;
    document.getElementById('ubicacion').value = datos.ubicacion;

    // Buscar el modelo y cargar su marca correspondiente
    try {
        console.log('🔍 Buscando información del modelo...');
        const respuesta = await fetch(`/proyecto_pmlx/modelos/buscarAPI`);
        const resultado = await respuesta.json();
        
        if (resultado.codigo == 1) {
            const modeloSeleccionado = resultado.data.find(m => m.id_modelo == datos.modelo);
            if (modeloSeleccionado) {
                console.log('📍 Modelo encontrado:', modeloSeleccionado);
                
                // Seleccionar la marca
                SelectMarca.value = modeloSeleccionado.id_marca;
                
                // Cargar modelos de esa marca
                await CargarModelos(modeloSeleccionado.id_marca);
                
                // Seleccionar el modelo
                SelectModelo.value = datos.modelo;
                
                console.log('✅ Formulario llenado correctamente');
            } else {
                console.warn('⚠️ Modelo no encontrado en la lista');
            }
        }
    } catch (error) {
        console.error('❌ Error al cargar datos para modificar:', error);
    }

    if (BtnGuardar) BtnGuardar.classList.add('d-none');
    if (BtnModificar) BtnModificar.classList.remove('d-none');

    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

const ModificarInventario = async (event) => {
    event.preventDefault();
    BtnModificar.disabled = true;

    if (!validarFormulario(FormInventario, ['codigo_producto', 'imei', 'ubicacion'])) {
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

    const body = new FormData(FormInventario);

    try {
        const respuesta = await fetch('/proyecto_pmlx/inventario/modificarAPI', {
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
            BuscarInventario();
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

const EliminarInventario = async (e) => {
    const idInventario = e.currentTarget.dataset.id
    const nombreProducto = e.currentTarget.dataset.producto
    const codigoProducto = e.currentTarget.dataset.codigo

    const AlertaConfirmarEliminar = await Swal.fire({
        position: "center",
        icon: "question",
        title: "¿Desea retirar este producto del inventario?",
        text: `${nombreProducto} (${codigoProducto}) será retirado del inventario`,
        showConfirmButton: true,
        confirmButtonText: 'Sí, retirar',
        confirmButtonColor: '#dc3545',
        cancelButtonText: 'No, Cancelar',
        showCancelButton: true
    });

    if (AlertaConfirmarEliminar.isConfirmed) {
        try {
            const consulta = await fetch(`/proyecto_pmlx/inventario/eliminar?id=${idInventario}`);
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

                BuscarInventario();
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
    if (FormInventario) {
        FormInventario.reset();
    }
    if (BtnGuardar) BtnGuardar.classList.remove('d-none');
    if (BtnModificar) BtnModificar.classList.add('d-none');
    
    // Limpiar selects
    if (SelectMarca) SelectMarca.value = '';
    if (SelectModelo) SelectModelo.innerHTML = '<option value="">Primero seleccione una marca</option>';
}

// Event listener para cambio de marca
const onMarcaChange = (event) => {
    const idMarca = event.target.value;
    console.log('🎯 Marca seleccionada:', idMarca);
    CargarModelos(idMarca);
}

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 DOM cargado, iniciando aplicación de inventario...');
    console.log('🔍 Verificando elementos:');
    console.log('  - SelectMarca:', SelectMarca ? '✅' : '❌');
    console.log('  - SelectModelo:', SelectModelo ? '✅' : '❌');
    console.log('  - FormInventario:', FormInventario ? '✅' : '❌');
    
    inicializarDataTable();
    
    setTimeout(() => {
        console.log('⏰ Ejecutando carga inicial...');
        CargarMarcas();
        BuscarInventario();
    }, 100);
    
    if (FormInventario) {
        FormInventario.addEventListener('submit', GuardarInventario);
        console.log('✅ Event listener agregado al formulario');
    }

    if (SelectMarca) {
        SelectMarca.addEventListener('change', onMarcaChange);
        console.log('✅ Event listener agregado a SelectMarca');
    }

    if (InputPrecioCompra) {
        InputPrecioCompra.addEventListener('change', ValidarPrecios);
    }

    if (InputPrecioVenta) {
        InputPrecioVenta.addEventListener('change', ValidarPrecios);
    }

    if (BtnLimpiar) {
        BtnLimpiar.addEventListener('click', limpiarTodo);
    }

    if (BtnModificar) {
        BtnModificar.addEventListener('click', ModificarInventario);
    }

    // Event listeners para DataTable
    setTimeout(() => {
        if (datatable) {
            datatable.on('click', '.eliminar', EliminarInventario);
            datatable.on('click', '.modificar', llenarFormulario);
            console.log('✅ Event listeners del DataTable configurados');
        }
    }, 500);
    
    console.log('🎉 Inicialización completada');
});