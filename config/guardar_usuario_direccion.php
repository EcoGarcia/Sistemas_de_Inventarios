<?php
// Inicia la sesión
session_start();

// Verifica si el formulario se ha enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Incluye el archivo de conexión a la base de datos
    include('../includes/conexion.php');

    // Recupera los datos del formulario
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Encripta la contraseña
    $direccion = $_POST['direccion'];

    // Busca el identificador y Fullname en la tabla direccion
    $query_select_direction = "SELECT identificador, Fullname FROM direccion WHERE identificador = '$direccion'";
    $result_select_direction = mysqli_query($conexion, $query_select_direction);
    $row_direction = mysqli_fetch_assoc($result_select_direction);

    if ($row_direction) {
        $identificador_direccion = $row_direction['identificador'];
        $fullname_direccion = $row_direction['Fullname'];

        // Generar un identificador único para el usuario basado en el contador
        $Identificador_usuario_direccion = generarIdentificadorUnico($conexion);
        // Verificar si ya existe un registro con el mismo nombre
        $query_check_duplicate = "SELECT COUNT(*) AS user_count FROM usuarios_direccion WHERE EmailId = ? AND Identificador_direccion = ?";
        $stmt_check_duplicate = mysqli_prepare($conexion, $query_check_duplicate);
        mysqli_stmt_bind_param($stmt_check_duplicate, 'ss', $email, $identificador_direccion);
        mysqli_stmt_execute($stmt_check_duplicate);
        $result_check_duplicate = mysqli_stmt_get_result($stmt_check_duplicate);
        $row_check_duplicate = mysqli_fetch_assoc($result_check_duplicate);

        // Verificar si ya existe un registro con el mismo nombre
        if ($row_check_duplicate['user_count'] > 0) {
            echo "<script>
                var mensaje_notificacion = 'El usuario \'$nombre\' ya está registrado en la dirección \'$fullname_direccion\'.';
                alert(mensaje_notificacion);
                window.location.href = '../dashboard/dashboard.php';
            </script>";
            exit(); // Detener la ejecución del script después de la redirección
        }
        
        // Inserta los datos en la tabla usuarios_direccion
        $query_insert = "INSERT INTO usuarios_direccion (Fullname, EmailId, Password, Identificador_direccion, Fullname_direccion, Identificador_usuario_direccion)
                        VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_insert = mysqli_prepare($conexion, $query_insert);
        mysqli_stmt_bind_param($stmt_insert, 'ssssss', $nombre, $email, $password, $identificador_direccion, $fullname_direccion, $Identificador_usuario_direccion);

        if (mysqli_stmt_execute($stmt_insert)) {
            // Notifica al usuario sobre el registro exitoso y redirige a la página correspondiente
            $notification_message = "Usuario registrado correctamente.";
            echo "<script>
                alert('$notification_message');
                window.location.href = '../dashboard/dashboard.php';
            </script>";
            exit(); // Asegura que no se ejecute más código después de redirigir
        } else {
            $_SESSION['mensaje_error'] = "Error al registrar usuario: " . mysqli_error($conexion);
        }
    } else {
        $_SESSION['mensaje_error'] = "La dirección especificada no existe en la tabla direccion.";
    }

    // Cierra la conexión
    mysqli_close($conexion);

    // Redirige a la página principal
    header('Location: ../dashboard/dashboard.php');
    exit();
} else {
    // Si el formulario no se ha enviado, redirige a la página principal
    header('Location: ../dashboard/dashboard.php');
    exit();
}

// Función para generar un identificador único basado en un contador
function generarIdentificadorUnico($conexion)
{
    // Consultar el último identificador generado
    $query_last_id = "SELECT MAX(Identificador_usuario_direccion) AS max_id FROM usuarios_direccion";
    $result_last_id = mysqli_query($conexion, $query_last_id);
    $row_last_id = mysqli_fetch_assoc($result_last_id);

    // Obtener el último identificador y sumar 1
    $last_id = $row_last_id['max_id'];
    $Identificador_usuario_direccion = ($last_id !== null) ? $last_id + 1 : 1;

    return $Identificador_usuario_direccion;
}
