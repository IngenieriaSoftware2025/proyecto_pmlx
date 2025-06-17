<div class="row justify-content-center p-3">
    <div class="col-lg-12">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                    <h3 class="text-center text-success">AGREGAR PRODUCTO A VENTA</h3>
                </div>

                <div class="row justify-content-center p-5 shadow-lg">
                    <form id="FormDetalleVentaProductos">
                        <input type="hidden" id="id_detalle" name="id_detalle">

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="id_venta" class="form-label">VENTA (FACTURA) <span class="text-danger">*</span></label>
                                <select class="form-select" id="id_venta" name="id_venta" required>
                                    <option value="">Seleccione una venta</option>
                                </select>
                                <div class="form-text">Solo se muestran ventas de productos activas</div>
                            </div>
                            <div class="col-lg-6">
                                <label for="id_inventario" class="form-label">PRODUCTO <span class="text-danger">*</span></label>
                                <select class="form-select" id="id_inventario" name="id_inventario" required>
                                    <option value="">Seleccione un producto</option>
                                </select>
                                <div class="form-text">Solo productos con stock disponible</div>
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-3">
                                <label for="stock_disponible" class="form-label">STOCK DISPONIBLE</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-box"></i></span>
                                    <input type="text" class="form-control" id="stock_disponible" readonly placeholder="Seleccione producto">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <label for="precio_catalogo" class="form-label">PRECIO CATÁLOGO</label>
                                <div class="input-group">
                                    <span class="input-group-text">Q</span>
                                    <input type="text" class="form-control" id="precio_catalogo" readonly placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <label for="cantidad" class="form-label">CANTIDAD <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-123"></i></span>
                                    <input type="number" class="form-control" id="cantidad" name="cantidad" min="1" placeholder="1" required>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <label for="precio_unitario" class="form-label">PRECIO UNITARIO <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Q</span>
                                    <input type="number" class="form-control" id="precio_unitario" name="precio_unitario" step="0.01" min="0.01" placeholder="0.00" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="subtotal_calculado" class="form-label">SUBTOTAL CALCULADO</label>
                                <div class="input-group">
                                    <span class="input-group-text">Q</span>
                                    <input type="text" class="form-control fs-5 fw-bold text-success" id="subtotal_calculado" readonly placeholder="0.00">
                                </div>
                                <div class="form-text">Cantidad × Precio Unitario</div>
                            </div>
                            <div class="col-lg-6">
                                <label class="form-label">INFORMACIÓN DEL PRODUCTO</label>
                                <div class="border rounded p-2 bg-light">
                                    <div id="info_producto" class="text-muted">
                                        <i class="bi bi-info-circle"></i> Seleccione un producto para ver su información
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-center">
                            <div class="col-lg-12">
                                <div class="alert alert-info" role="alert">
                                    <i class="bi bi-info-circle me-1"></i>
                                    <strong>Nota:</strong> Al agregar un producto se actualizará automáticamente el stock del inventario 
                                    y los totales de la venta. Los campos marcados con <span class="text-danger">*</span> son obligatorios.
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-center mt-4">
                            <div class="col-auto">
                                <button class="btn btn-success" type="submit" id="BtnGuardar">
                                    <i class="bi bi-save me-1"></i>Agregar Producto
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
                <h3 class="text-center text-success">PRODUCTOS EN VENTAS</h3>

                <div class="table-responsive p-2">
                    <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableDetalleVentaProductos">
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= asset('build/js/detalle_venta_productos/index.js') ?>"></script>
