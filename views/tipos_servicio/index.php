<div class="row justify-content-center p-3">
    <div class="col-lg-12">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                      <h3 class="text-center text-success">REGISTRAR TIPOS DE SERVICIO</h3>
                </div>

                <div class="row justify-content-center p-5 shadow-lg">

                    <form id="FormTiposServicio">
                        <input type="hidden" id="id_tipo_servicio" name="id_tipo_servicio">

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="nombre_servicio" class="form-label">NOMBRE DEL SERVICIO <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nombre_servicio" name="nombre_servicio" placeholder="Ej: Reparación de pantalla" required>
                            </div>
                            <div class="col-lg-6">
                                <label for="descripcion" class="form-label">DESCRIPCIÓN</label>
                                <input type="text" class="form-control" id="descripcion" name="descripcion" placeholder="Descripción del servicio (opcional)">
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-4">
                                <label for="precio_base" class="form-label">PRECIO BASE (Q.) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="precio_base" name="precio_base" placeholder="0.00" min="0.01" step="0.01" required>
                            </div>
                            <div class="col-lg-4">
                                <label for="tiempo_estimado_horas" class="form-label">TIEMPO ESTIMADO (HORAS) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="tiempo_estimado_horas" name="tiempo_estimado_horas" placeholder="1" min="1" required>
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-12">
                                <div class="alert alert-info" role="alert">
                                    <i class="bi bi-info-circle me-1"></i>
                                    <strong>Nota:</strong> Los campos marcados con <span class="text-danger">*</span> son obligatorios. El precio base puede ser ajustado por orden de servicio específica.
                                </div>
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
                <h3 class="text-center text-success">TIPOS DE SERVICIO REGISTRADOS</h3>

                <div class="table-responsive p-2">
                    <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableTiposServicio">
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="<?= asset('build/js/tipos_servicio/index.js') ?>"></script>