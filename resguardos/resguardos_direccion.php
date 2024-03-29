<?php
include('../includes/conexion.php');
include('../includes/header.php');

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sistemas";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener las opciones para el primer menú desplegable (select)
$optionsDireccion = "";
$sqlDireccion = "SELECT Identificador, Fullname FROM direccion";
$resultDireccion = $conn->query($sqlDireccion);

if ($resultDireccion->num_rows > 0) {
    while ($rowDireccion = $resultDireccion->fetch_assoc()) {
        $optionsDireccion .= "<option value='" . $rowDireccion["Identificador"] . "'>" . $rowDireccion["Fullname"] . "</option>";
    }
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

// Cerrar la conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DIF | Registro un nuevo usuario de dirección</title>
    <link rel="stylesheet" href="../assets/css/tarjeta.css">
</head>
<h2 style="text-align: center;">Añadir un nuevo resguardo de dirección</h2>
<body>

    <form method="post" action="../guardar/guardar_respaldo_direccion.php" class="tarjeta contenido" onsubmit="return validarFormulario()" enctype="multipart/form-data">

        <label for="consecutivo">Consecutivo No:</label>
        <input type="text" name="consecutivo" id="consecutivo" required>

        <!-- Campos del formulario -->
        <label for="fullname_direccion">Seleccione una dirección:</label>
        <select name="id_direccion" required>
            <option value="" disabled selected>Selecciona una dirección</option>
            <?php echo $optionsDireccion; ?>
        </select>
        <br>
        <!-- Campos del formulario -->
        <label for="fullname_categoria">Seleccione una categoria:</label>
        <select name="id_categoria" required>
            <option value="" disabled selected>Selecciona una categoria</option>
            <?php echo $optionsCategoria; ?>
        </select>

        <br>

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

        <label for="id_usuario">Seleccione un usuario de la dirección:</label>
        <select name="id_usuario" required>
            <option value="" disabled selected>Selecciona un usuario</option>
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
        <input type="file" name="imagen" id="" accept=".jpg, .jpeg" required />

        <button type="submit">Registrar Usuario</button>
    </form>

    <br>

    <script src="assets/js/validacion_resguardos_direccion.js"></script>

    <script>
        $(document).ready(function () {
            const direccionSelect = $('select[name="id_direccion"]');
            const usuarioSelect = $('select[name="id_usuario"]');
            const usuariosContainer = $('#usuariosContainer');

            direccionSelect.on('change', function () {
                const selectedDireccion = this.value;

                // Llamar a un script PHP que devuelva los usuarios según la dirección seleccionada
                fetch(`../total/get_usuarios_por_direccion.php?direccion=${selectedDireccion}`)
                    .then(response => response.json())
                    .then(data => {
                        // Limpiar opciones actuales
                        usuarioSelect.html('<option value="" disabled selected>Selecciona un usuario</option>');

                        // Agregar nuevas opciones
                        data.forEach(usuario => {
                            usuarioSelect.append(`<option value="${usuario.Identificador_usuario_direccion}">${usuario.Nombre_usuario}</option>`);
                        });

                        // Habilitar el select de usuarios
                        usuarioSelect.prop('disabled', false);
                    })
                    .catch(error => console.error('Error al obtener usuarios:', error));
            });
        });
    </script>
</body>

</html>
