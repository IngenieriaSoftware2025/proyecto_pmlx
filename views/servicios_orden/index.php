<div class="row justify-content-center p-3">
    <div class="col-lg-12">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                    <h3 class="text-center text-success">AGREGAR SERVICIO A ORDEN</h3>
                </div>

                <div class="row justify-content-center p-5 shadow-lg">
                    <form id="FormServiciosOrden">
                        <input type="hidden" id="id_servicio_orden" name="id_servicio_orden">

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="id_orden" class="form-label">ORDEN DE REPARACIÓN <span class="text-danger">*</span></label>
                                <select class="form-select" id="id_orden" name="id_orden" required>
                                    <option value="">Seleccione una orden</option>
                                </select>
                            </div>
                            <div class="col-lg-6">
                                <label for="id_tipo_servicio" class="form-label">TIPO DE SERVICIO <span class="text-danger">*</span></label>
                                <select class="form-select" id="id_tipo_servicio" name="id_tipo_servicio" required>
                                    <option value="">Seleccione un tipo de servicio</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="precio_base_info" class="form-label">PRECIO BASE SUGERIDO</label>
                                <div class="input-group">
                                    <span class="input-group-text">Q</span>
                                    <input type="text" class="form-control" id="precio_base_info" readonly placeholder="Seleccione un servicio">
                                    <span class="input-group-text">Precio sugerido del catálogo</span>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <label for="precio_servicio" class="form-label">PRECIO DEL SERVICIO <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Q</span>
                                    <input type="number" class="form-control" id="precio_servicio" name="precio_servicio" step="0.01" min="0.01" placeholder="0.00" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="estado_servicio" class="form-label">ESTADO DEL SERVICIO</label>
                                <select class="form-select" id="estado_servicio" name="estado_servicio">
                                    <option value="P" selected>Pendiente</option>
                                    <option value="E">En Proceso</option>
                                    <option value="C">Completado</option>
                                </select>
                            </div>
                            <div class="col-lg-6">
                                <label for="fecha_inicio" class="form-label">FECHA DE INICIO</label>
                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio">
                                <div class="form-text">Opcional - Cuando se inicia el servicio</div>
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="fecha_completado" class="form-label">FECHA DE COMPLETADO</label>
                                <input type="date" class="form-control" id="fecha_completado" name="fecha_completado">
                                <div class="form-text">Solo cuando esté completado</div>
                            </div>
                            <div class="col-lg-6">
                                <label for="observaciones" class="form-label">OBSERVACIONES</label>
                                <textarea class="form-control" id="observaciones" name="observaciones" rows="2" placeholder="Observaciones adicionales sobre el servicio (opcional)"></textarea>
                            </div>
                        </div>

                        <div class="row justify-content-center">
                            <div class="col-lg-12">
                                <div class="alert alert-info" role="alert">
                                    <i class="bi bi-info-circle me-1"></i>
                                    <strong>Nota:</strong> Los campos marcados con <span class="text-danger">*</span> son obligatorios. 
                                    El estado por defecto es "Pendiente" y se puede modificar posteriormente.
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-center mt-4">
                            <div class="col-auto">
                                <button class="btn btn-success" type="submit" id="BtnGuardar">
                                    <i class="bi bi-save me-1"></i>Guardar Servicio
                                </button>
                            </div>

                            <div class="col-auto">
                                <button class="btn btn-warning d-none" type="button" id="BtnModificar">
                                    <i class="bi bi-pencil-square me-1"></i>Modificar Servicio
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

<div class="row justify-content-center p-3">
    <div class="col-lg-12">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <h3 class="text-center text-success">SERVICIOS DE ÓRDENES REGISTRADOS</h3>

                <div class="table-responsive p-2">
                    <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableServiciosOrden">
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= asset('build/js/servicios_orden/index.js') ?>"></script>