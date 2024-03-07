<?php
include('../includes/header.php');

if (!isset($_SESSION['tipo_usuario'])) {
    exit();
}
if (isset($_GET['notification_message'])) {
    $notification_message = htmlspecialchars($_GET['notification_message']);
    echo "<script>alert('$notification_message');</script>";
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#searchInput').on('input', function() {
            var searchTerm = $(this).val().toLowerCase();
            $('.book-row').each(function() {
                var textToSearch = $(this).text().toLowerCase();
                $(this).toggle(textToSearch.indexOf(searchTerm) > -1);
            });
        });

        function toggleResponsiveDisplay() {
            var windowWidth = window.innerWidth;
            var responsive = windowWidth < 768;

            if (responsive) {
                $('.responsive-hide').hide();
                $('.responsive-show').show();
            } else {
                $('.responsive-hide, .responsive-show').show();
            }
        }

        toggleResponsiveDisplay();
        $(window).resize(toggleResponsiveDisplay);

        // Destruir DataTables antes de volver a inicializar
        if ($.fn.DataTable.isDataTable('#dataTable')) {
            $('#dataTable').DataTable().destroy();
        }

        // Inicializar DataTables con configuración básica
        $('#dataTable').DataTable({
            paging: true,
            ordering: false,
            info: true,
            searching: true,
            "language": {
              "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json" // Carga la configuración en español
            }
        });

    });
</script>
    </script>
</head>

<body>
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

                $identificador_direccion = $_GET['identificador_direccion'];

                $query_direccion = "SELECT Fullname FROM direccion WHERE identificador = $identificador_direccion";
                $result_direccion = mysqli_query($conexion, $query_direccion);
                $row_direccion = mysqli_fetch_assoc($result_direccion);
                $nombre_direccion = $row_direccion['Fullname'];

                $query = "SELECT * FROM resguardos_direccion WHERE identificador_direccion = $identificador_direccion";
                $result = mysqli_query($conn, $query);

                if ($result && mysqli_num_rows($result) > 0) {
                    echo "<div class='table-responsive'>";
                    echo "<div class='d-flex justify-content-end' style='margin-top: 10px;'>";
                    echo "</div>";

                    echo "<table id='dataTable' class='table table-bordered'>";
                    echo "<thead>";
                    echo "<tr>";
                    echo "<th class='responsive-hide cell'>ID</th>";
                    echo "<th class='responsive-hide cell'>Numero consecutivo</th>";
                    echo "<th class='responsive-hide cell'>Descripción</th>";
                    echo "<th class='responsive-hide cell'>Imagen</th>";
                    echo "<th class='responsive-hide cell'>Categoria</th>";
                    echo "<th class='responsive-hide cell'>Marca</th>";
                    echo "<th class='responsive-hide cell'>Modelo</th>";
                    echo "<th class='responsive-hide cell'>Usuario Responsable</th>";
                    echo "<th class='responsive-hide cell'>Numero de Factura</th>";
                    echo "<th class='responsive-hide cell'>Acciones</th>";


                    echo "</tr>";
                    
                    echo "</thead>";
                    echo "<tbody>";
                    $counter = 1;
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr class='book-row'>";
                        echo "<td data-label='ID' class='cell'>" . $counter . "</td>"; // Agregado data-label
                        echo "<td data-label='Numero consecutivo' class='cell'>" . $row['Consecutivo_No'] . "</td>";
                        echo "<td data-label='Descripción' class='cell'>" . $row['Descripcion'] . "</td>";
                        echo "<td data-label='Imagen' class='cell'><img src='" . $row['Image'] . "' alt='Imagen' class='book-image'></td>";
                        echo "<td data-label='Categoria' class='cell'>" . $row['Fullname_categoria'] . "</td>";
                        echo "<td data-label='Marca' class='cell'>" . $row['Marca'] . "</td>";
                        echo "<td data-label='Modelo' class='cell'>" . $row['Modelo'] . "</td>";
                        echo "<td data-label='Usuario Responsable' class='cell'>" . $row['usuario_responsable'] . "</td>";
                        echo "<td data-label='Numero de Factura' class='cell'>" . $row['Factura'] . "</td>";
                        echo "<td data-label='Acciones' class='cell'>
                                <hr>
                                <a href='../funciones/PDF_individual_direccion.php?consecutivo=" . $row['Consecutivo_No'] . "' class='btn btn-primary btn-export-pdf btn-sm'>Exportar en PDF</a>
                                <hr>
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

                <div class="text-right mt-3">
                    <a href='../funciones/PDF_All_direccion.php?identificador_direccion=<?php echo $identificador_direccion; ?>' class='btn btn-primary btn-export-pdf btn-sm'>Exportar Todo en PDF</a>
                </div>

                <a href="../dashboard/dashboard.php">Volver al inicio</a>

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
                var identificadorDireccion = $(this).data('identificador');

                $('#editUserId').val(userId);
                $('#identificadorDireccion').val(identificadorDireccion);
                $('#currentUsername').text(currentUsername);

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
                <form action="../editar/comentarios.php" method="post">
                    <input type="hidden" id="editUserId" name="userId">
                    <input type="hidden" id="identificadorDireccion" name="identificadorDireccion">
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
