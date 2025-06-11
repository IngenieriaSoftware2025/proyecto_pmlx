<div class="row justify-content-center p-3">
    <div class="col-lg-10">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                      <h3 class="text-center text-success">REGISTRAR MODELOS DE CELULARES</h3>
                </div>

                <div class="row justify-content-center p-5 shadow-lg">

                    <form id="FormModelos">
                        <input type="hidden" id="id_modelo" name="id_modelo">

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="id_marca" class="form-label">MARCA</label>
                                <select class="form-select" id="id_marca" name="id_marca" required>
                                    <option value="">Seleccione una marca</option>
                                </select>
                            </div>
                            <div class="col-lg-6">
                                <label for="nombre_modelo" class="form-label">NOMBRE DEL MODELO</label>
                                <input type="text" class="form-control" id="nombre_modelo" name="nombre_modelo" placeholder="Ej: Galaxy S24, iPhone 15 Pro..." required>
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-8">
                                <label for="especificaciones" class="form-label">ESPECIFICACIONES</label>
                                <textarea class="form-control" id="especificaciones" name="especificaciones" rows="3" placeholder="Especificaciones técnicas del modelo (opcional)"></textarea>
                                <div class="form-text">Incluya información como pantalla, cámara, memoria, etc.</div>
                            </div>
                            <div class="col-lg-4">
                                <label for="precio_referencia" class="form-label">PRECIO DE REFERENCIA</label>
                                <input type="number" class="form-control" id="precio_referencia" name="precio_referencia" placeholder="0.00" min="0" step="0.01">
                                <div class="form-text">Precio opcional de referencia</div>
                            </div>
                        </div>

                        <div class="row justify-content-center mt-5">
                            <div class="col-auto">
                                <button class="btn btn-success" type="submit" id="BtnGuardar">
                                    <i class="bi bi-save me-1"></i>Guardar
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
                <h3 class="text-center text-success">MODELOS REGISTRADOS</h3>

                <div class="table-responsive p-2">
                    <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableModelos">
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="<?= asset('build/js/modelos/index.js') ?>"></script>
