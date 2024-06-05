<?php
session_start();
if (!isset($_SESSION["dni"])) {
    header("Location: ./");
    exit();
}

$dni = $_SESSION["dni"];
$nombre = $_SESSION["nombre"];
$error = "";

// Conectar a la base de datos
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["entrar"])) {
        // Registrar la hora de entrada
        $sql = "INSERT INTO registro (dni, hora_entrada) VALUES (?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1, $dni);
        if ($stmt->execute()) {
            $mensaje = "Hora de entrada registrada";
        } else {
            $error = "Error al registrar la hora de entrada";
        }
    } elseif (isset($_POST["salir"])) {
        // Registrar la hora de salida
        $sql = "UPDATE registro SET hora_salida = NOW() WHERE dni = ? AND hora_salida IS NULL ORDER BY id DESC LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1, $dni);
        if ($stmt->execute()) {
            $mensaje = "Hora de salida registrada";
        } else {
            $error = "Error al registrar la hora de salida";
        }
    }
}
?>
<?php include("./templates/header.php"); ?>

<section class="vh-100" style="background-color: #3e3e3e;">
    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col col-xl-10">
                <div class="card" style="border-radius: 1rem;">
                    <div class="row g-0">
                        <div class="col-md-6 col-lg-5 d-none d-md-block">
                            <img src="assets/img/control.jpg" alt="login form" class="img-fluid" style="border-radius: 1rem 0 0 1rem;" />
                        </div>
                        <div class="col-md-6 col-lg-7 d-flex align-items-center">
                            <div class="card-body p-4 p-lg-5 text-black">

                                <div class="d-flex align-items-center mb-3 pb-1">
                                    <i class="fa-solid fa-eye fa-2x me-3"></i>
                                    <span class="h1 fw-bold mb-0">CONTROL</span>
                                </div>

                                <h5 class="fw-normal mb-3 pb-3" style="letter-spacing: 1px;">Bienvenido, <?php echo htmlspecialchars($nombre); ?>!</h5>

                                <form action="" method="post">
                                    <div class="pt-1 mb-4">
                                        <button data-mdb-button-init data-mdb-ripple-init class="btn btn-dark btn-lg btn-block" type="submit" name="entrar" style="background-color: #ee2c2d; border-width: 2px;">Entrar</button>
                                        <button data-mdb-button-init data-mdb-ripple-init class="btn btn-dark btn-lg btn-block" type="submit" name="salir" style="background-color: #ee2c2d; border-width: 2px;">Salir</button>
                                    </div>
                                    <?php
                                    if (!empty($mensaje)) {
                                        echo "<p>" . htmlspecialchars($mensaje) . "</p>";
                                    }
                                    if (!empty($error)) {
                                        echo "<p>" . htmlspecialchars($error) . "</p>";
                                    }
                                    ?>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include("./templates/footer.php"); ?>
