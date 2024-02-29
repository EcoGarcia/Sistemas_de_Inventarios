<?php
// Iniciar la sesión
session_start();

// Verificar si el tipo de usuario está definido en la sesión
if (!isset($_SESSION['tipo_usuario'])) {
    // Redirigir a la página de inicio si no hay tipo de usuario definido
    header('Location: index.php');
    exit();
}

// Obtener el tipo de usuario desde la sesión
$tipo_usuario = $_SESSION['tipo_usuario'];

// Incluir el archivo de conexión a la base de datos
include('../includes/conexion.php');

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $fullname_direccion = $_POST["fullname_direccion"];
    $coordinacion = $_POST["coordinacion"];

    // Obtener el identificador correspondiente al Fullname seleccionado
    $sql_select = "SELECT identificador FROM direccion WHERE Fullname = '$fullname_direccion'";
    $result_select = $conexion->query($sql_select);

    if ($result_select->num_rows > 0) {
        $row_select = $result_select->fetch_assoc();
        $identificador_direccion = $row_select["identificador"];

        // Verificar si ya existe una coordinación para esa dirección
        $sql_check = "SELECT COUNT(*) AS total FROM coordinacion WHERE identificador_direccion = '$identificador_direccion'";
        $result_check = $conexion->query($sql_check);

        if ($result_check->num_rows > 0) {
            $row_check = $result_check->fetch_assoc();
            $total_coordinaciones = $row_check["total"];

            // Obtener el último identificador de coordinación
            $sql_max = "SELECT MAX(identificador_coordinacion) AS max_id FROM coordinacion WHERE identificador_direccion = '$identificador_direccion'";
            $result_max = $conexion->query($sql_max);

            if ($result_max->num_rows > 0) {
                $row_max = $result_max->fetch_assoc();
                $last_id = $row_max["max_id"];

                // Asegurarse de que el identificador sea al menos 1
                $identificador_coordinacion = max(1, $last_id + 1);
            } else {
                // Si no hay identificadores anteriores, asignar identificador 1
                $identificador_coordinacion = 1;
            }

            // Procesar la imagen
            $image_name = $_FILES['image']['name'];
            $image_tmp = $_FILES['image']['tmp_name'];
            $image_path = "../imagenes/" . $image_name;

            // Verificar si ya existe una coordinación con el mismo nombre
            $sql_check_duplicate = "SELECT COUNT(*) AS duplicate_count FROM coordinacion WHERE Fullname_coordinacion = '$coordinacion'";
            $result_check_duplicate = $conexion->query($sql_check_duplicate);

            if ($result_check_duplicate->num_rows > 0) {
                $row_check_duplicate = $result_check_duplicate->fetch_assoc();
                $duplicate_count = $row_check_duplicate["duplicate_count"];

                if ($duplicate_count > 0) {
                    // Si ya existe una coordinación con el mismo nombre, mostrar mensaje y redirigir
                    echo "<script>
                        var mensaje_notificacion = 'La coordinación \"$coordinacion\" ya se encuentra agregado con anterioridad. Revise si esta bien escrito o intente con uno nuevo.';
                        alert(mensaje_notificacion);
                        window.location.href = '../dashboard/dashboard.php';
                    </script>";
                    exit(); // Salir del script para evitar la inserción duplicada
                }
            }

            // Insertar datos en la tabla "coordinacion"
            $sql_insert = "INSERT INTO coordinacion (identificador_coordinacion, Fullname_coordinacion, Fullname_direccion, identificador_direccion, image_path) VALUES ('$identificador_coordinacion', '$coordinacion', '$fullname_direccion', '$identificador_direccion', '$image_path')";

            // Ejecutar la consulta de inserción
            $result_insert = $conexion->query($sql_insert);

            if ($result_insert) {
                $notification_message = "Registro insertado correctamente. Nombre de la coordinación: " . $fullname_direccion;
                echo "<script>
                    alert('$notification_message');
                    window.location.href = '../dashboard/dashboard.php';
                </script>";
            } else {
                echo "Error al agregar la coordinación: " . $conexion->error;
            }
        } else {
            echo "Error al verificar coordinaciones.";
        }
    } else {
        echo "Error: No se encontró el identificador para la dirección seleccionada.";
    }
}
?>
