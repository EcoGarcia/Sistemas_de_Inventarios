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

// Establishing connection to the database for the additional query
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

foreach ($data as &$row) {
    // Query to retrieve the identifier based on the Fullname_direccion
    $queryDireccion = "SELECT identificador FROM direccion WHERE Fullname = ?";
    $stmtDireccion = $conn->prepare($queryDireccion);

    if (!$stmtDireccion) {
        die("Prepared statement failed: " . $conn->error);
    }

    // Binding parameters and executing the query
    $stmtDireccion->bind_param("s", $row['Fullname_direccion']);

    if (!$stmtDireccion->execute()) {
        die("Error executing query: " . $stmtDireccion->error);
    }

    // Binding the result variable and fetching the result
    $stmtDireccion->bind_result($direccionIdentificador);

    if ($stmtDireccion->fetch()) {
        // Assigning the retrieved identifier to the data array
        $row['Direccion_Identificador'] = $direccionIdentificador;
    } else {
        // If the name doesn't exist, insert it and get the new identifier
        $insertQuery = "INSERT INTO direccion (Fullname) VALUES (?)";
        $stmtInsert = $conn->prepare($insertQuery);

        if (!$stmtInsert) {
            die("Prepared statement failed: " . $conn->error);
        }

        // Binding parameters and executing the insert query
        $stmtInsert->bind_param("s", $row['Fullname_direccion']);

        if (!$stmtInsert->execute()) {
            die("Error inserting data: " . $stmtInsert->error);
        }

        // Retrieving the newly inserted identifier
        $row['Direccion_Identificador'] = $stmtInsert->insert_id;

        // Closing the prepared statement for the insert query
        $stmtInsert->close();
    }

    // Closing the prepared statement and database connection for the additional query
    $stmtDireccion->close();
}

// Insert the data into the resguardos_direccion table
$query = "INSERT INTO resguardos_direccion (Consecutivo_No, Fullname_direccion, Descripcion, Caracteristicas_Generales, Modelo, No_Serie, Color, Usuario_responsable, Comentarios, Observaciones, Condiciones, Marca, Fullname_categoria, Factura, Estado, identificador_direccion) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Prepared statement failed: " . $conn->error);
}

foreach ($data as $row) {
    $stmt->bind_param("ssssssssssssssii", $row['Consecutivo_No'], $row['Fullname_direccion'], $row['Descripcion'], $row['Caracteristicas_Generales'], $row['Modelo'], $row['No_Serie'], $row['Color'], $row['Usuario_responsable'], $row['Comentarios'], $row['Observaciones'], $row['Condiciones'], $row['Marca'], $row['Fullname_categoria'], $row['Factura'], $row['Estado'], $row['Direccion_Identificador']);
    
    if (!$stmt->execute()) {
        die("Error inserting data: " . $stmt->error);
    }
}

// Close the prepared statement and database connection
$stmt->close();
$conn->close();

echo "Data imported successfully.";
?>
