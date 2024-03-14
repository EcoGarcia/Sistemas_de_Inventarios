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
        'Fecha_creacion' => $worksheet->getCell('O' . $row)->getValue(),
        'Encargada_Área' => $worksheet->getCell('P' . $row)->getValue(),
        'Coordinación_recursos' => $worksheet->getCell('Q' . $row)->getValue(),
        'Estado' => $worksheet->getCell('R' . $row)->getValue(),
    ];
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
$newIdentifier = $lastIdentifier + 1;

// Obtener el último identificador utilizado en identificador_usuario_direccion
$query = "SELECT MAX(identificador_usuario_direccion) AS max_identificador_usuario FROM resguardos_direccion";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$lastIdentifierUsuario = $row['max_identificador_usuario'];
$newIdentifierUsuario = $lastIdentifierUsuario + 1;

foreach ($data as &$row) {
    // Asignar un nuevo identificador a cada fila de datos
    $row['identificador_direccion'] = $newIdentifier;
    $row['identificador_usuario_direccion'] = $newIdentifierUsuario;

    // Insertar los datos en la tabla resguardos_direccion
    $query = "INSERT INTO resguardos_direccion (Consecutivo_No, Fullname_direccion, Descripcion, Caracteristicas_Generales, Modelo, No_Serie, Color, usuario_responsable, Comentarios, Observaciones, Condiciones, Marca, Factura, Estado, identificador_direccion, identificador_usuario_direccion) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("La declaración preparada falló: " . $conn->error);
    }

    // Asegúrate de ajustar la cadena de definición de tipo según el número de variables
    $stmt->bind_param("ssssssssssssssii", $row['Consecutivo_No'], $row['Fullname_direccion'], $row['Descripcion'], $row['Caracteristicas_Generales'], $row['Modelo'], $row['No_Serie'], $row['Color'], $row['Usuario_responsable'], $row['Comentarios'], $row['Observaciones'], $row['Condiciones'], $row['Marca'], $row['Factura'], $row['Estado'], $newIdentifier, $newIdentifierUsuario);

    if (!$stmt->execute()) {
        die("Error al insertar datos: " . $stmt->error);
    }

    $stmt->close();

    // Incrementar los nuevos identificadores para la próxima inserción
    $newIdentifier++;
    $newIdentifierUsuario++;
}


// Cerrar la conexión a la base de datos
$conn->close();

echo "Datos importados exitosamente.";
?>
