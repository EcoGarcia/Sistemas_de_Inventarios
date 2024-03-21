<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["userId"]) && isset($_POST["newUsername"]) && isset($_POST["newPassword"])) {
        $userId = htmlspecialchars($_POST["userId"]);
        $newUsername = htmlspecialchars($_POST["newUsername"]);
        $newPassword = htmlspecialchars($_POST["newPassword"]);

        include("../includes/conexion.php");

        // Hash de la nueva contraseña
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Actualizar nombre de usuario y contraseña con hash
        $updateQuery = "UPDATE usuarios_coordinacion SET Fullname = '$newUsername', Password = '$hashedPassword' WHERE id = $userId";

        mysqli_begin_transaction($conexion);

        if (mysqli_query($conexion, $updateQuery)) {
            mysqli_commit($conexion);
            $notification_message = "Cambio exitoso";
            echo "<script>
                alert('$notification_message');
                window.location.href = '../tabla/tabla_usuarios_coordinacion.php';
            </script>";
        } else {
            mysqli_rollback($conexion);
            echo "Error al actualizar el nombre de usuario y contraseña: " . mysqli_error($conexion);
        }

        mysqli_close($conexion);
    } else {
        echo "Error: Datos insuficientes para actualizar el nombre de usuario y contraseña.";
    }
} else {
    echo "Acceso no válido a esta página.";
}
?>
