<div class="row justify-content-center p-3">
    <div class="col-lg-12">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                    <h3 class="text-center text-success">REGISTRAR MOVIMIENTO DE INVENTARIO</h3>
                </div>

                <div class="row justify-content-center p-5 shadow-lg">
                    <form id="FormMovimientosInventario">
                        <input type="hidden" id="id_movimiento" name="id_movimiento">

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="id_inventario" class="form-label">PRODUCTO <span class="text-danger">*</span></label>
                                <select class="form-select" id="id_inventario" name="id_inventario" required>
                                    <option value="">Seleccione un producto</option>
                                </select>
                                <div class="form-text">Seleccione el producto para el movimiento</div>
                            </div>
                            <div class="col-lg-6">
                                <label for="tipo_movimiento" class="form-label">TIPO DE MOVIMIENTO <span class="text-danger">*</span></label>
                                <select class="form-select" id="tipo_movimiento" name="tipo_movimiento" required>
                                    <option value="">Seleccione el tipo</option>
                                    <option value="E">Entrada (+)</option>
                                    <option value="S">Salida (-)</option>
                                    <option value="A">Ajuste (±)</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-3">
                                <label for="stock_actual" class="form-label">STOCK ACTUAL</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-box"></i></span>
                                    <input type="text" class="form-control" id="stock_actual" readonly placeholder="Seleccione producto">
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
                                <label for="stock_proyectado" class="form-label">STOCK PROYECTADO</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-calculator"></i></span>
                                    <input type="text" class="form-control fw-bold" id="stock_proyectado" readonly placeholder="0">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <label for="usuario_movimiento" class="form-label">USUARIO <span class="text-danger">*</span></label>
                                <select class="form-select" id="usuario_movimiento" name="usuario_movimiento" required>
                                    <option value="">Seleccione usuario</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="motivo" class="form-label">MOTIVO <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="motivo" name="motivo" placeholder="Descripción del motivo del movimiento" required>
                                <div class="form-text">Ej: Compra nueva, Venta, Daño, Inventario físico, etc.</div>
                            </div>
                            <div class="col-lg-6">
                                <label for="referencia_documento" class="form-label">REFERENCIA/DOCUMENTO</label>
                                <input type="text" class="form-control" id="referencia_documento" name="referencia_documento" placeholder="Número de factura, orden, etc.">
                                <div class="form-text">Opcional - Referencia del documento relacionado</div>
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-12">
                                <label for="observaciones" class="form-label">OBSERVACIONES</label>
                                <textarea class="form-control" id="observaciones" name="observaciones" rows="2" placeholder="Observaciones adicionales (opcional)"></textarea>
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-12">
                                <label class="form-label">INFORMACIÓN DEL PRODUCTO</label>
                                <div class="border rounded p-3 bg-light">
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
                                    <strong>Tipos de Movimiento:</strong><br>
                                    <strong>Entrada (+):</strong> Aumenta el stock (compras, devoluciones)<br>
                                    <strong>Salida (-):</strong> Reduce el stock (ventas, daños, pérdidas)<br>
                                    <strong>Ajuste (±):</strong> Corrección de inventario (conteo físico)
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-center mt-4">
                            <div class="col-auto">
                                <button class="btn btn-success" type="submit" id="BtnGuardar">
                                    <i class="bi bi-save me-1"></i>Registrar Movimiento
                                </button>
                            </div>

                            <div class="col-auto">
                                <button class="btn btn-warning d-none" type="button" id="BtnModificar">
                                    <i class="bi bi-pencil-square me-1"></i>Modificar Movimiento
                                </button>
                            </div>

                            <div class="col-auto">
                                <button class="btn btn-secondary" type="reset" id="BtnLimpiar">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Limpiar
                                </button>
                            </div>

                            <div class="col-auto">
                                <button class="btn btn-info" type="button" id="BtnVerResumen">
                                    <i class="bi bi-bar-chart me-1"></i>Ver Resumen
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
                <h3 class="text-center text-success">HISTORIAL DE MOVIMIENTOS</h3>

                <!-- Filtros -->
                <div class="row mb-3">
                    <div class="col-lg-4">
                        <label for="filtro_producto" class="form-label">FILTRAR POR PRODUCTO</label>
                        <select class="form-select" id="filtro_producto">
                            <option value="">Todos los productos</option>
                        </select>
                    </div>
                    <div class="col-lg-4">
                        <label for="filtro_tipo" class="form-label">FILTRAR POR TIPO</label>
                        <select class="form-select" id="filtro_tipo">
                            <option value="">Todos los tipos</option>
                            <option value="E">Entradas</option>
                            <option value="S">Salidas</option>
                            <option value="A">Ajustes</option>
                        </select>
                    </div>
                    <div class="col-lg-4 d-flex align-items-end">
                        <button class="btn btn-primary me-2" id="BtnFiltrar">
                            <i class="bi bi-funnel me-1"></i>Filtrar
                        </button>
                        <button class="btn btn-outline-secondary" id="BtnLimpiarFiltros">
                            <i class="bi bi-x-circle me-1"></i>Limpiar Filtros
                        </button>
                    </div>
                </div>

                <div class="table-responsive p-2">
                    <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableMovimientosInventario">
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Resumen -->
<div class="modal fade" id="ModalResumen" tabindex="-1" aria-labelledby="ModalResumenLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalResumenLabel">
                    <i class="bi bi-bar-chart me-2"></i>Resumen de Movimientos por Producto
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="contenido_resumen">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2">Cargando resumen...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= asset('build/js/movimientos_inventario/index.js') ?>"></script>
