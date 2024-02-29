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
    $direccionId = $_POST["fullname_direccion"];
    $fullname_coordinacion = $_POST["coordinacion_existente"];
    $fullname_servicio = $_POST["servicio_nuevo"];

    // Obtener el nombre de la dirección seleccionada
    $sql_select_direccion = "SELECT Fullname FROM direccion WHERE identificador = '$direccionId'";
    $result_select_direccion = $conexion->query($sql_select_direccion);

    // Verificar si se encontró el nombre de la dirección seleccionada
    if ($result_select_direccion->num_rows > 0) {
        $row_select_direccion = $result_select_direccion->fetch_assoc();
        $direccion = $row_select_direccion["Fullname"];
    } else {
        echo "Error: No se encontró el nombre de la dirección seleccionada.";
        exit();
    }

    // Obtener el identificador correspondiente a la coordinación seleccionada
    $sql_select_coordinacion = "SELECT identificador_coordinacion FROM coordinacion WHERE Fullname_coordinacion = '$fullname_coordinacion' AND identificador_direccion = '$direccionId'";
    $result_select_coordinacion = $conexion->query($sql_select_coordinacion);

    // Verificar si se encontró el identificador para la coordinación seleccionada en la dirección
    if ($result_select_coordinacion->num_rows > 0) {
        $row_select_coordinacion = $result_select_coordinacion->fetch_assoc();
        $identificador_coordinacion = $row_select_coordinacion["identificador_coordinacion"];
    } else {
        echo "Error: No se encontró el identificador para la coordinación seleccionada en la dirección.";
        exit();
    }

    // Obtener el último identificador de servicio para la dirección y coordinación específicas
    $sql_max_servicio = "SELECT MAX(identificador_servicio) AS max_id FROM servicios WHERE identificador_direccion = '$direccionId' AND identificador_coordinacion = '$identificador_coordinacion'";
    $result_max_servicio = $conexion->query($sql_max_servicio);

    // Verificar si se obtuvo el último identificador de servicio
    if ($result_max_servicio->num_rows > 0) {
        $row_max_servicio = $result_max_servicio->fetch_assoc();
        $last_id_servicio = $row_max_servicio["max_id"];

        // Verificar si ya hay servicios para la dirección y coordinación específicas
        if (!is_null($last_id_servicio)) {
            // Si ya hay servicios, incrementar el contador
            $identificador_servicio = $last_id_servicio + 1;
        } else {
            // Si no hay servicios, comenzar desde 1
            $identificador_servicio = 1;
        }
    }

    // Verificar si el servicio ya existe para la dirección, coordinación y nombre de servicio especificados
    $sql_check_servicio = "SELECT id FROM servicios WHERE identificador_direccion = '$direccionId' AND identificador_coordinacion = '$identificador_coordinacion' AND Fullname_servicio = '$fullname_servicio'";
    $result_check_servicio = $conexion->query($sql_check_servicio);

    // Verificar si ya existe un servicio con esos datos
    if ($result_check_servicio->num_rows > 0) {
        // Si ya existe, mostrar mensaje de notificación y redireccionar
        echo "<script>
        var mensaje_notificacion = 'El servicio \'$fullname_servicio\' ya fue registrado anteriormente para la dirección \'$direccion\' y la coordinación \'$fullname_coordinacion\'. Revise si esta bien escrito o intente con uno nuevo.';
        alert(mensaje_notificacion);
        window.location.href = '../dashboard/dashboard.php';
    </script>";
        exit(); // Salir del script para evitar la inserción duplicada
    }

    // Resto del código sin cambios
    // ...

    // Procesar la imagen
    $image_name = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $image_path = "../imagenes/" . $image_name;

    // Insertar datos en la tabla "servicios"
    $sql_insert_servicio = "INSERT INTO servicios (id, identificador_direccion, identificador_coordinacion, identificador_servicio, Fullname_servicio, Fullname_direccion, Fullname_coordinacion, image_path) VALUES (NULL, '$direccionId', '$identificador_coordinacion', '$identificador_servicio', '$fullname_servicio', '$direccion', '$fullname_coordinacion', '$image_path')";

    // Ejecutar la consulta de inserción
    $result_insert_servicio = $conexion->query($sql_insert_servicio);

    // Verificar el resultado de la inserción
    if ($result_insert_servicio) {
        // Mensaje de notificación y redirección a la página de servicios
        $notification_message = "Servicio insertado correctamente. Nombre del servicio: " . $fullname_servicio;
        echo "<script>
            alert('$notification_message');
            window.location.href = '../dashboard/dashboard.php';
        </script>";
    } else {
        // Mostrar un mensaje de error si hay un problema con la inserción
        echo "Error al agregar el servicio: " . $conexion->error;
    }
}
?>
