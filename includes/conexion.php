<?php
// Definición de los parámetros de conexión a la base de datos
$host = "localhost";        // Host de la base de datos
$usuario_db = "root";       // Nombre de usuario de la base de datos
$contrasena_db = "";        // Contraseña de la base de datos
$nombre_db = "sistemas";    // Nombre de la base de datos a la que se va a conectar

// Creación de una nueva instancia de la clase mysqli para establecer la conexión
$conexion = new mysqli($host, $usuario_db, $contrasena_db, $nombre_db);

// Verificar si ocurrió algún error durante la conexión
if ($conexion->connect_error) {
    // Si hubo un error, se imprime un mensaje de error y se termina la ejecución del script
    die("Error de conexión: " . $conexion->connect_error);
}
?>
