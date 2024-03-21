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
include('../includes/header.php');

// Verificar si se ha enviado el identificador de la dirección
if (!isset($_GET['identificador_direccion'])) {
    // Si no se proporciona el identificador de la dirección, redirige a alguna página de manejo de errores o a la página principal
    header('Location: index.php');
    exit();
}

// Obtener el identificador de la dirección desde la URL
$identificador_direccion = $_GET['identificador_direccion'];

// Consultar las coordinaciones relacionadas con la dirección específica
$query = "SELECT * FROM coordinacion WHERE identificador_direccion = $identificador_direccion";
$result = mysqli_query($conexion, $query);

// Consultar la información de resguardos_admin relacionada con la dirección específica
$query_resguardos_admin = "SELECT * FROM resguardos_admin WHERE identificador_direccion = $identificador_direccion";
$result_resguardos_admin = mysqli_query($conexion, $query_resguardos_admin);

// Consultar el nombre de la dirección
$query_direccion = "SELECT Fullname FROM direccion WHERE identificador = ?";
$stmt_direccion = mysqli_prepare($conexion, $query_direccion);

// Verificar si la preparación de la consulta fue exitosa
if ($stmt_direccion) {
    // Vincular el parámetro
    mysqli_stmt_bind_param($stmt_direccion, "i", $identificador_direccion);

    // Ejecutar la consulta
    mysqli_stmt_execute($stmt_direccion);

    // Obtener el resultado
    $result_direccion = mysqli_stmt_get_result($stmt_direccion);

    // Verificar si se obtuvo el resultado correctamente
    if ($result_direccion) {
        // Obtener la fila de resultado
        $row_direccion = mysqli_fetch_assoc($result_direccion);

        // Verificar si se encontró la dirección
        if ($row_direccion) {
            // Obtener el nombre de la dirección
            $nombre_direccion = $row_direccion['Fullname'];
        } else {
            // Manejar el caso en que no se encuentra la dirección
            die("No se encontró la dirección con el identificador proporcionado.");
        }
    } else {
        // Manejar el caso en que no se obtuvo el resultado correctamente
        die("Error al obtener el resultado de la consulta SQL para obtener el nombre de la dirección.");
    }
} else {
    // Manejar el caso en que la preparación de la consulta falló
    die("Error en la preparación de la consulta SQL para obtener el nombre de la dirección: " . mysqli_error($conexion));
}


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
            <div style="text-align: left;">
                <a href="../dashboard/dashboard.php" class="btn btn-primary">Regresar al Inicio</a>
            </div>

            <div class="col-md-12">
                <h2>Coordinaciones de la <?php echo $nombre_direccion; ?></h2>
                <div class="category-container">
                    <?php
                    // Mostrar las coordinaciones en tarjetas de título
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<div class='category-card' style='background-image: url(" . $row['image_path'] . ");'>";
                        echo "<h3 style='margin-top: 22%;'>" . $row['Fullname_coordinacion'] . "</h3>";

                        echo "<div class='btn-container'>";
                        // Puedes mostrar más detalles de la coordinación si es necesario
                        echo "<a href='ver_servicio.php?identificador_direccion=" . $identificador_direccion . "&identificador_coordinacion=" . $row['identificador_coordinacion'] . "' class='btn btn-primary'>Ver los servicios</a>";
                        echo "<a href='../inventario/inventarios_admin.php?identificador_direccion=" . $identificador_direccion . "&identificador_coordinacion=" . $row['identificador_coordinacion'] . "' class='btn btn-secondary'>Revisar inventario admin</a>";
                        echo "<a href='../inventario/inventarios_coordinacion_admin.php?identificador_direccion=" . $identificador_direccion . "&identificador_coordinacion=" . $row['identificador_coordinacion'] . "' class='btn btn-secondary'>Revisar inventario total</a>";


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
    <form action="../excel/importar_coordinacion.php" method="POST" enctype="multipart/form-data">
            <input type="file" name="file" accept=".xlsx, .xls, .csv" required>
            <input type="hidden" name="identificador_direccion" value="<?php echo $identificador_direccion; ?>">
            <button type="submit" class="btn btn-primary btn-import-excel btn-sm">Importar desde Excel</button>
        </form>

</body>

</html>