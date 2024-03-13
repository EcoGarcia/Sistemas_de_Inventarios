<?php
session_start();

if (!isset($_SESSION['tipo_usuario'])) {
    header('Location: index.php');
    exit();
}

include('../includes/conexion.php');
include('../includes/header.php');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sistemas";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$options = "";
$coordinacionesPorDireccion = array();

$sql = "SELECT identificador, Fullname FROM direccion";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $direccionId = $row["identificador"];
        $direccion = $row["Fullname"];
        $options .= "<option value='$direccionId'>$direccion</option>";

        $sql_coordinaciones_por_direccion = "SELECT identificador_coordinacion, Fullname_coordinacion FROM coordinacion WHERE identificador_direccion = '$direccionId'";
        $result_coordinaciones_por_direccion = $conn->query($sql_coordinaciones_por_direccion);

        if ($result_coordinaciones_por_direccion->num_rows > 0) {
            $coordinaciones = array();
            while ($row_coordinacion = $result_coordinaciones_por_direccion->fetch_assoc()) {
                $coordinacionId = $row_coordinacion["identificador_coordinacion"];
                $coordinacionNombre = $row_coordinacion["Fullname_coordinacion"];
                $coordinaciones[] = array("id" => $coordinacionId, "nombre" => $coordinacionNombre);
            }
            $coordinacionesPorDireccion[$direccionId] = $coordinaciones;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DIF | Registro de Usuario</title>
    <link rel="stylesheet" href="../assets/css/tarjeta.css">
</head>
<h2 style="text-align: center;">Añadir un nuevo administrador</h2>
<body>

    <!-- Formulario para registrar un nuevo usuario -->
    <form method="post" action="../config/guardar_administrador_coordinacion.php" class="tarjeta contenido" onsubmit="return validarFormulario()">
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

        <label for="servicios">Seleccione un servicio:</label>
        <select name="servicios" id="servicios">
            <option value="" disabled selected>Selecciona un Servicio</option>
        </select>

        <br>

        <button type="submit">Registrar Usuario</button>
    </form>

    <br>

    <script src="assets/js/validacion_resguardos_coordinacion.js"></script>

    <script>
        var selectDireccion = document.getElementById('fullname_direccion');
        var selectCoordinacion = document.getElementById('coordinacion_existente');
        var selectServicios = document.getElementById('servicios');

        var coordinacionesPorDireccion = <?php echo json_encode($coordinacionesPorDireccion); ?>;

        selectDireccion.addEventListener('change', function() {
            var selectedDireccionId = this.value;
            selectCoordinacion.innerHTML = '<option value="" disabled selected>Selecciona una Coordinación</option>';
            selectServicios.innerHTML = '<option value="" disabled selected>Selecciona un Servicio</option>';

            if (coordinacionesPorDireccion[selectedDireccionId]) {
                coordinacionesPorDireccion[selectedDireccionId].forEach(function(coordinacion) {
                    var option = document.createElement('option');
                    option.value = coordinacion.id;
                    option.text = coordinacion.nombre;
                    selectCoordinacion.add(option);
                });
            }
        });

        selectCoordinacion.addEventListener('change', function() {
            var selectedDireccionId = selectDireccion.value;
            var selectedCoordinacionId = this.value;
            selectServicios.innerHTML = '<option value="" disabled selected>Selecciona un Servicio</option>';
            obtenerServicios(selectedDireccionId, selectedCoordinacionId);
        });

        function obtenerServicios(direccionId, coordinacionId) {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var servicios = JSON.parse(xhr.responseText);
                    selectServicios.innerHTML = '<option value="" disabled selected>Selecciona un Servicio</option>';

                    servicios.forEach(function(servicio) {
                        var option = document.createElement('option');
                        option.value = servicio.identificador_servicio;
                        option.text = servicio.Fullname_servicio;
                        selectServicios.add(option);
                    });
                }
            };
            xhr.open("GET", "../obtener/obtener_servicios.php?direccionId=" + direccionId + "&coordinacionId=" + coordinacionId, true);
            xhr.send();
        }
    </script>

</body>

</html>
