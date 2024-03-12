<?php
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

$inputFileType = 'Xlsx';
$inputFileName = $_FILES['file']['tmp_name'];

$spreadsheet = IOFactory::load($inputFileName);
$worksheet = $spreadsheet->getActiveSheet();
$totalRows = $worksheet->getHighestRow();

// Assuming the columns A to K contain the data
$data = [];
for ($row = 2; $row <= $totalRows; $row++) {
    $data[] = [
        'Consecutivo_No' => $worksheet->getCell('A' . $row)->getValue(),
        'Fullname_direccion' => $worksheet->getCell('B' . $row)->getValue(),
        'Descripcion' => $worksheet->getCell('D' . $row)->getValue(),
        'Caracteristicas_Generales' => $worksheet->getCell('E' . $row)->getValue(),
        'Modelo' => $worksheet->getCell('F' . $row)->getValue(),
        'No_Serie' => $worksheet->getCell('G' . $row)->getValue(),
        'Color' => $worksheet->getCell('H' . $row)->getValue(),
        'Comentarios' => $worksheet->getCell('K' . $row)->getValue(),
        'Observaciones' => $worksheet->getCell('L' . $row)->getValue(),
        'Condiciones' => $worksheet->getCell('M' . $row)->getValue(),
        'Marca' => $worksheet->getCell('N' . $row)->getValue(),
        'Factura' => $worksheet->getCell('Q' . $row)->getValue(),
        'Estado' => ($worksheet->getCell('S' . $row)->getValue() == 'Activo' ? 1 : 0),
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

foreach ($data as &$row) {
    // Consulta para recuperar el identificador basado en Fullname_direccion
    $queryDireccion = "SELECT identificador FROM direccion WHERE Fullname = ?";
    $stmtDireccion = $conn->prepare($queryDireccion);

    if (!$stmtDireccion) {
        die("La declaración preparada falló: " . $conn->error);
    }

    // Vincular parámetros y ejecutar la consulta
    $stmtDireccion->bind_param("s", $row['Fullname_direccion']);

    if (!$stmtDireccion->execute()) {
        die("Error al ejecutar la consulta: " . $stmtDireccion->error);
    }

    // Vincular la variable de resultado y obtener el resultado
    $stmtDireccion->bind_result($direccionIdentificador);

    if ($stmtDireccion->fetch()) {
        // Asignar el identificador recuperado al array de datos
        $row['Direccion_Identificador'] = $direccionIdentificador;

        // Liberar los resultados antes de la siguiente consulta
        $stmtDireccion->free_result();
    } else {
        // Si el nombre no existe, insértalo y obtén el nuevo identificador
        $insertQuery = "INSERT INTO direccion (Fullname) VALUES (?)";
        $stmtInsert = $conn->prepare($insertQuery);

        if (!$stmtInsert) {
            die("La declaración preparada falló: " . $conn->error);
        }

        // Vincular parámetros y ejecutar la consulta de inserción
        $stmtInsert->bind_param("s", $row['Fullname_direccion']);

        if (!$stmtInsert->execute()) {
            die("Error al insertar datos: " . $stmtInsert->error);
        }

        // Recuperar el nuevo identificador insertado
        $row['Direccion_Identificador'] = $stmtInsert->insert_id;

        // Cerrar la declaración preparada para la consulta de inserción
        $stmtInsert->close();
    }

    // Cerrar la declaración preparada y liberar los resultados
    $stmtDireccion->close();

}// Insertar los datos en la tabla resguardos_direccion
$query = "INSERT INTO resguardos_direccion (Consecutivo_No, Fullname_direccion, Descripcion, Caracteristicas_Generales, Modelo, No_Serie, Color, Comentarios, Observaciones, Condiciones, Marca, Factura, Estado, identificador_direccion) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("La declaración preparada falló: " . $conn->error);
}

foreach ($data as $row) {
    // Asegúrate de ajustar la cadena de definición de tipo según el número de variables
    $stmt->bind_param("sssssssssssssi", $row['Consecutivo_No'], $row['Fullname_direccion'], $row['Descripcion'], $row['Caracteristicas_Generales'], $row['Modelo'], $row['No_Serie'], $row['Color'], $row['Comentarios'], $row['Observaciones'], $row['Condiciones'], $row['Marca'], $row['Factura'], $row['Estado'], $row['Direccion_Identificador']);

    if (!$stmt->execute()) {
        die("Error al insertar datos: " . $stmt->error);
    }
}

// Cerrar la declaración preparada y la conexión a la base de datos
$stmt->close();
$conn->close();

echo "Datos importados exitosamente.";
?>
