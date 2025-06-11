<div class="row justify-content-center p-3">
    <div class="col-lg-12">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                      <h3 class="text-center text-success">AGREGAR PRODUCTOS AL INVENTARIO</h3>
                </div>

                <div class="row justify-content-center p-5 shadow-lg">

                    <form id="FormInventario">
                        <input type="hidden" id="id_inventario" name="id_inventario">

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-4">
                                <label for="id_marca" class="form-label">MARCA <span class="text-danger">*</span></label>
                                <select class="form-select" id="id_marca" name="id_marca" required>
                                    <option value="">Seleccione una marca</option>
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <label for="id_modelo" class="form-label">MODELO <span class="text-danger">*</span></label>
                                <select class="form-select" id="id_modelo" name="id_modelo" required>
                                    <option value="">Primero seleccione un modelo</option>
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <label for="estado_producto" class="form-label">ESTADO <span class="text-danger">*</span></label>
                                <select class="form-select" id="estado_producto" name="estado_producto" required>
                                    <option value="">Seleccione estado</option>
                                    <option value="N">Nuevo</option>
                                    <option value="U">Usado</option>
                                    <option value="R">Reacondicionado</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-4">
                                <label for="codigo_producto" class="form-label">CÓDIGO DE PRODUCTO</label>
                                <input type="text" class="form-control" id="codigo_producto" name="codigo_producto" placeholder="Código único (opcional)">
                                <div class="form-text">Opcional - Código interno del producto</div>
                            </div>
                            <div class="col-lg-4">
                                <label for="imei" class="form-label">IMEI</label>
                                <input type="text" class="form-control" id="imei" name="imei" placeholder="Número IMEI (opcional)">
                                <div class="form-text">Opcional - Para identificación única</div>
                            </div>
                            <div class="col-lg-4">
                                <label for="stock_cantidad" class="form-label">CANTIDAD <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="stock_cantidad" name="stock_cantidad" placeholder="1" min="1" required>
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-4">
                                <label for="precio_compra" class="form-label">PRECIO DE COMPRA <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="precio_compra" name="precio_compra" placeholder="0.00" min="0" step="0.01" required>
                            </div>
                            <div class="col-lg-4">
                                <label for="precio_venta" class="form-label">PRECIO DE VENTA <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="precio_venta" name="precio_venta" placeholder="0.00" min="0.01" step="0.01" required>
                            </div>
                            <div class="col-lg-4">
                                <label for="ubicacion" class="form-label">UBICACIÓN</label>
                                <input type="text" class="form-control" id="ubicacion" name="ubicacion" placeholder="Ej: Estante A-1 (opcional)">
                                <div class="form-text">Opcional - Ubicación física</div>
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-12">
                                <div class="alert alert-info" role="alert">
                                    <i class="bi bi-info-circle me-1"></i>
                                    <strong>Nota:</strong> Los campos marcados con <span class="text-danger">*</span> son obligatorios. El precio de venta debería ser mayor al precio de compra.
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-center mt-5">
                            <div class="col-auto">
                                <button class="btn btn-success" type="submit" id="BtnGuardar">
                                    <i class="bi bi-save me-1"></i>Agregar al Inventario
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

<div class="row justify-content-center p-3">
    <div class="col-lg-12">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <h3 class="text-center text-success">INVENTARIO DE PRODUCTOS</h3>

                <div class="table-responsive p-2">
                    <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableInventario">
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="<?= asset('build/js/inventario/index.js') ?>"></script>