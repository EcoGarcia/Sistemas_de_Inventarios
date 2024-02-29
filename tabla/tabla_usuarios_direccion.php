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
</head>

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
                $query = "SELECT * FROM usuarios_direccion";
                $result = mysqli_query($conn, $query);

                if ($result && mysqli_num_rows($result) > 0) {
                    echo "<div class='table-responsive'>";
                    echo "<table class='table table-bordered'>";
                    echo "<thead>";
                    echo "<tr>";
                    echo "<th>Nombre del usuario</th>";
                    echo "<th>Email</th>";
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

                                    <a href="total.php">Volver a la tabla de usuarios</a>

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
                    <br>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>

                </form>
            </div>
        </div>
    </div>
</div>


</html>
