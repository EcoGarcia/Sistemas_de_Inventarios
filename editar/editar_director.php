<?php
// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si el ID del usuario y el nuevo nombre están presentes
    if (isset($_POST["userId"]) && isset($_POST["newUsername"]) && isset($_POST["newPassword"])) {
        // Sanitizar y obtener los datos del formulario
        $userId = htmlspecialchars($_POST["userId"]);
        $newUsername = htmlspecialchars($_POST["newUsername"]);
        $newPassword = htmlspecialchars($_POST["newPassword"]);

        // Realizar la actualización en la base de datos
        include("../includes/conexion.php");

        // Consulta para actualizar el nombre y la contraseña del usuario en la tabla director_area
        $updateQueryDirector = "UPDATE director_area SET Fullname = '$newUsername', Password = '$newPassword' WHERE id = $userId";
        // Consulta para actualizar solo el nombre del usuario en la tabla resguardos_direccion
        $updateQueryResguardos = "UPDATE resguardos_direccion SET Encargada_Area = '$newUsername' WHERE id = $userId";
        $updateQueryResguardos = "UPDATE respaldos_coordinacion SET Encargada_Area = '$newUsername' WHERE id = $userId";
        $updateQueryResguardos = "UPDATE respaldos_servicios SET Encargada_Area = '$newUsername' WHERE id = $userId";

        // Ejecutar ambas consultas de actualización
        if (mysqli_query($conexion, $updateQueryDirector) && mysqli_query($conexion, $updateQueryResguardos)) {
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
