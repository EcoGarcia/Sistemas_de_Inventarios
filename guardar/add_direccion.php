<?php
// Iniciar la sesión
session_start();

// Verificar si el tipo de usuario está definido en la sesión
if (!isset($_SESSION['tipo_usuario'])) {
    // Redirigir a la página de inicio si no hay tipo de usuario definido
    header('Location: index.php');
    exit();
}

// Obtener el tipo de usuario desde la sesión
$tipo_usuario = $_SESSION['tipo_usuario'];

// Incluir el archivo de conexión a la base de datos
include('../includes/conexion.php');

// Verificar si se ha enviado el formulario
if (isset($_POST['category_name'])) {
    // Obtener el nombre de la categoría desde el formulario
    $category_name = $_POST['category_name'];

    // Verificar si ya existe un registro con el mismo nombre
    $query_check_duplicate = "SELECT COUNT(*) as count FROM direccion WHERE FullName = ?";
    $stmt_check_duplicate = mysqli_prepare($conexion, $query_check_duplicate);
    mysqli_stmt_bind_param($stmt_check_duplicate, 's', $category_name);
    mysqli_stmt_execute($stmt_check_duplicate);
    $result_check_duplicate = mysqli_stmt_get_result($stmt_check_duplicate);
    $row_check_duplicate = mysqli_fetch_assoc($result_check_duplicate);

    // Verificar si ya existe un registro con el mismo nombre
    if ($row_check_duplicate['count'] > 0) {
        echo "<script>
        var mensaje_notificacion = 'La dirección \'$category_name\' ya se encuentra agregado con anterioridad. Revise si esta bien escrito o intente con uno nuevo.';
        alert(mensaje_notificacion);
        window.location.href = '../dashboard/dashboard.php';
      </script>";
        exit(); // Detener la ejecución del script después de la redirección
    }

    // Obtener el siguiente identificador disponible para la categoría
    $query_max_id = "SELECT MAX(identificador) AS max_id FROM direccion";
    $result_max_id = mysqli_query($conexion, $query_max_id);

    // Verificar si la consulta fue exitosa
    if (!$result_max_id) {
        echo "Error in query: " . mysqli_error($conexion);
        // Manejar el error, redirigir o salir del script
    } else {
        $row_max_id = mysqli_fetch_assoc($result_max_id);
        $next_identifier = $row_max_id['max_id'] + 1;

        // Procesar la imagen
        $image_name = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_path = "../imagenes/" . $image_name;

        move_uploaded_file($image_tmp, $image_path);
        
        // Insertar la categoría en la base de datos con el identificador asignado
        $query_insert = "INSERT INTO direccion (FullName, identificador, image_path) VALUES (?, ?, ?)";
        $stmt_insert = mysqli_prepare($conexion, $query_insert);
        mysqli_stmt_bind_param($stmt_insert, 'sis', $category_name, $next_identifier, $image_path);
        $result_insert = mysqli_stmt_execute($stmt_insert);

        // Verificar si la inserción fue exitosa
        if ($result_insert) {
            $mensaje_notificacion = "Registro insertado correctamente. Nombre de la dirección: " . $category_name;
            echo "<script>
                alert('$mensaje_notificacion');
                window.location.href = '../dashboard/dashboard.php';
            </script>";
            exit(); // Detener la ejecución del script después de la redirección
        } else {
            echo "Error al agregar la categoría: " . mysqli_error($conexion);
        }
    }
}
?>
