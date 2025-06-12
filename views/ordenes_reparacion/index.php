<div class="row justify-content-center p-3">
    <div class="col-lg-12">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                      <h3 class="text-center text-success">REGISTRAR ORDEN DE REPARACIÓN</h3>
                </div>

                <div class="row justify-content-center p-5 shadow-lg">

                    <form id="FormOrdenesReparacion">
                        <input type="hidden" id="id_orden" name="id_orden">

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-4">
                                <label for="numero_orden" class="form-label">NÚMERO DE ORDEN <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="numero_orden" name="numero_orden" placeholder="Ej: ORD-2025-001" required>
                            </div>
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
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="modelo_dispositivo" class="form-label">MODELO DEL DISPOSITIVO</label>
                                <input type="text" class="form-control" id="modelo_dispositivo" name="modelo_dispositivo" placeholder="Ej: iPhone 15, Galaxy S24, etc.">
                            </div>
                            <div class="col-lg-6">
                                <label for="imei_dispositivo" class="form-label">IMEI DEL DISPOSITIVO</label>
                                <input type="text" class="form-control" id="imei_dispositivo" name="imei_dispositivo" placeholder="IMEI del dispositivo (opcional)">
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="motivo_ingreso" class="form-label">MOTIVO DE INGRESO <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="motivo_ingreso" name="motivo_ingreso" placeholder="Descripción breve del problema" required>
                            </div>
                            <div class="col-lg-6">
                                <label for="descripcion_problema" class="form-label">DESCRIPCIÓN DETALLADA</label>
                                <textarea class="form-control" id="descripcion_problema" name="descripcion_problema" rows="2" placeholder="Descripción más detallada del problema (opcional)"></textarea>
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-4">
                                <label for="estado_orden" class="form-label">ESTADO DE LA ORDEN</label>
                                <select class="form-select" id="estado_orden" name="estado_orden">
                                    <option value="R" selected>Recibido</option>
                                    <option value="P">En Proceso</option>
                                    <option value="E">Esperando Repuestos</option>
                                    <option value="T">Terminado</option>
                                    <option value="N">Entregado</option>
                                    <option value="C">Cancelado</option>
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <label for="fecha_promesa_entrega" class="form-label">FECHA PROMESA DE ENTREGA</label>
                                <input type="date" class="form-control" id="fecha_promesa_entrega" name="fecha_promesa_entrega">
                                <div class="form-text">Opcional</div>
                            </div>
                            <div class="col-lg-4">
                                <label for="fecha_entrega_real" class="form-label">FECHA ENTREGA REAL</label>
                                <input type="date" class="form-control" id="fecha_entrega_real" name="fecha_entrega_real">
                                <div class="form-text">Solo cuando esté entregado</div>
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="id_trabajador_asignado" class="form-label">TÉCNICO ASIGNADO</label>
                                <select class="form-select" id="id_trabajador_asignado" name="id_trabajador_asignado">
                                    <option value="">Sin asignar</option>
                                </select>
                                <div class="form-text">Opcional - Se puede asignar después</div>
                            </div>
                            <div class="col-lg-6">
                                <label for="observaciones" class="form-label">OBSERVACIONES</label>
                                <textarea class="form-control" id="observaciones" name="observaciones" rows="2" placeholder="Observaciones adicionales (opcional)"></textarea>
                            </div>
                        </div>

                        <div class="row justify-content-center">
                            <div class="col-lg-12">
                                <div class="alert alert-info" role="alert">
                                    <i class="bi bi-info-circle me-1"></i>
                                    <strong>Nota:</strong> Los campos marcados con <span class="text-danger">*</span> son obligatorios. 
                                    El estado por defecto es "Recibido" y se puede modificar posteriormente.
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-center mt-4">
                            <div class="col-auto">
                                <button class="btn btn-success" type="submit" id="BtnGuardar">
                                    <i class="bi bi-save me-1"></i>Guardar Orden
                                </button>
                            </div>

                            <div class="col-auto ">
                                <button class="btn btn-warning d-none" type="button" id="BtnModificar">
                                    <i class="bi bi-pencil-square me-1"></i>Modificar Orden
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
                <h3 class="text-center text-success">ÓRDENES DE REPARACIÓN REGISTRADAS</h3>

                <div class="table-responsive p-2">
                    <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableOrdenesReparacion">
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="<?= asset('build/js/ordenes_reparacion/index.js') ?>"></script>