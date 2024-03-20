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
    $descripcion = $_POST['descripcion'];
    $caracteristicas = $_POST['caracteristicas'];
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $serie = $_POST['serie'];
    $color = $_POST['color'];
    $observaciones = $_POST['observaciones'];
    $select_condiciones = $_POST['select_condiciones'];
    $factura = $_POST['factura'];

    // Actualizar los datos en la base de datos
    $sql_update = "UPDATE resguardos_direccion SET 
                    Consecutivo_No = '$consecutivo',
                    Fullname_direccion = '$id_direccion', 
                    Fullname_categoria = '$id_categoria', 
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
