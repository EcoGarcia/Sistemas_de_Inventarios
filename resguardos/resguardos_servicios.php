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
    <title>DIF | Registro un nuevo usuario de dirección</title>
    <link rel="stylesheet" href="../assets/css/tarjeta.css">
</head>
<body>

    <form method="post" action="../guardar/guardar_respaldo_servicios.php" class="tarjeta contenido" onsubmit="return validarFormulario()" enctype="multipart/form-data">


    <label for="consecutivo">Consecutivo No:</label>
<input type="text" name="consecutivo" id="consecutivo" required>

<label for="fullname_direccion">Seleccione una dirección:</label>
<select name="fullname_direccion" id="fullname_direccion" required>
    <option value="" disabled selected>Selecciona una dirección</option>
    <?php echo $options; ?>
</select>

<br>

<label for="coordinacion_existente">Seleccione una coordinación existente:</label>
<select name="coordinacion_existente" id="coordinacion_existente" required>
    <option value="" disabled selected>Selecciona una Coordinación</option>
</select>

<br>

<label for="servicios">Seleccione un servicio:</label>
<select name="servicios" id="servicios" required>
    <option value="" disabled selected>Selecciona un Servicio</option>
</select>

<br>


<label for="descripcion">Descripción:</label>
<input type="text" name="descripcion" id="descripcion" required>

<label for="caracteristicas">Características Generales:</label>
<input type="text" name="caracteristicas" id="caracteristicas" required>

<label for="marca">Marca:</label>
<input type="text" name="marca" id="marca" required>

<label for="modelo">Modelo:</label>
<input type="text" name="modelo" id="modelo" required>

<label for="serie">NO. De Serie:</label>
<input type="text" name="serie" id="serie" required>

<label for="color">Color:</label>
<input type="text" name="color" id="color" required>


<label for="usuario_servicio">Seleccione un usuario de servicio:</label>
<select name="usuario_servicio" id="usuario_servicio" required>
    <option value="" disabled selected>Selecciona un Usuario</option>
</select>

<br>
<label for="observaciones">Observaciones:</label>
<input type="text" name="observaciones" id="observaciones" required>

<label for="imagen">Selecciona una imagen:</label>
<input type="file" name="imagen" id="imagen" accept=".jpg, .jpeg, .png" required>




        <button type="submit">Registrar Usuario</button>
    </form>

    <br>

    <script src="assets/js/validacion_resguardos_servicios.js"></script>

    <script>
        var selectDireccion = document.getElementById('fullname_direccion');
        var selectCoordinacion = document.getElementById('coordinacion_existente');
        var selectServicios = document.getElementById('servicios');
        var usuarioSelect = document.getElementById('usuario_servicio');

        var coordinacionesPorDireccion = <?php echo json_encode($coordinacionesPorDireccion); ?>;

        selectDireccion.addEventListener('change', function() {
            var selectedDireccionId = this.value;
            selectCoordinacion.innerHTML = '<option value="" disabled selected>Selecciona una Coordinación</option>';
            selectServicios.innerHTML = '<option value="" disabled selected>Selecciona un Servicio</option>';
            usuarioSelect.innerHTML = '<option value="" disabled selected>Selecciona un Usuario</option>';

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
            usuarioSelect.innerHTML = '<option value="" disabled selected>Selecciona un Usuario</option>';
            obtenerServicios(selectedDireccionId, selectedCoordinacionId);
        });

        selectServicios.addEventListener('change', function() {
            var selectedDireccionId = selectDireccion.value;
            var selectedCoordinacionId = selectCoordinacion.value;
            var selectedServicio = this.value;
            obtenerUsuarios(selectedDireccionId, selectedCoordinacionId, selectedServicio);
        });

        function obtenerUsuarios(direccionId, coordinacionId, servicio) {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var usuarios = JSON.parse(xhr.responseText);
                    usuarioSelect.innerHTML = '<option value="" disabled selected>Selecciona un Usuario</option>';

                    usuarios.forEach(function(usuario) {
                        var option = document.createElement('option');
                        option.value = usuario.identificador_usuario_servicios;
                        option.text = usuario.Fullname;
                        usuarioSelect.add(option);
                    });
                }
            };
            xhr.open("GET", "../total/get_usuarios_por_servicios.php?direccionId=" + direccionId + "&coordinacionId=" + coordinacionId, true);
            xhr.send();
        }

        function obtenerServicios(direccionId, coordinacionId) {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            var servicios = JSON.parse(xhr.responseText);
            selectServicios.innerHTML = '<option value="" disabled selected>Selecciona un Servicio</option>';

            servicios.forEach(function(servicio) {
                var option = document.createElement('option');
                option.value = servicio.identificador_servicio; // Utilizar identificador_servicio como valor
                option.text = servicio.Fullname_servicio; // Utilizar Fullname_servicio como texto mostrado
                selectServicios.add(option);
            });
        }
    };
    xhr.open("GET", "../total/obtener_servicio.php?direccionId=" + direccionId + "&coordinacionId=" + coordinacionId, true);
    xhr.send();
}

    </script>

</body>
</html>
