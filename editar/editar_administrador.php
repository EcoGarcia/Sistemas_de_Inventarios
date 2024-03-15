<?php
// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si el ID del usuario, el nuevo nombre y la nueva contraseña están presentes
    if (isset($_POST["userId"]) && isset($_POST["newUsername"]) && isset($_POST["newPassword"])) {
        // Sanitizar y obtener los datos del formulario
        $userId = htmlspecialchars($_POST["userId"]);
        $newUsername = htmlspecialchars($_POST["newUsername"]);
        $newPassword = htmlspecialchars($_POST["newPassword"]);

        // Encriptar la contraseña
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Realizar la actualización en la base de datos
        include("../includes/conexion.php");

        // Consulta para actualizar el nombre de usuario y la contraseña en la tabla administrador
        $updateQuery_admin = "UPDATE administrador SET Fullname = '$newUsername', Password = '$hashedPassword' WHERE id = $userId";

        // Consulta para actualizar el nombre de usuario y la contraseña en la tabla coordinacion_de_recursos
        $updateQuery_coordinacion = "UPDATE coordinación_de_recursos SET Fullname = '$newUsername', Password = '$hashedPassword' WHERE id = $userId";

        // Consulta para actualizar el nombre de usuario en las tablas relacionadas
        $updateRelatedTablesQuery = "UPDATE resguardos_direccion SET Coordinadora_Recursos = '$newUsername' WHERE Coordinadora_Recursos = (SELECT Fullname FROM administrador WHERE id = $userId);
                                     UPDATE respaldos_coordinacion SET Coordinadora_Recursos = '$newUsername' WHERE Coordinadora_Recursos = (SELECT Fullname FROM administrador WHERE id = $userId);
                                     UPDATE respaldos_servicios SET Coordinadora_Recursos = '$newUsername' WHERE Coordinadora_Recursos = (SELECT Fullname FROM administrador WHERE id = $userId);";

        // Ejecutar las consultas
        if (mysqli_query($conexion, $updateQuery_admin) && mysqli_query($conexion, $updateQuery_coordinacion) && mysqli_multi_query($conexion, $updateRelatedTablesQuery)) {
            // Éxito en la actualización
            $notification_message = "Cambio exitoso";
            echo "<script>
                alert('$notification_message');
                window.location.href = '../tabla/tabla_usuarios_administracion.php';
            </script>";
        } else {
            // Error en la actualización
            echo "Error al actualizar el nombre de usuario y contraseña: " . mysqli_error($conexion);
        }

        // Cerrar la conexión
        mysqli_close($conexion);
    } else {
        // Si no se proporcionan los datos esperados
        echo "Error: Datos insuficientes para actualizar el nombre de usuario y contraseña.";
    }
} else {
    // Si no se accede mediante un formulario POST
    echo "Acceso no válido a esta página.";
}
?>
