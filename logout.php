<?php
// Iniciar la sesión
session_start();

// Destruir la sesión actual (eliminar todos los datos de la sesión)
session_destroy();

// Redirigir a la página de inicio (index.php) después de cerrar la sesión
header('Location: index.php');

// Salir del script para evitar ejecución adicional
exit();
?>
