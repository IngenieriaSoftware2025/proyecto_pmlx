<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="build/js/app.js"></script>
    <link rel="shortcut icon" href="<?= asset('images/cit.png') ?>" type="image/x-icon">
    <link rel="stylesheet" href="<?= asset('build/styles.css') ?>">
    <title>DemoApp</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark  bg-dark">

        <div class="container-fluid">

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarToggler" aria-controls="navbarToggler" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand" href="/ejemplo/">
                <img src="<?= asset('./images/cit.png') ?>" width="35px'" alt="cit">
                Aplicaciones
            </a>
            <div class="collapse navbar-collapse" id="navbarToggler">

                <ul class="navbar-nav me-auto mb-2 mb-lg-0" style="margin: 0;">
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="/proyecto_pmlx/"><i class="bi bi-house-fill me-2"></i>Inicio</a>
                    </li>

                    <!-- <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="/proyecto_pmlx/usuarios"><i class="bi bi-house-fill me-2"></i>Usuarios</a>
                    </li> -->

                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="/proyecto_pmlx/roles"><i class="bi bi-house-fill me-2"></i>Roles</a>
                    </li>

                    <!-- <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="/proyecto_pmlx/marcas"><i class="bi bi-house-fill me-2"></i>Marcas</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="/proyecto_pmlx/modelos"><i class="bi bi-house-fill me-2"></i>Modelos</a>
                    </li>  -->

                    <!-- <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="/proyecto_pmlx/clientes"><i class="bi bi-house-fill me-2"></i>clientes</a>
                    </li> -->

                    <!-- <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="/proyecto_pmlx/tipos_servicio"><i class="bi bi-house-fill me-2"></i> Servicios adquiridos </a>
                    </li> -->

                    <!-- <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="/proyecto_pmlx/trabajadores"><i class="bi bi-house-fill me-2"></i>Trabajadores</a>
                    </li> -->

                    <!-- <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="/proyecto_pmlx/ordenes_reparacion"><i class="bi bi-house-fill me-2"></i>Ordenes de Reparacion </a>
                    </li> -->

                    <!-- <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="/proyecto_pmlx/servicios_orden"><i class="bi bi-house-fill me-2"></i>Servicios orden </a>
                    </li> -->

                    <!-- <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="/proyecto_pmlx/ventas"><i class="bi bi-house-fill me-2"></i>Ventas </a>
                    </li> -->

                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-gear me-2"></i>Telefonos
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark" style="margin: 0;">
                            <li>
                                <a class="dropdown-item nav-link text-white" href="/proyecto_pmlx/marcas">
                                    <i class="bi bi-box-seam me-2"></i>Marcas
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item nav-link text-white" href="/proyecto_pmlx/modelos">
                                    <i class="bi bi-people me-2"></i>Modelos
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-gear me-2"></i>Personal
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark" style="margin: 0;">
                            <li>
                                <a class="dropdown-item nav-link text-white" href="/proyecto_pmlx/trabajadores">
                                    <i class="bi bi-box-seam me-2"></i>Trabajadores
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item nav-link text-white" href="/proyecto_pmlx/usuarios">
                                    <i class="bi bi-people me-2"></i>Usuarios
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item nav-link text-white" href="/proyecto_pmlx/clientes">
                                    <i class="bi bi-people me-2"></i>Clientes
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-gear me-2"></i>Tipo de accion
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark" style="margin: 0;">
                            <li>
                                <a class="dropdown-item nav-link text-white" href="/proyecto_pmlx/ventas">
                                    <i class="bi bi-people me-2"></i> Ventas
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item nav-link text-white" href="/proyecto_pmlx/tipos_servicio">
                                    <i class="bi bi-box-seam me-2"></i>Servicios adquiridos
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-gear me-2"></i>Ordenes
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark" style="margin: 0;">
                            <li>
                                <a class="dropdown-item nav-link text-white" href="/proyecto_pmlx/ordenes_reparacion">
                                    <i class="bi bi-people me-2"></i>Orden de la reparacion
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item nav-link text-white" href="/proyecto_pmlx/servicios_orden">
                                    <i class="bi bi-people me-2"></i>Orden del servicio
                                </a>
                            </li>
                        </ul>
                    </div>

                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="/proyecto_pmlx/inventario"><i class="bi bi-house-fill me-2"></i>Inventario</a>
                    </li>



                </ul>
               
                </ul>
                <div class="col-lg-1 d-grid mb-lg-0 mb-2">
                    <!-- Ruta relativa desde el archivo donde se incluye menu.php -->
                    <a href="/menu/" class="btn btn-danger"><i class="bi bi-arrow-bar-left"></i>MENÚ</a>
                </div>


            </div>
        </div>

    </nav>
    <div class="progress fixed-bottom" style="height: 6px;">
        <div class="progress-bar progress-bar-animated bg-danger" id="bar" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
    <div class="container-fluid pt-5 mb-4" style="min-height: 85vh">

        <?php echo $contenido; ?>
    </div>
    <div class="container-fluid ">
        <div class="row justify-content-center text-center">
            <div class="col-12">
                <p style="font-size:xx-small; font-weight: bold;">
                    Comando de Informática y Tecnología, <?= date('Y') ?> &copy;
                </p>
            </div>
        </div>
    </div>
</body>

</html>