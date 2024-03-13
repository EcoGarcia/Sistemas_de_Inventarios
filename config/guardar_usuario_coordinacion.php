<?php
// Inicia la sesión
session_start();

// Verifica si el tipo de usuario está definido en la sesión
if (!isset($_SESSION['tipo_usuario'])) {
    // Si no hay tipo de usuario, redirige a la página de inicio de sesión
    header('Location: index.php');
    exit();
}

// Obtiene el tipo de usuario de la sesión
$tipo_usuario = $_SESSION['tipo_usuario'];

// Incluye el archivo de conexión a la base de datos
include('../includes/conexion.php');

// Detalles de conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sistemas";

// Crea una nueva conexión a la base de datos
$conexion = new mysqli($servername, $username, $password, $dbname);

// Verifica si la conexión fue exitosa
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Procesa el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtiene los datos del formulario
    $nombre = isset($_POST["nombre"]) ? $_POST["nombre"] : "";
    $email = isset($_POST["email"]) ? $_POST["email"] : "";
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Encripta la contraseña
    $direccionId = isset($_POST["fullname_direccion"]) ? $_POST["fullname_direccion"] : "";
    $coordinacion = isset($_POST["coordinacion_existente"]) ? $_POST["coordinacion_existente"] : "";

    // Obtiene el valor del checkbox
    // $esAdministrador = isset($_POST['es_administrador']) ? $_POST['es_administrador'] : 0;

    // Obtiene información de la dirección
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

    // Obtiene el identificador de la coordinación
    $sql_coordinacion = "SELECT identificador_coordinacion FROM coordinacion WHERE Fullname_coordinacion = '$coordinacion' AND identificador_direccion = '$direccionId'";
    $result_coordinacion = $conexion->query($sql_coordinacion);

    if ($result_coordinacion->num_rows > 0) {
        $row_coordinacion = $result_coordinacion->fetch_assoc();
        $identificador_coordinacion = $row_coordinacion["identificador_coordinacion"];
    } else {
        echo "Error al obtener el identificador de la coordinación.";
        exit();
    }

    // Obtiene el máximo identificador de usuario en la tabla sin considerar la dirección o coordinación
    $sql_max_user_id = "SELECT MAX(identificador_usuario_coordinacion) AS max_id FROM usuarios_coordinacion";
    $result_max_user_id = $conexion->query($sql_max_user_id);

    if ($result_max_user_id->num_rows > 0) {
        $row_max_user_id = $result_max_user_id->fetch_assoc();
        $next_user_id = $row_max_user_id["max_id"] + 1;
    } else {
        // Si no hay usuarios aún, empezar desde 1
        $next_user_id = 1;
    }

    // Verificar si ya existe un registro con el mismo nombre
    $query_check_duplicate = "SELECT COUNT(*) AS user_count FROM usuarios_coordinacion WHERE EmailId = ? AND Identificador_coordinacion = ?";
    $stmt_check_duplicate = mysqli_prepare($conexion, $query_check_duplicate);
    mysqli_stmt_bind_param($stmt_check_duplicate, 'ss', $email, $identificador_coordinacion);
    mysqli_stmt_execute($stmt_check_duplicate);
    $result_check_duplicate = mysqli_stmt_get_result($stmt_check_duplicate);
    $row_check_duplicate = mysqli_fetch_assoc($result_check_duplicate);

    // Verificar si ya existe un registro con el mismo nombre
    if ($row_check_duplicate['user_count'] > 0) {
        echo "<script>
            var mensaje_notificacion = 'El usuario \'$nombre\' ya está registrado en la coordinación \'$coordinacion\'.';
            alert(mensaje_notificacion);
            window.location.href = '../dashboard/dashboard.php';
        </script>";
        exit(); // Detener la ejecución del script después de la redirección
    }

    // Ejemplo: Guardar el usuario en la tabla de usuarios_coordinacion
    $sql_guardar_usuario = "INSERT INTO usuarios_coordinacion (identificador_usuario_coordinacion, Fullname, EmailId, Password, Fullname_direccion, identificador_direccion, Fullname_coordinacion, identificador_coordinacion, Puesto) 
                            VALUES ('$next_user_id', '$nombre', '$email', '$password', '$fullname_direccion', '$identificador_direccion', '$coordinacion', '$identificador_coordinacion', '$esAdministrador')";

    $result_guardar_usuario = $conexion->query($sql_guardar_usuario);

    if ($result_guardar_usuario) {
        // Notifica al usuario sobre el registro exitoso y redirige a la página correspondiente
        $notification_message = "Usuario registrado correctamente.";
        echo "<script>
        if (confirm('$notification_message ¿Quieres añadir un nuevo usuario?')) {
            window.location.href = '../usuarios/usuario_coordinación.php';
        } else {
            window.location.href = '../dashboard/dashboard.php'; 
        }
    </script>";       } else {
        // Muestra un mensaje de error si falla el registro del usuario
        echo "Error al registrar el usuario: " . $conexion->error;
    }
}

// Cierra la conexión a la base de datos
$conexion->close();
?>
