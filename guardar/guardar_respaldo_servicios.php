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
    $usuarioServicioId = $_POST["usuario_servicio"];
    $descripcion = $_POST["descripcion"];
    $caracteristicas = $_POST["caracteristicas"];
    $marca = $_POST["marca"];
    $modelo = $_POST["modelo"];
    $serie = $_POST["serie"];
    $color = $_POST["color"];
    $select_condiciones = $_POST["select_condiciones"]; // Nuevo campo de condiciones
    $factura = $_POST["factura"]; // Nuevo campo de número de factura
    $id_categoria = $_POST["id_categoria"]; // Agregado para obtener la categoría
    $observaciones = $_POST["observaciones"];

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

    // Obtener el nombre del usuario
    $queryUsuario = "SELECT Fullname FROM usuarios_servicios WHERE identificador_usuario_servicios = '$usuarioServicioId'";
    $resultUsuario = $conn->query($queryUsuario);

    if ($resultUsuario) {
        // Verificar si se encontró el usuario
        if ($resultUsuario->num_rows > 0) {
            $rowUsuario = $resultUsuario->fetch_assoc();
            $fullnameUsuario = $rowUsuario["Fullname"];
        } else {
            // Puedes manejar el caso en que el usuario no existe (por ejemplo, mostrar un mensaje de error)
            echo "Error: El usuario no existe. ID: $usuarioServicioId";
            exit();
        }
    } else {
        // Manejar el caso en que hay un error en la consulta
        echo "Error al obtener el usuario: " . $conn->error;
        exit();
    }

    // Verificar si el consecutivo ya existe
    $sqlVerificarConsecutivo = "SELECT consecutivo, usuario_responsable FROM respaldos_servicios WHERE consecutivo = '$consecutivo'";
    $resultVerificarConsecutivo = $conn->query($sqlVerificarConsecutivo);

    if ($resultVerificarConsecutivo->num_rows > 0) {
        // El consecutivo ya existe, obtener la información y mostrar una alerta
        $rowConsecutivo = $resultVerificarConsecutivo->fetch_assoc();
        $usuarioAsignado = $rowConsecutivo['usuario_responsable'];

        echo "<script>
            alert('Este resguardo con el numero consecutivo $consecutivo ya fue asignado a $usuarioAsignado');
            window.location.href = '../dashboard/dashboard.php';
        </script>";
        exit();
    }

        // Obtener el Fullname de la categoría seleccionada
        $sqlCategoria = "SELECT Fullname_categoria FROM categorias WHERE Identificador_categoria = '$id_categoria'";
        $resultCategoria = $conn->query($sqlCategoria);
    
        if ($resultCategoria === false) {
            // Handle the query error
            echo "Error: " . $conn->error;
            exit();
        }
    
        if ($resultCategoria->num_rows > 0) {
            $rowCategoria = $resultCategoria->fetch_assoc();
            $fullname_categoria = $rowCategoria['Fullname_categoria'];
        } else {
            // Manejar el caso donde no se encuentra la categoría
            echo "Error: Categoría no encontrada";
            exit();
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
    $sqlInsert = "INSERT INTO respaldos_servicios (consecutivo, identificador_direccion, fullname_direccion, identificador_coordinacion, fullname_coordinacion, identificador_usuario_servicios, identificador_servicio, fullname_servicio, descripcion, caracteristicas, marca, modelo, serie, color, observaciones, imagen, usuario_responsable, identificador_categoria, Fullname_categoria, Condiciones, Factura, Estado) VALUES ('$consecutivo', '$direccionId', '$fullnameDireccion', '$coordinacionId', '$fullnameCoordinacion', '$usuarioServicioId', '$servicioId', '$fullnameServicio', '$descripcion', '$caracteristicas', '$marca', '$modelo', '$serie', '$color', '$observaciones', '$imagenRuta', '$fullnameUsuario', '$id_categoria', '$fullname_categoria', '$select_condiciones', '$factura', 1)";

    if ($conn->query($sqlInsert) === TRUE) {
        // Notifica al usuario sobre el registro exitoso y redirige a la página correspondiente
        $notification_message = "Registro exitoso";
        echo "<script>
            if (confirm('$notification_message ¿Quieres hacer un nuevo resguardo?')) {
                window.location.href = '../resguardos/resguardos_serviicios.php';
            } else {
                window.location.href = '../dashboard/dashboard.php'; 
            }
        </script>";
    } else {
        echo "Error al guardar el respaldo de servicios: " . $conn->error;
    }
}

// Cerrar la conexión a la base de datos
$conn->close();
?>
