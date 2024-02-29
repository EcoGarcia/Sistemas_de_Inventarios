<?php
include('../includes/conexion.php');

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Conexión a la base de datos
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "sistemas";

    // Crear conexión
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificar la conexión
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Obtener datos del formulario
    $consecutivo = $_POST["consecutivo"];
    $id_direccion = $_POST["id_direccion"];
    $descripcion = $_POST["descripcion"];
    $caracteristicas = $_POST["caracteristicas"];
    $marca = $_POST["marca"];
    $modelo = $_POST["modelo"];
    $serie = $_POST["serie"];
    $color = $_POST["color"];
    $id_usuario = $_POST["id_usuario"];
    $observaciones = $_POST["observaciones"];

    // Obtener el Fullname de la dirección seleccionada
    $sqlDireccion = "SELECT Fullname FROM direccion WHERE Identificador = '$id_direccion'";
    $resultDireccion = $conn->query($sqlDireccion);

    if ($resultDireccion->num_rows > 0) {
        $rowDireccion = $resultDireccion->fetch_assoc();
        $fullname_direccion = $rowDireccion['Fullname'];
    } else {
        // Manejar el caso donde no se encuentra la dirección
        echo "Error: Dirección no encontrada";
        exit();
    }

    // Obtener el nombre del usuario responsable
    $sqlUsuario = "SELECT Fullname FROM usuarios_direccion WHERE Identificador_usuario_direccion = '$id_usuario'";
    $resultUsuario = $conn->query($sqlUsuario);

    if ($resultUsuario->num_rows > 0) {
        $rowUsuario = $resultUsuario->fetch_assoc();
        $usuario_responsable = $rowUsuario['Fullname'];
    } else {
        // Manejar el caso donde no se encuentra el usuario
        echo "Error: Usuario no encontrado";
        exit();
    }

    // Verificar si el consecutivo ya existe
    $sqlVerificarConsecutivo = "SELECT Consecutivo_No, usuario_responsable FROM resguardos_direccion WHERE Consecutivo_No = '$consecutivo'";
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
    
    // Insertar datos en la tabla resguardos_direccion
    $sql = "INSERT INTO resguardos_direccion (Consecutivo_No, identificador_direccion, Fullname_direccion, Descripcion, Caracteristicas_Generales, Marca, Modelo, No_Serie, Color, Image, usuario_responsable, Identificador_usuario_direccion, Observaciones)
            VALUES ('$consecutivo', '$id_direccion', '$fullname_direccion', '$descripcion', '$caracteristicas', '$marca', '$modelo', '$serie', '$color', '$imagenRuta', '$usuario_responsable', '$id_usuario', '$observaciones')";

    if ($conn->query($sql) === TRUE) {
        // Notifica al usuario sobre el registro exitoso y redirige a la página correspondiente
        $notification_message = "Registro exitoso";
        echo "<script>
            alert('$notification_message');
            window.location.href = '../dashboard/dashboard.php';
        </script>";
    } else {
        // Muestra un mensaje de error si falla el registro del usuario
        echo "Error al registrar el usuario: " . $conn->error;
    }

    // Cerrar la conexión
    $conn->close();
}
?>
