<?php
// Incluir el archivo header.php antes de session_start()
// Esto asegura que cualquier código en header.php, incluidas las configuraciones de sesión, se procese antes de que comience la sesión.
// include('includes/header.php');

// Verificar si el tipo de usuario está definido en la sesión
if (!isset($_SESSION['tipo_usuario'])) {
    header('Location: index.php');
    exit();
}

// Obtener el identificador de coordinación desde la sesión
$tipo_usuario = isset($_SESSION['tipo_usuario']) ? $_SESSION['tipo_usuario'] : null;
$identificador_usuario_coordinacion = isset($_SESSION['identificador_usuario_coordinacion']) ? $_SESSION['identificador_usuario_coordinacion'] : null;
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

            reader.onload = function(e) {
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
                    // Verificar que el identificador de coordinación tiene un valor válido y el tipo de usuario es UsuarioCoordinacion
                    if ($identificador_usuario_coordinacion !== null && $tipo_usuario === 'UsuarioCoordinacion') {
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

                        // Obtener la dirección específica del usuario actual
                        // Obtener la dirección específica del usuario actual
                        $query = "SELECT u.*, c.image_path FROM usuarios_coordinacion u
                        LEFT JOIN coordinacion c ON u.identificador_coordinacion = c.identificador_coordinacion
                        WHERE u.identificador_usuario_coordinacion = ?";
              
                        // Inicializar la variable $stmt
                        $stmt = $conn->prepare($query);

                        // Verificar si la preparación de la consulta fue exitosa
                        if (!$stmt) {
                            die("Error en la preparación de la consulta: " . $conn->error . "<br>Query: " . $query);
                        }

                        $stmt->bind_param("i", $identificador_usuario_coordinacion); // "i" indica que es un entero


                        // Ejecutar la consulta
                        $stmt->execute();

                        // Verificar si la consulta fue exitosa
                        $result = $stmt->get_result();
                        if (!$result) {
                            die("Error en la consulta: " . $stmt->error . "<br>Query: " . $query);
                        }

                        // Mostrar la información en una tarjeta de título
                        if ($row = mysqli_fetch_assoc($result)) {
                            echo "<div class='category-card' style='background-image: url(" . (isset($row['image_path']) ? $row['image_path'] : '') . ");'>";

                            // Agregar el formulario para cargar la imagen
                            echo "<form action='cambiar_imagen.php' method='post' enctype='multipart/form-data' class='image-form' style='position: absolute; top: 10px; right: 10px;'>";
                            echo "<input type='hidden' name='categoria_id' value='" . (isset($row['identificador_usuario_coordinacion']) ? $row['identificador_usuario_coordinacion'] : '') . "' />";
                            echo "<input type='file' name='nueva_imagen' accept='image/*' style='display: none;' onchange='previewImage(this, \"preview" . (isset($row['identificador_usuario_coordinacion']) ? $row['identificador_usuario_coordinacion'] : '') . "\");' />";
                            echo "<label for='nueva_imagen' style='cursor: pointer;'><img src='../assets/cambiar-de-camara.png' alt='Cambiar Imagen'></label>";
                            echo "<input type='submit' style='display: none;' />";
                            echo "</form>";

                            // Vista previa de la imagen seleccionada
                            echo "<div id='preview" . (isset($row['identificador_usuario_coordinacion']) ? $row['identificador_usuario_coordinacion'] : '') . "' class='image-preview'></div>";

                            echo "<h3 style='margin-top: 28%;'>" . (isset($row['Fullname_coordinacion']) ? $row['Fullname_coordinacion'] : '') . "</h3>";

                            // Contenedor de botones
                            echo "<div class='btn-container'>";
                            echo "<a href='../coordinacion/ver_servicio.php?identificador_usuario_coordinacion=" . (isset($row['identificador_usuario_coordinacion']) ? $row['identificador_usuario_coordinacion'] : '') . "&identificador_direccion=" . (isset($row['identificador_direccion']) ? $row['identificador_direccion'] : '') . "&identificador_coordinacion=" . (isset($row['identificador_coordinacion']) ? $row['identificador_coordinacion'] : '') . "' class='btn btn-primary'>Ver la coordinación</a>";
                            echo "<a href='../coordinacion/inventario_coordinacion_usuario.php?identificador_usuario_coordinacion=" . (isset($row['identificador_usuario_coordinacion']) ? $row['identificador_usuario_coordinacion'] : '') . "' class='btn btn-secondary'>Ver el inventario</a>";
                            echo "<input type='hidden' name='categoria_id' value='" . (isset($row['identificador_usuario_coordinacion']) ? $row['identificador_usuario_coordinacion'] : '') . "' />";
                            echo "</div>";

                            echo "</div>";
                        } else {
                            // Manejar el caso en que no se encontró la coordinación para el usuario actual
                            echo "No se encontró la coordinación para el usuario actual.";
                        }

                        // Cerrar la conexión y la sentencia preparada
                        $stmt->close();
                        mysqli_close($conn);
                    } else {
                        // Manejar el caso en que el identificador de coordinación no es válido o el tipo de usuario no es UsuarioCoordinacion
                        echo "Identificador de coordinación no válido o tipo de usuario incorrecto.";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>