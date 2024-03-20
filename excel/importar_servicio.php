<?php
// Obtener los identificadores de dirección y coordinación del formulario
$identificador_direccion = $_POST['identificador_direccion'];
$identificador_coordinacion = $_POST['identificador_coordinacion'];

// Verificar si los identificadores están presentes y no son nulos
if (!isset($identificador_direccion) || !isset($identificador_coordinacion)) {
    die("Error: Identificadores de dirección y coordinación no proporcionados.");
}

require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

$inputFileType = 'Xlsx';
$inputFileName = $_FILES['file']['tmp_name'];

$spreadsheet = IOFactory::load($inputFileName);
$worksheet = $spreadsheet->getActiveSheet();
$totalRows = $worksheet->getHighestRow();

// Establecer la conexión con la base de datos para la consulta adicional
$localhost = 'localhost'; // Reemplazar con tu host real
$root = 'root'; // Reemplazar con tu nombre de usuario real
$sistemas = 'sistemas'; // Reemplazar con el nombre real de tu base de datos

// Validar datos
if (empty($localhost) || empty($root) || empty($sistemas)) {
    die("Error: Detalles incompletos de conexión a la base de datos. Por favor, verifica tu configuración.");
}


$conn = mysqli_connect($localhost, $root, '', $sistemas);

// Verificar la conexión
if (!$conn) {
    die("Conexión fallida: " . mysqli_connect_error());
}

// Obtener el último identificador utilizado en identificador_servicio
$query = "SELECT MAX(identificador_servicio) AS max_identificador_servicio FROM respaldos_servicios";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$lastIdentifierCoordinacion = $row['max_identificador_servicio'];
// Obtener el último identificador utilizado en identificador_usuario_coordinacion
$query = "SELECT MAX(identificador_usuario_servicios) AS max_identificador_usuario FROM respaldos_servicios";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$lastIdentifierUsuario = $row['max_identificador_usuario'];

// Datos del archivo Excel
$data = [];
for ($row = 2; $row <= $totalRows; $row++) {
    $data[] = [
        'Consecutivo_No' => $worksheet->getCell('A' . $row)->getValue(),
        'Fullname_direccion' => $worksheet->getCell('B' . $row)->getValue(),
        'Fullname_coordinacion' => $worksheet->getCell('C' . $row)->getValue(),
        'Fullname_servicio' => $worksheet->getCell('D' . $row)->getValue(),
        'Descripcion' => $worksheet->getCell('E' . $row)->getValue(),
        'Caracteristicas_Generales' => $worksheet->getCell('F' . $row)->getValue(),
        'Modelo' => $worksheet->getCell('G' . $row)->getValue(),
        'No_Serie' => $worksheet->getCell('H' . $row)->getValue(),
        'Color' => $worksheet->getCell('I' . $row)->getValue(),
        'Usuario_responsable' => $worksheet->getCell('J' . $row)->getValue(),
        'Comentarios' => $worksheet->getCell('K' . $row)->getValue(),
        'Observaciones' => $worksheet->getCell('L' . $row)->getValue(),
        'Condiciones' => $worksheet->getCell('M' . $row)->getValue(),
        'Marca' => $worksheet->getCell('N' . $row)->getValue(),
        'Nombre_categoria' => $worksheet->getCell('O' . $row)->getValue(),
        'Factura' => $worksheet->getCell('P' . $row)->getValue(),
        'Encargada_Area' => $worksheet->getCell('Q' . $row)->getValue(),
        'Coordinadora_Recursos' => $worksheet->getCell('R' . $row)->getValue(),
    ];
    // Obtener el valor del estado
    $estado = $worksheet->getCell('S' . $row)->getValue();

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


foreach ($data as &$row) {
    // Verificar si ya existe un servicio en la base de datos
    $query = "SELECT identificador_servicio FROM respaldos_servicios WHERE Fullname_servicio = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $row['Fullname_servicio']);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Si el servicio ya existe, obtener el identificador asociado
        $stmt->bind_result($existingIdentifierCoordinacion);
        $stmt->fetch();
        $row['identificador_servicio'] = $existingIdentifierCoordinacion;
    } else {
        // Si el servicio es nuevo, asignar un nuevo identificador
        $row['identificador_servicio'] = ++$lastIdentifierCoordinacion;
    }

    $stmt->close();
    // Asignar un nuevo identificador de usuario
    $row['identificador_usuario_servicios'] = $lastIdentifierUsuario + 1;
    $lastIdentifierUsuario++;

// Insertar los datos en la tabla respaldos_servicios
$query = "INSERT INTO respaldos_servicios (consecutivo, usuario_responsable, Fullname_direccion, Fullname_coordinacion, Fullname_servicio, descripcion, caracteristicas, marca, comentarios, modelo, serie, color, observaciones, fecha_creacion, Fullname_categoria, Factura, condiciones, Estado, Encargada_Area, Coordinadora_Recursos, identificador_servicio, identificador_usuario_servicios) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("La declaración preparada falló: " . $conn->error);
}

// Ajustar la cadena de definición de tipo según el número de variables
$stmt->bind_param("sssssssssssssssssssii", $row['Consecutivo_No'], $row['Usuario_responsable'], $row['Fullname_direccion'], $row['Fullname_coordinacion'], $row['Fullname_servicio'], $row['Descripcion'], $row['Caracteristicas_Generales'], $row['Marca'], $row['Comentarios'], $row['Modelo'], $row['No_Serie'], $row['Color'], $row['Observaciones'], $row['Nombre_categoria'], $row['Factura'], $row['Condiciones'], $row['Estado'], $row['Encargada_Area'], $row['Coordinadora_Recursos'], $row['identificador_servicio'], $row['identificador_usuario_servicios']);

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
    window.location.href = '../tarjeta/ver_servicio.php?identificador_coordinacion=$identificador_coordinacion';
</script>";
?>