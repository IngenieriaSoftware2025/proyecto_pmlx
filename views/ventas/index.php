<div class="row justify-content-center p-3">
    <div class="col-lg-12">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                    <h3 class="text-center text-success">REGISTRAR VENTA</h3>
                </div>

                <div class="row justify-content-center p-5 shadow-lg">
                    <form id="FormVentas">
                        <input type="hidden" id="id_venta" name="id_venta">

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-4">
                                <label for="numero_factura" class="form-label">NÚMERO DE FACTURA <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="numero_factura" name="numero_factura" placeholder="FAC-2025-0001" required>
                                    <button class="btn btn-outline-secondary" type="button" id="BtnGenerarFactura">
                                        <i class="bi bi-arrow-clockwise"></i> Generar
                                    </button>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <label for="id_cliente" class="form-label">CLIENTE</label>
                                <select class="form-select" id="id_cliente" name="id_cliente">
                                    <option value="">Cliente general (opcional)</option>
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <label for="tipo_venta" class="form-label">TIPO DE VENTA <span class="text-danger">*</span></label>
                                <select class="form-select" id="tipo_venta" name="tipo_venta" required>
                                    <option value="P" selected>Productos</option>
                                    <option value="S">Servicios</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-3">
                                <label for="subtotal" class="form-label">SUBTOTAL</label>
                                <div class="input-group">
                                    <span class="input-group-text">Q</span>
                                    <input type="number" class="form-control" id="subtotal" name="subtotal" step="0.01" min="0" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <label for="descuento" class="form-label">DESCUENTO</label>
                                <div class="input-group">
                                    <span class="input-group-text">Q</span>
                                    <input type="number" class="form-control" id="descuento" name="descuento" step="0.01" min="0" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <label for="impuestos" class="form-label">IMPUESTOS (IVA)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Q</span>
                                    <input type="number" class="form-control" id="impuestos" name="impuestos" step="0.01" min="0" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <label for="total" class="form-label">TOTAL <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Q</span>
                                    <input type="number" class="form-control" id="total" name="total" step="0.01" min="0.01" placeholder="0.00" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-4">
                                <label for="metodo_pago" class="form-label">MÉTODO DE PAGO</label>
                                <select class="form-select" id="metodo_pago" name="metodo_pago">
                                    <option value="E" selected>Efectivo</option>
                                    <option value="T">Tarjeta</option>
                                    <option value="R">Transferencia</option>
                                    <option value="C">Crédito</option>
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <label for="estado_venta" class="form-label">ESTADO DE LA VENTA</label>
                                <select class="form-select" id="estado_venta" name="estado_venta">
                                    <option value="C" selected>Completada</option>
                                    <option value="P">Pendiente</option>
                                    <option value="N">Cancelada</option>
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <label for="id_usuario_vendedor" class="form-label">VENDEDOR <span class="text-danger">*</span></label>
                                <select class="form-select" id="id_usuario_vendedor" name="id_usuario_vendedor" required>
                                    <option value="">Seleccione un vendedor</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-12">
                                <label for="observaciones" class="form-label">OBSERVACIONES</label>
                                <textarea class="form-control" id="observaciones" name="observaciones" rows="2" placeholder="Observaciones adicionales sobre la venta (opcional)"></textarea>
                            </div>
                        </div>

                        <div class="row justify-content-center">
                            <div class="col-lg-12">
                                <div class="alert alert-info" role="alert">
                                    <i class="bi bi-info-circle me-1"></i>
                                    <strong>Nota:</strong> Los campos marcados con <span class="text-danger">*</span> son obligatorios. 
                                    Después de crear la venta, podrá agregar los productos o servicios correspondientes.
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-center mt-4">
                            <div class="col-auto">
                                <button class="btn btn-success" type="submit" id="BtnGuardar">
                                    <i class="bi bi-save me-1"></i>Guardar Venta
                                </button>
                            </div>

                            <div class="col-auto">
                                <button class="btn btn-warning d-none" type="button" id="BtnModificar">
                                    <i class="bi bi-pencil-square me-1"></i>Modificar Venta
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
                <h3 class="text-center text-success">VENTAS REGISTRADAS</h3>

                <div class="table-responsive p-2">
                    <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableVentas">
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= asset('build/js/ventas/index.js') ?>"></script>
