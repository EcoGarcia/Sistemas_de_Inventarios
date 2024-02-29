<?php
if (!isset($_SESSION['tipo_usuario'])) {
    header('Location: index.php');
    exit();
}

$tipo_usuario = isset($_SESSION['tipo_usuario']) ? $_SESSION['tipo_usuario'] : null;
$identificador_usuario_direccion = isset($_SESSION['identificador_usuario_direccion']) ? $_SESSION['identificador_usuario_direccion'] : null;
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
    <link rel="stylesheet" type="text/css" href="../assets/css/tarjeta.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/estilos.css">

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
                    if ($identificador_usuario_direccion !== null && $tipo_usuario === 'UsuarioDireccion') {
                        $servername = "localhost";
                        $username = "root";
                        $password = "";
                        $dbname = "sistemas";

                        $conn = mysqli_connect($servername, $username, $password, $dbname);

                        if (!$conn) {
                            die("Conexión fallida: " . mysqli_connect_error());
                        }

                        $query = "SELECT u.*, d.image_path FROM usuarios_direccion u
                        LEFT JOIN direccion d ON u.identificador_usuario_direccion = d.identificador
                        WHERE u.identificador_usuario_direccion = ?";
                        $stmt = $conn->prepare($query);

                        if (!$stmt) {
                            die("Error en la preparación de la consulta: " . $conn->error . "<br>Query: " . $query);
                        }

                        $stmt->bind_param("i", $identificador_usuario_direccion);

                        $stmt->execute();

                        $result = $stmt->get_result();
                        if (!$result) {
                            die("Error en la consulta: " . $stmt->error . "<br>Query: " . $query);
                        }

                        if ($row = mysqli_fetch_assoc($result)) {
                            echo "<div class='category-card' style='background-image: url(" . (isset($row['image_path']) ? $row['image_path'] : '') . ");'>";
                            echo "<h3 style='margin-top: 28%;'>" . (isset($row['Fullname_direccion']) ? $row['Fullname_direccion'] : '') . "</h3>";

                            echo "<div class='btn-container'>";
                            echo "<a href='../direccion/ver_coordinacion.php?identificador_direccion=" . $identificador_usuario_direccion  . "' class='btn btn-primary'>Ver coordinación</a>";
                            echo "<input type='hidden' name='categoria_id' value='" . (isset($row['identificador_usuario_direccion']) ? $row['identificador_usuario_direccion'] : '') . "' />";
                            echo "<a href='../direccion/inventario_direccion_usuario.php?identificador_usuario_direccion=" . (isset($row['identificador_usuario_direccion']) ? $row['identificador_usuario_direccion'] : '') . "' class='btn btn-secondary'>Ver el inventario</a>";
                            echo "<input type='hidden' name='categoria_id' value='" . (isset($row['identificador_usuario_direccion']) ? $row['identificador_usuario_direccion'] : '') . "' />";
                            echo "</div>";

                            echo "</div>";
                        } else {
                            echo "No se encontró la dirección para el usuario actual.";
                        }

                        $stmt->close();
                        mysqli_close($conn);
                    } else {
                        echo "Identificador de dirección no válido o tipo de usuario incorrecto.";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
