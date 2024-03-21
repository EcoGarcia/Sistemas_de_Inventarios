<?php
include('../includes/conexion.php');

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $consecutivo = $_POST['consecutivo'];
    $id_direccion = $_POST['id_direccion'];
    $fullname_categoria = $_POST['id_categoria'];
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
// Obtener la categoría actual del formulario
$categoria_actual = $_POST['categoria_actual'];

// Verificar si la categoría seleccionada es diferente a la categoría actual

// Obtener el identificador de la categoría seleccionada
$id_categoria = $_POST['fullname_categoria'];

// Consultar el nombre de la categoría seleccionada
$sql_categoria = "SELECT Fullname_categoria FROM categorias WHERE Identificador_categoria = '$id_categoria'";
$result_categoria = $conexion->query($sql_categoria);

if ($result_categoria->num_rows > 0) {
    // Obtener el nombre de la categoría
    $row_categoria = $result_categoria->fetch_assoc();
    $nombre_categoria = $row_categoria['Fullname_categoria'];

    // Asignar el nombre de la categoría para ser guardado en la base de datos
    $fullname_categoria = $nombre_categoria;
} else {
    // Manejar el caso en que no se encuentre la categoría seleccionada
    echo "Error: No se encontró la categoría seleccionada.";
    exit();
}
    // Obtener el ID del registro a editar
    $id = $_POST['id'];

    // Ruta de la carpeta donde se guardarán las imágenes
    $carpeta_destino = '../direccion/';

    // Obtener el nombre temporal del archivo subido
    $imagen_temporal = $_FILES['imagen']['tmp_name'];

    // Generar un nombre único para la imagen
    $nombre_imagen = uniqid('imagen_') . '_' . $_FILES['imagen']['name'];

    // Mover la imagen a la carpeta de destino
    $ruta_imagen_destino = $carpeta_destino . $nombre_imagen;
    if (move_uploaded_file($imagen_temporal, $ruta_imagen_destino)) {
        // Consulta para actualizar los datos en la tabla resguardos_direccion
        $sql = "UPDATE resguardos_direccion 
                SET 
                    Consecutivo_No = '$consecutivo', 
                    Fullname_direccion = '$id_direccion',
                    Fullname_categoria = '$id_categoria',
                    Descripcion = '$descripcion',
                    Caracteristicas_Generales= '$caracteristicas',
                    Marca = '$marca',
                    Modelo = '$modelo',
                    No_Serie = '$serie',
                    Color = '$color',
                    usuario_responsable = '$id_usuario',
                    Observaciones = '$observaciones',
                    Factura = '$factura',
                    Image = '$ruta_imagen_destino'  -- Guardar la URL de la imagen en la columna Image
                WHERE id = $id";

        if ($conexion->query($sql) === TRUE) {
            $notification_message = "Datos actualizados exitosamente.";
            echo "<script>
                alert('$notification_message');
                window.location.href = '../inventario/inventarios_direccion_admin.php?identificador_direccion=$id_direccion';
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
