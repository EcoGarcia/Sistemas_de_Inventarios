<?php
include('../includes/conexion.php');
include('../includes/header.php');

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $id = $_POST['id'];
    $consecutivo = $_POST['consecutivo'];
    $id_direccion = $_POST['id_direccion'];
    $id_categoria = $_POST['id_categoria'];
    $fullname_direccion = $_POST['fullname_direccion'];
    $fullname_categoria = $_POST['fullname_categoria'];
    $descripcion = $_POST['descripcion'];
    $caracteristicas = $_POST['caracteristicas'];
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $serie = $_POST['serie'];
    $color = $_POST['color'];
    $observaciones = $_POST['observaciones'];
    $select_condiciones = $_POST['select_condiciones'];
    $factura = $_POST['factura'];
    // Obtener el Fullname de la dirección seleccionada
    $sqlDireccion = "SELECT Fullname FROM direccion WHERE Identificador = '$id_direccion'";
    $resultDireccion = $conexion->query($sqlDireccion);

    if ($resultDireccion->num_rows > 0) {
        $rowDireccion = $resultDireccion->fetch_assoc();
        $fullname_direccion = $rowDireccion['Fullname'];
    } else {
        // Manejar el caso donde no se encuentra la dirección
        echo "Error: Dirección no encontrada";
        exit();
    }

    // Obtener el Fullname de la categoría seleccionada
    $sqlCategoria = "SELECT Fullname_categoria FROM categorias WHERE Identificador_categoria = '$id_categoria'";
    $resultCategoria = $conexion->query($sqlCategoria);

    if ($resultCategoria === false) {
        // Handle the query error
        echo "Error: " . $conexion->error;
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

    // Obtener el nombre del usuario responsable

    // Verificar si el consecutivo ya existe
    $sqlVerificarConsecutivo = "SELECT Consecutivo_No, usuario_responsable FROM resguardos_direccion WHERE Consecutivo_No = '$consecutivo'";
    $resultVerificarConsecutivo = $conexion->query($sqlVerificarConsecutivo);

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


    // Actualizar los datos en la base de datos
    $sql_update = "UPDATE resguardos_direccion SET 
    Consecutivo_No = '$consecutivo',
    Fullname_direccion = '$fullname_direccion', 
    Fullname_categoria = '$fullname_categoria', 
    Descripcion = '$descripcion', 
    Caracteristicas_Generales = '$caracteristicas', 
    Marca = '$marca', 
    Modelo = '$modelo', 
    No_Serie = '$serie', 
    Color = '$color', 
    Observaciones = '$observaciones', 
    Condiciones = '$select_condiciones', 
    Factura = '$factura' 
    WHERE id = $id";

if ($conexion->query($sql_update) === TRUE) {
    $notification_message = "Datos actualizados exitosamente.";
    echo "<script>
        alert('$notification_message');
        window.location.href = '../inventario/inventarios_direccion_admin.php?identificador_direccion=$id';
    </script>";
} else {
    echo "Error al actualizar el registro: " . $conexion->error;
}
    // Cerrar la conexión
    $conexion->close();
    exit; // Terminar el script después de procesar la solicitud POST
}

// Si el formulario no ha sido enviado,
