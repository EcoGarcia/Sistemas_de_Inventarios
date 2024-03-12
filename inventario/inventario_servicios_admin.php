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

                // Verificar si el parámetro identificador_servicio está presente en la URL
                if (!isset($_GET['identificador_servicios'])) {
                    // Manejar el caso en el que el parámetro no está presente
                    echo "Identificador de servicio no proporcionado.";
                    exit();
                }
                // Obtener el nombre de la coordinación
                $identificador_servicios = $_GET['identificador_servicios'];
                $query_servicio = "SELECT Fullname_servicio FROM servicios WHERE identificador_servicio = $identificador_servicios";
                $result_servicio = mysqli_query($conn, $query_servicio);

                if (!$result_servicio) {
                    die("Query failed: " . mysqli_error($conn));
                }

                $row_servicio = mysqli_fetch_assoc($result_servicio);
                $nombre_servicio = $row_servicio['Fullname_servicio'];

                $query = "SELECT * FROM respaldos_servicios WHERE identificador_servicio = $identificador_servicios";
                $result = mysqli_query($conn, $query);


                $identificador_servicios = $_GET['identificador_servicios'];

                $query = "SELECT * FROM respaldos_servicios WHERE identificador_servicio = $identificador_servicios";
                $result = mysqli_query($conn, $query);

                if ($result && mysqli_num_rows($result) > 0) {
                    
                        echo "<div class='table-responsive'>";
                        echo "<div class='d-flex justify-content-end' style='margin-top: 10px;'>";
                        echo "</div>";
    
                        echo "<table id='dataTable' class='table table-bordered'>";
                        echo "<thead>";
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
                        
                        echo "<td data-label='Numero consecutivo' class='cell'>" . $row['consecutivo'] . "</td>";
                        echo "<td data-label='Descripción' class='cell'>" . $row['descripcion'] . "</td>";
                        echo "<td data-label='Imagen' class='cell'><img src='" . $row['imagen'] . "' alt='Imagen' class='book-image'></td>";
                        echo "<td data-label='Categoria' class='cell'>" . $row['Fullname_categoria'] . "</td>";
                        echo "<td data-label='Marca' class='cell'>" . $row['marca'] . "</td>";
                        echo "<td data-label='Modelo' class='cell'>" . $row['modelo'] . "</td>";
                        echo "<td data-label='Usuario Responsable' class='cell'>" . $row['usuario_responsable'] . "</td>";                        
                        echo "<td data-label='Comentarios' class='cell'>" . $row['comentarios'] . "</td>";                        
                        echo "<td data-label='Numero de Factura' class='cell'>" . $row['Factura'] . "</td>";
                        echo "<td data-label='Estado' class='cell'>" . ($row['Estado'] == 1 ? 'Activo' : 'Baja') . "</td>";
                        echo "<td data-label='Acciones' class='cell'>
                        <a href='../funciones/PDF_individual_servicio.php?id=" . $row['id'] . "' class='btn btn-primary btn-export-pdf btn-sm'>Exportar en PDF</a>
                        <hr>
                        <button class='btn btn-primary btn-edit btn-sm' data-toggle='modal' data-target='#editModal' data-userid='" . $row['id'] . "' data-username='" . $row['comentarios'] . "' data-identificador='" . $row['identificador_servicio'] . "'>Añadir comentarios</button>                        <hr>
                        <button class='btn btn-warning btn-cambiar-estado btn-sm' data-id='" . $row['id'] . "' data-estado='" . $row['Estado'] . "'>Cambiar Estado</button>
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
<a href='../funciones/PDF_All_servicio.php?identificador_servicio=<?php echo $identificador_servicios; ?>' class='btn btn-primary btn-export-pdf btn-sm'>Exportar Todo en PDF</a>
</div>

<div class="text-right mt-3">
<form action="../excel/exportar_coordinacion.php" method="POST">
    <input type="hidden" name="export" value="1">
    <button type="submit" id="btnExportExcel" class="btn btn-success btn-export-excel btn-sm">Exportar a Excel</button>
</form>
<form action="../excel/importar_coordinacion.php" method="POST" enctype="multipart/form-data">
<input type="file" name="file" accept=".xlsx, .xls, .csv" required>
    <input type="hidden" name="identificador_servicios" value="<?php echo $identificador_servicios; ?>">
    <button type="submit" class="btn btn-primary btn-import-excel btn-sm">Importar desde Excel</button>
</form>
</div>

                <a href="../../tarjeta/ver_servicio.php">Volver al inicio</a>
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
                var identificadorServicio = $(this).data('identificador');

                $('#editUserId').val(userId);
                $('#identificadorServicio').val(identificadorServicio);
                $('#currentUsername').text(currentUsername);

                // Abrir el modal de edición
                $('#editModal').modal('show');
            });
        });
    </script>
        <script>
  $(document).ready(function() {
    $('.btn-cambiar-estado').click(function() {
      var id = $(this).data('id');
      var estado = $(this).data('estado');
      
      // Realiza una solicitud AJAX para cambiar el estado en el servidor
      $.ajax({
        type: 'POST',
        url: '../editar/cambiar_estado_servicio.php', // Ajusta la ruta al archivo que maneja la actualización del estado
        data: { id: id, estado: estado },
        success: function(response) {
          // Maneja la respuesta del servidor (puede mostrar un mensaje de éxito o actualizar la interfaz de usuario)
          alert(response);
          location.reload(); // Recarga la página después de cambiar el estado (puedes implementar una actualización más sofisticada)
        },
        error: function(error) {
          console.error('Error al cambiar el estado:', error);
        }
      });
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
                    <form action="../editar/comentarios_servicios.php" method="post">
                        <input type="hidden" id="editUserId" name="userId">
                        <input type="hidden" id="identificadorServicio" name="identificadorServicio">
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