<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["userId"]) && isset($_POST["newUsername"]) && isset($_POST["identificadorDireccion"])) {
        $userId = htmlspecialchars($_POST["userId"]);
        $newUsername = htmlspecialchars($_POST["newUsername"]);
        $identificadorDireccion = htmlspecialchars($_POST["identificadorDireccion"]);

        include("../includes/conexion.php");

        $updateQuery = "UPDATE resguardos_direccion SET comentarios = '$newUsername' WHERE id = $userId AND identificador_direccion = $identificadorDireccion";

        if (mysqli_query($conexion, $updateQuery)) {
            $notification_message = "Cambio exitoso";
            echo "<script>
                alert('$notification_message');
                window.location.href = '../inventario/inventarios_direccion_admin.php?identificador_direccion=$identificadorDireccion';
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
