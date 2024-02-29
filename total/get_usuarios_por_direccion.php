<?php
// Archivo: get_usuarios_por_direccion.php

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

// Obtener el identificador de la dirección seleccionada
$selectedDireccion = $_GET['direccion'];

// Consulta SQL para obtener los usuarios de la dirección seleccionada
$sqlUsuarios = "SELECT Identificador_usuario_direccion, Fullname as Nombre_usuario FROM usuarios_direccion WHERE Identificador_direccion = '$selectedDireccion'";
$resultUsuarios = $conn->query($sqlUsuarios);

// Crear un array para almacenar los resultados
$usuarios = array();

if ($resultUsuarios->num_rows > 0) {
    while ($rowUsuario = $resultUsuarios->fetch_assoc()) {
        $usuarios[] = array(
            'Identificador_usuario_direccion' => $rowUsuario['Identificador_usuario_direccion'],
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
