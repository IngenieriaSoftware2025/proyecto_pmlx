<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Reparación de Teléfonos</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
        }

        .login-header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 40px 30px 30px;
            text-align: center;
            position: relative;
        }

        .login-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 20"><defs><radialGradient id="a" cx="50%" cy="40%"><stop offset="0%" stop-color="%23ffffff" stop-opacity="0.1"/><stop offset="100%" stop-color="%23ffffff" stop-opacity="0"/></radialGradient></defs><rect width="100" height="20" fill="url(%23a)"/></svg>');
            opacity: 0.3;
        }

        .company-logo {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            border: 3px solid rgba(255, 255, 255, 0.2);
        }

        .company-logo i {
            font-size: 2.5rem;
            color: #fff;
        }

        .company-name {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 5px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .company-tagline {
            font-size: 0.95rem;
            opacity: 0.9;
            font-weight: 300;
        }

        .login-body {
            padding: 40px 30px;
        }

        .form-floating {
            margin-bottom: 20px;
        }

        .form-floating input {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 12px 15px;
            height: auto;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-floating input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            transform: translateY(-2px);
        }

        .form-floating label {
            padding: 12px 15px;
            color: #6c757d;
        }

        .input-group-text {
            background: transparent;
            border: 2px solid #e9ecef;
            border-right: none;
            border-radius: 12px 0 0 12px;
            color: #6c757d;
        }

        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            padding: 15px;
            font-size: 1.1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .floating-elements {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .floating-phone {
            position: absolute;
            color: rgba(255, 255, 255, 0.1);
            animation: float 6s ease-in-out infinite;
        }

        .floating-phone:nth-child(1) {
            top: 10%;
            left: 10%;
            font-size: 2rem;
            animation-delay: 0s;
        }

        .floating-phone:nth-child(2) {
            top: 20%;
            right: 15%;
            font-size: 1.5rem;
            animation-delay: 2s;
        }

        .floating-phone:nth-child(3) {
            bottom: 30%;
            left: 5%;
            font-size: 1.8rem;
            animation-delay: 4s;
        }

        .floating-phone:nth-child(4) {
            bottom: 15%;
            right: 10%;
            font-size: 2.2rem;
            animation-delay: 1s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }

        .footer-text {
            text-align: center;
            margin-top: 30px;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
        }

        @media (max-width: 576px) {
            .login-card {
                margin: 10px;
                border-radius: 15px;
            }
            
            .login-header {
                padding: 30px 20px 20px;
            }
            
            .login-body {
                padding: 30px 20px;
            }
            
            .company-name {
                font-size: 1.5rem;
            }
        }

        /* Loading animation */
        .btn-login.loading {
            position: relative;
            color: transparent;
        }

        .btn-login.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin: -10px 0 0 -10px;
            border: 2px solid #ffffff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>
<body>
    <!-- Elementos flotantes decorativos -->
    <div class="floating-elements">
        <i class="fas fa-mobile-alt floating-phone"></i>
        <i class="fas fa-tools floating-phone"></i>
        <i class="fas fa-smartphone floating-phone"></i>
        <i class="fas fa-screwdriver floating-phone"></i>
    </div>

    <div class="login-container">
        <div class="login-card">
            <!-- Header de la empresa -->
            <div class="login-header">
                <div class="company-logo">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <h1 class="company-name">TechRepair Pro</h1>
                <p class="company-tagline">Especialistas en Reparación de Teléfonos</p>
            </div>

            <!-- Formulario de login -->
            <div class="login-body">
                <form id="FormLogin">
                    <div class="form-floating">
                        <input type="text" 
                               class="form-control" 
                               id="nombre_usuario" 
                               name="nombre_usuario" 
                               placeholder="Usuario" 
                               required>
                        <label for="nombre_usuario">
                            <i class="fas fa-user me-2"></i>Nombre de Usuario
                        </label>
                    </div>

                    <div class="form-floating">
                        <input type="password" 
                               class="form-control" 
                               id="password" 
                               name="password" 
                               placeholder="Contraseña" 
                               required>
                        <label for="password">
                            <i class="fas fa-lock me-2"></i>Contraseña
                        </label>
                    </div>

                    <button type="submit" 
                            class="btn btn-login w-100" 
                            id="BtnIniciar">
                        <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                    </button>
                </form>

                <div class="footer-text">
                    <i class="fas fa-shield-alt me-1"></i>
                    Acceso seguro al sistema
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div style="position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); color: rgba(255,255,255,0.7); font-size: 0.85rem; text-align: center;">
        <i class="fas fa-copyright me-1"></i>2025 TechRepair Pro - Sistema de Gestión
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Simulación del comportamiento del botón
        document.getElementById('FormLogin').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const btn = document.getElementById('BtnIniciar');
            btn.classList.add('loading');
            btn.disabled = true;
            
            // Simular proceso de login
            setTimeout(() => {
                btn.classList.remove('loading');
                btn.disabled = false;
                // Aquí iría tu lógica de login real
            }, 2000);
        });

        // Efecto de enfoque automático
        window.addEventListener('load', function() {
            document.getElementById('nombre_usuario').focus();
        });
    </script>
</body>
</html>

<script src="<?= asset('build/js/login/login.js') ?>"></script>