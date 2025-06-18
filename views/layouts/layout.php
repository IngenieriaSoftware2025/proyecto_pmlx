<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="build/js/app.js"></script>
    <link rel="shortcut icon" href="<?= asset('images/cit.png') ?>" type="image/x-icon">
    <link rel="stylesheet" href="<?= asset('build/styles.css') ?>">
    <title>DemoApp</title>
    <style>
        .navbar-custom {
            background:rgb(85, 15, 85) !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-brand {
            font-weight: 600;
            font-size: 1.3rem;
            color: white !important;
        }
        
        .navbar-nav .nav-link {
            font-weight: 500;
            color: rgba(255, 255, 255, 0.9) !important;
            transition: all 0.3s ease;
            border-radius: 8px;
            margin: 0 2px;
            padding: 8px 12px !important;
        }
        
        .navbar-nav .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white !important;
        }
        
        .dropdown-menu-custom {
            background: #495057 !important;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            padding: 8px;
        }
        
        .dropdown-item-custom {
            color: rgba(255, 255, 255, 0.9) !important;
            border-radius: 6px;
            margin: 2px 0;
            padding: 8px 12px !important;
            transition: all 0.3s ease;
        }
        
        .dropdown-item-custom:hover {
            background: rgba(255, 255, 255, 0.1) !important;
            color: white !important;
        }
        
        .btn-logout {
            background: #dc3545;
            border: none;
            color: white;
            font-weight: 500;
            border-radius: 6px;
            padding: 8px 16px;
            transition: all 0.3s ease;
        }
        
        .btn-logout:hover {
            background: #c82333;
            color: white;
            transform: translateY(-1px);
        }
        
        .navbar-toggler {
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 4px;
        }
        
        .navbar-toggler:focus {
            box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.25);
        }
        
        body {
            background-color: #f8f9fa;
        }
        
        .main-content {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin: 20px;
            min-height: calc(100vh - 200px);
        }
        
        .footer-custom {
            background: #343a40;
            color: white;
            padding: 15px 0;
            margin-top: auto;
        }
        
        /* Asegurar que los dropdowns se vean correctamente */
        .dropdown-menu {
            z-index: 1050;
        }
        
        .navbar-nav .dropdown-toggle::after {
            color: white;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarToggler" aria-controls="navbarToggler" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <a class="navbar-brand d-flex align-items-center" href="">
                <img src="<?= asset('./images/cit.png') ?>" width="35" height="35" alt="cit" class="me-2">
                <span>TechRepair Pro</span>
            </a>
            
            <div class="collapse navbar-collapse" id="navbarToggler">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="/proyecto_pmlx/inicio">
                            <i class="bi bi-house-fill me-2"></i>Inicio
                        </a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-phone me-2"></i>Teléfonos
                        </a>
                        <ul class="dropdown-menu dropdown-menu-custom">
                            <li>
                                <a class="dropdown-item dropdown-item-custom" href="/proyecto_pmlx/marcas">
                                    <i class="bi bi-tags me-2"></i>Marcas
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item dropdown-item-custom" href="/proyecto_pmlx/modelos">
                                    <i class="bi bi-phone me-2"></i>Modelos
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-people me-2"></i>Personal
                        </a>
                        <ul class="dropdown-menu dropdown-menu-custom">
                            <li>
                                <a class="dropdown-item dropdown-item-custom" href="/proyecto_pmlx/trabajadores">
                                    <i class="bi bi-hammer me-2"></i>Trabajadores
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item dropdown-item-custom" href="/proyecto_pmlx/usuarios">
                                    <i class="bi bi-person-gear me-2"></i>Usuarios
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item dropdown-item-custom" href="/proyecto_pmlx/clientes">
                                    <i class="bi bi-person-hearts me-2"></i>Clientes
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-clipboard-data me-2"></i>Registros
                        </a>
                        <ul class="dropdown-menu dropdown-menu-custom">
                            <li>
                                <a class="dropdown-item dropdown-item-custom" href="/proyecto_pmlx/ventas">
                                    <i class="bi bi-cart-check me-2"></i>Ventas
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item dropdown-item-custom" href="/proyecto_pmlx/tipos_servicio">
                                    <i class="bi bi-gear-wide-connected me-2"></i>Servicios
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-clipboard-check me-2"></i>Órdenes
                        </a>
                        <ul class="dropdown-menu dropdown-menu-custom">
                            <li>
                                <a class="dropdown-item dropdown-item-custom" href="/proyecto_pmlx/ordenes_reparacion">
                                    <i class="bi bi-tools me-2"></i>Reparaciones
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item dropdown-item-custom" href="/proyecto_pmlx/servicios_orden">
                                    <i class="bi bi-list-check me-2"></i>Servicios de Orden
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-receipt me-2"></i>Ventas Detalle
                        </a>
                        <ul class="dropdown-menu dropdown-menu-custom">
                            <li>
                                <a class="dropdown-item dropdown-item-custom" href="/proyecto_pmlx/detalle_venta_productos">
                                    <i class="bi bi-phone me-2"></i>Teléfonos
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item dropdown-item-custom" href="/proyecto_pmlx/detalle_venta_servicios">
                                    <i class="bi bi-gear me-2"></i>Servicios
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="/proyecto_pmlx/inventario">
                            <i class="bi bi-boxes me-2"></i>Inventario
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="/proyecto_pmlx/estadisticas">
                            <i class="bi bi-graph-up me-2"></i>Estadísticas
                        </a>
                    </li>
                </ul>

                <div class="d-flex">
                    <a href="/proyecto_pmlx/logout" class="btn btn-logout">
                        <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="progress fixed-bottom" style="height: 6px;">
        <div class="progress-bar progress-bar-animated bg-danger" id="bar" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <div class="main-content">
        <?php echo $contenido; ?>
    </div>

    <footer class="footer-custom">
        <div class="container-fluid">
            <div class="row justify-content-center text-center">
                <div class="col-12">
                    <p class="mb-0" style="font-size: 0.9rem;">
                        <i class="bi bi-shield-check me-2"></i>
                        Comando de Informática y Tecnología, <?= date('Y') ?> &copy;
                    </p>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>