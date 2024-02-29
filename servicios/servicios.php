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

// Array para almacenar las coordinaciones asociadas a cada dirección
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



// Inicializar la cadena de opciones para el menú desplegable de coordinaciones
$coordinaciones_options = "";

// Consultar las coordinaciones guardadas en la tabla "coordinacion"
$sql_coordinaciones = "SELECT identificador_coordinacion, Fullname_coordinacion FROM coordinacion";
$result_coordinaciones = $conn->query($sql_coordinaciones);

// Verificar si hay coordinaciones disponibles
if ($result_coordinaciones->num_rows > 0) {
    while ($row_coordinacion = $result_coordinaciones->fetch_assoc()) {
        // Construir la cadena de opciones para el menú desplegable
        $coordinaciones_options .= "<option value='" . $row_coordinacion["Fullname_coordinacion"] . "'>" . $row_coordinacion["Fullname_coordinacion"] . "</option>";
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
    <title>DIF | Añadir servicios</title>
    <link rel="stylesheet" href="assets/css/tarjeta.css">
</head>

<body>

    <form action="../guardar/add_servicios.php" method="POST" enctype="multipart/form-data" class="tarjeta contenido">
        <!-- Menú desplegable para seleccionar una dirección -->
        <label for="fullname_direccion">Seleccione una dirección:</label>
        <select name="fullname_direccion" id="fullname_direccion" required>
            <option value="" disabled selected>Selecciona una dirección</option>
            <?php echo $options; ?>
        </select>
        <br><br>

        <!-- Menú desplegable para seleccionar una coordinación existente -->
        <label for="coordinacion_existente">Seleccione una coordinación existente:</label>
        <select name="coordinacion_existente" id="coordinacion_existente">
            <option value="" disabled selected>Selecciona una Coordinación</option>
            <!-- Las opciones se llenarán dinámicamente mediante JavaScript -->
        </select>
        <br><br>

        <!-- Campo para ingresar el nombre del nuevo servicio -->
        <label for="servicio_nuevo">Ingrese el nombre del nuevo servicio:</label>
        <input type="text" name="servicio_nuevo" required>
        <br><br>

        <div class="form-group">
            <label>Imagen de portada</label>
            <input type="file" name="image" accept="image/*" required>
        </div>
        <!-- Botón para enviar el formulario -->
        <input type="submit" value="Guardar">
    </form>

    <!-- Script JavaScript para actualizar dinámicamente las opciones del menú desplegable de coordinaciones -->
    <script>
        var selectDireccion = document.getElementById('fullname_direccion');
        var selectCoordinacion = document.getElementById('coordinacion_existente');

        // Coordinaciones asociadas a cada dirección
        var coordinacionesPorDireccion = <?php echo json_encode($coordinacionesPorDireccion); ?>;

        // Mapa de identificadores de dirección a nombres de dirección
        var direccionNombreMap = <?php echo json_encode(array_column($result->fetch_all(MYSQLI_ASSOC), 'Fullname', 'identificador')); ?>;

        // Variable para almacenar el nombre de la dirección seleccionada
        var direccion = "";

        // Función para actualizar las opciones de coordinación al cambiar la dirección
        selectDireccion.addEventListener('change', function() {
            var selectedDireccionId = this.value;

            // Obtener el nombre de la dirección seleccionada
            var selectedDireccionNombre = direccionNombreMap[selectedDireccionId];

            // Limpiar las opciones actuales
            selectCoordinacion.innerHTML = '<option value="" disabled selected>Selecciona una Coordinación</option>';

            // Agregar las nuevas opciones basadas en la dirección seleccionada
            coordinacionesPorDireccion[selectedDireccionId].forEach(function(coordinacion) {
                var option = document.createElement('option');
                option.value = coordinacion;
                option.text = coordinacion;
                selectCoordinacion.add(option);
            });

            // Actualizar el valor de la variable dirección con el nombre de la dirección seleccionada
            direccion = selectedDireccionNombre;
        });
    </script>
</body>

</html>