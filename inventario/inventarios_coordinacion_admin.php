<?php
// Incluye el encabezado
include('../includes/header.php');

// Verifica si el tipo de usuario está establecido en la sesión
if (!isset($_SESSION['tipo_usuario'])) {
    exit();
}

// Muestra un mensaje de notificación si está presente
if (isset($_GET['notification_message'])) {
    $notification_message = htmlspecialchars($_GET['notification_message']);
    echo "<script>alert('$notification_message');</script>";
}

// Obtiene el tipo de usuario de la sesión
$tipo_usuario = $_SESSION['tipo_usuario'];
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
                var searchTerm = $(this).val().toLowerCase();
                $('.book-row').each(function() {
                    var textToSearch = $(this).text().toLowerCase();
                    $(this).toggle(textToSearch.indexOf(searchTerm) > -1);
                });
            });

            // Verifica el ancho de la ventana y decide qué mostrar
            function toggleResponsiveDisplay() {
                var windowWidth = window.innerWidth;
                var responsive = windowWidth < 768;

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
        <input type="text" class="form-control" id="searchInput" placeholder="Buscar">
    </div>
    <div class="panel-body">
        <div class="panel-body">
            <div class="col-md-12">
                <?php
                // Incluye el archivo de conexión
                include("../includes/conexion.php");

                // Datos de conexión a la base de datos
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "sistemas";

                // Conexión a la base de datos
                $conn = mysqli_connect($servername, $username, $password, $dbname);
                if (!$conn) {
                    die("Conexión fallida: " . mysqli_connect_error());
                }

                // Verificar si el parámetro identificador_coordinacion está presente en la URL
                if (!isset($_GET['identificador_coordinacion'])) {
                    // Manejar el caso en el que el parámetro no está presente
                    echo "Identificador de coordinación no proporcionado.";
                    exit();
                }

                // Obtener el identificador de coordinación de la URL
                $identificador_coordinacion = $_GET['identificador_coordinacion'];

                // Obtener el nombre de la coordinación
                $query_coordinacion = "SELECT Fullname_coordinacion FROM coordinacion WHERE identificador_coordinacion = $identificador_coordinacion";
                $result_coordinacion = mysqli_query($conn, $query_coordinacion);
                $row_coordinacion = mysqli_fetch_assoc($result_coordinacion);
                $nombre_coordinacion = $row_coordinacion['Fullname_coordinacion'];

                // Consulta SQL para obtener los resguardos de coordinación
                $query = "SELECT rc.*, ra.* FROM respaldos_coordinacion rc 
                          LEFT JOIN resguardos_admin ra 
                          ON rc.identificador_coordinacion = ra.identificador_coordinacion 
                          WHERE rc.identificador_coordinacion = $identificador_coordinacion";
                $result = mysqli_query($conn, $query);

                // Verificar si hay resultados
                if ($result && mysqli_num_rows($result) > 0) {
                    echo "<div class='table-responsive'>";
                    echo "<table class='table table-bordered'>";
                    echo "<thead>";
                    echo "<tr>";
                    echo "<th class='responsive-hide'>Numero consecutivo</th>";
                    echo "<th class='responsive-show'>Dirección</th>";
                    echo "<th class='responsive-show'>Descripción</th>";
                    echo "<th class='responsive-hide'>Dirección</th>";
                    echo "<th class='responsive-hide'>Descripción</th>";
                    echo "<th class='responsive-show'>Ver Imagen</th>";
                    echo "<th class='responsive-hide'>Imagen</th>";
                    echo "<th class='responsive-hide'>Características Generales</th>";

                    echo "<th class='responsive-show'>Usuario Responsable</th>";
                    echo "<th class='responsive-hide'>Usuario Responsable</th>";
                    echo "<th class='responsive-show'>Servicio</th>";
                    echo "<th class='responsive-hide'>Marca</th>";
                    echo "<th class='responsive-hide'>Modelo</th>";
                    echo "<th class='responsive-hide'>No. de serie</th>";
                    echo "<th class='responsive-hide'>Color</th>";
                    echo "<th class='responsive-hide'>Observaciones</th>";
                    echo "<th class='responsive-hide'>Fecha de creación</th>";
                    echo "<th class='responsive-hide'>Imagen</th>";
                    echo "<th class='responsive-hide'>Comentarios</th>";
                    echo "<th class='responsive-show'>Acciones</th>";
                    echo "<th class='responsive-hide'>Acciones</th>";
                    echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";
                    $counter = 1;

                    // Iterar sobre los resultados
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr class='book-row'>";
                        echo "<td class='responsive-hide'>" . $row['consecutivo'] . "</td>";
                        echo "<td class='responsive-show'>" . $row['Fullname_direccion'] . "</td>";
                        echo "<td class='responsive-show'>" . $row['descripcion'] . "</td>";
                        echo "<td class='responsive-hide'>" . $row['Fullname_direccion'] . "</td>";
                        echo "<td class='responsive-hide'>" . $row['descripcion'] . "</td>";
                        echo "<td class='responsive-show'><button class='btn btn-primary show-image-btn' data-image='" . $row['Image'] . "'>Ver Imagen</button></td>";
                        echo "<td class='responsive-hide'><img src='" . $row['Image'] . "' alt='Imagen' class='book-image' id='imagenModal'></td>";
                        echo "<td class='responsive-hide'>" . $row['caracteristicas'] . "</td>";

                        if (!empty($row['identificador_usuario_admin'])) {
                            // Mostrar aquí las columnas específicas de resguardos_admin
                            echo "<td class='responsive-show'>" . $row['usuario_responsable'] . "</td>";
                            echo "<td class='responsive-hhide'>" . $row['usuario_responsable'] . "</td>";
                            echo "<td class='responsive-show'>" . $row['Fullname_servicio'] . "</td>";
                            echo "<td class='responsive-hide'>" . $row['marca'] . "</td>";
                            echo "<td class='responsive-hide'>" . $row['modelo'] . "</td>";
                            echo "<td class='responsive-hide'>" . $row['serie'] . "</td>";
                            echo "<td class='responsive-hide'>" . $row['color'] . "</td>";
                            echo "<td class='responsive-hide'>" . $row['observaciones'] . "</td>";
                            echo "<td class='responsive-hide'>" . $row['fecha_creacion'] . "</td>";
                            echo "<td class='responsive-hide'><img src='" . $row['imagen'] . "' alt='Imagen' class='book-image' id='imagenModal'></td>";
                        } else {
                            // Mostrar información específica de respaldos_coordinacion
                            echo "<td class='responsive-hide'>" . $row['usuario_responsable'] . "</td>";
                            echo "<td class='responsive-show'>" . $row['usuario_responsable'] . "</td>";

                            echo "<td class='responsive-hide'></td>";  // Puedes dejar esto en blanco si no aplica a respaldos_coordinacion
                            echo "<td class='responsive-show'>" . $row['marca'] . "</td>";
                            echo "<td class='responsive-show'>" . $row['modelo'] . "</td>";
                            echo "<td class='responsive-hide'>" . $row['serie'] . "</td>";
                            echo "<td class='responsive-hide'>" . $row['color'] . "</td>";
                            echo "<td class='responsive-hide'>" . $row['observaciones'] . "</td>";
                            echo "<td class='responsive-hide'>" . $row['fecha_creacion'] . "</td>";
                            echo "<td class='responsive-hide'></td>";  // Puedes dejar esto en blanco si no aplica a respaldos_coordinacion
                        }

                        echo "<td class='responsive-hide'>" . $row['comentarios'] . "</td>";
                        echo "<td><button class='btn btn-primary btn-edit' data-toggle='modal' data-target='#editModal' data-userid='" . $row['id'] . "' data-username='" . $row['comentarios'] . "' data-identificador='" . $row['identificador_coordinacion'] . "'>Añadir comentarios</button></td>";
                        echo "</tr>";
                        $counter++;
                    }

                    echo "</tbody>";
                    echo "</table>";
                    echo "</div>";
                } else {
                    echo "<p>No se encontraron resguardos para la $nombre_coordinacion</p>";
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

    <script>
        $(document).ready(function() {
            $('.btn-edit').click(function() {
                var userId = $(this).data('userid');
                var currentUsername = $(this).data('username');
                var identificadorCoordinacion = $(this).data('identificador');

                $('#editUserId').val(userId);
                $('#identificadorCoordinacion').val(identificadorCoordinacion);
                $('#currentUsername').text(currentUsername);

                // Abrir el modal de edición
                $('#editModal').modal('show');
            });
        });
    </script>

</body>

<div id="editModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Añadir comentarios</h4>
            </div>
            <div class="modal-body">
                <p>Nombre actual: <span id="currentUsername"></span></p>
                <form action="../editar/comentarios_coordinacion.php" method="post">
                    <input type="hidden" id="editUserId" name="userId">
                    <input type="hidden" id="identificadorCoordinacion" name="identificadorCoordinacion">
                    <label for="newUsername">Tienes comentarios:</label>
                    <input type="text" id="newUsername" name="newUsername" class="form-control" required>
                    <br>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </form>
            </div>
        </div>
    </div>
</div>

</html>