<?php
// Definición de los parámetros de conexión a la base de datos
$servidor = "localhost";               // Host de la base de datos
$usuario = "root";                     // Nombre de usuario de la base de datos
$password = "";                         // Contraseña de la base de datos
$database = "sistema_de_inventarios";  // Nombre de la base de datos a la que se va a conectar

// Intentar establecer una conexión a la base de datos
$conexion = mysqli_connect($servidor, $usuario, $password, $database);

// Verificar si la conexión fue exitosa
if ($conexion) {
    // Si la conexión fue exitosa, se imprime un mensaje indicando que la conexión se realizó con éxito
    echo "Sí se conectó";
} else {
    // Si la conexión falló, se imprime un mensaje de error
    echo "Error";
}
?>
