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

// Verificar si se ha enviado el identificador de la dirección desde la URL
$identificador_direccion = isset($_GET['identificador_direccion']) ? $_GET['identificador_direccion'] : null;

// Verificar si el identificador de la dirección es válido
if ($identificador_direccion === null || !is_numeric($identificador_direccion)) {
    die("Identificador de dirección no válido.");
}

// Consultar las coordinaciones relacionadas con la dirección específica
$query = "SELECT * FROM coordinacion WHERE identificador_direccion = ?";
$stmt = mysqli_prepare($conexion, $query);

// Verificar si la preparación de la consulta fue exitosa
if ($stmt) {
    // Asociar el parámetro
    mysqli_stmt_bind_param($stmt, "i", $identificador_direccion);

    // Ejecutar la consulta
    mysqli_stmt_execute($stmt);

    // Obtener el resultado
    $result = mysqli_stmt_get_result($stmt);
?>

    <!DOCTYPE html>
    <html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
        <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
        <title>DIF | Coordinaciones</title>
        <link rel="stylesheet" type="text/css" href="../assets/css/tarjeta.css"> <!-- Agrega esta línea para incluir el CSS -->
        <link rel="stylesheet" type="text/css" href="../assets/css/estilos.css"> <!-- Agrega esta línea para incluir el CSS -->
    </head>

    <body>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2>Selecciona la coordinación al revisar</h2>
                    <div class="category-container">
                        <?php
                        // Mostrar las coordinaciones en tarjetas de título
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<div class='category-card' style='background-image: url(" . $row['image_path'] . ");'>";
                            echo "<h3 style='margin-top: 22%;'>" . $row['Fullname_coordinacion'] . "</h3>";

                            echo "<div class='btn-container'>";
                            // Puedes mostrar más detalles de la coordinación si es necesario
                            echo "<a href='ver_servicio.php?identificador_direccion=" . $identificador_direccion . "&identificador_coordinacion=" . $row['identificador_coordinacion'] . "' class='btn btn-primary'>Ver los servicios</a>";
                            echo "<a href='../direccion/inventarios_coordinacion_usuario.php?identificador_direccion=" . $identificador_direccion . "&identificador_coordinacion=" . $row['identificador_coordinacion'] . "' class='btn btn-secondary'>Ver el inventario</a>";

                            echo "<input type='hidden' name='categoria_id' value='" . $identificador_direccion . "' />";
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
<?php
} else {
    die("Error en la preparación de la consulta SQL: " . mysqli_error($conexion));
}

?>