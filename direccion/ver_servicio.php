
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
include('../includes/header_usuario_direccion.php');

// Verificar si se han enviado los identificadores de dirección y coordinación
if (!isset($_GET['identificador_direccion']) || !isset($_GET['identificador_coordinacion'])) {
    // Si no se proporcionan ambos identificadores, redirige a alguna página de manejo de errores o a la página principal
    header('Location: index.php');
    exit();
}

// Obtener los identificadores de dirección y coordinación desde la URL
$identificador_direccion = $_GET['identificador_direccion'];
$identificador_coordinacion = $_GET['identificador_coordinacion'];

// Mueve la inclusión del archivo header.php antes de session_start()
// include('includes/header.php');

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
    <title>DIF | Servicios</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/estilos.css"> <!-- Agrega esta línea para incluir el CSS -->
    <link rel="stylesheet" type="text/css" href="../assets/css/tarjeta.css"> <!-- Agrega esta línea para incluir el CSS -->



</head>

<body>

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>Servicios relacionados con la dirección y la coordinación</h2>
                <div class="servicio-container">
                    <?php
                    // Conectarse a la base de datos (reemplaza con tus propios detalles)
                    $servername = "localhost";
                    $username = "root";
                    $password = "";
                    $dbname = "sistemas";

                    $conn = mysqli_connect($servername, $username, $password, $dbname);

                    // Comprobar la conexión
                    if (!$conn) {
                        die("Conexión fallida: " . mysqli_connect_error());
                    }

                    // Obtener todos los servicios relacionados con la dirección y la coordinación específicas
                    $query = "SELECT * FROM servicios WHERE identificador_direccion = $identificador_direccion AND identificador_coordinacion = $identificador_coordinacion";
                    $result = mysqli_query($conn, $query);

                    // Mostrar los servicios en tarjetas de título
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<div class='category-card' style='background-image: url(" . $row['image_path'] . ");'>";
                        echo "<h3 style='margin-top: 28%;'>" . $row['Fullname_servicio'] . "</h3>";
                        echo "<div class='btn-container'>";
                        // Enlace para ver el inventario asociado al servicio
                        echo "<a href='inventario_servicio_usuario.php?identificador_servicios=" . $row['identificador_servicio'] . "' class='btn btn-secondary'>Ver el inventario</a>";

                        
                        echo "</div>";
                        echo "</div>";
                    }

                    // Cerrar la conexión
                    mysqli_close($conn);
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>