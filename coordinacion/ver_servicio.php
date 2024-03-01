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

// Consultar la información de la coordinación
$query_coordinacion = "SELECT Fullname_coordinacion FROM coordinacion WHERE identificador_coordinacion = ?";
$stmt_coordinacion = mysqli_prepare($conexion, $query_coordinacion);
mysqli_stmt_bind_param($stmt_coordinacion, 'i', $identificador_coordinacion);
mysqli_stmt_execute($stmt_coordinacion);
$result_coordinacion = mysqli_stmt_get_result($stmt_coordinacion);

// Verificar si se encontró la coordinación
if (mysqli_num_rows($result_coordinacion) > 0) {
    $row_coordinacion = mysqli_fetch_assoc($result_coordinacion);
    $nombre_coordinacion = $row_coordinacion['Fullname_coordinacion'];
} else {
    // Manejar la falta de resultados o redirigir a una página de error
    echo "Coordinación no encontrada";
    exit();
}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
    <title>DIF | Servicios</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/estilos.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/tarjeta.css">
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2><?php echo $nombre_coordinacion; ?></h2>
                <div class="servicio-container">
                    <?php
                    // Obtener todos los servicios relacionados con la dirección y la coordinación específicas
                    $query = "SELECT * FROM servicios WHERE identificador_direccion = $identificador_direccion AND identificador_coordinacion = $identificador_coordinacion";
                    $result = mysqli_query($conexion, $query);

                    // Mostrar los servicios en tarjetas de título
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<div class='category-card' style='background-image: url(" . $row['image_path'] . ");'>";
                        echo "<h3 style='margin-top: 28%;'>Resguardos de " . $row['Fullname_servicio'] . "</h3>";
                        echo "<div class='btn-container'>";
                        // Enlace para ver el inventario asociado al servicio
                        echo "<a href='inventario_servicio_coordinacion.php?identificador_servicios=" . $row['identificador_servicio'] . "' class='btn btn-secondary'>Ver el inventario</a>";
                        echo "</div>";
                        echo "</div>";
                    }

                    // Cerrar la conexión
                    mysqli_close($conexion);
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
