<?php

//Mostrar hora de entrada o de salida del ultimo usuario de la app
function obtenerUltimoUsuario($conn) {
    $sql = "SELECT t.nombre, r.timestamp, r.tipo FROM registro r 
            JOIN trabajador t ON r.idtrabajador = t.idtrabajador 
            ORDER BY r.timestamp DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


?>
