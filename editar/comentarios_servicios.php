<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["userId"]) && isset($_POST["newUsername"]) && isset($_POST["identificadorServicio"])) {
        $userId = htmlspecialchars($_POST["userId"]);
        $newUsername = htmlspecialchars($_POST["newUsername"]);
        $identificadorServicio = htmlspecialchars($_POST["identificadorServicio"]);

        include("../includes/conexion.php");

        $updateQuery = "UPDATE respaldos_servicios SET comentarios = '$newUsername' WHERE id = $userId AND identificador_servicio = $identificadorServicio";

        if (mysqli_query($conexion, $updateQuery)) {
            $notification_message = "Cambio exitoso";
            echo "<script>
            alert('$notification_message');
            window.location.href = '../inventario/inventario_servicios_admin.php?identificador_servicios=$identificadorServicio';
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
