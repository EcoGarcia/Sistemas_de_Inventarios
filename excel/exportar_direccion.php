<?php
require_once '../vendor/autoload.php';
require_once '../includes/conexion.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Crea un nuevo objeto Spreadsheet
$spreadsheet = new Spreadsheet();

// Establece propiedades del documento
$spreadsheet->getProperties()->setCreator("Nombre del Creador")
                             ->setLastModifiedBy("Nombre del Modificador")
                             ->setTitle("Título del Documento")
                             ->setSubject("Asunto del Documento")
                             ->setDescription("Descripción del Documento")
                             ->setKeywords("palabras clave")
                             ->setCategory("Categoría del Documento");

// Agrega datos al objeto Spreadsheet
$spreadsheet->setActiveSheetIndex(0);
$spreadsheet->getActiveSheet()->setCellValue('A1', 'Consecutivo_No');
$spreadsheet->getActiveSheet()->setCellValue('B1', 'Nombre de la dirección');
$spreadsheet->getActiveSheet()->setCellValue('C1', 'Identificador de la dirección');
$spreadsheet->getActiveSheet()->setCellValue('D1', 'Descripcion');
$spreadsheet->getActiveSheet()->setCellValue('E1', 'Caracteristicas Generales');
$spreadsheet->getActiveSheet()->setCellValue('F1', 'Modelo');
$spreadsheet->getActiveSheet()->setCellValue('G1', 'No_Serie');
$spreadsheet->getActiveSheet()->setCellValue('H1', 'Color');
$spreadsheet->getActiveSheet()->setCellValue('I1', 'Usuario Responsable');
$spreadsheet->getActiveSheet()->setCellValue('J1', 'Identificador del usuario');
$spreadsheet->getActiveSheet()->setCellValue('K1', 'comentarios');
$spreadsheet->getActiveSheet()->setCellValue('L1', 'Observaciones');
$spreadsheet->getActiveSheet()->setCellValue('M1', 'Condiciones');
$spreadsheet->getActiveSheet()->setCellValue('N1', 'Marca');
$spreadsheet->getActiveSheet()->setCellValue('O1', 'Nombre de la categoria');
$spreadsheet->getActiveSheet()->setCellValue('P1', 'Identificador de la categoria');
$spreadsheet->getActiveSheet()->setCellValue('Q1', 'Factura');
$spreadsheet->getActiveSheet()->setCellValue('R1', 'fecha_creacion');
$spreadsheet->getActiveSheet()->setCellValue('S1', 'Estado');

// Obtén el identificador de dirección de la URL
$identificador_direccion = $_GET['identificador_direccion'];

// Consulta SQL modificada para seleccionar solo los datos asociados a la dirección específica
$query = mysqli_query($conexion, "SELECT * FROM `resguardos_direccion` WHERE identificador_direccion = $identificador_direccion") or die(mysqli_error($conexion));
$rowIndex = 2; // Comienza desde la segunda fila
while ($fetch = mysqli_fetch_array($query)) {
    // Inserta los datos en el archivo de Excel
    $spreadsheet->getActiveSheet()->setCellValue('A' . $rowIndex, $fetch['Consecutivo_No']);
    $spreadsheet->getActiveSheet()->setCellValue('B' . $rowIndex, $fetch['Fullname_direccion']);
    $spreadsheet->getActiveSheet()->setCellValue('C' . $rowIndex, $fetch['identificador_direccion']);
    $spreadsheet->getActiveSheet()->setCellValue('D' . $rowIndex, $fetch['Descripcion']);
    $spreadsheet->getActiveSheet()->setCellValue('E' . $rowIndex, $fetch['Caracteristicas_Generales']);
    $spreadsheet->getActiveSheet()->setCellValue('F' . $rowIndex, $fetch['Modelo']);
    $spreadsheet->getActiveSheet()->setCellValue('G' . $rowIndex, $fetch['No_Serie']);
    $spreadsheet->getActiveSheet()->setCellValue('H' . $rowIndex, $fetch['Color']);
    $spreadsheet->getActiveSheet()->setCellValue('I' . $rowIndex, $fetch['usuario_responsable']);
    $spreadsheet->getActiveSheet()->setCellValue('J' . $rowIndex, $fetch['Identificador_usuario_direccion']);
    $spreadsheet->getActiveSheet()->setCellValue('K' . $rowIndex, $fetch['comentarios']);
    $spreadsheet->getActiveSheet()->setCellValue('L' . $rowIndex, $fetch['Observaciones']);
    $spreadsheet->getActiveSheet()->setCellValue('M' . $rowIndex, $fetch['Condiciones']);
    $spreadsheet->getActiveSheet()->setCellValue('N' . $rowIndex, $fetch['Marca']);
    $spreadsheet->getActiveSheet()->setCellValue('O' . $rowIndex, $fetch['Fullname_categoria']);
    $spreadsheet->getActiveSheet()->setCellValue('P' . $rowIndex, $fetch['identificador_categoria']);
    $spreadsheet->getActiveSheet()->setCellValue('Q' . $rowIndex, $fetch['Factura']);
    $spreadsheet->getActiveSheet()->setCellValue('R' . $rowIndex, $fetch['fecha_creacion']);
    $spreadsheet->getActiveSheet()->setCellValue('S' . $rowIndex, ($fetch['Estado'] == 1 ? 'Activo' : 'Baja'));

    // Ajusta automáticamente el ancho de la columna al contenido
    foreach (range('A', 'S') as $col) {
        $spreadsheet->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
    }

    $rowIndex++;
}

// Establece el nombre del archivo y tipo de archivo
$filename = "RESGUARDOS_DE_DIRECCIÓN_" . date('Y-m-d_H-i-s') . ".xlsx";

// Configura el Writer para guardar el archivo en formato Excel (xlsx)
$writer = new Xlsx($spreadsheet);

// Configura las cabeceras para la descarga del archivo
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

// Limpia el búfer de salida antes de enviar las cabeceras
ob_end_clean();

// Guarda el archivo en formato Excel (xlsx) y envía al navegador
$writer->save('php://output');

// Termina la ejecución del script
exit;
?>
