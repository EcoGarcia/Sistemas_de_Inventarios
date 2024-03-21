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


        // Consulta para actualizar el nombre del usuario en respaldos_coordinacion
        $updateQuery1 = "UPDATE respaldos_coordinacion SET usuario_responsable = '$newUsername' WHERE usuario_responsable = (
                            SELECT Fullname FROM usuarios_coordinacion WHERE id = $userId
                          ) AND identificador_usuario_coordinacion = (
                            SELECT identificador_usuario_coordinacion FROM usuarios_coordinacion WHERE id = $userId
                          )";
        // Consulta para actualizar el nombre del usuario en usuarios_coordinacion
        $updateQuery2 = "UPDATE usuarios_coordinacion SET Fullname = '$newUsername' WHERE id = $userId";

        // Ejecutar ambas consultas dentro de una transacción para mantener la consistencia de los datos
        mysqli_begin_transaction($conexion);

        if (mysqli_query($conexion, $updateQuery1) && mysqli_query($conexion, $updateQuery2)) {
            // Éxito en la actualización
            // Éxito en la actualización
            mysqli_commit($conexion);
            $notification_message = "Cambio exitoso";
            echo "<script>
                alert('$notification_message');
                window.location.href = '../tabla/tabla_usuarios_coordinacion.php';
            </script>";
        } else {
            // Error en la actualización
            mysqli_rollback($conexion);
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
