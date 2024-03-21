<?php
include('../includes/conexion.php');

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $consecutivo = $_POST['consecutivo'];
    $id_servicio = $_POST['id_servicio'];
    $id_categoria = $_POST['id_categoria'];
    $descripcion = $_POST['descripcion'];
    $caracteristicas = $_POST['caracteristicas'];
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $serie = $_POST['serie'];
    $color = $_POST['color'];
    $id_usuario = $_POST['id_usuario'];
    $observaciones = $_POST['observaciones'];
    $select_condiciones = $_POST['select_condiciones'];
    $factura = $_POST['factura'];
    $imagen = $_FILES['imagen']['name'];

    // Obtener el ID del registro a editar
    $id = $_POST['id'];

    // Ruta de la carpeta donde se guardarán las imágenes
    $carpeta_destino = '../areas/';

    // Obtener el nombre temporal del archivo subido
    $imagen_temporal = $_FILES['imagen']['tmp_name'];

    // Generar un nombre único para la imagen
    $nombre_imagen = uniqid('imagen_') . '_' . $_FILES['imagen']['name'];

    // Mover la imagen a la carpeta de destino
    $ruta_imagen_destino = $carpeta_destino . $nombre_imagen;
    if (move_uploaded_file($imagen_temporal, $ruta_imagen_destino)) {
        // Consulta para actualizar los datos en la tabla respaldos_coordinacion
        $sql = "UPDATE respaldos_servicios 
        SET 
            consecutivo = '$consecutivo', 
            identificador_servicio = '$id_servicio',   
            identificador_categoria = '$id_categoria',
            descripcion = '$descripcion',
            caracteristicas= '$caracteristicas',
            marca = '$marca',
            modelo = '$modelo',
            serie = '$serie',
            color = '$color',
            identificador_usuario_servicios = '$id_usuario',
            observaciones = '$observaciones',
            condiciones = '$select_condiciones',
            factura = '$factura',
            imagen = '$ruta_imagen_destino'  
        WHERE id = $id";

        if ($conexion->query($sql) === TRUE) {
            $notification_message = "Datos actualizados exitosamente.";
            echo "<script>
                alert('$notification_message');
                window.location.href = '../inventario/inventario_servicios_admin.php?identificador_servicio=$id';
            </script>";
        } else {
            echo "Error al actualizar el registro: " . $conexion->error;
        }
    } else {
        echo "Error al mover la imagen a la carpeta de destino.";
    }
    
    // Cerrar la conexión
    $conexion->close();
    exit; // Terminar el script después de procesar la solicitud POST
}

// Si el formulario no ha sido enviado,
?>
