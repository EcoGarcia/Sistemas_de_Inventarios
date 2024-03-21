<?php
// Incluir el archivo de conexión a la base de datos si es necesario
include('../includes/conexion.php');

// Verificar si se ha proporcionado un ID de dirección en la solicitud
if (isset($_GET['direccion'])) {
    // Obtener el ID de dirección proporcionado
    $id_direccion = $_GET['direccion'];

    // Consulta SQL para obtener las coordinaciones según la dirección seleccionada
    $sql = "SELECT identificador_coordinacion, Fullname_coordinacion FROM coordinacion WHERE identificador_direccion = $id_direccion";

    // Ejecutar la consulta
    $result = $conn->query($sql);

    // Verificar si se encontraron resultados
    if ($result->num_rows > 0) {
        // Inicializar una cadena para almacenar las opciones de las coordinaciones
        $options = "<option value='' disabled selected>Selecciona una coordinación</option>";

        // Recorrer los resultados y construir las opciones de las coordinaciones
        while ($row = $result->fetch_assoc()) {
            $options .= "<option value='" . $row['identificador_coordinacion'] . "'>" . $row['Fullname_coordinacion'] . "</option>";
        }

        // Devolver las opciones de las coordinaciones como respuesta
        echo $options;
    } else {
        // Si no se encuentran coordinaciones, devolver un mensaje de error
        echo "<option value='' disabled>No se encontraron coordinaciones</option>";
    }
} else {
    // Si no se proporcionó el ID de dirección, devolver un mensaje de error
    echo "<option value='' disabled>Error: ID de dirección no proporcionado</option>";
}
?>
