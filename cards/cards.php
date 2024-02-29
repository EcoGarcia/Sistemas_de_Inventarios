<?php
// Incluir el archivo header.php antes de session_start()
// Esto asegura que cualquier código en header.php, incluidas las configuraciones de sesión, se procese antes de que comience la sesión.
// include('includes/header.php');

// Verificar si el tipo de usuario está definido en la sesión
if (!isset($_SESSION['tipo_usuario'])) {
    header('Location: index.php');
    exit();
}

// Obtener el tipo de usuario desde la sesión
$tipo_usuario = $_SESSION['tipo_usuario'];
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
    <link rel="stylesheet" type="text/css" href="../assets/css/tarjeta.css"> <!-- Agrega esta línea para incluir el CSS -->
    <link rel="stylesheet" type="text/css" href="../assets/css/estilos.css"> <!-- Agrega esta línea para incluir el CSS -->

    <!-- Agregar script para vista previa de imagen -->
    <script>
        function previewImage(input, previewId) {
            var preview = document.getElementById(previewId);
            var file = input.files[0];
            var reader = new FileReader();

            reader.onload = function (e) {
                preview.style.backgroundImage = "url(" + e.target.result + ")";
            };

            reader.readAsDataURL(file);
        }
    </script>

</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>Selecciona el inventario al revisar</h2>
                <div class="category-container">
                    <?php
                    // Conectarse a la base de datos (reemplaza con tus propios detalles)
                    $servername = "localhost";
                    $username = "root";
                    $password = "";
                    $dbname = "sistemas";

                    $conn = mysqli_connect($servername, $username, $password, $dbname);

                    // Comprobar la conexión
                    if (!$conn) {
                        die("Conexión fallida: " . mysqli_connect_error());
                    }

                    // Obtener todas las categorías de la tabla categorias
                    $query = "SELECT * FROM direccion";
                    $result = mysqli_query($conn, $query);

                    // Mostrar las categorías en tarjetas de título
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<div class='category-card' style='background-image: url(" . $row['image_path'] . ");'>";
                        
                        // Agregar el formulario para cargar la imagen
                        echo "<form action='cambiar_imagen.php' method='post' enctype='multipart/form-data' class='image-form' style='position: absolute; top: 10px; right: 10px;'>";
                        echo "<input type='hidden' name='categoria_id' value='" . $row['identificador'] . "' />";
                        echo "<input type='file' name='nueva_imagen' accept='image/*' style='display: none;' onchange='previewImage(this, \"preview" . $row['identificador'] . "\");' />";
                        echo "<label for='nueva_imagen' style='cursor: pointer;'><img src='assets/cambiar-de-camara.png' alt='Cambiar Imagen'></label>";
                        echo "<input type='submit' style='display: none;' />";
                        echo "</form>";

                        // Vista previa de la imagen seleccionada
                        echo "<div id='preview" . $row['identificador'] . "' class='image-preview'></div>";

                        echo "<h3 style='margin-top: 28%;'>" . $row['Fullname'] . "</h3>";

                        // Contenedor de botones
                        echo "<div class='btn-container'>";
                        echo "<a href='../tarjeta/ver_coordinacion.php?identificador_direccion=" . $row['identificador'] . "' class='btn btn-primary'>Ver la coordinación</a>";
                        echo "<input type='hidden' name='categoria_id' value='" . $row['identificador'] . "' />";
                        echo "<a href='../inventario/inventarios_direccion_admin.php?identificador_direccion=" . $row['identificador'] . "' class='btn btn-secondary'>Ver el inventario</a>";
                        echo "<input type='hidden' name='categoria_id' value='" . $row['identificador'] . "' />";
                        echo "</div>";

                        echo "</div>";
                    }

                    // Cerrar la conexión
                    mysqli_close($conn);
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
