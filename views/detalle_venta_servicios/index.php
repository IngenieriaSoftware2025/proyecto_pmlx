<div class="row justify-content-center p-3">
    <div class="col-lg-12">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                    <h3 class="text-center text-success">AGREGAR SERVICIO A VENTA</h3>
                </div>

                <div class="row justify-content-center p-5 shadow-lg">
                    <form id="FormDetalleVentaServicios">
                        <input type="hidden" id="id_detalle_servicio" name="id_detalle_servicio">

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="id_venta" class="form-label">VENTA (FACTURA) <span class="text-danger">*</span></label>
                                <select class="form-select" id="id_venta" name="id_venta" required>
                                    <option value="">Seleccione una venta</option>
                                </select>
                                <div class="form-text">Solo se muestran ventas de servicios activas</div>
                            </div>
                            <div class="col-lg-6">
                                <label for="id_orden" class="form-label">ORDEN DE REPARACIÓN <span class="text-danger">*</span></label>
                                <select class="form-select" id="id_orden" name="id_orden" required>
                                    <option value="">Seleccione una orden</option>
                                </select>
                                <div class="form-text">Solo órdenes terminadas no facturadas</div>
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-4">
                                <label for="precio_sugerido" class="form-label">PRECIO SUGERIDO</label>
                                <div class="input-group">
                                    <span class="input-group-text">Q</span>
                                    <input type="text" class="form-control" id="precio_sugerido" readonly placeholder="0.00">
                                </div>
                                <div class="form-text">Suma de servicios de la orden</div>
                            </div>
                            <div class="col-lg-4">
                                <label for="precio_servicio" class="form-label">PRECIO DEL SERVICIO <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Q</span>
                                    <input type="number" class="form-control" id="precio_servicio" name="precio_servicio" step="0.01" min="0.01" placeholder="0.00" required>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <label for="estado_orden" class="form-label">ESTADO DE LA ORDEN</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-info-circle"></i></span>
                                    <input type="text" class="form-control" id="estado_orden" readonly placeholder="Seleccione orden">
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-12">
                                <label for="descripcion_servicio" class="form-label">DESCRIPCIÓN DEL SERVICIO</label>
                                <textarea class="form-control" id="descripcion_servicio" name="descripcion_servicio" rows="2" placeholder="Descripción del servicio realizado"></textarea>
                                <div class="form-text">Se autocompletará con el motivo de ingreso de la orden</div>
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-12">
                                <label class="form-label">INFORMACIÓN DE LA ORDEN</label>
                                <div class="border rounded p-3 bg-light">
                                    <div id="info_orden" class="text-muted">
                                        <i class="bi bi-info-circle"></i> Seleccione una orden para ver su información
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-center">
                            <div class="col-lg-12">
                                <div class="alert alert-info" role="alert">
                                    <i class="bi bi-info-circle me-1"></i>
                                    <strong>Nota:</strong> Solo se pueden facturar órdenes terminadas o entregadas que no hayan sido 
                                    facturadas previamente. Al agregar el servicio se actualizarán automáticamente los totales de la venta.
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-center mt-4">
                            <div class="col-auto">
                                <button class="btn btn-success" type="submit" id="BtnGuardar">
                                    <i class="bi bi-save me-1"></i>Agregar Servicio
                                </button>
                            </div>

                            <div class="col-auto">
                                <button class="btn btn-warning d-none" type="button" id="BtnModificar">
                                    <i class="bi bi-pencil-square me-1"></i>Modificar Detalle
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
                <h3 class="text-center text-success">SERVICIOS EN VENTAS</h3>

                <div class="table-responsive p-2">
                    <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableDetalleVentaServicios">
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= asset('build/js/detalle_venta_servicios/index.js') ?>"></script>

