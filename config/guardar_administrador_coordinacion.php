<?php
session_start();

if (!isset($_SESSION['tipo_usuario'])) {
    header('Location: index.php');
    exit();
}

include('../includes/conexion.php');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sistemas";

$conexion = new mysqli($servername, $username, $password, $dbname);

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = isset($_POST["nombre"]) ? $_POST["nombre"] : "";
    $email = isset($_POST["email"]) ? $_POST["email"] : "";
    $password = password_hash(isset($_POST['password']) ? $_POST['password'] : "", PASSWORD_BCRYPT);

    $direccionId = isset($_POST["fullname_direccion"]) ? $_POST["fullname_direccion"] : "";
    $coordinacion = isset($_POST["coordinacion_existente"]) ? $_POST["coordinacion_existente"] : "";

    $sql_direccion = "SELECT Fullname, identificador FROM direccion WHERE identificador = '$direccionId'";
    $result_direccion = $conexion->query($sql_direccion);

    if ($result_direccion->num_rows > 0) {
        $row_direccion = $result_direccion->fetch_assoc();
        $fullname_direccion = $row_direccion["Fullname"];
        $identificador_direccion = $row_direccion["identificador"];
    } else {
        echo "Error al obtener la información de la dirección.";
        exit();
    }

    $sql_coordinacion = "SELECT identificador_coordinacion FROM coordinacion WHERE Fullname_coordinacion = '$coordinacion' AND identificador_direccion = '$direccionId'";
    $result_coordinacion = $conexion->query($sql_coordinacion);

    if ($result_coordinacion->num_rows > 0) {
        $row_coordinacion = $result_coordinacion->fetch_assoc();
        $identificador_coordinacion = $row_coordinacion["identificador_coordinacion"];
    } else {
        echo "Error al obtener el identificador de la coordinación.";
        exit();
    }

    $sql_max_user_id = "SELECT MAX(identificador_administrador_coordinacion) AS max_id FROM administrador";
    $result_max_user_id = $conexion->query($sql_max_user_id);

    if ($result_max_user_id->num_rows > 0) {
        $row_max_user_id = $result_max_user_id->fetch_assoc();
        $next_user_id = $row_max_user_id["max_id"] + 1;
    } else {
        $next_user_id = 1;
    }

    $sql_guardar_usuario_servicio = "INSERT INTO administrador (identificador_administrador_coordinacion, Fullname, EmailId, Password, Fullname_direccion, identificador_direccion, Fullname_coordinacion, identificador_coordinacion, Puesto) VALUES ('$next_user_id', '$nombre', '$email', '$password', '$fullname_direccion', '$identificador_direccion', '$coordinacion', '$identificador_coordinacion', 1)";

    $result_guardar_usuario_servicio = $conexion->query($sql_guardar_usuario_servicio);

    if ($result_guardar_usuario_servicio) {
        $notification_message = "Usuario registrado correctamente.";
        echo "<script>
            alert('$notification_message');
            window.location.href = '../dashboard/dashboard.php';
        </script>";
    } else {
        echo "Error al registrar el usuario: " . $conexion->error;
    }
}

$conexion->close();
?>
