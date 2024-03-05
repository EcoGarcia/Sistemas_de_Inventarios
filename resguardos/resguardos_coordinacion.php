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

// Obtener las opciones para el segundo menú desplegable (select)
$optionsCategoria = "";
$sqlCategoria = "SELECT Identificador_categoria, Fullname_categoria FROM categorias";
$resultCategoria = $conn->query($sqlCategoria);

if ($resultCategoria) {
    if ($resultCategoria->num_rows > 0) {
        while ($rowCategoria = $resultCategoria->fetch_assoc()) {
            $optionsCategoria .= "<option value='" . $rowCategoria["Identificador_categoria"] . "'>" . $rowCategoria["Fullname_categoria"] . "</option>";
        }
    }
} else {
    echo "Error executing query: " . $conn->error;
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
    <link rel="stylesheet" href="assets/css/tarjeta.css">
</head>

<body>

    <form method="post" action="../guardar/guardar_respaldo_coordinacion.php" class="tarjeta contenido" onsubmit="return validarFormulario()" enctype="multipart/form-data">

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

        <!-- Campos del formulario -->
        <label for="fullname_categoria">Seleccione una categoria:</label>
        <select name="id_categoria" required>
            <option value="" disabled selected>Selecciona una categoria</option>
            <?php echo $optionsCategoria; ?>
        </select>


        <label for="">Descripción:</label>
        <input type="text" name="descripcion" id="" required>

        <label for="">Características Generales:</label>
        <input type="text" name="caracteristicas" id="" required>

        <label for="">Marca:</label>
        <input type="text" name="marca" id="" required>

        <label for="">Modelo:</label>
        <input type="text" name="modelo" id="" required>

        <label for="">NO. De Serie:</label>
        <input type="text" name="serie" id="" required>

        <label for="">Color:</label>
        <input type="text" name="color" id="" required>

        <label for="usuario_coordinacion">Seleccione un usuario de servicio:</label>
        <select name="usuario_coordinacion" id="usuario_coordinacion" required>
            <option value="" disabled selected>Selecciona un Usuario</option>
        </select>

        <br>
        <label for="">Observaciones:</label>
        <input type="text" name="observaciones" id="" required>

        <!-- Nuevo campo de selección para condiciones -->
        <label for="select_condiciones">Condiciones:</label>
        <select name="select_condiciones" required>
            <option value="Buenas">Buenas Condiciones</option>
            <option value="Regular">Condiciones Regulares</option>
            <option value="Malas">Malas Condiciones</option>
        </select>

        <label for="">Numero de Factura:</label>
        <input type="text" name="factura" id="" required>

        <label for="">Selecciona una imagen:</label>
        <input type="file" name="imagen" id="" accept=".jpg, .jpeg, .png" required />

        <button type="submit">Registrar Usuario</button>
    </form>

    <br>

    <script src="assets/js/validacion_resguardos_coordinacion.js"></script>

    <script>
        var selectDireccion = document.getElementById('fullname_direccion');
        var selectCoordinacion = document.getElementById('coordinacion_existente');
        var usuarioSelect = document.getElementById('usuario_coordinacion');

        var coordinacionesPorDireccion = <?php echo json_encode($coordinacionesPorDireccion); ?>;

        selectDireccion.addEventListener('change', function() {
            var selectedDireccionId = this.value;
            selectCoordinacion.innerHTML = '<option value="" disabled selected>Selecciona una Coordinación</option>';
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
            usuarioSelect.innerHTML = '<option value="" disabled selected>Selecciona un Usuario</option>';
            obtenerUsuarios(selectedDireccionId, selectedCoordinacionId);
        });

        function obtenerUsuarios(direccionId, coordinacionId) {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4) {
                    if (xhr.status == 200) {
                        console.log("Respuesta de get_usuarios_por_coordinacion.php:", xhr.responseText);

                        var usuarios = JSON.parse(xhr.responseText);
                        usuarioSelect.innerHTML = '<option value="" disabled selected>Selecciona un Usuario</option>';

                        usuarios.forEach(function(usuario) {
                            var option = document.createElement('option');
                            option.value = usuario.identificador_usuario_coordinacion;
                            option.text = usuario.Fullname; // Corregir aquí para obtener el nombre del usuario
                            usuarioSelect.add(option);
                        });
                    } else {
                        console.error("Error en la solicitud AJAX:", xhr.status, xhr.statusText);
                    }
                }
            };

            xhr.open("GET", "../total/get_usuarios_por_coordinacion.php?direccionId=" + direccionId + "&coordinacionId=" + coordinacionId, true);
            xhr.send();
        }
    </script>


</body>

</html>