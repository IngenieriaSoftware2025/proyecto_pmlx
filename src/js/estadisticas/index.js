import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario } from "../funciones";
import DataTable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";
import { Chart } from "chart.js/auto";

const grafico1 = document.getElementById("grafico1").getContext("2d");
const grafico2 = document.getElementById("grafico2").getContext("2d");
const grafico3 = document.getElementById("grafico3").getContext("2d");
const grafico4 = document.getElementById("grafico4").getContext("2d");

function getColorForEstado(cantidad) {
    let color = "";
  
    if(cantidad > 5){
        color = "lightblue";
    } else if(cantidad > 2 && cantidad <= 5){
        color = 'lightpink';
    } else if(cantidad <= 2){
        color = 'mistyrose';
    }
   
    return color;
}

// Crear las gráficas
window.graficaProductos = new Chart(grafico1, {
    type: 'bar',
    data: {
        labels: [],
        datasets: []
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true }
        },
        plugins: {
            title: {
                display: true,
                text: 'Productos Más Vendidos'
            }
        }
    }
});

window.graficaMarcas = new Chart(grafico2, {
    type: 'pie',
    data: {
        labels: [],
        datasets: []
    },
    options: {
        responsive: true,
        plugins: {
            title: {
                display: true,
                text: 'Marcas Más Populares en Reparaciones'
            }
        }
    }
});

window.graficaTrabajadores = new Chart(grafico3, {
    type: 'bar',
    data: {
        labels: [],
        datasets: []
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true }
        },
        plugins: {
            title: {
                display: true,
                text: 'Trabajadores con Más Órdenes'
            }
        }
    }
});

window.graficaUsuarios = new Chart(grafico4, {
    type: 'doughnut',
    data: {
        labels: [],
        datasets: []
    },
    options: {
        responsive: true,
        plugins: {
            title: {
                display: true,
                text: 'Usuarios con Más Ventas'
            }
        }
    }
});

// Función para buscar productos vendidos
const BuscarProductos = async () => {
    const url = '/proyecto_pmlx/estadisticas/buscarAPI';
    const config = {
        method: 'GET'
    }

    try {
        console.log('Iniciando búsqueda de productos...');
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje, data } = datos;
        
        if (codigo == 1) {
            console.log('Productos:', data)
            const productos = [];
            const datosProductos = new Map();
            
            data.forEach(d => {
                if (!datosProductos.has(d.producto)) {
                    datosProductos.set(d.producto, d.cantidad);
                    productos.push({ 
                        producto: d.producto, 
                        pro_id: d.pro_id, 
                        cantidad: d.cantidad 
                    });
                }
            });
            
            const etiquetasProductos = [...new Set(data.map(d => d.producto))];
            
            const datasets = productos.map(e => ({
                label: e.producto,
                data: etiquetasProductos.map(productos => {
                    const match = data.find(d => d.producto === productos && e.producto === d.producto);
                    return match ? match.cantidad : 0;
                }),
                backgroundColor: getColorForEstado(e.cantidad)
            }));
            
            if (window.graficaProductos) {
                window.graficaProductos.data.labels = etiquetasProductos;
                window.graficaProductos.data.datasets = datasets;
                window.graficaProductos.update();
            }

        } else {
            console.error('Error en productos:', mensaje);
        }

    } catch (error) {
        console.error('Error al cargar productos:', error);
    }
}

// Función para buscar marcas más populares en reparaciones
const BuscarMarcas = async () => {
    const url = '/proyecto_pmlx/estadisticas/buscarMarcasAPI';
    const config = {
        method: 'GET'
    }

    try {
        console.log('Iniciando búsqueda de marcas...');
        const respuesta = await fetch(url, config);
        
        if (!respuesta.ok) {
            throw new Error(`HTTP error! status: ${respuesta.status}`);
        }
        
        const datos = await respuesta.json();
        const { codigo, mensaje, data } = datos;
        
        if (codigo == 1) {
            console.log('Marcas encontradas:', data);
            
            const etiquetasMarcas = data.map(d => d.marca);
            const totalReparaciones = data.map(d => parseInt(d.total_reparaciones));
            
            if (window.graficaMarcas) {
                window.graficaMarcas.data.labels = etiquetasMarcas;
                window.graficaMarcas.data.datasets = [{
                    label: 'Reparaciones',
                    data: totalReparaciones,
                    backgroundColor: totalReparaciones.map(cantidad => getColorForEstado(cantidad)),
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }];
                window.graficaMarcas.update();
                console.log('Gráfica de marcas actualizada');
            }

        } else {
            console.error('Error en marcas:', mensaje);
            await Swal.fire({
                position: "center",
                icon: "error",
                title: "Error",
                text: mensaje,
                showConfirmButton: true,
            });
        }

    } catch (error) {
        console.error('Error al cargar marcas:', error);
        await Swal.fire({
            position: "center",
            icon: "error",
            title: "Error de conexión",
            text: "No se pudo conectar con la API de marcas: " + error.message,
            showConfirmButton: true,
        });
    }
}

// Función para buscar trabajadores con más órdenes
const BuscarTrabajadores = async () => {
    const url = '/proyecto_pmlx/estadisticas/buscarTrabajadoresAPI';
    const config = {
        method: 'GET'
    }

    try {
        console.log('Iniciando búsqueda de trabajadores...');
        const respuesta = await fetch(url, config);
        
        if (!respuesta.ok) {
            throw new Error(`HTTP error! status: ${respuesta.status}`);
        }
        
        const datos = await respuesta.json();
        const { codigo, mensaje, data } = datos;
        
        if (codigo == 1) {
            console.log('Trabajadores encontrados:', data);
            
            const etiquetasTrabajadores = data.map(d => d.trabajador);
            const totalOrdenes = data.map(d => parseInt(d.total_ordenes));
            
            if (window.graficaTrabajadores) {
                window.graficaTrabajadores.data.labels = etiquetasTrabajadores;
                window.graficaTrabajadores.data.datasets = [{
                    label: 'Órdenes Completadas',
                    data: totalOrdenes,
                    backgroundColor: totalOrdenes.map(cantidad => getColorForEstado(cantidad)),
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }];
                window.graficaTrabajadores.update();
                console.log('Gráfica de trabajadores actualizada');
            }

        } else {
            console.error('Error en trabajadores:', mensaje);
            await Swal.fire({
                position: "center",
                icon: "error",
                title: "Error",
                text: mensaje,
                showConfirmButton: true,
            });
        }

    } catch (error) {
        console.error('Error al cargar trabajadores:', error);
        await Swal.fire({
            position: "center",
            icon: "error",
            title: "Error de conexión",
            text: "No se pudo conectar con la API de trabajadores: " + error.message,
            showConfirmButton: true,
        });
    }
}

// Función para buscar usuarios con más ventas
const BuscarUsuarios = async () => {
    const url = '/proyecto_pmlx/estadisticas/buscarUsuariosAPI';
    const config = {
        method: 'GET'
    }

    try {
        console.log('Iniciando búsqueda de usuarios...');
        const respuesta = await fetch(url, config);
        
        if (!respuesta.ok) {
            throw new Error(`HTTP error! status: ${respuesta.status}`);
        }
        
        const datos = await respuesta.json();
        const { codigo, mensaje, data } = datos;
        
        if (codigo == 1) {
            console.log('Usuarios encontrados:', data);
            
            const etiquetasUsuarios = data.map(d => d.usuario);
            const totalVentas = data.map(d => parseInt(d.total_ventas));
            
            if (window.graficaUsuarios) {
                window.graficaUsuarios.data.labels = etiquetasUsuarios;
                window.graficaUsuarios.data.datasets = [{
                    label: 'Ventas Realizadas',
                    data: totalVentas,
                    backgroundColor: totalVentas.map(cantidad => getColorForEstado(cantidad)),
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }];
                window.graficaUsuarios.update();
                console.log('Gráfica de usuarios actualizada');
            }

        } else {
            console.error('Error en usuarios:', mensaje);
            await Swal.fire({
                position: "center",
                icon: "error",
                title: "Error",
                text: mensaje,
                showConfirmButton: true,
            });
        }

    } catch (error) {
        console.error('Error al cargar usuarios:', error);
        await Swal.fire({
            position: "center",
            icon: "error",
            title: "Error de conexión",
            text: "No se pudo conectar con la API de usuarios: " + error.message,
            showConfirmButton: true,
        });
    }
}

// Llamar todas las funciones con delay para debug
console.log('Iniciando carga de gráficas...');
BuscarProductos();

setTimeout(() => {
    BuscarMarcas();
}, 1000);

setTimeout(() => {
    BuscarTrabajadores();
}, 2000);

setTimeout(() => {
    BuscarUsuarios();
}, 3000);