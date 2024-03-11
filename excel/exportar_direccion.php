<?php
require_once '../vendor/autoload.php'; // Ruta al autoloader de Composer
require_once '../includes/conexion.php'; // Incluye tu archivo de conexión

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Crea un nuevo objeto Spreadsheet (antes PHPExcel)
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
$spreadsheet->getActiveSheet()->setCellValue('B1', 'Fullname_direccion');
$spreadsheet->getActiveSheet()->setCellValue('C1', 'Descripcion');
$spreadsheet->getActiveSheet()->setCellValue('D1', 'Caracteristicas_Generales');
$spreadsheet->getActiveSheet()->setCellValue('E1', 'Modelo');
$spreadsheet->getActiveSheet()->setCellValue('F1', 'No_Serie');
$spreadsheet->getActiveSheet()->setCellValue('G1', 'Color');
$spreadsheet->getActiveSheet()->setCellValue('H1', 'usuario_responsable');
$spreadsheet->getActiveSheet()->setCellValue('I1', 'comentarios');
$spreadsheet->getActiveSheet()->setCellValue('J1', 'Observaciones');
$spreadsheet->getActiveSheet()->setCellValue('K1', 'Condiciones');
$spreadsheet->getActiveSheet()->setCellValue('L1', 'Marca');
$spreadsheet->getActiveSheet()->setCellValue('N1', 'Fullname_categoria');
$spreadsheet->getActiveSheet()->setCellValue('M1', 'Factura');
$spreadsheet->getActiveSheet()->setCellValue('O1', 'fecha_creacion');
$spreadsheet->getActiveSheet()->setCellValue('P1', 'Estado');


// Obtén los datos desde la base de datos y agrega las filas correspondientes
$query = mysqli_query($conexion, "SELECT * FROM `resguardos_direccion`") or die(mysqli_error($conexion));
$rowIndex = 2; // Comienza desde la segunda fila
while ($fetch = mysqli_fetch_array($query)) {
    $spreadsheet->getActiveSheet()->setCellValue('A' . $rowIndex, $fetch['Consecutivo_No']);
    $spreadsheet->getActiveSheet()->setCellValue('B' . $rowIndex, $fetch['Fullname_direccion']);
    $spreadsheet->getActiveSheet()->setCellValue('C' . $rowIndex, $fetch['Descripcion']);
    $spreadsheet->getActiveSheet()->setCellValue('D' . $rowIndex, $fetch['Caracteristicas_Generales']);
    $spreadsheet->getActiveSheet()->setCellValue('E' . $rowIndex, $fetch['Modelo']);
    $spreadsheet->getActiveSheet()->setCellValue('F' . $rowIndex, $fetch['No_Serie']);
    $spreadsheet->getActiveSheet()->setCellValue('G' . $rowIndex, $fetch['Color']);
    $spreadsheet->getActiveSheet()->setCellValue('H' . $rowIndex, $fetch['usuario_responsable']);
    $spreadsheet->getActiveSheet()->setCellValue('I' . $rowIndex, $fetch['comentarios']);
    $spreadsheet->getActiveSheet()->setCellValue('J' . $rowIndex, $fetch['Observaciones']);
    $spreadsheet->getActiveSheet()->setCellValue('K' . $rowIndex, $fetch['Condiciones']);
    $spreadsheet->getActiveSheet()->setCellValue('L' . $rowIndex, $fetch['Marca']);
    $spreadsheet->getActiveSheet()->setCellValue('M' . $rowIndex, $fetch['Fullname_categoria']);
    $spreadsheet->getActiveSheet()->setCellValue('N' . $rowIndex, $fetch['Factura']);
    $spreadsheet->getActiveSheet()->setCellValue('O' . $rowIndex, $fetch['fecha_creacion']);
    $spreadsheet->getActiveSheet()->setCellValue('P' . $rowIndex, ($fetch['Estado'] == 1 ? 'Activo' : 'Baja'));


    // Ajusta automáticamente el ancho de la columna al contenido
    foreach (range('A', 'P') as $col) {
        $spreadsheet->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
    }

    $rowIndex++;
}

// Establece el nombre del archivo y tipo de archivo
$filename = "RESGUARDOS DE DIRECCIÓN" . date('Y-m-d_H:i:s') . ".xlsx";

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
