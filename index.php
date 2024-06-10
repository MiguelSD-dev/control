<?php
session_start();
include("conexion.php");

$error = "";
$mensaje = "";
$ultimoUsuario = null;

// Obtener los datos del último usuario que haya registrado su entrada
$sql = "SELECT t.nombre, r.timestamp, r.tipo FROM registro r 
        JOIN trabajador t ON r.idtrabajador = t.idtrabajador 
        ORDER BY r.timestamp DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->execute();
$ultimoUsuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Procesar solicitudes AJAX
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajax'])) {
    if (isset($_POST['dni']) && isset($_POST['password'])) {
        // Validación de usuario
        $dni = $_POST['dni'];
        $password = $_POST['password'];

        $sql = "SELECT * FROM trabajador WHERE dni = ? AND password = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1, $dni);
        $stmt->bindParam(2, $password);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $_SESSION['dni'] = $dni;
            $_SESSION['nombre'] = $row['nombre'];
            $_SESSION['idtrabajador'] = $row['idtrabajador'];

            // Obtener el último registro del trabajador para determinar qué botón mostrar
            $sql = "SELECT tipo FROM registro WHERE idtrabajador = ? ORDER BY timestamp DESC LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(1, $row['idtrabajador']);
            $stmt->execute();
            $ultimoRegistro = $stmt->fetch(PDO::FETCH_ASSOC);
            $mostrarEntrar = ($ultimoRegistro && $ultimoRegistro['tipo'] == 'salida') ? true : false;

            echo json_encode(['success' => true, 'nombre' => $row['nombre'], 'mostrarEntrar' => $mostrarEntrar]);
        } else {
            echo json_encode(['success' => false, 'error' => 'DNI o password incorrectos']);
        }
        exit();
        
    } elseif (isset($_SESSION['dni']) && isset($_POST['action'])) {
        $idtrabajador = $_SESSION['idtrabajador'];
        $action = $_POST['action'];

        if ($action == 'entrar') {
            // Registrar la hora de entrada
            $sql = "INSERT INTO registro (idtrabajador, tipo) VALUES (?, 'entrada')";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(1, $idtrabajador);
            if ($stmt->execute()) {
                session_destroy();
                echo json_encode(['success' => true, 'mensaje' => 'Hora de entrada registrada', 'action' => 'salir']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Error al registrar la hora de entrada']);
            }
        } elseif ($action == 'salir') {
            // Registrar la hora de salida
            $sql = "INSERT INTO registro (idtrabajador, tipo) VALUES (?, 'salida')";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(1, $idtrabajador);
            if ($stmt->execute()) {
                session_destroy();
                echo json_encode(['success' => true, 'mensaje' => 'Hora de salida registrada', 'action' => 'entrar']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Error al registrar la hora de salida']);
            }
        }
        exit();
    }
}
?>

<?php include("./templates/header.php"); ?>

<section class="vh-100">
    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col col-xl-10">
                <div class="card">
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

<?php include("./templates/footer.php"); ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#login-button').click(function() {
        var dni = $('#dni').val();
        var password = $('#password').val();
        $.ajax({
            type: 'POST',
            url: '',
            data: {dni: dni, password: password, ajax: true},
            success: function(response) {
                var data = JSON.parse(response);
                if (data.success) {
                    $('#login-form').hide();
                    $('#nombre-trabajador').text(data.nombre);
                    $('#control-form').show();
                    if (data.mostrarEntrar) {
                        $('#entrar-button').show();
                        $('#salir-button').hide();
                    } else {
                        $('#entrar-button').hide();
                        $('#salir-button').show();
                    }
                } else {
                    $('#login-error').text(data.error);
                }
            }
        });
    });

    $('#entrar-button').click(function() {
        $.ajax({
            type: 'POST',
            url: '',
            data: {action: 'entrar', ajax: true},
            success: function(response) {
                var data = JSON.parse(response);
                if (data.success) {
                    $('#entrar-button').hide();
                    $('#salir-button').show();
                    $('#control-mensaje').text(data.mensaje);
                    $('#control-error').text('');
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                } else {
                    $('#control-error').text(data.error);
                    $('#control-mensaje').text('');
                }
            }
        });
    });

    $('#salir-button').click(function() {
        $.ajax({
            type: 'POST',
            url: '',
            data: {action: 'salir', ajax: true},
            success: function(response) {
                var data = JSON.parse(response);
                if (data.success) {
                    $('#salir-button').hide();
                    $('#entrar-button').show();
                    $('#control-mensaje').text(data.mensaje);
                    $('#control-error').text('');
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                } else {
                    $('#control-error').text(data.error);
                    $('#control-mensaje').text('');
                }
            }
        });
    });
});
</script>
