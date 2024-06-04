<?php
session_start();
if (isset($_SESSION["dni"])) {
    $dni = $_SESSION["dni"];
    $nombre = $_SESSION["nombre"];
    echo $dni . " " . $nombre;
} else {
    header("Location: ./");
}

?>
<?php include("./templates/header.php"); ?>

<section class="vh-100" style="background-color: #9A616D;">
    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col col-xl-10">
                <div class="card" style="border-radius: 1rem;">
                    <div class="row g-0">
                        <div class="col-md-6 col-lg-5 d-none d-md-block">
                            <img src="assets/img/img1.webp" alt="login form" class="img-fluid" style="border-radius: 1rem 0 0 1rem;" />
                        </div>
                        <div class="col-md-6 col-lg-7 d-flex align-items-center">
                            <div class="card-body p-4 p-lg-5 text-black">

                                <form action="" method="post">

                                    <div class="d-flex align-items-center mb-3 pb-1">
                                        <i class="fa-solid fa-eye fa-2x me-3" style="color: #ff6219;"></i>
                                        <span class="h1 fw-bold mb-0">CONTROL</span>
                                    </div>

                                    <h5 class="fw-normal mb-3 pb-3" style="letter-spacing: 1px;">Valida tus datos</h5>

                                    <div class="pt-1 mb-4">
                                        <button data-mdb-button-init data-mdb-ripple-init class="btn btn-dark btn-lg btn-block" type="submit">Validar</button>
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