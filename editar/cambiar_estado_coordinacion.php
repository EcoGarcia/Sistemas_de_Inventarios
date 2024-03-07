<?php
session_start();

include('../includes/conexion.php');

if (!isset($_SESSION['tipo_usuario'])) {
    header('Location: ../index.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $estado = $_POST['estado'];

    // Realiza la lógica para cambiar el estado en la base de datos
    // Aquí asumimos que tienes una tabla llamada 'resguardos_admin' con una columna 'Estado'

    $nuevoEstado = ($estado == 1) ? 0 : 1;

    $sqlUpdate = "UPDATE respaldos_coordinacion SET Estado = $nuevoEstado WHERE id = $id";

    if ($conexion->query($sqlUpdate) === TRUE) {
        echo "Estado cambiado correctamente";
    } else {
        echo "Error al cambiar el estado: " . $conexion->error;
    }
}

$conexion->close();
?>
