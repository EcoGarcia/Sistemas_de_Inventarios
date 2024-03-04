<?php
// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si el ID del usuario y el nuevo nombre están presentes
    if (isset($_POST["userId"]) && isset($_POST["newUsername"])) {
        // Sanitizar y obtener los datos del formulario
        $userId = htmlspecialchars($_POST["userId"]);
        $newUsername = htmlspecialchars($_POST["newUsername"]);

        // Realizar la actualización en la base de datos
        include("../includes/conexion.php");

        // Consulta para actualizar el nombre del usuario
        $updateQuery = "UPDATE director_area SET Fullname = '$newUsername' WHERE id = $userId";

        if (mysqli_query($conexion, $updateQuery)) {
            // Éxito en la actualización
            $notification_message = "Cambio exitoso";
            echo "<script>
                alert('$notification_message');
                window.location.href = '../tabla/Tabla_director.php';
            </script>";
        } else {
            // Error en la actualización
            echo "Error al actualizar el nombre de usuario: " . mysqli_error($conexion);
        }

        // Cerrar la conexión
        mysqli_close($conexion);
    } else {
        // Si no se proporcionan los datos esperados
        echo "Error: Datos insuficientes para actualizar el nombre de usuario.";
    }
} else {
    // Si no se accede mediante un formulario POST
    echo "Acceso no válido a esta página.";
}
?>
