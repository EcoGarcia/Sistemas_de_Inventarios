<?php
session_start();

if (!isset($_SESSION['tipo_usuario'])) {
    header('Location: index.php');
    exit();
}

include('../includes/conexion.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve data from the form
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Encripta la contraseña
    
    // Obtener valores de la tabla direccion
    $query_direccion = "SELECT Fullname, identificador FROM direccion WHERE identificador";
    $result_direccion = $conexion->query($query_direccion);

    // Obtener valores de la tabla coordinacion
    $query_coordinacion = "SELECT Fullname_coordinacion, identificador_coordinacion FROM coordinacion WHERE identificador_coordinacion";
    $result_coordinacion = $conexion->query($query_coordinacion);

    // Obtener valores de la tabla servicios
    $query_servicios = "SELECT Fullname_servicio, identificador_servicio FROM servicios WHERE identificador_servicio";
    $result_servicios = $conexion->query($query_servicios);

    if ($result_direccion && $result_coordinacion && $result_servicios) {
        $row_direccion = $result_direccion->fetch_assoc();
        $fullname_direccion = $row_direccion['Fullname'];
        $identificador_direccion = $row_direccion['identificador'];

        $row_coordinacion = $result_coordinacion->fetch_assoc();
        $fullname_coordinacion = $row_coordinacion['Fullname_coordinacion'];
        $identificador_coordinacion = $row_coordinacion['identificador_coordinacion'];

        $row_servicios = $result_servicios->fetch_assoc();
        $fullname_servicio = $row_servicios['Fullname_servicio'];
        $identificador_servicio = $row_servicios['identificador_servicio'];

        $sql_max_user_id = "SELECT MAX(identificador_administrador_coordinacion) AS max_id FROM administrador";
        $result_max_user_id = $conexion->query($sql_max_user_id);
    
        if ($result_max_user_id->num_rows > 0) {
            $row_max_user_id = $result_max_user_id->fetch_assoc();
            $next_user_id = $row_max_user_id["max_id"] + 1;
        } else {
            $next_user_id = 1;
        }
    
        // Insertar en la tabla administrador
        $sql = "INSERT INTO administrador (identificador_administrador_coordinacion, Fullname, EmailId, password, Fullname_direccion, identificador_direccion, Fullname_coordinacion, identificador_coordinacion, Fullname_servicio, identificador_servicio, Puesto) 
                VALUES ('$next_user_id', '$nombre', '$email', '$password', '$fullname_direccion', '$identificador_direccion', '$fullname_coordinacion', '$identificador_coordinacion', '$fullname_servicio', '$identificador_servicio', 1)";

        if ($conexion->query($sql) === TRUE) {
            $notification_message = "Usuario registrado correctamente.";
            echo "<script>
                alert('$notification_message');
                window.location.href = '../dashboard/dashboard.php';
            </script>";
            } else {
            echo "Error al registrar usuario: " . $conexion->error;
        }
    } else {
        echo "Error al obtener datos necesarios: " . $conexion->error;
    }

    $conexion->close();
} else {
    // If someone tries to access this script directly without sending data by POST, redirect to the form.
    header('Location: tu_formulario.php');
    exit();
}
?>
