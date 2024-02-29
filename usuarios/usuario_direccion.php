<?php
// Iniciar la sesión para manejar variables de sesión
session_start();

// Verificar si el usuario está autenticado; de lo contrario, redirigir a la página de inicio
if (!isset($_SESSION['tipo_usuario'])) {
    header('Location: index.php');
    exit();
}

// Obtener el tipo de usuario de la sesión
$tipo_usuario = $_SESSION['tipo_usuario'];

// Incluir el archivo de conexión a la base de datos
include('../includes/conexion.php');
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/tarjeta.css">
    <title>DIF | Registro de Usuario</title>
</head>

<body>

    <!-- Encabezado para el formulario de registro de usuario -->

    <?php
    // Mostrar mensaje de error si existe
    if (isset($_SESSION['mensaje_error'])) {
        echo '<p class="mensaje_llenar">' . $_SESSION['mensaje_error'] . '</p>';
        unset($_SESSION['mensaje_error']);
    }

    // Mostrar mensaje de éxito si el registro fue exitoso
    if (isset($_SESSION['registro_exitoso']) && $_SESSION['registro_exitoso']) {
        echo '<p class="mensaje_exitoso">¡Registro exitoso! Se ha registrado correctamente.</p>';
        unset($_SESSION['registro_exitoso']);
    }
    ?>

    <!-- Formulario para el registro de un nuevo usuario -->
    <form method="post" action="../config/guardar_usuario_direccion.php" class="tarjeta contenido" onsubmit="return validarFormulario()">
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" id="nombre" required>

        <label for="email">Correo electrónico:</label>
        <input type="text" name="email" id="email" required>

        <br>

        <label for="password">Contraseña:</label>
        <input type="password" name="password" id="password" required>

        <br>

     

        <!-- Menú desplegable para seleccionar una dirección -->
        <label>Nombre de la dirección<span style="color:red;">*</span></label>
        <select name="direccion" class="form-control">
            <option value="" disabled selected>Selecciona una Dirección</option>

            <?php
            // Conectar a la base de datos y obtener las direcciones disponibles
            include('includes/conexion.php');
            $query = "SELECT DISTINCT Fullname, identificador FROM direccion";
            $result = mysqli_query($conexion, $query);

            // Mostrar opciones para cada dirección
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<option value='" . $row['identificador'] . "'>" . $row['Fullname'] . "</option>";
            }

            // Cerrar la conexión a la base de datos
            mysqli_close($conexion);
            ?>
        </select>

        <!-- Botón para enviar el formulario -->
        <button type="submit">Registrar Usuario</button>
    </form>

    <br>
    <!-- Enlace para volver al panel de control -->

    <!-- Incluir el script de validación -->
    <script src="assets/js/validacion.js"></script>
</body>

</html>