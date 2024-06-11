<?php
session_start();
include("includes/conexion.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajax'])) {
    if (isset($_POST['dni']) && isset($_POST['password'])) {
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

            //Mostrar el ultimo registro
            $sql = "SELECT tipo, timestamp FROM registro WHERE idtrabajador = ? ORDER BY timestamp DESC LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(1, $row['idtrabajador']);
            $stmt->execute();
            $ultimoRegistro = $stmt->fetch(PDO::FETCH_ASSOC);

            //Mostrar boton entrar si no existen registros previos o si el ultimo registro es "salida"
            $mostrarEntrar = true;
            if ($ultimoRegistro) {
                $mostrarEntrar = ($ultimoRegistro['tipo'] == 'salida') ? true : false;
            }

            //Mostrar horas trabajadas
            $horasTrabajadas = "No hay registro de entrada previo";
            if ($ultimoRegistro && $ultimoRegistro['tipo'] == 'entrada') {
                $entradaTime = new DateTime($ultimoRegistro['timestamp']);
                $salidaTime = new DateTime();
                $interval = $entradaTime->diff($salidaTime);
                $horasTrabajadas = $interval->format('%h horas y %i minutos');
            }

            echo json_encode(['success' => true, 'nombre' => $row['nombre'], 'mostrarEntrar' => $mostrarEntrar, 'ultimoRegistro' => $ultimoRegistro, 'horasTrabajadas' => $horasTrabajadas]);
        } else {
            echo json_encode(['success' => false, 'error' => 'DNI o password incorrectos']);
        }
        exit();

    } elseif (isset($_SESSION['dni']) && isset($_POST['action'])) {
        $idtrabajador = $_SESSION['idtrabajador'];
        $action = $_POST['action'];

        //Si se pulsa el boton ENTRAR
        if ($action == 'entrar') {
            $sql = "INSERT INTO registro (idtrabajador, tipo) VALUES (?, 'entrada')";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(1, $idtrabajador);
            if ($stmt->execute()) {
                session_destroy();
                echo json_encode(['success' => true, 'mensaje' => 'Hora de entrada registrada', 'action' => 'salir']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Error al registrar la hora de entrada']);
            }

        //Si se pulsa el boton SALIR
        } elseif ($action == 'salir') {
            $sql = "INSERT INTO registro (idtrabajador, tipo) VALUES (?, 'salida')";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(1, $idtrabajador);
            if ($stmt->execute()) {
                session_destroy();
                echo json_encode(['success' => true, 'mensaje' => 'Hora de salida registrada', 'action' => 'entrar']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Error al registrar la hora de salida']);
            }

        //Si se pulsa el boton X esquina superior derecha
        } elseif ($action == 'logout') {
            session_destroy();
            echo json_encode(['success' => true, 'mensaje' => 'Sesión cerrada']);
        }
        exit();
    }
}