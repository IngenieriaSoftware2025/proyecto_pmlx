<div class="row justify-content-center p-3">
    <div class="col-lg-12">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                      <h3 class="text-center text-success">CREAR ORDEN DE REPARACIÓN</h3>
                </div>

                <div class="row justify-content-center p-4 shadow-lg">

                    <form id="FormOrdenesReparacion">
                        <input type="hidden" id="id_orden" name="id_orden">

                        <!-- Información del Cliente y Dispositivo -->
                        <div class="row mb-3">
                            <div class="col-lg-4">
                                <label for="id_cliente" class="form-label">CLIENTE <span class="text-danger">*</span></label>
                                <select class="form-select" id="id_cliente" name="id_cliente" required>
                                    <option value="">Seleccione un cliente</option>
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <label for="id_marca" class="form-label">MARCA DEL DISPOSITIVO <span class="text-danger">*</span></label>
                                <select class="form-select" id="id_marca" name="id_marca" required>
                                    <option value="">Seleccione una marca</option>
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <label for="modelo_dispositivo" class="form-label">MODELO DEL DISPOSITIVO <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="modelo_dispositivo" name="modelo_dispositivo" placeholder="Ej: iPhone 15 Pro" required>
                            </div>
                        </div>

                        <!-- IMEI y Motivo -->
                        <div class="row mb-3">
                            <div class="col-lg-4">
                                <label for="imei_dispositivo" class="form-label">IMEI/SERIE</label>
                                <input type="text" class="form-control" id="imei_dispositivo" name="imei_dispositivo" placeholder="Número IMEI o serie (opcional)">
                            </div>
                            <div class="col-lg-8">
                                <label for="motivo_ingreso" class="form-label">MOTIVO DE INGRESO <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="motivo_ingreso" name="motivo_ingreso" placeholder="Ej: Pantalla rota, no enciende, problema de batería..." required>
                            </div>
                        </div>

                        <!-- Descripción del Problema -->
                        <div class="row mb-3">
                            <div class="col-lg-12">
                                <label for="descripcion_problema" class="form-label">DESCRIPCIÓN DETALLADA DEL PROBLEMA</label>
                                <textarea class="form-control" id="descripcion_problema" name="descripcion_problema" rows="3" placeholder="Describa detalladamente el problema reportado por el cliente..."></textarea>
                            </div>
                        </div>

                        <!-- Estado, Trabajador y Fecha -->
                        <div class="row mb-3">
                            <div class="col-lg-3">
                                <label for="estado_orden" class="form-label">ESTADO INICIAL</label>
                                <select class="form-select" id="estado_orden" name="estado_orden">
                                    <option value="R" selected>Recibido</option>
                                    <option value="P">En Proceso</option>
                                    <option value="E">Esperando Repuestos</option>
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <label for="id_trabajador_asignado" class="form-label">TÉCNICO ASIGNADO</label>
                                <select class="form-select" id="id_trabajador_asignado" name="id_trabajador_asignado">
                                    <option value="">Sin asignar (se asignará después)</option>
                                </select>
                            </div>
                            <div class="col-lg-5">
                                <label for="fecha_promesa_entrega" class="form-label">FECHA PROMESA DE ENTREGA</label>
                                <input type="date" class="form-control" id="fecha_promesa_entrega" name="fecha_promesa_entrega">
                            </div>
                        </div>

                        <!-- Observaciones -->
                        <div class="row mb-3">
                            <div class="col-lg-12">
                                <label for="observaciones" class="form-label">OBSERVACIONES ADICIONALES</label>
                                <textarea class="form-control" id="observaciones" name="observaciones" rows="2" placeholder="Observaciones internas, condiciones del dispositivo, accesorios entregados, etc..."></textarea>
                            </div>
                        </div>

                        <!-- Nota informativa -->
                        <div class="row mb-3">
                            <div class="col-lg-12">
                                <div class="alert alert-info" role="alert">
                                    <i class="bi bi-info-circle me-1"></i>
                                    <strong>Nota:</strong> Los campos marcados con <span class="text-danger">*</span> son obligatorios. Se generará automáticamente un número de orden único para esta reparación.
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="row justify-content-center mt-4">
                            <div class="col-auto">
                                <button class="btn btn-success" type="submit" id="BtnGuardar">
                                    <i class="bi bi-save me-1"></i>Crear Orden
                                </button>
                            </div>

                            <div class="col-auto ">
                                <button class="btn btn-warning d-none" type="button" id="BtnModificar">
                                    <i class="bi bi-pencil-square me-1"></i>Modificar
                                </button>
                            </div>

                            <div class="col-auto">
                                <button class="btn btn-secondary" type="reset" id="BtnLimpiar">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Limpiar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de Órdenes -->
<div class="row justify-content-center p-3">
    <div class="col-lg-12">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <h3 class="text-center text-success">ÓRDENES DE REPARACIÓN</h3>

                <!-- Filtros -->
                <div class="row mb-3">
                    <div class="col-lg-3">
                        <label for="filtro_estado" class="form-label">Filtrar por Estado:</label>
                        <select class="form-select" id="filtro_estado">
                            <option value="">Todos los estados</option>
                            <option value="R">Recibido</option>
                            <option value="P">En Proceso</option>
                            <option value="E">Esperando Repuestos</option>
                            <option value="T">Terminado</option>
                            <option value="N">Entregado</option>
                            <option value="C">Cancelado</option>
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <label for="filtro_cliente" class="form-label">Buscar Cliente:</label>
                        <input type="text" class="form-control" id="filtro_cliente" placeholder="Nombre del cliente...">
                    </div>
                    <div class="col-lg-3">
                        <label for="filtro_numero" class="form-label">Número de Orden:</label>
                        <input type="text" class="form-control" id="filtro_numero" placeholder="ORD-2025-0001">
                    </div>
                    <div class="col-lg-3 d-flex align-items-end">
                        <button class="btn btn-primary" id="BtnAplicarFiltros">
                            <i class="bi bi-filter me-1"></i>Aplicar Filtros
                        </button>
                        <button class="btn btn-outline-secondary ms-2" id="BtnLimpiarFiltros">
                            <i class="bi bi-x-circle me-1"></i>Limpiar
                        </button>
                    </div>
                </div>

                <div class="table-responsive p-2">
                    <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableOrdenesReparacion">
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="<?= asset('build/js/ordenes_reparacion/index.js') ?>"></script>