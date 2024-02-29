<?php
// Archivo: get_usuarios_por_coordinacion.php

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sistemas";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener los identificadores de dirección y coordinación seleccionados
$selectedDireccion = $_GET['direccionId'];
$selectedCoordinacion = $_GET['coordinacionId'];

// Consulta SQL para obtener los usuarios de la dirección y coordinación seleccionadas
$sqlUsuarios = "SELECT identificador_administrador_coordinacion, Fullname as Nombre_usuario FROM administrador WHERE identificador_direccion = '$selectedDireccion' AND identificador_coordinacion = '$selectedCoordinacion'";
$resultUsuarios = $conn->query($sqlUsuarios);

// Crear un array para almacenar los resultados
$usuarios = array();

if ($resultUsuarios->num_rows > 0) {
    while ($rowUsuario = $resultUsuarios->fetch_assoc()) {
        $usuarios[] = array(
            'identificador_administrador_coordinacion' => $rowUsuario['identificador_administrador_coordinacion'],
            'Nombre_usuario' => $rowUsuario['Nombre_usuario']
        );
    }
}

// Cerrar la conexión
$conn->close();

// Devolver los resultados en formato JSON
header('Content-Type: application/json');
echo json_encode($usuarios);
?>
