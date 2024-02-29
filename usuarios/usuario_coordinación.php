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

// Configuración de la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sistemas";

// Crear una nueva conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar si la conexión es exitosa
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Inicializar la cadena de opciones para el menú desplegable de direcciones
$options = "";
// Almacena las coordinaciones asociadas a cada dirección
$coordinacionesPorDireccion = array();

// Consultar las direcciones disponibles
$sql = "SELECT identificador, Fullname FROM direccion";
$result = $conn->query($sql);

// Verificar si hay direcciones disponibles
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Obtener datos de la dirección actual
        $direccionId = $row["identificador"];
        $direccion = $row["Fullname"];

        // Construir la cadena de opciones para el menú desplegable
        $options .= "<option value='$direccionId'>$direccion</option>";

        // Obtener las coordinaciones asociadas a la dirección actual
        $sql_coordinaciones_por_direccion = "SELECT identificador_coordinacion, Fullname_coordinacion FROM coordinacion WHERE identificador_direccion = '$direccionId'";
        $result_coordinaciones_por_direccion = $conn->query($sql_coordinaciones_por_direccion);

        // Verificar si hay coordinaciones asociadas
        if ($result_coordinaciones_por_direccion->num_rows > 0) {
            $coordinaciones = array();
            while ($row_coordinacion = $result_coordinaciones_por_direccion->fetch_assoc()) {
                $coordinaciones[] = $row_coordinacion["Fullname_coordinacion"];
            }
            // Almacenar las coordinaciones asociadas a la dirección en el array
            $coordinacionesPorDireccion[$direccionId] = $coordinaciones;
        }
    }
}

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $nombre = $_POST["nombre"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $direccionId = $_POST["fullname_direccion"];
    $coordinacion = $_POST["coordinacion_existente"];

    // Resto del código para guardar el usuario, utilizando los datos obtenidos
    // ...

    // Ejemplo: Guardar el usuario en la tabla de usuarios
    $sql_guardar_usuario = "INSERT INTO usuarios (nombre, email, password, direccion_id, coordinacion) VALUES ('$nombre', '$email', '$password', '$direccionId', '$coordinacion')";
    $result_guardar_usuario = $conn->query($sql_guardar_usuario);

    // Verificar el resultado de la inserción
    if ($result_guardar_usuario) {
        // Mensaje de notificación y redirección a la página de servicios
        $notification_message = "Usuario registrado correctamente.";
        echo "<script>
            alert('$notification_message');
            window.location.href = 'dashboard.php';
        </script>";
    } else {
        // Mostrar un mensaje de error si hay un problema con la inserción
        echo "Error al registrar el usuario: " . $conn->error;
    }
}

// Cerrar la conexión a la base de datos
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DIF | Registro de Usuario</title>
    <link rel="stylesheet" href="assets/css/">
</head>

<body>


    <!-- Formulario para registrar un nuevo usuario -->
    <form method="post" action="../config/guardar_usuario_coordinacion.php" class="tarjeta contenido" onsubmit="return validarFormulario()">
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" id="nombre" required>

        <label for="email">Correo electrónico:</label>
        <input type="text" name="email" id="email" required>

        <br>

        <label for="password">Contraseña:</label>
        <input type="password" name="password" id="password" required>

        <br>

        <label for="fullname_direccion">Seleccione una dirección:</label>
        <select name="fullname_direccion" id="fullname_direccion" required>
            <option value="" disabled selected>Selecciona una dirección</option>
            <?php echo $options; ?>
        </select>


        <!-- Agregar un menú desplegable para las coordinaciones existentes -->
        <label for="coordinacion_existente">Seleccione una coordinación existente:</label>
        <select name="coordinacion_existente" id="coordinacion_existente">
            <option value="" disabled selected>Selecciona una Coordinación</option>
            <!-- Las opciones se llenarán dinámicamente mediante JavaScript -->
        </select>
        <br><br>

        <!-- Botón para enviar el formulario -->
        <button type="submit">Registrar Usuario</button>
    </form>

    <br>


    <!-- Incluir el script de validación -->
    <script src="assets/js/validacion.js"></script>

    <!-- Script JavaScript para actualizar dinámicamente las opciones del menú desplegable de coordinaciones -->
    <script>
        var selectDireccion = document.getElementById('fullname_direccion');
        var selectCoordinacion = document.getElementById('coordinacion_existente');

        // Coordinaciones asociadas a cada dirección
        var coordinacionesPorDireccion = <?php echo json_encode($coordinacionesPorDireccion); ?>;

        // Función para actualizar las opciones de coordinación al cambiar la dirección
        selectDireccion.addEventListener('change', function() {
            var selectedDireccionId = this.value;

            // Limpiar las opciones actuales
            selectCoordinacion.innerHTML = '<option value="" disabled selected>Selecciona una Coordinación</option>';

            // Agregar las nuevas opciones basadas en la dirección seleccionada
            coordinacionesPorDireccion[selectedDireccionId].forEach(function(coordinacion) {
                var option = document.createElement('option');
                option.value = coordinacion;
                option.text = coordinacion;
                selectCoordinacion.add(option);
            });
        });
    </script>
</body>

</html>