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
$spreadsheet->getActiveSheet()->setCellValue('A1', 'Consecutivo');
$spreadsheet->getActiveSheet()->setCellValue('B1', 'Identificador de dirección');
$spreadsheet->getActiveSheet()->setCellValue('C1', 'Identificador de coordinación');
$spreadsheet->getActiveSheet()->setCellValue('D1', 'Usuario Responsable');
$spreadsheet->getActiveSheet()->setCellValue('E1', 'Identificador del usuario de coordinación');
$spreadsheet->getActiveSheet()->setCellValue('F1', 'Nombre de dirección');
$spreadsheet->getActiveSheet()->setCellValue('G1', 'Nombre de coordinación');
$spreadsheet->getActiveSheet()->setCellValue('H1', 'Descripción');
$spreadsheet->getActiveSheet()->setCellValue('I1', 'Características');
$spreadsheet->getActiveSheet()->setCellValue('J1', 'Marca');
$spreadsheet->getActiveSheet()->setCellValue('K1', 'Comentarios');
$spreadsheet->getActiveSheet()->setCellValue('L1', 'Modelo');
$spreadsheet->getActiveSheet()->setCellValue('M1', 'Número de Serie');
$spreadsheet->getActiveSheet()->setCellValue('N1', 'Color');
$spreadsheet->getActiveSheet()->setCellValue('O1', 'Observaciones');
$spreadsheet->getActiveSheet()->setCellValue('P1', 'Fecha de Creación');
$spreadsheet->getActiveSheet()->setCellValue('Q1', 'Identificador de categoría');
$spreadsheet->getActiveSheet()->setCellValue('R1', 'Nombre de categoría');
$spreadsheet->getActiveSheet()->setCellValue('S1', 'Factura');
$spreadsheet->getActiveSheet()->setCellValue('T1', 'Condiciones');
$spreadsheet->getActiveSheet()->setCellValue('U1', 'Estado');

// Obtén los datos desde la base de datos y agrega las filas correspondientes
$query = mysqli_query($conexion, "SELECT * FROM `respaldos_coordinacion`") or die(mysqli_error($conexion));
$rowIndex = 2; // Comienza desde la segunda fila
while ($fetch = mysqli_fetch_array($query)) {
    $spreadsheet->getActiveSheet()->setCellValue('A' . $rowIndex, $fetch['consecutivo']);
    $spreadsheet->getActiveSheet()->setCellValue('B' . $rowIndex, $fetch['identificador_direccion']);
    $spreadsheet->getActiveSheet()->setCellValue('C' . $rowIndex, $fetch['identificador_coordinacion']);
    $spreadsheet->getActiveSheet()->setCellValue('D' . $rowIndex, $fetch['usuario_responsable']);
    $spreadsheet->getActiveSheet()->setCellValue('E' . $rowIndex, $fetch['identificador_usuario_coordinacion']);
    $spreadsheet->getActiveSheet()->setCellValue('F' . $rowIndex, $fetch['Fullname_direccion']);
    $spreadsheet->getActiveSheet()->setCellValue('G' . $rowIndex, $fetch['Fullname_coordinacion']);
    $spreadsheet->getActiveSheet()->setCellValue('H' . $rowIndex, $fetch['descripcion']);
    $spreadsheet->getActiveSheet()->setCellValue('I' . $rowIndex, $fetch['caracteristicas']);
    $spreadsheet->getActiveSheet()->setCellValue('J' . $rowIndex, $fetch['marca']);
    $spreadsheet->getActiveSheet()->setCellValue('K' . $rowIndex, $fetch['comentarios']);
    $spreadsheet->getActiveSheet()->setCellValue('L' . $rowIndex, $fetch['modelo']);
    $spreadsheet->getActiveSheet()->setCellValue('M' . $rowIndex, $fetch['serie']);
    $spreadsheet->getActiveSheet()->setCellValue('N' . $rowIndex, $fetch['color']);
    $spreadsheet->getActiveSheet()->setCellValue('O' . $rowIndex, $fetch['observaciones']);
    $spreadsheet->getActiveSheet()->setCellValue('P' . $rowIndex, $fetch['fecha_creacion']);
    $spreadsheet->getActiveSheet()->setCellValue('Q' . $rowIndex, $fetch['identificador_categoria']);
    $spreadsheet->getActiveSheet()->setCellValue('R' . $rowIndex, $fetch['Fullname_categoria']);
    $spreadsheet->getActiveSheet()->setCellValue('S' . $rowIndex, $fetch['Factura']);
    $spreadsheet->getActiveSheet()->setCellValue('T' . $rowIndex, $fetch['Condiciones']);
    $spreadsheet->getActiveSheet()->setCellValue('U' . $rowIndex, ($fetch['Estado'] == 1 ? 'Activo' : 'Baja'));

    // Ajusta automáticamente el ancho de la columna al contenido
    foreach (range('A', 'W') as $col) {
        $spreadsheet->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
    }

    $rowIndex++;
}

// Establece el nombre del archivo y tipo de archivo
$filename = "RESGUARDOS DE COORDINACIÓN" . date('Y-m-d_H:i:s') . ".xlsx";

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
