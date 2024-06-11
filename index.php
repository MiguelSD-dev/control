<?php
session_start();
include("includes/conexion.php");
include("includes/functions.php");

$error = "";
$mensaje = "";
$ultimoUsuario = obtenerUltimoUsuario($conn);

include("includes/header.php"); ?>

<section class="vh-100">
    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col col-xl-10">
                <div class="card position-relative">
                    <button id="logout-button" class="btn btn-danger position-absolute top-0 end-0 m-2" title="Salir" style="display: none;">X</button>
                    <div class="row g-0">
                        <div class="col-md-6 col-lg-5 d-none d-md-block">
                            <img src="assets/img/control.jpg" alt="login form" class="img-fluid" />
                        </div>
                        <div class="col-md-6 col-lg-7 d-flex align-items-center">
                            <div class="card-body p-4 p-lg-5 text-black">

                                <div id="login-form">
                                    <div class="d-flex align-items-center mb-3 pb-1">
                                        <i class="fa-solid fa-eye fa-2x me-3"></i>
                                        <span class="h1 fw-bold mb-0">CONTROL</span>
                                    </div>
                                    <h5 class="fw-normal mb-3 pb-3">Valida tus datos</h5>
                                    <div class="form-outline mb-4">
                                        <input type="text" name="dni" id="dni" class="form-control form-control-lg" />
                                        <label class="form-label" for="dni">DNI</label>
                                    </div>
                                    <div class="form-outline mb-4">
                                        <input type="password" name="password" id="password" class="form-control form-control-lg" />
                                        <label class="form-label" for="password">Password</label>
                                    </div>
                                    <div class="pt-1 mb-4">
                                        <button id="login-button" class="btn btn-dark btn-lg btn-block">Validar</button>
                                        <p id="login-error" class="error-message"></p>
                                    </div>
                                    <?php if ($ultimoUsuario): ?>
                                        <div class="last-user-info">
                                            <h6>Último registro:</h6>
                                            <p>Nombre: <?php echo htmlspecialchars($ultimoUsuario['nombre']); ?></p>
                                            <p>Hora de <?php echo htmlspecialchars($ultimoUsuario['tipo']); ?>: <?php echo htmlspecialchars($ultimoUsuario['timestamp']); ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div id="control-form" style="display: none;">
                                    <div class="d-flex align-items-center mb-3 pb-1">
                                        <i class="fa-solid fa-eye fa-2x me-3"></i>
                                        <span class="h1 fw-bold mb-0">CONTROL</span>
                                    </div>
                                    <h5 class="fw-normal mb-3 pb-3">Bienvenido, <span id="nombre-trabajador"></span>!</h5>
                                    <div class="pt-1 mb-4">
                                        <button id="entrar-button" class="btn btn-dark btn-lg btn-block">Entrar</button>
                                        <button id="salir-button" class="btn btn-dark btn-lg btn-block" style="display: none;">Salir</button>
                                        <p id="control-mensaje" class="success-message"></p>
                                        <p id="control-error" class="error-message"></p>
                                        <p id="horas-trabajadas" class="info-message" style="display: none;"></p>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="assets/js/main.js"></script>

<?php include("includes/footer.php"); ?>