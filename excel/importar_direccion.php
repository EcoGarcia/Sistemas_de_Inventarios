<?php
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

$inputFileType = 'Xlsx';
$inputFileName = $_FILES['file']['tmp_name'];

$spreadsheet = IOFactory::load($inputFileName);
$worksheet = $spreadsheet->getActiveSheet();
$totalRows = $worksheet->getHighestRow();

// Assuming the columns A to N contain the data
$data = [];
for ($row = 2; $row <= $totalRows; $row++) {
    $data[] = [
        'Consecutivo_No' => $worksheet->getCell('A' . $row)->getValue(),
        'Fullname_direccion' => $worksheet->getCell('B' . $row)->getValue(),
        'Descripcion' => $worksheet->getCell('C' . $row)->getValue(),
        'Caracteristicas_Generales' => $worksheet->getCell('D' . $row)->getValue(),
        'Modelo' => $worksheet->getCell('E' . $row)->getValue(),
        'No_Serie' => $worksheet->getCell('F' . $row)->getValue(),
        'Color' => $worksheet->getCell('G' . $row)->getValue(),
        'Usuario_responsable' => $worksheet->getCell('H' . $row)->getValue(),
        'Comentarios' => $worksheet->getCell('I' . $row)->getValue(),
        'Observaciones' => $worksheet->getCell('J' . $row)->getValue(),
        'Condiciones' => $worksheet->getCell('K' . $row)->getValue(),
        'Marca' => $worksheet->getCell('L' . $row)->getValue(),
        'Nombre_categoria' => $worksheet->getCell('M' . $row)->getValue(),
        'Factura' => $worksheet->getCell('N' . $row)->getValue(),
        'Encargada_Área' => $worksheet->getCell('O' . $row)->getValue(),
        'Coordinadora_Recursos' => $worksheet->getCell('P' . $row)->getValue(),
    ];
    // Obtener el valor del estado
$estado = $worksheet->getCell('Q' . $row)->getValue();

// Asignar el valor apropiado al campo 'Estado'
if (strtolower($estado) === 'activo') {
    $estadoValue = 1;
} elseif (strtolower($estado) === 'inactivo') {
    $estadoValue = 0;
} else {
    // Manejar otro caso si es necesario
    $estadoValue = ''; // Asignar un valor predeterminado o dejarlo en blanco según tu necesidad
}

// Asignar el valor del estado al campo 'Estado'
$data[$row - 2]['Estado'] = $estadoValue;
}



// Estableciendo conexión con la base de datos para la consulta adicional
$localhost = 'localhost'; // Reemplazar con tu host real
$root = 'root'; // Reemplazar con tu nombre de usuario real
$sistemas = 'sistemas'; // Reemplazar con el nombre real de tu base de datos

// Validación de datos
if (empty($localhost) || empty($root) || empty($sistemas)) {
    die("Error: Detalles incompletos de conexión a la base de datos. Por favor, verifica tu configuración.");
}

$conn = mysqli_connect($localhost, $root, '', $sistemas);

// Verificar la conexión
if (!$conn) {
    die("Conexión fallida: " . mysqli_connect_error());
}

// Obtener el último identificador utilizado en identificador_direccion
$query = "SELECT MAX(identificador_direccion) AS max_identificador FROM resguardos_direccion";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$lastIdentifier = $row['max_identificador'];

// Obtener el último identificador utilizado en identificador_categoria
$query = "SELECT MAX(identificador_categoria) AS max_identificador_categoria FROM resguardos_direccion";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$lastCategoryIdentifier = $row['max_identificador_categoria'];

// Obtener el último identificador utilizado en identificador_usuario_direccion
$query = "SELECT MAX(identificador_usuario_direccion) AS max_identificador_usuario FROM resguardos_direccion";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$lastIdentifierUsuario = $row['max_identificador_usuario'];
foreach ($data as &$row) {
    // Verificar si ya existe un registro con el mismo Fullname_direccion
    $query = "SELECT identificador_direccion FROM resguardos_direccion WHERE Fullname_direccion = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $row['Fullname_direccion']);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Si ya existe, obtener el identificador asociado
        $stmt->bind_result($existingIdentifier);
        $stmt->fetch();
        $row['identificador_direccion'] = $existingIdentifier;
    } else {
        // Si no existe, utilizar el siguiente identificador disponible
        $row['identificador_direccion'] = $lastIdentifier + 1;
        $lastIdentifier++;
    }

    $stmt->close();

    // Verificar si ya existe una categoría con el mismo Nombre_categoria
$query = "SELECT identificador_categoria FROM resguardos_direccion WHERE Fullname_categoria = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $row['Nombre_categoria']); // Aquí debería ser Fullname_categoria
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Si ya existe, obtener el identificador asociado
    $stmt->bind_result($existingCategoryIdentifier);
    $stmt->fetch();
    $row['identificador_categoria'] = $existingCategoryIdentifier;
} else {
    // Si no existe, utilizar el siguiente identificador disponible
    $row['identificador_categoria'] = $lastCategoryIdentifier + 1;
    $lastCategoryIdentifier++;
}

$stmt->close();

    // Asignar un nuevo identificador de usuario
    $row['identificador_usuario_direccion'] = $lastIdentifierUsuario + 1;
    $lastIdentifierUsuario++;

    // Insertar los datos en la tabla resguardos_direccion
    $query = "INSERT INTO resguardos_direccion (Consecutivo_No, Fullname_direccion, Descripcion, Caracteristicas_Generales, Modelo, No_Serie, Color, usuario_responsable, Comentarios, Observaciones, Condiciones, Marca, Fullname_categoria, Factura, Encargada_Area, Coordinadora_Recursos, Estado, identificador_direccion, identificador_usuario_direccion, identificador_categoria) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("La declaración preparada falló: " . $conn->error);
    }

    // Asegúrate de ajustar la cadena de definición de tipo según el número de variables
    $stmt->bind_param("ssssssssssssssssiiii", $row['Consecutivo_No'], $row['Fullname_direccion'], $row['Descripcion'], $row['Caracteristicas_Generales'], $row['Modelo'], $row['No_Serie'], $row['Color'], $row['Usuario_responsable'], $row['Comentarios'], $row['Observaciones'], $row['Condiciones'], $row['Marca'], $row['Nombre_categoria'], $row['Factura'], $row['Encargada_Área'], $row['Coordinadora_Recursos'], $row['Estado'], $row['identificador_direccion'], $row['identificador_usuario_direccion'], $row['identificador_categoria']);

    if (!$stmt->execute()) {
        die("Error al insertar datos: " . $stmt->error);
    }

    $stmt->close();

}
// Cerrar la conexión a la base de datos
$conn->close();

$notification_message = "Datos importados exitosamente.";
echo "<script>
    alert('$notification_message');
    window.location.href = '../dashboard/dashboard.php';
</script>";
?>

    // Insertar los datos en la tabla resguardos_direccion
    $query = "INSERT INTO resguardos_direccion (Consecutivo_No, Fullname_direccion, Descripcion, Caracteristicas_Generales, Modelo, No_Serie, Color, usuario_responsable, Comentarios, Observaciones, Condiciones, Marca, Fullname_categoria, Factura, Encargada_Area, Coordinadora_Recursos, Estado, identificador_direccion, identificador_usuario_direccion, identificador_categoria) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("La declaración preparada falló: " . $conn->error);
    }

    // Asegúrate de ajustar la cadena de definición de tipo según el número de variables
    $stmt->bind_param("ssssssssssssssssiiii", $row['Consecutivo_No'], $row['Fullname_direccion'], $row['Descripcion'], $row['Caracteristicas_Generales'], $row['Modelo'], $row['No_Serie'], $row['Color'], $row['Usuario_responsable'], $row['Comentarios'], $row['Observaciones'], $row['Condiciones'], $row['Marca'], $row['Nombre_categoria'], $row['Factura'], $row['Encargada_Área'], $row['Coordinadora_Recursos'], $row['Estado'], $row['identificador_direccion'], $row['identificador_usuario_direccion'], $row['identificador_categoria']);

    if (!$stmt->execute()) {
        die("Error al insertar datos: " . $stmt->error);
    }

    $stmt->close();

}
// Cerrar la conexión a la base de datos
$conn->close();

$notification_message = "Datos importados exitosamente.";
echo "<script>
    alert('$notification_message');
    window.location.href = '../dashboard/dashboard.php';
</script>";
?>