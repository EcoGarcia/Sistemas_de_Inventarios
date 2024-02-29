<?php
include('../includes/header_usuario_coordinacion.php');

if (!isset($_SESSION['tipo_usuario'])) {
    exit();
}

$identificador_usuario_coordinacion = isset($_GET['identificador_usuario_coordinacion']) ? $_GET['identificador_usuario_coordinacion'] : null;

if ($identificador_usuario_coordinacion !== null) {
    $_SESSION['identificador_usuario_coordinacion'] = $identificador_usuario_coordinacion;
} else {
    // Manejar el caso en el que el parámetro no está presente
    exit("Identificador de usuario coordinación no proporcionado");
}

$tipo_usuario = $_SESSION['tipo_usuario'];
$identificador_usuario_coordinacion_session = isset($_SESSION['identificador_usuario_coordinacion']) ? $_SESSION['identificador_usuario_coordinacion'] : null;

// Verifica si el identificador de usuario coordinación está presente en la sesión
if ($identificador_usuario_coordinacion_session !== null) {
    // Aquí puedes usar $identificador_usuario_coordinacion_session en tus consultas
} else {
    // Manejar el caso en el que el identificador de usuario coordinación no está presente en la sesión
    exit("Identificador de usuario coordinación no válido o no encontrado en la sesión.");
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DIF | Inventario</title>
    <link rel="stylesheet" href="../assets/css/tarjeta.css">
    <link rel="stylesheet" href="../assets/css/tabla.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        $(document).ready(function() {
            // Captura el evento de cambio en el campo de búsqueda
            $('#searchInput').on('input', function() {
                var searchTerm = $(this).val().toLowerCase(); // Obtiene el valor del campo de búsqueda en minúsculas
                $('.book-row').each(function() {
                    var textToSearch = $(this).text().toLowerCase(); // Obtiene el contenido de la fila en minúsculas
                    // Muestra u oculta la fila según si coincide con el término de búsqueda
                    $(this).toggle(textToSearch.indexOf(searchTerm) > -1);
                });
            });

            // Verifica el ancho de la ventana y decide qué mostrar
            function toggleResponsiveDisplay() {
                var windowWidth = window.innerWidth;
                var responsive = windowWidth < 768; // Decide aquí el ancho de ventana a partir del cual consideras que es un dispositivo móvil

                // Si es responsive, oculta algunas columnas
                if (responsive) {
                    $('.responsive-hide').hide();
                    $('.responsive-show').show();
                } else {
                    $('.responsive-hide').show();
                    $('.responsive-show').hide();
                }
            }

            // Ejecuta la función al cargar la página y al cambiar el tamaño de la ventana
            toggleResponsiveDisplay();
            $(window).resize(toggleResponsiveDisplay);
        });
    </script>
</head>

<body>
    <div class="form-group">
        <input type="text" class="form-control" id="searchInput" placeholder="Search">
    </div>
    <div class="panel-body">
        <div class="panel-body">
            <div class="col-md-12">
                <?php
                include("../includes/conexion.php");

                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "sistemas";

                $conn = mysqli_connect($servername, $username, $password, $dbname);
                if (!$conn) {
                    die("Conexión fallida: " . mysqli_connect_error());
                }

                $identificador_usuario_coordinacion = $_SESSION['identificador_usuario_coordinacion'];

                $query = "SELECT * FROM respaldos_coordinacion WHERE identificador_usuario_coordinacion = '$identificador_usuario_coordinacion'";
                $result = mysqli_query($conn, $query);

                if ($result && mysqli_num_rows($result) > 0) {
                    echo "<div class='table-responsive'>";
                    echo "<table class='table table-bordered'>";
                    echo "<thead>";
                    echo "<tr>";
                    echo "<th class='responsive-hide'>Numero consecutivo</th>";
                    echo "<th class='responsive-show'>Coordinacion</th>";
                    echo "<th class='responsive-show'>Descripción</th>";
                    echo "<th class='responsive-hide'>Coordinacion</th>";
                    echo "<th class='responsive-hide'>Descripción</th>";
                    echo "<th class='responsive-hide'>Imagen</th>";
                    echo "<th class='responsive-show'>Marca</th>";
                    echo "<th class='responsive-show'>Modelo</th>";
                    echo "<th class='responsive-hide'>Caracteristicas Generales</th>";

                    echo "<th class='responsive-hide'>Marca</th>";
                    echo "<th class='responsive-hide'>Modelo</th>";
                    echo "<th class='responsive-hide'>No. de serie</th>";
                    echo "<th class='responsive-hide'>Color</th>";
                    echo "<th class='responsive-hide'>Usuario Responsable</th>";
                    echo "<th class='responsive-hide'>Observaciones</th>";
                    echo "<th class='responsive-show'>Comentarios</th>";
                    echo "<th class='responsive-hide'>Comentarios</th>";
                    echo "<th class='responsive-hide'>Fecha de creación</th>";
                    // echo "<th class='responsive-show'>Acciones</th>";
                    // echo "<th class='responsive-hide'>Acciones</th>";
                    echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";

                    $counter = 1;
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr class='book-row'>";
                        echo "<td class='responsive-hide'>" . $row['consecutivo'] . "</td>";
                        echo "<td class='responsive-show'>" . $row['Fullname_coordinacion'] . "</td>";
                        echo "<td class='responsive-show'>" . $row['descripcion'] . "</td>";
                        echo "<td class='responsive-hide'>" . $row['Fullname_coordinacion'] . "</td>";
                        echo "<td class='responsive-hide'>" . $row['descripcion'] . "</td>";
                        echo "<td class='responsive-hide'><img src='" . $row['Image'] . "' alt='Imagen' class='book-image' id='imagenModal'></td>";
                        echo "<td class='responsive-show'><button class='btn btn-primary show-image-btn' data-image='" . $row['Image'] . "'>Ver Imagen</button></td>";

                        echo "<td class='responsive-hide'>" . $row['caracteristicas'] . "</td>";

                        echo "<td class='responsive-show'>" . $row['marca'] . "</td>";
                        echo "<td class='responsive-hide'>" . $row['marca'] . "</td>";
                        echo "<td class='responsive-show'>" . $row['modelo'] . "</td>";
                        echo "<td class='responsive-hide'>" . $row['modelo'] . "</td>";
                        echo "<td class='responsive-hide'>" . $row['serie'] . "</td>";
                        echo "<td class='responsive-hide'>" . $row['color'] . "</td>";
                        echo "<td class='responsive-hide'>" . $row['usuario_responsable'] . "</td>";
                        echo "<td class='responsive-hide'>" . $row['observaciones'] . "</td>";
                        echo "<td class='responsive-hide'>" . $row['comentarios'] . "</td>";
                        echo "<td class='responsive-hide'>" . $row['fecha_creacion'] . "</td>";


                        echo "</tr>";
                        $counter++;
                    }

                    echo "</tbody>";
                    echo "</table>";
                    echo "</div>";
                } else {
                    echo "<p>No se encontraron resguardos para la dirección seleccionada.</p>";
                }

                mysqli_close($conn);
                ?>
                <a href="../dashboard/dashboard.php">Volver al inicio</a>
                <!-- Modal para mostrar la imagen -->
                <div class="modal fade" id="imagenModalModal" tabindex="-1" role="dialog" aria-labelledby="imagenModalModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-sm">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <img src="" alt="Imagen" id="imagenModalEnModal" class="img-fluid">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>

    <script src="../assets/js/jquery-1.10.2.js"></script>
    <script src="../assets/js/bootstrap.js"></script>
    <script src="../assets/js/custom.js"></script>
    <script>
        $('.show-image-btn').click(function() {
            var imageUrl = $(this).data('image');
            $('#imagenModalEnModal').attr('src', imageUrl);
            $('#imagenModalModal').modal('show');
        });

        $('.book-image').click(function() {
            var imageUrl = $(this).attr('src');
            $('#imagenModalEnModal').attr('src', imageUrl);
            $('#imagenModalModal').modal('show');
        });
    </script>

</body>

</html>
