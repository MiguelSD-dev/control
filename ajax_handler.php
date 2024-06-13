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

            // Obtener todos los registros del trabajador para el día actual
            $sql = "SELECT tipo, timestamp FROM registro WHERE idtrabajador = ? AND DATE(timestamp) = CURDATE() ORDER BY timestamp";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(1, $row['idtrabajador']);
            $stmt->execute();
            $registrosDia = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Mostrar botón entrar si no existen registros previos o si el último registro es "salida"
            $mostrarEntrar = true;
            if (!empty($registrosDia)) {
                $ultimoRegistro = end($registrosDia);
                $mostrarEntrar = ($ultimoRegistro['tipo'] == 'salida') ? true : false;
            }

            // Calcular el total de horas trabajadas en el día actual
            $horasTrabajadas = 0;
            $entradaTime = null;
            $horaActual = new DateTime();

            foreach ($registrosDia as $registro) {
                if ($registro['tipo'] == 'entrada') {
                    $entradaTime = new DateTime($registro['timestamp']);
                } elseif ($registro['tipo'] == 'salida' && $entradaTime) {
                    $salidaTime = new DateTime($registro['timestamp']);
                    $interval = $entradaTime->diff($salidaTime);
                    $horasTrabajadas += $interval->h + ($interval->i / 60);
                    $entradaTime = null;  // Reinicia entradaTime después de calcular el intervalo
                }
            }
            
            // Si hay una entrada sin salida, calcula el tiempo trabajado hasta la hora actual
            if ($entradaTime) {
                $interval = $entradaTime->diff($horaActual);
                $horasTrabajadas += $interval->h + ($interval->i / 60);
            }

            // Convertir horasTrabajadas a formato 'X horas y Y minutos'
            $horas = floor($horasTrabajadas);
            $minutos = round(($horasTrabajadas - $horas) * 60);
            $horasTrabajadasFormatted = "$horas horas y $minutos minutos";

            echo json_encode(['success' => true, 'nombre' => $row['nombre'], 'mostrarEntrar' => $mostrarEntrar, 'ultimoRegistro' => $ultimoRegistro ?? null, 'horasTrabajadas' => $horasTrabajadasFormatted]);
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
