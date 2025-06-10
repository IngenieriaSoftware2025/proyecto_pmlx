<div class="row justify-content-center p-3">
    <div class="col-lg-10">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                      <h3 class="text-center text-success">REGISTRAR USUARIOS</h3>
                </div>

                <div class="row justify-content-center p-5 shadow-lg">

                    <form id="FormUsuarios">
                        <input type="hidden" id="id_usuario" name="id_usuario">

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="nombre_usuario" class="form-label">NOMBRE DE USUARIO</label>
                                <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" placeholder="Usuario de acceso" required>
                            </div>
                            <div class="col-lg-6">
                                <label for="password" class="form-label">CONTRASEÑA</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required>
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-8">
                                <label for="nombre_completo" class="form-label">NOMBRE COMPLETO</label>
                                <input type="text" class="form-control" id="nombre_completo" name="nombre_completo" placeholder="Nombre completo del usuario" required>
                            </div>
                            <div class="col-lg-4">
                                <label for="id_rol" class="form-label">ROL</label>
                                <select class="form-select" id="id_rol" name="id_rol" required>
                                    <option value="">Seleccione un rol</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="email" class="form-label">EMAIL</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="correo@ejemplo.com">
                            </div>
                            <div class="col-lg-6">
                                <label for="telefono" class="form-label">TELÉFONO</label>
                                <input type="text" class="form-control" id="telefono" name="telefono" placeholder="1234-5678">
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
                <h3 class="text-center text-success">USUARIOS REGISTRADOS</h3>

                <div class="table-responsive p-2">
                    <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableUsuarios">
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="<?= asset('build/js/usuarios/index.js') ?>"></script>