<?php
// Incluir el archivo header.php antes de session_start()
// Esto asegura que cualquier código en header.php, incluidas las configuraciones de sesión, se procese antes de que comience la sesión.
// include('includes/header.php');

// Verificar si el tipo de usuario está definido en la sesión
if (!isset($_SESSION['tipo_usuario'])) {
    header('Location: index.php');
    exit();
}

// Obtener el identificador de usuario de servicios desde la sesión
$tipo_usuario = isset($_SESSION['tipo_usuario']) ? $_SESSION['tipo_usuario'] : null;
$identificador_usuario_servicios = isset($_SESSION['identificador_usuario_servicios']) ? $_SESSION['identificador_usuario_servicios'] : null;
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
                    // Verificar que el identificador de usuario de servicios tiene un valor válido y el tipo de usuario es UsuarioServicio
                    if ($identificador_usuario_servicios !== null && $tipo_usuario === 'UsuarioServicio') {
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

                        // Obtener la información específica del usuario de servicios
                        $query = "SELECT u.*, s.image_path FROM usuarios_servicios u
LEFT JOIN servicios s ON u.identificador_servicio = s.identificador_servicio
WHERE u.identificador_usuario_servicios = ?";

                        // Inicializar la variable $stmt
                        $stmt = $conn->prepare($query);

                        // Verificar si la preparación de la consulta fue exitosa
                        if (!$stmt) {
                            die("Error en la preparación de la consulta: " . $conn->error . "<br>Query: " . $query);
                        }

                        $stmt->bind_param("i", $identificador_usuario_servicios); // "i" indica que es un entero

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
                            echo "<h3 style='margin-top: 28%;'>" . (isset($row['Fullname_servicio']) ? $row['Fullname_servicio'] : '') . "</h3>";

                            // Contenedor de botones
                            echo "<div class='btn-container'>";
                            echo "<a href='../puesto/inventario.php?identificador_usuario_servicios=" . (isset($row['identificador_usuario_servicios']) ? $row['identificador_usuario_servicios'] : '') . "' class='btn btn-secondary'>Ver el inventario</a>";
                            echo "<input type='hidden' name='categoria_id' value='" . (isset($row['identificador_usuario_servicios']) ? $row['identificador_usuario_servicios'] : '') . "' />";
                            echo "</div>";

                            echo "</div>";
                        } else {
                            // Manejar el caso en que no se encontró el usuario de servicios para el usuario actual
                            echo "No se encontró el usuario de servicios para el usuario actual.";
                        }

                        // Cerrar la conexión y la sentencia preparada
                        $stmt->close();
                        mysqli_close($conn);
                    } else {
                        // Manejar el caso en que el identificador de usuario de servicios no es válido o el tipo de usuario no es UsuarioServicio
                        echo "Identificador de usuario de servicios no válido o tipo de usuario incorrecto.";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
