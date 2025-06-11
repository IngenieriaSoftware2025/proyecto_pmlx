<div class="row justify-content-center p-3">
    <div class="col-lg-12">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                      <h3 class="text-center text-success">REGISTRAR TRABAJADORES</h3>
                </div>

                <div class="row justify-content-center p-5 shadow-lg">

                    <form id="FormTrabajadores">
                        <input type="hidden" id="id_trabajador" name="id_trabajador">

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="id_usuario" class="form-label">USUARIO <span class="text-danger">*</span></label>
                                <select class="form-select" id="id_usuario" name="id_usuario" required>
                                    <option value="">Seleccione un usuario</option>
                                </select>
                                <div class="form-text">Solo se muestran usuarios que no son trabajadores</div>
                            </div>
                            <div class="col-lg-6">
                                <label for="especialidad" class="form-label">ESPECIALIDAD <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="especialidad" name="especialidad" placeholder="Ej: Técnico en reparación de pantallas" required>
                                <div class="form-text">Área de especialización del trabajador</div>
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-12">
                                <div class="alert alert-info" role="alert">
                                    <i class="bi bi-info-circle me-1"></i>
                                    <strong>Nota:</strong> Los campos marcados con <span class="text-danger">*</span> son obligatorios. Una vez registrado como trabajador, el usuario podrá ser asignado a órdenes de servicio.
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
                <h3 class="text-center text-success">TRABAJADORES REGISTRADOS</h3>

                <div class="table-responsive p-2">
                    <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableTrabajadores">
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="<?= asset('build/js/trabajadores/index.js') ?>"></script>