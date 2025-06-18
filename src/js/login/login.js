import Swal from 'sweetalert2';
import { validarFormulario } from '../funciones';

const FormLogin = document.getElementById('FormLogin');
const BtnIniciar = document.getElementById('BtnIniciar');

const login = async (e) => {

    e.preventDefault();

    BtnIniciar.disabled = true;

    if (!validarFormulario(FormLogin, [''])) {
        Swal.fire({
            title: "Campos vacíos",
            text: "Debe llenar todos los campos",
            icon: "info"
        });
        BtnIniciar.disabled = false
        return;
    }

    try {
        const body = new FormData(FormLogin)
        const url = '/proyecto_pmlx/API/login'; // ← CORREGIDO: debe ser /login no /modelos

        const config = {
            method: 'POST',
            body
        }

        const respuesta = await fetch(url, config);
        const data = await respuesta.json();
        
        const { codigo, mensaje } = data

        if (codigo == 1) {
            await Swal.fire({
                title: '¡Bienvenido!',
                text: mensaje,
                icon: 'success',
                showConfirmButton: true,
                timer: 1500,
                timerProgressBar: false,
                background: '#e0f7fa',
                customClass: {
                    title: 'custom-title-class',
                    text: 'custom-text-class'
                }
            });

            FormLogin.reset();
            // ← CORREGIDO: redirigir al inicio del programa
            location.href = '/proyecto_pmlx/inicio'
        } else {
            Swal.fire({
                title: '¡Error!',
                text: mensaje,
                icon: 'warning',
                showConfirmButton: true,
                timer: 1500,
                timerProgressBar: false,
                background: '#e0f7fa',
                customClass: {
                    title: 'custom-title-class',
                    text: 'custom-text-class'
                }
            });
        }

    } catch (error) {
        console.log('Error en JavaScript:', error);
        Swal.fire({
            title: '¡Error de conexión!',
            text: 'No se pudo conectar con el servidor',
            icon: 'error',
            showConfirmButton: true,
            background: '#e0f7fa',
            customClass: {
                title: 'custom-title-class',
                text: 'custom-text-class'
            }
        });
    }

    BtnIniciar.disabled = false;
}

FormLogin.addEventListener('submit', login)