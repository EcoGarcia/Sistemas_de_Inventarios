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
    // Recuperar los valores del formulario
    $consecutivo = $_POST["consecutivo"];
    $direccionId = $_POST["fullname_direccion"];
    $coordinacionId = $_POST["coordinacion_existente"];
    $usuarioCoordinacionId = $_POST["usuario_coordinacion"];
    $descripcion = $_POST["descripcion"];
    $caracteristicas = $_POST["caracteristicas"];
    $marca = $_POST["marca"];
    $modelo = $_POST["modelo"];
    $serie = $_POST["serie"];
    $color = $_POST["color"];
    $select_condiciones = $_POST["select_condiciones"]; // Nuevo campo de condiciones
    $factura = $_POST["factura"]; // Nuevo campo de número de factura
    $observaciones = $_POST["observaciones"];
    $imagenRuta = ''; // La ruta de la imagen se establecerá después de procesar la carga de la imagen

    // Validar que todos los campos obligatorios estén llenos
    if (
        empty($consecutivo) ||
        empty($direccionId) ||
        empty($coordinacionId) ||
        empty($usuarioCoordinacionId) ||
        empty($descripcion) ||
        empty($caracteristicas) ||
        empty($marca) ||
        empty($modelo) ||
        empty($serie) ||
        empty($color) ||
        empty($observaciones)
    ) {
        echo "<script>
                alert('Por favor, rellena todos los campos obligatorios.');
                window.history.back();
              </script>";
        exit();
    }

    // Obtener el nombre de la dirección seleccionada
    $queryDireccion = "SELECT Fullname FROM direccion WHERE id = '$direccionId'";
    $resultDireccion = $conn->query($queryDireccion);

    if ($resultDireccion->num_rows > 0) {
        $rowDireccion = $resultDireccion->fetch_assoc();
        $fullnameDireccion = $rowDireccion["Fullname"];
    } else {
        $fullnameDireccion = '';  // Puedes manejar un valor predeterminado o mostrar un error
    }


    // Obtener el nombre de la coordinación seleccionada
    $queryCoordinacion = "SELECT Fullname_coordinacion FROM coordinacion WHERE identificador_coordinacion = ?";
    $stmtCoordinacion = $conn->prepare($queryCoordinacion);
    $stmtCoordinacion->bind_param("s", $coordinacionId);
    $stmtCoordinacion->execute();
    $stmtCoordinacion->store_result();

    if ($stmtCoordinacion->num_rows > 0) {
        $stmtCoordinacion->bind_result($fullnameCoordinacion);
        $stmtCoordinacion->fetch();
    } else {
        $fullnameCoordinacion = '';  // Puedes manejar un valor predeterminado o mostrar un error
    }

    $stmtCoordinacion->close();
    // Obtener el nombre del usuario de coordinación seleccionado
    $queryUsuarioCoordinacion = "SELECT Fullname FROM usuarios_coordinacion WHERE identificador_usuario_coordinacion = '$usuarioCoordinacionId'";
    $resultUsuarioCoordinacion = $conn->query($queryUsuarioCoordinacion);

    if ($resultUsuarioCoordinacion->num_rows > 0) {
        $rowUsuarioCoordinacion = $resultUsuarioCoordinacion->fetch_assoc();
        $fullnameUsuarioCoordinacion = $rowUsuarioCoordinacion["Fullname"];
    } else {
        // Manejar el caso en que el usuario responsable no existe
        echo "<script>
            alert('Error: El usuario responsable no existe en la tabla usuarios_coordinacion.');
            window.history.back();
          </script>";
        exit();
    }

    // Obtener el Fullname de la categoría seleccionada
    $id_categoria = $_POST["id_categoria"];
    $sqlCategoria = "SELECT Fullname_categoria FROM categorias WHERE Identificador_categoria = '$id_categoria'";
    $resultCategoria = $conn->query($sqlCategoria);

    if ($resultCategoria === false) {
        // Handle the query error
        echo "Error: " . $conn->error;
        exit();
    }

    if ($resultCategoria->num_rows > 0) {
        $rowCategoria = $resultCategoria->fetch_assoc();
        $fullnameCategoria = $rowCategoria['Fullname_categoria'];
    } else {
        // Manejar el caso donde no se encuentra la categoría
        echo "Error: Categoría no encontrada";
        exit();
    }

    // Verificar si el consecutivo ya existe
    $sqlVerificarConsecutivo = "SELECT consecutivo, usuario_responsable FROM respaldos_coordinacion WHERE consecutivo = '$consecutivo'";
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

    // Manejar la carga de la imagen
    $imagenNombre = isset($_FILES["imagen"]["name"]) ? $_FILES["imagen"]["name"] : '';
    $imagenTemp = isset($_FILES["imagen"]["tmp_name"]) ? $_FILES["imagen"]["tmp_name"] : '';
    $imagenRuta = "../areas/" . $imagenNombre; // Carpeta "areas" en la que se guardará la imagen

    // Mover la imagen a la carpeta "areas" si se ha subido una
    if (!empty($imagenNombre) && !empty($imagenTemp)) {
        move_uploaded_file($imagenTemp, $imagenRuta);
    }

    // Obtener la fecha actual en el formato de MySQL
    $fechaCreacion = date("Y-m-d H:i:s");

    // Ejemplo de inserción en la tabla de respaldos de coordinación
    $sqlInsert = "INSERT INTO respaldos_coordinacion (
        consecutivo, identificador_direccion, fullname_direccion, 
        identificador_coordinacion, fullname_coordinacion, 
        identificador_usuario_coordinacion, usuario_responsable, descripcion, 
        caracteristicas, marca, modelo, serie, color, 
        observaciones, fecha_creacion, Image, identificador_categoria, fullname_categoria, Condiciones, Factura
    ) VALUES (
        '$consecutivo', '$direccionId', '$fullnameDireccion', 
        '$coordinacionId', '$fullnameCoordinacion', 
        '$usuarioCoordinacionId', '$fullnameUsuarioCoordinacion', '$descripcion', 
        '$caracteristicas', '$marca', '$modelo', '$serie', '$color', 
        '$observaciones', '$fechaCreacion', '$imagenRuta', '$id_categoria', '$fullnameCategoria', '$select_condiciones', '$factura')";

    if ($conn->query($sqlInsert) === TRUE) {
        // Notifica al usuario sobre el registro exitoso y redirige a la página correspondiente
        $notification_message = "Registro exitoso";
        echo "<script>
            alert('$notification_message');
            window.location.href = '../dashboard/dashboard.php';
        </script>";
    } else {
        echo "Error al guardar el respaldo de coordinación: " . $conn->error;
    }
}

// Cerrar la conexión a la base de datos
$conn->close();
?>
