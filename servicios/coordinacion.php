<?php
// Iniciar la sesión
session_start();

// Verificar si el tipo de usuario está definido en la sesión
if (!isset($_SESSION['tipo_usuario'])) {
    // Redirigir a la página de inicio si no hay tipo de usuario definido
    header('Location: index.php');
    exit();
}

// Obtener el tipo de usuario desde la sesión
$tipo_usuario = $_SESSION['tipo_usuario'];
include('../includes/conexion.php');

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


// Obtener las opciones para el menú desplegable (select)
$options = "";
$sql = "SELECT Fullname FROM direccion";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $options .= "<option value='" . $row["Fullname"] . "'>" . $row["Fullname"] . "</option>";
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
    <title>DIF | Coordinación</title>
    <link rel="stylesheet" href="assets/css/tarjeta.css">
</head>

<body>
<form action="../guardar/add_coordinacion.php" method="POST" enctype="multipart/form-data" class="tarjeta contenido">
        <label for="fullname_direccion">Seleccione una dirección:</label>
        <select name="fullname_direccion" required>
        <option value="" disabled selected>Selecciona una dirección</option>

            <?php echo $options; ?>
        </select>
        <label for="coordinacion">Ingrese la coordinación:</label>
        <input type="text" name="coordinacion" required>
        <div class="form-group">
                <label>Imagen de portada</label>
                <input type="file" name="image" accept="image/*" required>
            </div>
        <input type="submit" value="Guardar">
    </form>
</body>

</html>