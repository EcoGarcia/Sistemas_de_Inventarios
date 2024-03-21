<?php
// Incluir el archivo header.php antes de session_start()
// Esto asegura que cualquier código en header.php, incluidas las configuraciones de sesión, se procese antes de que comience la sesión.
include('../includes/header.php');

// Verificar si el tipo de usuario está definido en la sesión
if (!isset($_SESSION['tipo_usuario'])) {
    // header('Location: index.php');
    exit();
}

// Obtener el tipo de usuario desde la sesión
$tipo_usuario = $_SESSION['tipo_usuario'];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DIF | Total de usuarios relacionados a la dirección</title>
    <!-- Incluir el archivo de estilos CSS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
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


<body>
    <div class="panel-body">
        <div class="panel-body">
            <div class="col-md-12">
                
                <?php
                include("../includes/conexion.php");

                // Conexión a la base de datos (reemplaza con tus propios detalles)
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "sistemas";

                $conn = mysqli_connect($servername, $username, $password, $dbname);
                // Comprobar la conexión
                if (!$conn) {
                    die("Conexión fallida: " . mysqli_connect_error());
                }

                // Obtener todos los usuarios de la dirección
                $query = "SELECT DISTINCT * FROM usuarios_direccion";
                $result = mysqli_query($conn, $query);

                if ($result && mysqli_num_rows($result) > 0) {
                    echo "<div class='table-responsive'>";
                    echo "<div class='d-flex justify-content-end' style='margin-top: 10px;'>";
                    echo "</div>";

                    echo "<table id='dataTable' class='table table-bordered'>";
                    echo "<thead>";
                    echo "<tr>";
                    echo "<th>Nombre del usuario</th>";
                    echo "<th>Email</th>";
                    echo "<th>Dirección</th>";
                    echo "<th>Puesto</th>";
                    echo "<th>Editar</th>";
                    echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";

                    $counter = 1;
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr class='book-row'>";
                        echo "<td>" . $row['Fullname'] . "</td>";
                        echo "<td>" . $row['EmailId'] . "</td>";
                        echo "<td>" . $row['Fullname_direccion'] . "</td>";
                        echo "<td>" . ($row['Puesto'] == 0 ? "Usuario" : "Administrador") ."</td>";
                        echo "<td><button class='btn btn-primary btn-edit' data-toggle='modal' data-target='#editModal' data-userid='" . $row['id'] . "' data-username='" . $row['Fullname'] . "'>Editar</button></td>";

                        echo "</div>";
                        echo "</div>";
                        echo "</td>";
                        echo "</tr>";
                        $counter++;
                    }

                    echo "</tbody>";
                    echo "</table>";
                    echo "</div>";
                } else {
                    echo "<p>No se encontraron usuarios de la dirección.</p>";
                }

                // Cerrar la conexión
                mysqli_close($conn);
                ?>

                <a href="../tabla/total_usuarios.php">Volver a la tabla de usuarios</a>

            </div>
        </div>
    </div>
    <br>

    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/custom.js"></script>

<!-- Agregar antes del cierre del body -->
<script>
    $(document).ready(function() {
        $('.btn-edit').click(function() {
            var userId = $(this).data('userid');
            var currentUsername = $(this).data('username');
            
            // Puedes realizar cualquier lógica adicional aquí, como mostrar el nombre actual en el modal
            
            // Establecer el ID del usuario y el nombre actual en el formulario de edición
            $('#editUserId').val(userId);
            $('#currentUsername').text(currentUsername);
            
            // Abrir el modal de edición
            $('#editModal').modal('show');
        });

        // Captura el evento de cambio en el campo de búsqueda
        $('#searchInput').on('input', function() {
            var searchTerm = $(this).val().toLowerCase(); // Obtiene el valor del campo de búsqueda en minúsculas
            $('.book-row').each(function() {
                var textToSearch = $(this).text().toLowerCase(); // Obtiene el contenido de la fila en minúsculas
                // Muestra u oculta la fila según si coincide con el término de búsqueda
                $(this).toggle(textToSearch.indexOf(searchTerm) > -1);
            });
        });
    });
</script>

    
</body>
                <!-- Agregar al final del body -->
                <div id="editModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar Nombre de Usuario</h4>
            </div>
            <div class="modal-body">
                <p>Nombre actual: <span id="currentUsername"></span></p>
                <form action="../editar/editar_usuario_direccion.php" method="post">
                    <input type="hidden" id="editUserId" name="userId">
                    <label for="newUsername">Nuevo Nombre:</label>
                    <input type="text" id="newUsername" name="newUsername" class="form-control" required>
                    <label for="newPassword">Nueva contraseña:</label>
                    <input type="password" id="newpassword" name="newPassword" class="form-control" required>
                    <br>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>

                </form>
            </div>
        </div>
    </div>
</div>


</html>
