<?php
session_start();

if (!isset($_SESSION['tipo_usuario'])) {
    header('Location: index.php');
    exit();
}

include('../includes/conexion.php');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sistemas";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Procesar el formulario cuando se envía (método POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $consecutivo = $_POST["consecutivo"];
    $direccionId = $_POST["fullname_direccion"];
    $coordinacionId = $_POST["coordinacion_existente"];
    $servicioId = $_POST["servicios"];
    $usuarioServicioId = $_POST["usuario_admin"];
    $descripcion = $_POST["descripcion"];
    $caracteristicas = $_POST["caracteristicas"];
    $marca = $_POST["marca"];
    $modelo = $_POST["modelo"];
    $serie = $_POST["serie"];
    $color = $_POST["color"];
    $observaciones = $_POST["observaciones"];

    // Obtener el nombre del administrador seleccionado
    $queryUsuario = "SELECT Fullname FROM administrador WHERE identificador_administrador_coordinacion = '$usuarioServicioId'";
    $resultUsuario = $conn->query($queryUsuario);

    if ($resultUsuario->num_rows > 0) {
        $rowUsuario = $resultUsuario->fetch_assoc();
        $fullnameUsuario = $rowUsuario["Fullname"];
    } else {
        $fullnameUsuario = '';  // Puedes manejar un valor predeterminado o mostrar un error
    }

    // Obtener el nombre de la dirección seleccionada
    $queryDireccion = "SELECT Fullname FROM direccion WHERE identificador = '$direccionId'";
    $resultDireccion = $conn->query($queryDireccion);

    if ($resultDireccion->num_rows > 0) {
        $rowDireccion = $resultDireccion->fetch_assoc();
        $fullnameDireccion = $rowDireccion["Fullname"];
    } else {
        $fullnameDireccion = '';  // Puedes manejar un valor predeterminado o mostrar un error
    }

    // Obtener el nombre de la coordinación seleccionada
    $queryCoordinacion = "SELECT Fullname_coordinacion FROM coordinacion WHERE identificador_coordinacion = '$coordinacionId'";
    $resultCoordinacion = $conn->query($queryCoordinacion);

    if ($resultCoordinacion->num_rows > 0) {
        $rowCoordinacion = $resultCoordinacion->fetch_assoc();
        $fullnameCoordinacion = $rowCoordinacion["Fullname_coordinacion"];
    } else {
        $fullnameCoordinacion = '';  // Puedes manejar un valor predeterminado o mostrar un error
    }

    // Obtener el nombre del servicio seleccionado
    $queryServicio = "SELECT Fullname_servicio FROM servicios WHERE identificador_servicio = '$servicioId'";
    $resultServicio = $conn->query($queryServicio);

    if ($resultServicio->num_rows > 0) {
        $rowServicio = $resultServicio->fetch_assoc();
        $fullnameServicio = $rowServicio["Fullname_servicio"];
    } else {
        $fullnameServicio = '';  // Puedes manejar un valor predeterminado o mostrar un error
    }

    // Manejar la carga de la imagen
    $imagenNombre = isset($_FILES["imagen"]["name"]) ? $_FILES["imagen"]["name"] : '';
    $imagenTemp = isset($_FILES["imagen"]["tmp_name"]) ? $_FILES["imagen"]["tmp_name"] : '';
    $imagenRuta = "../areas/" . $imagenNombre; // Carpeta "areas" en la que se guardará la imagen

    // Mover la imagen a la carpeta "areas" si se ha subido una
    if (!empty($imagenNombre) && !empty($imagenTemp)) {
        move_uploaded_file($imagenTemp, $imagenRuta);
    }

    // Puedes continuar con la lógica para procesar y guardar los datos en la base de datos
    // Ejemplo de inserción en la tabla resguardos_admin
    $sqlInsert = "INSERT INTO resguardos_admin (consecutivo, identificador_direccion, fullname_direccion, identificador_coordinacion, fullname_coordinacion, identificador_usuario_admin, identificador_servicio, fullname_servicio, descripcion, caracteristicas, marca, modelo, serie, color, observaciones, imagen, usuario_responsable, Estado) VALUES ('$consecutivo', '$direccionId', '$fullnameDireccion', '$coordinacionId', '$fullnameCoordinacion', '$usuarioServicioId', '$servicioId', '$fullnameServicio', '$descripcion', '$caracteristicas', '$marca', '$modelo', '$serie', '$color', '$observaciones', '$imagenRuta', '$fullnameUsuario', 1)";

    if ($conn->query($sqlInsert) === TRUE) {
        // Notifica al usuario sobre el registro exitoso y redirige a la página correspondiente
        $notification_message = "Registro exitoso";
        echo "<script>
            alert('$notification_message');
            window.location.href = '../dashboard/dashboard.php';
        </script>";
    } else {
        echo "Error al guardar el respaldo de servicios: " . $conn->error;
    }
}

// Cerrar la conexión a la base de datos
$conn->close();
?>
