<?php
include('../includes/header_usuario_direccion.php');

$identificador_servicios = isset($_GET['identificador_servicios']) ? $_GET['identificador_servicios'] : null;

if ($identificador_servicios === null || !is_numeric($identificador_servicios)) {
    die("Identificador de coordinación no válido.");
}

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

                $identificador_servicios = $_GET['identificador_servicios'];

                $query = "SELECT * FROM respaldos_servicios WHERE identificador_servicio = ?";
                $stmt = mysqli_prepare($conexion, $query);

                // Verificar si la preparación de la consulta fue exitosa
                if ($stmt) {
                    // Asociar el parámetro
                    mysqli_stmt_bind_param($stmt, "i", $identificador_servicios);

                    // Ejecutar la consulta
                    mysqli_stmt_execute($stmt);

                    // Obtener el resultado
                    $result = mysqli_stmt_get_result($stmt);
                }
                if ($result && mysqli_num_rows($result) > 0) {
                    echo "<div class='table-responsive'>";
                    echo "<div class='d-flex justify-content-end' style='margin-top: 10px;'>";
                    echo "</div>";

                    echo "<table id='dataTable' class='table table-bordered'>";
                    echo "<thead>";
                    echo "<tr>";
                    echo "<th class='responsive-hide cell'>Numero consecutivo</th>";
                    echo "<th class='responsive-hide cell'>Descripción</th>";
                    echo "<th class='responsive-hide cell'>Imagen</th>";
                    echo "<th class='responsive-hide cell'>Categoria</th>";
                    echo "<th class='responsive-hide cell'>Marca</th>";
                    echo "<th class='responsive-hide cell'>Modelo</th>";
                    echo "<th class='responsive-hide cell'>Usuario Responsable</th>";
                    echo "<th class='responsive-hide cell'>Comentarios</th>";
                    echo "<th class='responsive-hide cell'>Numero de Factura</th>";
                    echo "<th class='responsive-hide cell'>Estado</th>";
                    echo "<th class='responsive-hide cell'>Acciones</th>";


                    echo "</tr>";

                    echo "</thead>";
                    echo "<tbody>";
                    $counter = 1;
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr class='book-row'>";

                        echo "<td data-label='Numero consecutivo' class='cell'>" . $row['Consecutivo_No'] . "</td>";
                        echo "<td data-label='Descripción' class='cell'>" . $row['Descripcion'] . "</td>";
                        echo "<td data-label='Imagen' class='cell'><img src='" . $row['Image'] . "' alt='Imagen' class='book-image'></td>";
                        echo "<td data-label='Categoria' class='cell'>" . $row['Fullname_categoria'] . "</td>";
                        echo "<td data-label='Marca' class='cell'>" . $row['Marca'] . "</td>";
                        echo "<td data-label='Modelo' class='cell'>" . $row['Modelo'] . "</td>";
                        echo "<td data-label='Usuario Responsable' class='cell'>" . $row['usuario_responsable'] . "</td>";
                        echo "<td data-label='Comentarios' class='cell'>" . $row['comentarios'] . "</td>";
                        echo "<td data-label='Numero de Factura' class='cell'>" . $row['Factura'] . "</td>";
                        echo "<td data-label='Estado' class='cell'>" . ($row['Estado'] == 1 ? 'Activo' : 'Baja') . "</td>";
                        echo "<td data-label='Acciones' class='cell'>
                                                        <a href='../funciones/PDF_individual_direccion.php?id=" . $row['id'] . "' class='btn btn-primary btn-export-pdf btn-sm'>Exportar en PDF</a>
                                                        <hr>
                                                        <button class='btn btn-primary btn-edit btn-sm' data-toggle='modal' data-target='#editModal' data-userid='" . $row['id'] . "' data-username='" . $row['comentarios'] . "' data-identificador='" . $row['identificador_direccion'] . "'>Añadir comentarios</button>
                                                        <hr>
                                                        <button class='btn btn-warning btn-cambiar-estado btn-sm' data-id='" . $row['id'] . "' data-estado='" . $row['Estado'] . "'>Cambiar Estado</button>
                                                        <hr>
                                                        <button class='btn btn-secondary btn-editar btn-sm' onclick=\"window.location.href='../editar/resguardos/resguardos_direccion.php?id=" . $row['id'] . "'\">Editar</button>
                                                    </td>";


                        echo "</tr>";
                        $counter++;
                    }

                    echo "</tbody>";
                    echo "</table>";

                    echo "</div>";
                } else {
                    echo "<p>No se encontraron resguardos para la dirección $nombre_direccion</p>";
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