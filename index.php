<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('includes/conexion.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];

    $consulta_direccion = "SELECT * FROM usuarios_direccion WHERE Fullname = ?";
    $consulta_coordinacion = "SELECT * FROM usuarios_coordinacion WHERE Fullname = ?";
    $consulta_servicios = "SELECT * FROM usuarios_servicios WHERE Fullname = ?";
    $consulta_admin = "SELECT * FROM administrador WHERE Fullname = ?";

    $stmt_direccion = $conexion->prepare($consulta_direccion);
    $stmt_coordinacion = $conexion->prepare($consulta_coordinacion);
    $stmt_servicios = $conexion->prepare($consulta_servicios);
    $stmt_admin = $conexion->prepare($consulta_admin);

    $stmt_direccion->bind_param("s", $usuario);
    $stmt_coordinacion->bind_param("s", $usuario);
    $stmt_servicios->bind_param("s", $usuario);
    $stmt_admin->bind_param("s", $usuario);

    $stmt_direccion->execute();
    $resultado_direccion = $stmt_direccion->get_result();
    $fila_direccion = $resultado_direccion->fetch_assoc();

    $stmt_coordinacion->execute();
    $resultado_coordinacion = $stmt_coordinacion->get_result();
    $fila_coordinacion = $resultado_coordinacion->fetch_assoc();

    $stmt_servicios->execute();
    $resultado_servicios = $stmt_servicios->get_result();
    $fila_servicios = $resultado_servicios->fetch_assoc();

    $stmt_admin->execute();
    $resultado_admin = $stmt_admin->get_result();
    $fila_admin = $resultado_admin->fetch_assoc();  // Fetch admin result here

    if ($fila_direccion) {
        $tipo_usuario = isset($fila_direccion['Puesto']) && $fila_direccion['Puesto'] == 1 ? 'Administrador' : 'UsuarioDireccion';
        $identificador_usuario = $fila_direccion['Identificador_direccion'];

        $_SESSION['tipo_usuario'] = $tipo_usuario;
        $_SESSION['identificador_usuario_direccion'] = $identificador_usuario;

    } elseif ($fila_coordinacion) {
        $tipo_usuario = isset($fila_coordinacion['Puesto']) && $fila_coordinacion['Puesto'] == 1 ? 'Administrador' : 'UsuarioCoordinacion';
        $identificador_usuario = $fila_coordinacion['identificador_usuario_coordinacion'];

        $_SESSION['tipo_usuario'] = $tipo_usuario;
        $_SESSION['identificador_usuario_coordinacion'] = $identificador_usuario;

    } elseif ($fila_servicios) {
        $tipo_usuario = isset($fila_servicios['Puesto']) && $fila_servicios['Puesto'] == 1 ? 'Administrador' : 'UsuarioServicio';
        $identificador_usuario = $fila_servicios['identificador_usuario_servicios'];

        $_SESSION['tipo_usuario'] = $tipo_usuario;
        $_SESSION['identificador_usuario_servicios'] = $identificador_usuario;
    } elseif ($fila_admin !== null) {  // Check if $fila_admin is not null
        $tipo_usuario = isset($fila_admin['Puesto']) && $fila_admin['Puesto'] == 1 ? 'Administrador' : 'UsuarioAdministrador';

        if (isset($fila_admin['identificador_administrador_coordinacion'])) {
            $identificador_usuario = $fila_admin['identificador_administrador_coordinacion'];
            $_SESSION['tipo_usuario'] = $tipo_usuario;
            $_SESSION['identificador_admin'] = $identificador_usuario;
            header('Location: dashboard/dashboard.php');
            exit();
        } else {
            $mensaje_error = 'Error al obtener el identificador del administrador';
        }
    } else {
        $mensaje_error = 'Nombre del usuario o contraseña incorrecta';
    }

    if (isset($tipo_usuario)) {
        $hash_contraseña = ($tipo_usuario == 'UsuarioDireccion') ? $fila_direccion['Password'] : (($tipo_usuario == 'UsuarioCoordinacion') ? $fila_coordinacion['Password'] : $fila_servicios['Password']);
        if (password_verify($contrasena, $hash_contraseña)) {
            if ($tipo_usuario == 'UsuarioDireccion') {
                header('Location: dashboard/dashboard_direccion.php');
                exit();
            } elseif ($tipo_usuario == 'UsuarioCoordinacion') {
                header('Location: dashboard/dashboard_coordinacion.php');
                exit();
            } elseif ($tipo_usuario == 'UsuarioServicio') {
                header('Location: dashboard/dashboard_servicio.php');
                exit();
            } elseif ($tipo_usuario == 'UsuarioAdministrador') {
                // Código para redireccionar a administradores
            }
            exit();
        } else {
            $mensaje_error = 'Usuario o contraseña incorrecto, intenta de nuevo';
        }
    }

    $stmt_direccion->close();
    $stmt_coordinacion->close();
    $stmt_servicios->close();
    $stmt_admin->close();

    $conexion->close();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DIF | Iniciar Sesión</title>
    <!-- Incluir el archivo de estilos CSS -->
    <link rel="stylesheet" href="assets/css/tarjeta.css">
</head>

<body>
    <!-- Mensajes de error en el formulario -->
    <div class="error-container">
        <?php if (isset($mensaje_error)) { ?>
            <p style="color: red;"><?php echo $mensaje_error; ?></p>
        <?php } ?>
    </div>

    <!-- Contenedor principal -->
    <div class="contenedor">
        <!-- Tarjeta de inicio de sesión -->
        <div class="tarjeta">
            <!-- Contenido de la tarjeta -->
            <div class="contenido">
                <!-- Logo del DIF -->
                <img src="assets/img/DIF2.png" alt="dif">

                <!-- Formulario de inicio de sesión -->
                <form method="post" action="">
                    <label for="usuario">Usuario:</label>
                    <input type="text" name="usuario" required>

                    <label for="contrasena">Contraseña:</label>
                    <input type="password" name="contrasena" id="passwordInput" required>

                    <!-- Botón para enviar el formulario -->
                    <button type="submit">Iniciar sesión</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Incluir el archivo de script JavaScript para mostrar/ocultar la contraseña -->
    <script src="assets/js/contrasena.js"></script>
</body>

</html>
