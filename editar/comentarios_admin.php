<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["userId"]) && isset($_POST["newUsername"]) && isset($_POST["identificadorCoordinacion"])) {
        $userId = htmlspecialchars($_POST["userId"]);
        $newUsername = htmlspecialchars($_POST["newUsername"]);
        $identificadorCoordinacion = htmlspecialchars($_POST["identificadorCoordinacion"]);

        include("../includes/conexion.php");

        $updateQuery = "UPDATE resguardos_admin SET comentarios = '$newUsername' WHERE id = $userId AND identificador_coordinacion = $identificadorCoordinacion";

        if (mysqli_query($conexion, $updateQuery)) {
            $notification_message = "Cambio exitoso";
            echo "<script>
                alert('$notification_message');
                window.location.href = '../inventario/inventarios_admin.php?identificador_coordinacion=$identificadorCoordinacion';
            </script>";
        } else {
            echo "Error al actualizar el nombre de usuario: " . mysqli_error($conexion);
        }

        mysqli_close($conexion);
    } else {
        echo "Error: Datos insuficientes para actualizar el nombre de usuario.";
    }
} else {
    echo "Acceso no válido a esta página.";
}
?>
