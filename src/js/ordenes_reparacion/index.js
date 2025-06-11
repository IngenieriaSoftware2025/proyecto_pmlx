// 🧪 TEST SIMPLE PARA CARGAR DATOS - COPIA ESTO EN LA CONSOLA
console.log('🚀 Iniciando test simple...');

// 1. Verificar que los elementos existen
const selectCliente = document.getElementById('id_cliente');
const selectMarca = document.getElementById('id_marca');
const selectTrabajador = document.getElementById('id_trabajador_asignado');

console.log('📍 Verificando elementos:');
console.log('SelectCliente:', selectCliente ? 'EXISTE' : 'NO EXISTE');
console.log('SelectMarca:', selectMarca ? 'EXISTE' : 'NO EXISTE');
console.log('SelectTrabajador:', selectTrabajador ? 'EXISTE' : 'NO EXISTE');

// 2. Test manual básico
if (selectCliente) {
    selectCliente.innerHTML = `
        <option value="">Seleccione un cliente</option>
        <option value="1">Cliente Test 1</option>
        <option value="2">Cliente Test 2</option>
    `;
    console.log('✅ SelectCliente poblado manualmente');
}

if (selectMarca) {
    selectMarca.innerHTML = `
        <option value="">Seleccione una marca</option>
        <option value="1">Apple</option>
        <option value="2">Samsung</option>
        <option value="3">Huawei</option>
    `;
    console.log('✅ SelectMarca poblado manualmente');
}

if (selectTrabajador) {
    selectTrabajador.innerHTML = `
        <option value="">Sin asignar</option>
        <option value="1">Técnico 1</option>
        <option value="2">Técnico 2</option>
    `;
    console.log('✅ SelectTrabajador poblado manualmente');
}

// 3. Test de las URLs una por una
async function testURL(url, descripcion) {
    console.log(`\n🔄 Probando ${descripcion}: ${url}`);
    try {
        const response = await fetch(url);
        console.log(`📡 Status: ${response.status} ${response.statusText}`);
        
        if (response.ok) {
            const data = await response.json();
            console.log('📦 Datos:', data);
            return data;
        } else {
            const errorText = await response.text();
            console.error(`❌ Error: ${errorText}`);
        }
    } catch (error) {
        console.error(`❌ Error de red: ${error.message}`);
    }
}

// 4. Probar las URLs principales
async function probarURLs() {
    console.log('\n🌐 Probando URLs del API...');
    
    await testURL('/ordenes_reparacion/clientesDisponiblesAPI', 'Clientes');
    await testURL('/ordenes_reparacion/marcasDisponiblesAPI', 'Marcas');
    await testURL('/ordenes_reparacion/trabajadoresDisponiblesAPI', 'Trabajadores');
    
    // Probar también con /proyecto_pmlx por si acaso
    console.log('\n🔄 Probando con prefijo /proyecto_pmlx...');
    await testURL('/proyecto_pmlx/ordenes_reparacion/clientesDisponiblesAPI', 'Clientes con prefijo');
    await testURL('/proyecto_pmlx/ordenes_reparacion/marcasDisponiblesAPI', 'Marcas con prefijo');
    await testURL('/proyecto_pmlx/ordenes_reparacion/trabajadoresDisponiblesAPI', 'Trabajadores con prefijo');
    
    console.log('\n🎯 Test completado. Revisa los resultados arriba.');
}

// Ejecutar tests
probarURLs();

// 5. Función para cargar datos reales si alguna URL funciona
window.cargarDatosReales = async function(urlBase = '') {
    console.log(`\n🔄 Intentando cargar datos reales con base: "${urlBase}"`);
    
    try {
        // Cargar clientes
        const respClientes = await fetch(`${urlBase}/ordenes_reparacion/clientesDisponiblesAPI`);
        if (respClientes.ok) {
            const dataClientes = await respClientes.json();
            if (dataClientes.codigo == 1 && dataClientes.data) {
                selectCliente.innerHTML = '<option value="">Seleccione un cliente</option>';
                dataClientes.data.forEach(cliente => {
                    const info = cliente.telefono ? ` - ${cliente.telefono}` : '';
                    selectCliente.innerHTML += `<option value="${cliente.id_cliente}">${cliente.nombre}${info}</option>`;
                });
                console.log('✅ Clientes cargados:', dataClientes.data.length);
            }
        }
        
        // Cargar marcas
        const respMarcas = await fetch(`${urlBase}/ordenes_reparacion/marcasDisponiblesAPI`);
        if (respMarcas.ok) {
            const dataMarcas = await respMarcas.json();
            if (dataMarcas.codigo == 1 && dataMarcas.data) {
                selectMarca.innerHTML = '<option value="">Seleccione una marca</option>';
                dataMarcas.data.forEach(marca => {
                    selectMarca.innerHTML += `<option value="${marca.id_marca}">${marca.nombre_marca}</option>`;
                });
                console.log('✅ Marcas cargadas:', dataMarcas.data.length);
            }
        }
        
        // Cargar trabajadores
        const respTrabajadores = await fetch(`${urlBase}/ordenes_reparacion/trabajadoresDisponiblesAPI`);
        if (respTrabajadores.ok) {
            const dataTrabajadores = await respTrabajadores.json();
            if (dataTrabajadores.codigo == 1 && dataTrabajadores.data) {
                selectTrabajador.innerHTML = '<option value="">Sin asignar</option>';
                dataTrabajadores.data.forEach(trabajador => {
                    selectTrabajador.innerHTML += `<option value="${trabajador.id_trabajador}">${trabajador.nombre_completo} - ${trabajador.especialidad}</option>`;
                });
                console.log('✅ Trabajadores cargados:', dataTrabajadores.data.length);
            }
        }
        
    } catch (error) {
        console.error('❌ Error cargando datos:', error);
    }
};

console.log('\n💡 INSTRUCCIONES:');
console.log('1. Los selects han sido poblados manualmente para probar');
console.log('2. Revisa los resultados de las pruebas de URLs arriba');
console.log('3. Si alguna URL funciona, usa: cargarDatosReales("") o cargarDatosReales("/proyecto_pmlx")');
console.log('4. Reporta qué URL funcionó para corregir el código principal');