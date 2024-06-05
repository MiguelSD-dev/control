<?php
session_start();
if (isset($_SESSION["user"])) {
    header("Location: user.php");
    exit();
}
if (isset($_POST["dni"])) {

    include("conexion.php");

    $dni = $_POST["dni"];
    $password = $_POST["password"];

    $sql = "select * from trabajador where dni=? and password=?";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(1, $dni);
    $stmt->bindParam(2, $password);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $_SESSION["dni"] = $dni;
        $_SESSION["nombre"] = $row["nombre"];
        header("Location: user.php");
        exit();
    } else {
        $error = "DNI o password incorrectos";
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

                                <form action="" method="post">

                                    <div class="d-flex align-items-center mb-3 pb-1">
                                        <i class="fa-solid fa-eye fa-2x me-3"></i>
                                        <span class="h1 fw-bold mb-0">CONTROL</span>
                                    </div>

                                    <h5 class="fw-normal mb-3 pb-3" style="letter-spacing: 1px;">Valida tus datos</h5>

                                    <div data-mdb-input-init class="form-outline mb-4">
                                        <input type="text" name="dni" id="dni" class="form-control form-control-lg" />
                                        <label class="form-label" for="dni">DNI</label>
                                    </div>

                                    <div data-mdb-input-init class="form-outline mb-4">
                                        <input type="password" name="password" id="password" class="form-control form-control-lg" />
                                        <label class="form-label" for="password">Password</label>
                                    </div>

                                    <div class="pt-1 mb-4">
                                        <button data-mdb-button-init data-mdb-ripple-init class="btn btn-dark btn-lg btn-block" type="submit" style="background-color: #ee2c2d; border-width: 2px;">Validar</button>
                                        <?php
                                        if (isset($error)) {
                                            echo "<p>" . $error . "</p>";
                                        }
                                        ?>
                                    </div>

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