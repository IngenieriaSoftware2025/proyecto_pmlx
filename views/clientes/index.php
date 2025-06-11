<div class="row justify-content-center p-3">
    <div class="col-lg-10">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                      <h3 class="text-center text-success">REGISTRAR CLIENTES</h3>
                </div>

                <div class="row justify-content-center p-5 shadow-lg">

                    <form id="FormClientes">
                        <input type="hidden" id="id_cliente" name="id_cliente">

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-8">
                                <label for="nombre" class="form-label">NOMBRE COMPLETO <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre completo del cliente" required>
                            </div>
                            <div class="col-lg-4">
                                <label for="nit" class="form-label">NIT</label>
                                <input type="text" class="form-control" id="nit" name="nit" placeholder="Número de NIT (opcional)">
                                <div class="form-text">Opcional - Solo si es necesario para facturación</div>
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="telefono" class="form-label">TELÉFONO</label>
                                <input type="text" class="form-control" id="telefono" name="telefono" placeholder="1234-5678">
                            </div>
                            <div class="col-lg-6">
                                <label for="celular" class="form-label">CELULAR</label>
                                <input type="text" class="form-control" id="celular" name="celular" placeholder="5555-5555">
                            </div>
                        </div>
                        
                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-12">
                                <div class="alert alert-info" role="alert">
                                    <i class="bi bi-info-circle me-1"></i>
                                    <strong>Nota:</strong> Debe proporcionar al menos un número telefónico (teléfono o celular)
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="email" class="form-label">EMAIL</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="correo@ejemplo.com">
                                <div class="form-text">Opcional</div>
                            </div>
                            <div class="col-lg-6">
                                <label for="direccion" class="form-label">DIRECCIÓN</label>
                                <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Dirección completa">
                                <div class="form-text">Opcional</div>
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
                <h3 class="text-center text-success">CLIENTES REGISTRADOS</h3>

                <div class="table-responsive p-2">
                    <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableClientes">
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="<?= asset('build/js/clientes/index.js') ?>"></script>
