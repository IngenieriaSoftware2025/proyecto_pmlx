<div class="row justify-content-center p-3">
    <div class="col-lg-10">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                      <h3 class="text-center text-success">REGISTRAR MARCAS DE CELULARES</h3>
                </div>

                <div class="row justify-content-center p-5 shadow-lg">

                    <form id="FormMarcas">
                        <input type="hidden" id="id_marca" name="id_marca">

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-8">
                                <label for="nombre_marca" class="form-label">NOMBRE DE LA MARCA</label>
                                <input type="text" class="form-control" id="nombre_marca" name="nombre_marca" placeholder="Ej: Samsung, Apple, Huawei..." required>
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-8">
                                <label for="descripcion" class="form-label">DESCRIPCIÓN</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="3" placeholder="Descripción de la marca (opcional)"></textarea>
                                <div class="form-text">La descripción es opcional pero recomendada</div>
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
                <h3 class="text-center text-success">MARCAS REGISTRADAS</h3>

                <div class="table-responsive p-2">
                    <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableMarcas">
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="<?= asset('build/js/marcas/index.js') ?>"></script>