<?php
include('../includes/conexion.php');
include('../includes/header.php');

// Verificar si se ha proporcionado un ID en la URL
if (!isset($_GET['id'])) {
    // Redireccionar o mostrar un mensaje de error si no se proporciona un ID válido
    exit("ID no proporcionado");
}

// Obtener el ID de la URL y asegurarse de que sea un entero válido
$id = intval($_GET['id']);

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

// Consulta para obtener los datos correspondientes al ID proporcionado
$sql = "SELECT consecutivo, Fullname_direccion, descripcion, caracteristicas, marca, modelo, serie, color, observaciones, Factura, Fullname_categoria, usuario_responsable FROM resguardos_admin WHERE id = $id";

$result = $conn->query($sql);

// Verificar si se encontraron resultados
if ($result === false) {
    echo "Error al ejecutar la consulta: " . $conn->error;
} else {
    if ($result->num_rows > 0) {
        // Obtener la fila de resultados como un arreglo asociativo
        $row = $result->fetch_assoc();

        // Obtener el valor de la columna Consecutivo_No
        $consecutivo = $row['consecutivo'];
        $fullname_categoria = $row['Fullname_categoria'];
        $descripcion = $row['descripcion'];
        $usuario_responsable = $row['usuario_responsable'];
        $caracteristicas = $row['caracteristicas'];
        $marca = $row['marca'];
        $modelo = $row['modelo'];
        $serie = $row['serie'];
        $color = $row['color'];
        $observaciones = $row['observaciones'];
        $factura = $row['Factura'];
    } else {
        // Manejar el caso en que no se encuentren resultados
        echo "No se encontraron resultados para el ID proporcionado";
    }
}

// Obtener las opciones para el tercer menú desplegable (select) de coordinaciones
$optionsCoordinacion = "";
$sqlCoordinacion = "SELECT c.identificador_coordinacion, c.Fullname_coordinacion 
                    FROM coordinacion c 
                    WHERE c.identificador_direccion = $id"; // Usar la columna correcta para la relación
$resultCoordinacion = $conn->query($sqlCoordinacion);

if ($resultCoordinacion === false) {
    echo "Error al ejecutar la consulta: " . $conn->error;
} else {
    if ($resultCoordinacion->num_rows > 0) {
        while ($rowCoordinacion = $resultCoordinacion->fetch_assoc()) {
            $optionsCoordinacion .= "<option value='" . $rowCoordinacion["identificador_coordinacion"] . "'>" . $rowCoordinacion["Fullname_coordinacion"] . "</option>";
        }
    }
}

// Obtener las opciones para el segundo menú desplegable (select)
$optionsCategoria = "";
$sqlCategoria = "SELECT Identificador_categoria, Fullname_categoria FROM categorias";
$resultCategoria = $conn->query($sqlCategoria);

if ($resultCategoria === false) {
    echo "Error al ejecutar la consulta: " . $conn->error;
} else {
    if ($resultCategoria->num_rows > 0) {
        while ($rowCategoria = $resultCategoria->fetch_assoc()) {
            $optionsCategoria .= "<option value='" . $rowCategoria["Identificador_categoria"] . "'>" . $rowCategoria["Fullname_categoria"] . "</option>";
        }
    }
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

<body>

    <form method="post" action="../guardar/edit_respaldo_admin.php" class="tarjeta contenido" onsubmit="return validarFormulario()" enctype="multipart/form-data">

        <label for="consecutivo">Consecutivo No:</label>
        <input type="text" name="consecutivo" id="consecutivo" value="<?php echo $consecutivo; ?>" required>


        <br><!-- Campos del formulario -->
<label for="fullname_categoria">Seleccione una coordinación:</label>
<select name="id_coordinacion" id="coordinacion" required>
    <option value="" disabled>Selecciona una coordinación</option>
    <?php
    echo $optionsCoordinacion; // Imprime las opciones generadas dinámicamente
    ?>
</select>


        <!-- Campos del formulario -->
        <label for="fullname_categoria">Seleccione una dirección:</label>
        <select name="id_categoria" id="categoria" required>
            <option value="<?php echo $fullname_categoria; ?>" disabled>Selecciona una dirección</option>
            <?php
            echo $optionsCategoria; // Imprime las opciones generadas dinámicamente
            ?>
        </select>


        <br>

        <label for="">Descripción:</label>
        <input type="text" name="descripcion" id="descripcion" value="<?php echo $descripcion; ?>" required>

        <label for="">Características Generales:</label>
        <input type="text" name="caracteristicas" id="caracteristicas" value="<?php echo $caracteristicas; ?>" required>

        <label for="">Marca:</label>
        <input type="text" name="marca" id="marca" value="<?php echo $marca; ?>" required>

        <label for="">Modelo:</label>
        <input type="text" name="modelo" id="modelo" value="<?php echo $modelo; ?>" required>
        <input type="hidden" name="nombre_direccion" value="<?php echo $fullname_direccion; ?>">
        <input type="hidden" name="identificador_direccion" value="<?php echo $identificador_direccion; ?>">

        <label for="">NO. De Serie:</label>
        <input type="text" name="serie" id="serie" value="<?php echo $serie; ?>" required>

        <label for="">Color:</label>
        <input type="text" name="color" id="color" value="<?php echo $color; ?>" required>




        <label for="id_usuario">Seleccione un usuario de la dirección:</label>
        <select name="id_usuario" required>
            <option value="" disabled>Selecciona un usuario</option>
            <option value="<?php echo $usuario_responsable; ?>" selected><?php echo $usuario_responsable; ?></option>
            <!-- Aquí se agregarán las opciones de los demás usuarios si es necesario -->
        </select>

        <br>
        <label for="">Observaciones:</label>
        <input type="text" name="observaciones" id="observaciones" value="<?php echo $observaciones; ?>" required>

        <!-- Nuevo campo de selección para condiciones -->
        <label for="select_condiciones">Condiciones:</label>
        <select name="select_condiciones" required>
            <option value="Buenas">Buenas Condiciones</option>
            <option value="Regular">Condiciones Regulares</option>
            <option value="Malas">Malas Condiciones</option>
        </select>

        <label for="">Numero de Factura:</label>
        <input type="text" name="factura" id="factura" value="<?php echo $factura; ?>" required>

        <label for="">Selecciona una imagen:</label>
        <input type="file" name="imagen" id="" accept=".jpg, .jpeg" required />

        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <button type="submit">Registrar Usuario</button>
    </form>

    <br>

    <script src="assets/js/validacion_resguardos_direccion.js"></script>


</body>

</html>