<?php
include('../includes/conexion.php');

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $consecutivo = $_POST['consecutivo'];
    $id_coordinacion = $_POST['id_coordinacion'];
    $fullname_categoria = $_POST['fullname_categoria']; // Corregir el nombre del campo
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
    $carpeta_destino = '../direccion/';

    // Obtener el nombre temporal del archivo subido
    $imagen_temporal = $_FILES['imagen']['tmp_name'];

    // Generar un nombre único para la imagen
    $nombre_imagen = uniqid('imagen_') . '_' . $_FILES['imagen']['name'];

    // Mover la imagen a la carpeta de destino
    $ruta_imagen_destino = $carpeta_destino . $nombre_imagen;
    if (move_uploaded_file($imagen_temporal, $ruta_imagen_destino)) {
        // Verificar si la categoría ha cambiado
        if ($_POST['fullname_categoria'] != $_POST['categoria_actual']) {
            // Si la categoría ha cambiado, obtener el nombre de la nueva categoría
            $sql_categoria = "SELECT Fullname_categoria FROM categorias WHERE Identificador_categoria = $fullname_categoria";
            $result_categoria = $conexion->query($sql_categoria);

            if ($result_categoria->num_rows > 0) {
                // Obtener el nombre de la categoría
                $row_categoria = $result_categoria->fetch_assoc();
                $fullname_categoria = $row_categoria['Fullname_categoria'];
            } else {
                echo "Error: No se encontró la categoría seleccionada.";
                exit();
            }
        } else {
            // Si la categoría no ha cambiado, conservar el valor anterior
            $fullname_categoria = $_POST['categoria_actual'];
        }

        // Consulta para obtener el nombre de la coordinación seleccionada
        $sql_coordinacion = "SELECT Fullname_coordinacion FROM coordinacion WHERE Identificador_coordinacion = $id_coordinacion";
        $result_coordinacion = $conexion->query($sql_coordinacion);

        if ($result_coordinacion->num_rows > 0) {
            // Obtener el nombre de la coordinación
            $row_coordinacion = $result_coordinacion->fetch_assoc();
            $fullname_coordinacion = $row_coordinacion['Fullname_coordinacion'];

            // Consulta para actualizar los datos en la tabla resguardos_coordinacion
            $sql = "UPDATE resguardos_admin 
                    SET 
                        consecutivo = '$consecutivo', 
                        Fullname_coordinacion = '$fullname_coordinacion',  
                        Fullname_categoria = '$fullname_categoria',  -- Utilizar el nuevo nombre de la categoría
                        descripcion = '$descripcion',
                        caracteristicas= '$caracteristicas',
                        marca = '$marca',
                        modelo = '$modelo',
                        serie = '$serie',
                        color = '$color',
                        usuario_responsable = '$id_usuario',
                        observaciones = '$observaciones',
                        Factura = '$factura',
                        Image = '$ruta_imagen_destino'  
                    WHERE id = $id";

            if ($conexion->query($sql) === TRUE) {
                $notification_message = "Datos actualizados exitosamente.";
                echo "<script>
                    alert('$notification_message');
                    window.location.href = '../inventario/inventarios_admin.php?identificador_coordinacion=$id_coordinacion';
                    </script>";
            } else {
                echo "Error al actualizar el registro: " . $conexion->error;
            }
        } else {
            echo "Error: No se encontró la coordinación seleccionada.";
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
