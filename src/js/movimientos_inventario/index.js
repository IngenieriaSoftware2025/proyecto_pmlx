import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario } from '../funciones';
import DataTable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";

const FormMovimientosInventario = document.getElementById('FormMovimientosInventario');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const BtnVerResumen = document.getElementById('BtnVerResumen');
const BtnFiltrar = document.getElementById('BtnFiltrar');
const BtnLimpiarFiltros = document.getElementById('BtnLimpiarFiltros');
const SelectProducto = document.getElementById('id_inventario');
const SelectTipoMovimiento = document.getElementById('tipo_movimiento');
const InputCantidad = document.getElementById('cantidad');
const InputStockActual = document.getElementById('stock_actual');
const InputStockProyectado = document.getElementById('stock_proyectado');
const InfoProducto = document.getElementById('info_producto');

let datatable;
let modalResumen;

// Calcular stock proyectado
const calcularStockProyectado = () => {
    const stockActual = parseInt(InputStockActual?.value) || 0;
    const cantidad = parseInt(InputCantidad?.value) || 0;
    const tipoMovimiento = SelectTipoMovimiento?.value;
    
    let stockProyectado = stockActual;
    
    if (tipoMovimiento && cantidad > 0) {
        switch (tipoMovimiento) {
            case 'E': // Entrada
                stockProyectado = stockActual + cantidad;
                break;
            case 'S': // Salida
                stockProyectado = stockActual - cantidad;
                break;
            case 'A': // Ajuste
                stockProyectado = cantidad; // En ajuste, la cantidad es el nuevo stock
                break;
        }
    }
    
    if (InputStockProyectado) {
        InputStockProyectado.value = stockProyectado;
        
        // Colorear según el resultado
        InputStockProyectado.className = 'form-control fw-bold';
        if (stockProyectado < 0) {
            InputStockProyectado.classList.add('text-danger');
        } else if (stockProyectado < stockActual) {
            InputStockProyectado.classList.add('text-warning');
        } else {
            InputStockProyectado.classList.add('text-success');
        }
    }
}

// Actualizar información del producto seleccionado
const actualizarInfoProducto = () => {
    if (!SelectProducto) return;
    
    const selectedOption = SelectProducto.options[SelectProducto.selectedIndex];
    
    if (selectedOption && selectedOption.value) {
        const stock = selectedOption.dataset.stock;
        const marca = selectedOption.dataset.marca;
        const modelo = selectedOption.dataset.modelo;
        const codigo = selectedOption.dataset.codigo;
        const imei = selectedOption.dataset.imei;
        const estado = selectedOption.dataset.estado;
        
        // Actualizar stock actual
        if (InputStockActual) InputStockActual.value = stock || '0';
        
        // Mostrar información del producto
        if (InfoProducto) {
            InfoProducto.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <strong>Marca:</strong> ${marca || 'N/A'}<br>
                        <strong>Modelo:</strong> ${modelo || 'N/A'}<br>
                        <strong>Estado:</strong> ${estado || 'N/A'}
                    </div>
                    <div class="col-md-6">
                        <strong>Código:</strong> ${codigo || 'N/A'}<br>
                        ${imei ? `<strong>IMEI:</strong> ${imei}<br>` : ''}
                        <strong>Stock Actual:</strong> <span class="badge bg-primary">${stock || 0}</span>
                    </div>
                </div>
            `;
        }
        
        // Recalcular stock proyectado
        calcularStockProyectado();
    } else {
        // Limpiar campos informativos
        if (InputStockActual) InputStockActual.value = '';
        if (InputStockProyectado) InputStockProyectado.value = '';
        if (InfoProducto) InfoProducto.innerHTML = '<i class="bi bi-info-circle"></i> Seleccione un producto para ver su información';
    }
}

// Cargar productos de inventario
const CargarProductos = async () => {
    try {
        console.log('🔄 Cargando productos de inventario...');
        const respuesta = await fetch('http://localhost:9002/proyecto_pmlx/movimientos_inventario/productosInventarioAPI');
        
        if (!respuesta.ok) {
            throw new Error(`HTTP error! status: ${respuesta.status}`);
        }
        
        const datos = await respuesta.json();
        const { codigo, data, mensaje } = datos;

        const selectProducto = document.getElementById('id_inventario');
        const filtroProducto = document.getElementById('filtro_producto');
        
        if (!selectProducto) {
            console.error('❌ No se encontró el elemento select #id_inventario');
            return;
        }

        if (codigo === 1 && data && Array.isArray(data)) {
            // Limpiar selects
            selectProducto.innerHTML = '<option value="">Seleccione un producto</option>';
            if (filtroProducto) {
                filtroProducto.innerHTML = '<option value="">Todos los productos</option>';
            }
            
            data.forEach(producto => {
                // Select principal
                const option = document.createElement('option');
                option.value = producto.id_inventario;
                
                // Agregar datos como data attributes
                option.dataset.stock = producto.stock_cantidad;
                option.dataset.marca = producto.nombre_marca;
                option.dataset.modelo = producto.nombre_modelo;
                option.dataset.codigo = producto.codigo_producto;
                option.dataset.imei = producto.imei || '';
                option.dataset.estado = producto.estado_producto_texto;
                
                option.textContent = `${producto.nombre_marca} ${producto.nombre_modelo} - ${producto.codigo_producto} (Stock: ${producto.stock_cantidad})`;
                selectProducto.appendChild(option);
                
                // Select de filtro
                if (filtroProducto) {
                    const optionFiltro = document.createElement('option');
                    optionFiltro.value = producto.id_inventario;
                    optionFiltro.textContent = `${producto.nombre_marca} ${producto.nombre_modelo} - ${producto.codigo_producto}`;
                    filtroProducto.appendChild(optionFiltro);
                }
            });
            
            console.log('✅ Productos cargados:', data.length);
        } else {
            console.error('❌ Error en la respuesta de productos:', mensaje);
        }
    } catch (error) {
        console.error('❌ Error cargando productos:', error);
    }
}

// Cargar usuarios disponibles
const CargarUsuarios = async () => {
    try {
        console.log('🔄 Cargando usuarios...');
        const respuesta = await fetch('http://localhost:9002/proyecto_pmlx/movimientos_inventario/usuariosDisponiblesAPI');
        
        if (!respuesta.ok) {
            throw new Error(`HTTP error! status: ${respuesta.status}`);
        }
        
        const datos = await respuesta.json();
        const { codigo, data, mensaje } = datos;

        const selectUsuario = document.getElementById('usuario_movimiento');
        if (!selectUsuario) {
            console.error('❌ No se encontró el elemento select #usuario_movimiento');
            return;
        }

        if (codigo === 1 && data && Array.isArray(data)) {
            selectUsuario.innerHTML = '<option value="">Seleccione usuario</option>';
            
            data.forEach(usuario => {
                const option = document.createElement('option');
                option.value = usuario.id_usuario;
                option.textContent = usuario.nombre_completo;
                selectUsuario.appendChild(option);
            });
            
            // Seleccionar automáticamente el primer usuario (simulando usuario logueado)
            if (data.length > 0) {
                selectUsuario.value = data[0].id_usuario;
            }
            
            console.log('✅ Usuarios cargados:', data.length);
        } else {
            console.error('❌ Error en la respuesta de usuarios:', mensaje);
        }
    } catch (error) {
        console.error('❌ Error cargando usuarios:', error);
    }
}

// Guardar movimiento de inventario
const GuardarMovimientoInventario = async (event) => {
    event.preventDefault();
    console.log('🔄 Iniciando proceso de guardado...');
    
    if (BtnGuardar) BtnGuardar.disabled = true;

    // Validaciones básicas
    const id_inventario = document.getElementById('id_inventario')?.value;
    const tipo_movimiento = document.getElementById('tipo_movimiento')?.value;
    const cantidad = document.getElementById('cantidad')?.value;
    const motivo = document.getElementById('motivo')?.value;
    const usuario_movimiento = document.getElementById('usuario_movimiento')?.value;

    console.log('📋 Datos del movimiento:');
    console.log('- ID Inventario:', id_inventario);
    console.log('- Tipo movimiento:', tipo_movimiento);
    console.log('- Cantidad:', cantidad);
    console.log('- Motivo:', motivo);

    if (!id_inventario) {
        await Swal.fire({
            position: "center",
            icon: "warning",
            title: "CAMPO REQUERIDO",
            text: "Debe seleccionar un producto",
            showConfirmButton: true,
        });
        if (BtnGuardar) BtnGuardar.disabled = false;
        return;
    }

    if (!tipo_movimiento) {
        await Swal.fire({
            position: "center",
            icon: "warning",
            title: "CAMPO REQUERIDO",
            text: "Debe seleccionar el tipo de movimiento",
            showConfirmButton: true,
        });
        if (BtnGuardar) BtnGuardar.disabled = false;
        return;
    }

    if (!cantidad || cantidad === '' || parseInt(cantidad) <= 0) {
        await Swal.fire({
            position: "center",
            icon: "warning",
            title: "CANTIDAD INVÁLIDA",
            text: "La cantidad debe ser mayor que 0",
            showConfirmButton: true,
        });
        if (BtnGuardar) BtnGuardar.disabled = false;
        return;
    }

    if (!motivo || motivo.trim() === '') {
        await Swal.fire({
            position: "center",
            icon: "warning",
            title: "CAMPO REQUERIDO",
            text: "El motivo del movimiento es obligatorio",
            showConfirmButton: true,
        });
        if (BtnGuardar) BtnGuardar.disabled = false;
        return;
    }

    if (!usuario_movimiento) {
        await Swal.fire({
            position: "center",
            icon: "warning",
            title: "CAMPO REQUERIDO",
            text: "Debe seleccionar un usuario",
            showConfirmButton: true,
        });
        if (BtnGuardar) BtnGuardar.disabled = false;
        return;
    }

    // Validar stock para salidas
    const stockActual = parseInt(InputStockActual?.value) || 0;
    const cantidadMovimiento = parseInt(cantidad);
    
    if (tipo_movimiento === 'S' && cantidadMovimiento > stockActual) {
        await Swal.fire({
            position: "center",
            icon: "warning",
            title: "STOCK INSUFICIENTE",
            text: `Stock disponible: ${stockActual}, Cantidad solicitada: ${cantidadMovimiento}`,
            showConfirmButton: true,
        });
        if (BtnGuardar) BtnGuardar.disabled = false;
        return;
    }

    console.log('✅ Validaciones pasadas, preparando envío...');

    const body = new FormData(FormMovimientosInventario);

    try {
        console.log('🌐 Enviando petición al servidor...');
        const respuesta = await fetch('http://localhost:9002/proyecto_pmlx/movimientos_inventario/guardarAPI', {
            method: 'POST',
            body
        });

        if (!respuesta.ok) {
            const errorData = await respuesta.json();
            await Swal.fire({
                position: "center",
                icon: "error",
                title: "Error del Servidor",
                text: errorData.mensaje || `Error ${respuesta.status}`,
                showConfirmButton: true,
            });
            if (BtnGuardar) BtnGuardar.disabled = false;
            return;
        }

        const datos = await respuesta.json();
        const { codigo, mensaje } = datos;

        if (codigo == 1) {
            await Swal.fire({
                position: "center",
                icon: "success",
                title: "Éxito",
                text: mensaje,
                showConfirmButton: true,
            });

            limpiarTodo();
            BuscarMovimientos();
            CargarProductos(); // Recargar para actualizar stock
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
        console.error('❌ Error guardando:', error);
        await Swal.fire({
            position: "center",
            icon: "error",
            title: "Error de Conexión",
            text: "No se pudo conectar con el servidor.",
            showConfirmButton: true,
        });
    }
    
    if (BtnGuardar) BtnGuardar.disabled = false;
}

// Buscar movimientos de inventario
const BuscarMovimientos = async (filtros = {}) => {
    try {
        console.log('🔍 Buscando movimientos de inventario...');
        const respuesta = await fetch('http://localhost:9002/proyecto_pmlx/movimientos_inventario/buscarAPI');
        const datos = await respuesta.json();
        const { codigo, mensaje, data } = datos;

        if (codigo == 1) {
            let dataFiltrada = data || [];
            
            // Aplicar filtros si existen
            if (filtros.producto) {
                dataFiltrada = dataFiltrada.filter(item => item.id_inventario == filtros.producto);
            }
            
            if (filtros.tipo) {
                dataFiltrada = dataFiltrada.filter(item => item.tipo_movimiento === filtros.tipo);
            }
            
            if (datatable) {
                datatable.clear().draw();
                if (dataFiltrada.length > 0) {
                    datatable.rows.add(dataFiltrada).draw();
                    console.log('✅ Movimientos cargados:', dataFiltrada.length);
                }
            }
        } else {
            console.error('❌ Error al buscar movimientos:', mensaje);
        }
    } catch (error) {
        console.error('❌ Error en la búsqueda:', error);
    }
}

// Inicializar DataTable
const inicializarDataTable = () => {
    if (document.getElementById('TableMovimientosInventario')) {
        console.log('🗂️ Inicializando DataTable...');
        datatable = new DataTable('#TableMovimientosInventario', {
            language: lenguaje,
            data: [],
            order: [[0, 'desc']], // Ordenar por fecha más reciente
            columns: [
                {
                    title: 'Fecha',
                    data: 'fecha_movimiento',
                    render: (data) => {
                        if (data) {
                            const fecha = new Date(data);
                            return fecha.toLocaleDateString('es-GT') + '<br><small>' + fecha.toLocaleTimeString('es-GT') + '</small>';
                        }
                        return '';
                    }
                },
                { 
                    title: 'Producto', 
                    data: 'nombre_marca',
                    render: (data, type, row) => {
                        return `<strong>${data} ${row.nombre_modelo}</strong><br><small class="text-muted">${row.codigo_producto}</small>`;
                    }
                },
                { 
                    title: 'Tipo', 
                    data: 'tipo_movimiento_texto',
                    render: (data, type, row) => {
                        let badgeClass = 'bg-secondary';
                        let icon = 'bi-arrow-left-right';
                        
                        switch(row.tipo_movimiento) {
                            case 'E': 
                                badgeClass = 'bg-success'; 
                                icon = 'bi-arrow-down';
                                break;
                            case 'S': 
                                badgeClass = 'bg-danger'; 
                                icon = 'bi-arrow-up';
                                break;
                            case 'A': 
                                badgeClass = 'bg-warning'; 
                                icon = 'bi-tools';
                                break;
                        }
                        return `<span class="badge ${badgeClass}"><i class="bi ${icon} me-1"></i>${data}</span>`;
                    }
                },
                { 
                    title: 'Cantidad', 
                    data: 'cantidad',
                    render: (data, type, row) => {
                        let textClass = 'text-dark';
                        let symbol = '';
                        
                        switch(row.tipo_movimiento) {
                            case 'E': 
                                textClass = 'text-success fw-bold'; 
                                symbol = '+';
                                break;
                            case 'S': 
                                textClass = 'text-danger fw-bold'; 
                                symbol = '-';
                                break;
                            case 'A': 
                                textClass = 'text-warning fw-bold'; 
                                symbol = '±';
                                break;
                        }
                        return `<span class="${textClass}">${symbol}${data}</span>`;
                    }
                },
                { 
                    title: 'Stock Actual', 
                    data: 'stock_actual',
                    render: (data) => {
                        return `<span class="badge bg-info">${data}</span>`;
                    }
                },
                { 
                    title: 'Motivo', 
                    data: 'motivo',
                    render: (data) => {
                        if (data && data.length > 30) {
                            return data.substring(0, 30) + '...';
                        }
                        return data || 'Sin motivo';
                    }
                },
                { 
                    title: 'Referencia', 
                    data: 'referencia_documento',
                    render: (data) => {
                        return data || '<span class="text-muted">N/A</span>';
                    }
                },
                { 
                    title: 'Usuario', 
                    data: 'usuario_nombre'
                },
                {
                    title: 'Acciones',
                    data: 'id_movimiento',
                    orderable: false,
                    render: (data, type, row) => {
                        // No permitir modificar movimientos automáticos del sistema
                        const motivosAutomaticos = ['venta de producto', 'devolución por eliminación de detalle'];
                        const esAutomatico = motivosAutomaticos.some(motivo => 
                            row.motivo.toLowerCase().includes(motivo.toLowerCase())
                        );
                        
                        if (esAutomatico) {
                            return '<span class="text-muted"><i class="bi bi-lock"></i> Sistema</span>';
                        }
                        
                        return `
                            <button class="btn btn-warning btn-sm modificar" 
                                data-id="${data}"
                                data-id_inventario="${row.id_inventario || ''}"
                                data-tipo_movimiento="${row.tipo_movimiento || ''}"
                                data-cantidad="${row.cantidad || '0'}"
                                data-motivo="${(row.motivo || '').replace(/"/g, '&quot;')}"
                                data-referencia_documento="${(row.referencia_documento || '').replace(/"/g, '&quot;')}"
                                data-usuario_movimiento="${row.usuario_movimiento || ''}"
                                data-observaciones="${(row.observaciones || '').replace(/"/g, '&quot;')}">
                                <i class="bi bi-tools text-warning fs-3"></i>
                                <h5 class="card-title text-warning">${resumen.total_ajustes || 0}</h5>
                                <p class="card-text">Ajustes</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center border-info">
                            <div class="card-body">
                                <i class="bi bi-box text-info fs-3"></i>
                                <h5 class="card-title text-info">${resumen.stock_actual || 0}</h5>
                                <p class="card-text">Stock Actual</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <h6>Últimos Movimientos (${resumen.total_movimientos || 0} total)</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Tipo</th>
                                        <th>Cantidad</th>
                                        <th>Motivo</th>
                                        <th>Usuario</th>
                                    </tr>
                                </thead>
                                <tbody>
            `;

            if (movimientos && movimientos.length > 0) {
                movimientos.slice(0, 10).forEach(mov => {
                    const fecha = new Date(mov.fecha_movimiento).toLocaleDateString('es-GT');
                    let badgeClass = 'bg-secondary';
                    let symbol = '';
                    
                    switch(mov.tipo_movimiento) {
                        case 'E': badgeClass = 'bg-success'; symbol = '+'; break;
                        case 'S': badgeClass = 'bg-danger'; symbol = '-'; break;
                        case 'A': badgeClass = 'bg-warning'; symbol = '±'; break;
                    }

                    contenidoHTML += `
                        <tr>
                            <td>${fecha}</td>
                            <td><span class="badge ${badgeClass}">${mov.tipo_movimiento_texto}</span></td>
                            <td><strong>${symbol}${mov.cantidad}</strong></td>
                            <td>${mov.motivo}</td>
                            <td>${mov.usuario_nombre}</td>
                        </tr>
                    `;
                });
            } else {
                contenidoHTML += '<tr><td colspan="5" class="text-center text-muted">No hay movimientos registrados</td></tr>';
            }

            contenidoHTML += `
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;

            document.getElementById('contenido_resumen').innerHTML = contenidoHTML;
        } else {
            document.getElementById('contenido_resumen').innerHTML = '<div class="alert alert-warning">No se pudo cargar el resumen</div>';
        }
    } catch (error) {
        console.error('Error cargando resumen:', error);
        document.getElementById('contenido_resumen').innerHTML = '<div class="alert alert-danger">Error al cargar el resumen</div>';
    }
}

// Aplicar filtros
const aplicarFiltros = () => {
    const filtroProducto = document.getElementById('filtro_producto')?.value;
    const filtroTipo = document.getElementById('filtro_tipo')?.value;
    
    const filtros = {};
    if (filtroProducto) filtros.producto = filtroProducto;
    if (filtroTipo) filtros.tipo = filtroTipo;
    
    BuscarMovimientos(filtros);
}

// Limpiar filtros
const limpiarFiltros = () => {
    document.getElementById('filtro_producto').value = '';
    document.getElementById('filtro_tipo').value = '';
    BuscarMovimientos();
}

// Limpiar formulario
const limpiarTodo = () => {
    console.log('🧹 Limpiando formulario...');
    
    if (FormMovimientosInventario) {
        FormMovimientosInventario.reset();
    }
    if (BtnGuardar) BtnGuardar.classList.remove('d-none');
    if (BtnModificar) BtnModificar.classList.add('d-none');
    
    // Limpiar campos informativos
    if (InputStockActual) InputStockActual.value = '';
    if (InputStockProyectado) {
        InputStockProyectado.value = '';
        InputStockProyectado.className = 'form-control fw-bold';
    }
    if (InfoProducto) InfoProducto.innerHTML = '<i class="bi bi-info-circle"></i> Seleccione un producto para ver su información';
}

// INICIALIZACIÓN
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Inicializando aplicación de movimientos de inventario...');
    
    // Verificar elementos principales
    const elementosRequeridos = [
        'FormMovimientosInventario',
        'id_inventario',
        'tipo_movimiento',
        'cantidad',
        'motivo',
        'TableMovimientosInventario'
    ];
    
    elementosRequeridos.forEach(id => {
        const elemento = document.getElementById(id);
        if (!elemento) {
            console.error(`❌ No se encontró el elemento #${id}`);
        } else {
            console.log(`✅ Elemento encontrado: ${id}`);
        }
    });
    
    // Inicializar DataTable
    inicializarDataTable();
    
    // Cargar datos iniciales
    console.log('🔄 Cargando datos iniciales...');
    CargarProductos();
    CargarUsuarios();
    
    setTimeout(() => {
        BuscarMovimientos();
    }, 1000);
    
    // Event listeners para cálculos automáticos
    if (InputCantidad) {
        InputCantidad.addEventListener('input', calcularStockProyectado);
        console.log('✅ Event listener agregado para cantidad');
    }
    
    if (SelectTipoMovimiento) {
        SelectTipoMovimiento.addEventListener('change', calcularStockProyectado);
        console.log('✅ Event listener agregado para tipo de movimiento');
    }

    if (SelectProducto) {
        SelectProducto.addEventListener('change', actualizarInfoProducto);
        console.log('✅ Event listener agregado para selección de producto');
    }

    // Event listeners del formulario
    if (FormMovimientosInventario) {
        FormMovimientosInventario.addEventListener('submit', GuardarMovimientoInventario);
        console.log('✅ Event listener agregado para el formulario');
    }

    if (BtnLimpiar) {
        BtnLimpiar.addEventListener('click', limpiarTodo);
        console.log('✅ Event listener agregado para limpiar');
    }

    if (BtnModificar) {
        BtnModificar.addEventListener('click', ModificarMovimiento);
        console.log('✅ Event listener agregado para modificar');
    }

    if (BtnVerResumen) {
        BtnVerResumen.addEventListener('click', VerResumen);
        console.log('✅ Event listener agregado para ver resumen');
    }

    if (BtnFiltrar) {
        BtnFiltrar.addEventListener('click', aplicarFiltros);
        console.log('✅ Event listener agregado para filtrar');
    }

    if (BtnLimpiarFiltros) {
        BtnLimpiarFiltros.addEventListener('click', limpiarFiltros);
        console.log('✅ Event listener agregado para limpiar filtros');
    }

    // Event listeners para DataTable
    setTimeout(() => {
        if (datatable) {
            datatable.on('click', '.eliminar', EliminarMovimiento);
            datatable.on('click', '.modificar', llenarFormulario);
            console.log('✅ Event listeners agregados para DataTable');
        }
    }, 1500);
    
    console.log('✅ Aplicación de movimientos de inventario inicializada completamente');
});pencil"></i>
                            </button>
                            <button class="btn btn-danger btn-sm eliminar" 
                                data-id="${data}"
                                data-producto="${row.nombre_marca} ${row.nombre_modelo}"
                                data-motivo="${row.motivo}">
                                <i class="bi bi-trash"></i>
                            </button>
                        `;
                    }
                }
            ]
        });
        console.log('✅ DataTable inicializado');
    }
}

// Llenar formulario para edición
const llenarFormulario = (event) => {
    const datos = event.currentTarget.dataset;
    
    console.log('📝 Llenando formulario con datos:', datos);

    // Llenar campos básicos
    document.getElementById('id_movimiento').value = datos.id || '';
    document.getElementById('tipo_movimiento').value = datos.tipo_movimiento || '';
    document.getElementById('cantidad').value = datos.cantidad || '0';
    document.getElementById('motivo').value = datos.motivo || '';
    document.getElementById('referencia_documento').value = datos.referencia_documento || '';
    document.getElementById('observaciones').value = datos.observaciones || '';

    // Cargar selects y establecer valores
    Promise.all([
        CargarProductos(),
        CargarUsuarios()
    ]).then(() => {
        setTimeout(() => {
            const selectProducto = document.getElementById('id_inventario');
            const selectUsuario = document.getElementById('usuario_movimiento');
            
            if (selectProducto && datos.id_inventario) {
                selectProducto.value = datos.id_inventario;
                actualizarInfoProducto();
            }
            
            if (selectUsuario && datos.usuario_movimiento) {
                selectUsuario.value = datos.usuario_movimiento;
            }
            
            // Recalcular stock proyectado
            calcularStockProyectado();
        }, 500);
    });

    // Cambiar botones
    if (BtnGuardar) BtnGuardar.classList.add('d-none');
    if (BtnModificar) BtnModificar.classList.remove('d-none');

    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Modificar movimiento
const ModificarMovimiento = async (event) => {
    event.preventDefault();
    console.log('🔄 Iniciando modificación...');
    
    if (BtnModificar) BtnModificar.disabled = true;

    const cantidad = document.getElementById('cantidad')?.value;
    const motivo = document.getElementById('motivo')?.value;

    if (!cantidad || cantidad === '' || parseInt(cantidad) <= 0) {
        await Swal.fire({
            position: "center",
            icon: "warning",
            title: "CANTIDAD INVÁLIDA",
            text: "La cantidad debe ser mayor que 0",
            showConfirmButton: true,
        });
        if (BtnModificar) BtnModificar.disabled = false;
        return;
    }

    if (!motivo || motivo.trim() === '') {
        await Swal.fire({
            position: "center",
            icon: "warning",
            title: "CAMPO REQUERIDO",
            text: "El motivo del movimiento es obligatorio",
            showConfirmButton: true,
        });
        if (BtnModificar) BtnModificar.disabled = false;
        return;
    }

    const body = new FormData(FormMovimientosInventario);

    try {
        const respuesta = await fetch('http://localhost:9002/proyecto_pmlx/movimientos_inventario/modificarAPI', {
            method: 'POST',
            body
        });

        const datos = await respuesta.json();
        const { codigo, mensaje } = datos;

        if (codigo == 1) {
            await Swal.fire({
                position: "center",
                icon: "success",
                title: "Éxito",
                text: mensaje,
                showConfirmButton: true,
            });

            limpiarTodo();
            BuscarMovimientos();
            CargarProductos(); // Recargar para actualizar stock
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
        console.error('💥 Error:', error);
        await Swal.fire({
            position: "center",
            icon: "error",
            title: "Error de Conexión",
            text: "No se pudo conectar con el servidor.",
            showConfirmButton: true,
        });
    }
    
    if (BtnModificar) BtnModificar.disabled = false;
}

// Eliminar movimiento
const EliminarMovimiento = async (e) => {
    const idMovimiento = e.currentTarget.dataset.id;
    const nombreProducto = e.currentTarget.dataset.producto;
    const motivo = e.currentTarget.dataset.motivo;

    const AlertaConfirmarEliminar = await Swal.fire({
        position: "center",
        icon: "question",
        title: "¿Desea eliminar este movimiento?",
        text: `Producto: "${nombreProducto}" - Motivo: "${motivo}"`,
        showConfirmButton: true,
        confirmButtonText: 'Sí, eliminar',
        confirmButtonColor: '#dc3545',
        cancelButtonText: 'No, Cancelar',
        showCancelButton: true
    });

    if (AlertaConfirmarEliminar.isConfirmed) {
        try {
            const consulta = await fetch(`http://localhost:9002/proyecto_pmlx/movimientos_inventario/eliminar?id=${idMovimiento}`);
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

                BuscarMovimientos();
                CargarProductos(); // Recargar para actualizar stock
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
            console.error('❌ Error eliminando:', error);
        }
    }
}

// Ver resumen de movimientos por producto
const VerResumen = async () => {
    const selectProducto = document.getElementById('id_inventario');
    const idProducto = selectProducto?.value;
    
    if (!idProducto) {
        await Swal.fire({
            icon: 'warning',
            title: 'Seleccione un producto',
            text: 'Debe seleccionar un producto para ver su resumen',
            showConfirmButton: true,
        });
        return;
    }

    // Mostrar modal
    if (!modalResumen) {
        modalResumen = new bootstrap.Modal(document.getElementById('ModalResumen'));
    }
    modalResumen.show();

    try {
        // Cargar resumen
        const respuestaResumen = await fetch(`http://localhost:9002/proyecto_pmlx/movimientos_inventario/resumenPorProductoAPI?id_inventario=${idProducto}`);
        const datosResumen = await respuestaResumen.json();

        // Cargar movimientos del producto
        const respuestaMovimientos = await fetch(`http://localhost:9002/proyecto_pmlx/movimientos_inventario/movimientosPorProductoAPI?id_inventario=${idProducto}`);
        const datosMovimientos = await respuestaMovimientos.json();

        if (datosResumen.codigo === 1 && datosMovimientos.codigo === 1) {
            const resumen = datosResumen.data;
            const movimientos = datosMovimientos.data;
            const productoSeleccionado = selectProducto.options[selectProducto.selectedIndex].textContent;

            let contenidoHTML = `
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="text-primary">Producto: ${productoSeleccionado}</h6>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-center border-success">
                            <div class="card-body">
                                <i class="bi bi-arrow-down text-success fs-3"></i>
                                <h5 class="card-title text-success">${resumen.total_entradas || 0}</h5>
                                <p class="card-text">Entradas</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center border-danger">
                            <div class="card-body">
                                <i class="bi bi-arrow-up text-danger fs-3"></i>
                                <h5 class="card-title text-danger">${resumen.total_salidas || 0}</h5>
                                <p class="card-text">Salidas</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center border-warning">
                            <div class="card-body">
                                <i class="bi bi-