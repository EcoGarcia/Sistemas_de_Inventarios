<?php
session_start();

if (!isset($_SESSION['tipo_usuario'])) {
    header('Location: index.php');
    exit();
}

$tipo_usuario = $_SESSION['tipo_usuario'];
include('../includes/conexion.php');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sistemas";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_BCRYPT); // Encriptar la contraseña
    $direccionId = $_POST["fullname_direccion"];
    $coordinacionId = $_POST["coordinacion_existente"];
    $servicioId = $_POST["servicios"];

    // Verificar si ya existe un registro con el mismo nombre
    $query_check_duplicate = "SELECT COUNT(*) AS user_count FROM usuarios_servicios WHERE EmailId = ? AND Identificador_servicio = ?";
    $stmt_check_duplicate = mysqli_prepare($conexion, $query_check_duplicate);
    mysqli_stmt_bind_param($stmt_check_duplicate, 'ss', $email, $servicioId);
    mysqli_stmt_execute($stmt_check_duplicate);
    $result_check_duplicate = mysqli_stmt_get_result($stmt_check_duplicate);
    $row_check_duplicate = mysqli_fetch_assoc($result_check_duplicate);

    // Verificar si ya existe un registro con el mismo nombre
    if ($row_check_duplicate['user_count'] > 0) {
        // Obtener el nombre del servicio existente
        $query_servicio_existente = "SELECT Fullname_servicio FROM servicios WHERE identificador_servicio = ?";
        $stmt_servicio_existente = mysqli_prepare($conexion, $query_servicio_existente);
        mysqli_stmt_bind_param($stmt_servicio_existente, 's', $servicioId);
        mysqli_stmt_execute($stmt_servicio_existente);
        $result_servicio_existente = mysqli_stmt_get_result($stmt_servicio_existente);
        $row_servicio_existente = mysqli_fetch_assoc($result_servicio_existente);
        $nombre_servicio_existente = $row_servicio_existente['Fullname_servicio'];

        echo "<script>
            var mensaje_notificacion = 'El usuario \'$nombre\' ya está registrado en el servicio \'$nombre_servicio_existente\'.';
            alert(mensaje_notificacion);
            window.location.href = '../dashboard/dashboard.php';
        </script>";
        exit(); // Detener la ejecución del script después de la redirección
    }

    // Resto del código para guardar el usuario
    $options = "";
    $coordinacionesPorDireccion = array();

    // Obtener el contador global
    $sql_max_user_id = "SELECT MAX(identificador_usuario_servicios) AS max_id FROM usuarios_servicios";
    $result_max_user_id = $conn->query($sql_max_user_id);

    if ($result_max_user_id->num_rows > 0) {
        $row_max_user_id = $result_max_user_id->fetch_assoc();
        $next_user_id = $row_max_user_id["max_id"] + 1;
    } else {
        // Si no hay usuarios aún, empezar desde 1
        $next_user_id = 1;
    }

    $sql_check_duplicate = "SELECT COUNT(*) AS count FROM usuarios_servicios WHERE Fullname_direccion = 'Dirección Administrativa'";
    $result_check_duplicate = $conn->query($sql_check_duplicate);

    if ($result_check_duplicate->num_rows > 0) {
        $row_check_duplicate = $result_check_duplicate->fetch_assoc();
        $count_duplicate = $row_check_duplicate["count"];

        if ($count_duplicate > 0) {
            // Handle the duplicate entry, for example, by showing an error message or updating the existing record.
            echo "Error: Duplicate entry 'Dirección Administrativa' already exists.";
            // Additional code to handle the situation, if needed.
            exit(); // Stop further processing to prevent insertion of a duplicate record.
        }
    }

    // Obtener Fullname_direccion e identificador_direccion de la tabla Direccion
    $sql_direccion = "SELECT Fullname, identificador FROM direccion WHERE identificador = '$direccionId'";
    $result_direccion = $conn->query($sql_direccion);

    if ($result_direccion !== false && $result_direccion->num_rows > 0) {
        $row_direccion = $result_direccion->fetch_assoc();
        $fullname_direccion = $row_direccion["Fullname"];
        $identificador_direccion = $row_direccion["identificador"];
    } else {
        // Trata el caso en el que no se encuentra la dirección
        $fullname_direccion = "";
        $identificador_direccion = "";
    }

    // Obtener el nombre de la coordinación seleccionada
    $queryCoordinacion = "SELECT Fullname_coordinacion FROM coordinacion WHERE identificador_coordinacion = '$coordinacionId'";
    $resultCoordinacion = $conn->query($queryCoordinacion);

    if ($resultCoordinacion->num_rows > 0) {
        $rowCoordinacion = $resultCoordinacion->fetch_assoc();
        $fullnameCoordinacion = $rowCoordinacion["Fullname_coordinacion"];
    } else {
        $fullnameCoordinacion = '';  // Puedes manejar un valor predeterminado o mostrar un error
    }

    // Obtener el nombre del servicio seleccionado
    $queryServicio = "SELECT Fullname_servicio FROM servicios WHERE identificador_servicio = '$servicioId'";
    $resultServicio = $conn->query($queryServicio);

    if ($resultServicio->num_rows > 0) {
        $rowServicio = $resultServicio->fetch_assoc();
        $fullnameServicio = $rowServicio["Fullname_servicio"];
    } else {
        $fullnameServicio = '';  // Puedes manejar un valor predeterminado o mostrar un error
    }

    // Obtener identificador_servicio desde el formulario directamente
    $identificador_servicio = $servicioId;

    // Generar el identificador de usuario
    $identificador_usuario_servicio = sprintf("%04d", $next_user_id);

    // Insertar datos en la tabla usuarios_servicios
    $sql_guardar_usuario_servicio = "INSERT INTO usuarios_servicios (Identificador_usuario_servicios, Fullname, EmailId, Password, Fullname_direccion, identificador_direccion, Fullname_coordinacion, identificador_coordinacion, Fullname_servicio, identificador_servicio) VALUES ('$identificador_usuario_servicio', '$nombre', '$email', '$password', '$fullname_direccion', '$identificador_direccion', '$fullnameCoordinacion', '$coordinacionId', '$fullnameServicio', '$identificador_servicio')";

    $result_guardar_usuario_servicio = $conn->query($sql_guardar_usuario_servicio);

    if ($result_guardar_usuario_servicio) {
        $notification_message = "Usuario registrado correctamente.";
        echo "<script>
            alert('$notification_message');
            window.location.href = '../dashboard/dashboard.php';
        </script>";
    } else {
        echo "Error al registrar el usuario: " . $conn->error;
    }
}

$conn->close();
?>
