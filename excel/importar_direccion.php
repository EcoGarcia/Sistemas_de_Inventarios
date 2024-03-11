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
        'Fullname_categoria' => $worksheet->getCell('M' . $row)->getValue(),
        'Factura' => $worksheet->getCell('N' . $row)->getValue(),
        'Estado' => ($worksheet->getCell('O' . $row)->getValue() == 'Activo' ? 1 : 0),
    ];
}

// Insert the data into the resguardos_admin table
$localhost = 'localhost'; // Replace with your actual host
$root = 'root'; // Replace with your actual username
$sistemas = 'sistemas'; // Replace with your actual database name

// Data validation
if (empty($localhost) || empty($root) || empty($sistemas)) {
    die("Error: Incomplete database connection details. Please check your configuration.");
}

$conn = mysqli_connect($localhost, $root, '', $sistemas);

// Check connection
if (!$conn) {
    die("Conexión fallida: " . mysqli_connect_error());
}

$query = "INSERT INTO resguardos_direccion (Consecutivo_No, Fullname_direccion, Descripcion, Caracteristicas_Generales, Modelo, No_Serie, Color, Usuario_responsable, Comentarios, Observaciones, Condiciones, Marca, Fullname_categoria, Factura, Estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Prepared statement failed: " . $conn->error);
}

foreach ($data as $row) {
    $stmt->bind_param("ssssssssssssssi", $row['Consecutivo_No'], $row['Fullname_direccion'], $row['Descripcion'], $row['Caracteristicas_Generales'], $row['Modelo'], $row['No_Serie'], $row['Color'], $row['Usuario_responsable'], $row['Comentarios'], $row['Observaciones'], $row['Condiciones'], $row['Marca'], $row['Fullname_categoria'], $row['Factura'], $row['Estado']);
    
    if (!$stmt->execute()) {
        die("Error inserting data: " . $stmt->error);
    }
}

// Close the prepared statement and database connection
$stmt->close();
$conn->close();

echo "Data imported successfully.";
?>
