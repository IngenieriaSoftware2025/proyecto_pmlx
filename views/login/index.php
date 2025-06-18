<div class="row justify-content-center">
  <form class="col-lg-4 border rounded shadow p-4 bg-light" id="FormLogin" >
    <h3 class="text-center mb-4"><b>INICIO DE SESION</b></h3>
    <div class="text-center mb-4">
      <img src="<?= asset('./images/login.jpg') ?>" alt="Logo" width="200px" class="img-fluid rounded-circle">
    </div>
    <div class="row mb-3">
      <div class="col">
        <label for="nombre_usuario" class="form-label">Nombre de Usuario</label>
        <input type="text" name="nombre_usuario" id="nombre_usuario" class="form-control" placeholder="Ingresa tu nombre de usuario" required>
      </div>
    </div>
    <div class="row mb-3">
      <div class="col">
        <label for="password" class="form-label">Contraseña</label>
        <input type="password" name="password" id="password" class="form-control" placeholder="Ingresa tu contraseña" required>
      </div>
    </div>
    <div class="row">
      <div class="col">
        <button type="submit" class="btn btn-primary w-100 btn-lg" id="BtnIniciar" >
          Iniciar sesión
        </button>
      </div>
    </div>
  </form>
</div>

<script src="<?= asset('build/js/login/login.js') ?>"></script>