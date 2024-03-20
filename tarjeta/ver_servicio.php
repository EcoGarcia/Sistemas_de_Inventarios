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

// Verificar si se han enviado los identificadores de dirección y coordinación
if (!isset($_GET['identificador_coordinacion'])) {
    // Si no se proporciona el identificador de coordinación, redirige a alguna página de manejo de errores o a la página principal
    exit();
}

// Obtener el identificador de coordinación desde la URL
$identificador_coordinacion = $_GET['identificador_coordinacion'];

// Consultar el nombre de la coordinación
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

// Mueve la inclusión del archivo header.php antes de session_start()
// include('includes/header.php');

include('../includes/header.php');
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
                <h2><?php echo $nombre_coordinacion; ?></h2>
                <div class="servicio-container">
                    <?php
                    // Obtener todos los servicios relacionados con la coordinación específica
                    $query = "SELECT * FROM servicios WHERE identificador_coordinacion = ?";
                    $stmt_servicios = mysqli_prepare($conexion, $query);
                    mysqli_stmt_bind_param($stmt_servicios, 'i', $identificador_coordinacion);
                    mysqli_stmt_execute($stmt_servicios);
                    $result_servicios = mysqli_stmt_get_result($stmt_servicios);

                    // Mostrar los servicios en tarjetas de título
                    while ($row = mysqli_fetch_assoc($result_servicios)) {
                        echo "<div class='category-card' style='background-image: url(" . $row['image_path'] . ");'>";
                        echo "<h3 style='margin-top: 28%;'>Resguardos de " . $row['Fullname_servicio'] . "</h3>";
                        echo "<div class='btn-container'>";
                        // Enlace para ver el inventario asociado al servicio
                        echo "<a href='../inventario/inventario_servicios_admin.php?identificador_servicios=" . $row['identificador_servicio'] . "' class='btn btn-secondary'>Ver el inventario</a>";
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
    <form action="../excel/importar_servicio.php" method="POST" enctype="multipart/form-data">
    <input type="file" name="file" accept=".xlsx, .xls, .csv" required>
    <input type="hidden" name="identificador_direccion" value="<?php echo $identificador_direccion; ?>">
    <input type="hidden" name="identificador_coordinacion" value="<?php echo $identificador_coordinacion; ?>"> <!-- Agregado: campo oculto para identificador de coordinación -->
    <button type="submit" class="btn btn-primary btn-import-excel btn-sm">Importar desde Excel</button>
</form>
<a href="../tarjeta/ver_coordinacion.php?identificador_direccion=<?php echo $identificador_coordinacion; ?>" class="btn btn-primary">Regresar a las coordinaciones</a>

</body>

</html>
